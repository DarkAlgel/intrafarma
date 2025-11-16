-- Schema Farmácia (PostgreSQL)
-- Consolida entidades, enums, constraints, views, triggers e funções
-- cobrindo as regras de negócio incluídas nas análises (inclui itens 3.1–3.7).

-- =====================
-- Tipos Enumerados
-- =====================
CREATE TYPE tarja_tipo AS ENUM ('sem_tarja', 'tarja_amarela', 'tarja_vermelha', 'tarja_preta');
CREATE TYPE forma_retirada_tipo AS ENUM ('MIP', 'com_prescricao');
CREATE TYPE forma_fisica_tipo AS ENUM ('solida', 'pastosa', 'liquida', 'gasosa');
CREATE TYPE unidade_contagem AS ENUM ('comprimido', 'capsula', 'dragea', 'sache', 'ampola', 'frasco', 'caixa', 'ml', 'g', 'unidade', 'aerosol', 'xarope', 'solucao');
CREATE TYPE estado_item AS ENUM ('novo', 'lacrado', 'aberto', 'avariado');
CREATE TYPE fornecedor_tipo AS ENUM ('doacao', 'compra', 'parceria', 'outros');

-- =====================
-- Tabelas Mestres
-- =====================
CREATE TABLE laboratorios (
  id BIGSERIAL PRIMARY KEY,
  nome TEXT NOT NULL UNIQUE
);

-- Cada classe tem um código fixo (ex.: 1 vitaminas, 2 analgésicos, ...)
CREATE TABLE classes_terapeuticas (
  id BIGSERIAL PRIMARY KEY,
  codigo_classe SMALLINT NOT NULL UNIQUE,
  nome TEXT NOT NULL UNIQUE
);

CREATE TABLE fornecedores (
  id BIGSERIAL PRIMARY KEY,
  nome TEXT NOT NULL,
  tipo fornecedor_tipo NOT NULL DEFAULT 'doacao',
  contato TEXT
);

CREATE TABLE pacientes (
  id BIGSERIAL PRIMARY KEY,
  nome TEXT NOT NULL,
  cpf TEXT NOT NULL UNIQUE,
  telefone TEXT,
  cidade TEXT
);

-- =====================
-- Medicamentos
-- =====================
CREATE TABLE medicamentos (
  id BIGSERIAL PRIMARY KEY,
  codigo TEXT NOT NULL UNIQUE,             -- código único manual (CPF do remédio)
  nome TEXT NOT NULL,                      -- princípio ativo + dosagem + apresentação
  laboratorio_id BIGINT REFERENCES laboratorios(id) ON DELETE RESTRICT,
  classe_terapeutica_id BIGINT NOT NULL REFERENCES classes_terapeuticas(id) ON DELETE RESTRICT,
  tarja tarja_tipo NOT NULL,
  forma_retirada forma_retirada_tipo NOT NULL,
  forma_fisica forma_fisica_tipo NOT NULL,
  apresentacao unidade_contagem NOT NULL,  -- ex.: comprimido/cápsula/solução
  unidade_base unidade_contagem NOT NULL,  -- unidade para contagem de estoque
  dosagem_valor NUMERIC(12,3) NOT NULL,
  dosagem_unidade TEXT NOT NULL,           -- ex.: mg, ml
  generico BOOLEAN NOT NULL DEFAULT FALSE,
  limite_minimo NUMERIC(12,3) NOT NULL DEFAULT 0,
  serial_por_classe INTEGER NOT NULL,      -- número sequencial dentro da classe (3.3)
  ativo BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE INDEX idx_medicamentos_classe ON medicamentos (classe_terapeutica_id);
CREATE INDEX idx_medicamentos_tarja ON medicamentos (tarja);
CREATE INDEX idx_medicamentos_generico ON medicamentos (generico);

-- Serial automático por classe terapêutica (3.3)
CREATE OR REPLACE FUNCTION fn_set_serial_por_classe()
RETURNS TRIGGER LANGUAGE plpgsql AS
$$
BEGIN
  IF NEW.serial_por_classe IS NOT NULL THEN
    RETURN NEW;
  END IF;

  SELECT COALESCE(MAX(m.serial_por_classe), 0) + 1
  INTO NEW.serial_por_classe
  FROM medicamentos m
  WHERE m.classe_terapeutica_id = NEW.classe_terapeutica_id;

  RETURN NEW;
END;
$$;

CREATE TRIGGER trg_set_serial_por_classe
BEFORE INSERT ON medicamentos
FOR EACH ROW EXECUTE FUNCTION fn_set_serial_por_classe();

-- =====================
-- Lotes (por validade, com agrupamento mensal)
-- =====================
CREATE TABLE lotes (
  id BIGSERIAL PRIMARY KEY,
  medicamento_id BIGINT NOT NULL REFERENCES medicamentos(id) ON DELETE RESTRICT,
  data_fabricacao DATE NOT NULL,
  validade DATE NOT NULL,
  validade_mes DATE GENERATED ALWAYS AS (
    make_date(
      date_part('year', validade)::int,
      date_part('month', validade)::int,
      1
    )
  ) STORED,
  nome_comercial TEXT,
  ativo BOOLEAN NOT NULL DEFAULT TRUE,
  observacao TEXT,
  UNIQUE (medicamento_id, validade_mes)  -- um lote por mês de validade (3.1 e 3.7)
);

CREATE INDEX idx_lotes_validade ON lotes (validade);
CREATE INDEX idx_lotes_validade_mes ON lotes (validade_mes);

-- =====================
-- Entradas e Dispensações
-- =====================
CREATE TABLE entradas (
  id BIGSERIAL PRIMARY KEY,
  data_entrada DATE NOT NULL DEFAULT CURRENT_DATE,
  fornecedor_id BIGINT NOT NULL REFERENCES fornecedores(id) ON DELETE RESTRICT,
  lote_id BIGINT NOT NULL REFERENCES lotes(id) ON DELETE RESTRICT,
  numero_lote_fornecedor TEXT NOT NULL,     -- lote do fabricante (único por medicamento)
  quantidade_informada NUMERIC(12,3) NOT NULL CHECK (quantidade_informada > 0),
  quantidade_base NUMERIC(12,3) NOT NULL CHECK (quantidade_base > 0),
  unidade unidade_contagem NOT NULL,
  unidades_por_embalagem NUMERIC(12,3),     -- ex.: 1 caixa = 20 comprimidos; 1 frasco = 120 ml
  estado estado_item,
  observacao TEXT,
  UNIQUE (lote_id, numero_lote_fornecedor)
);

CREATE INDEX idx_entradas_lote ON entradas (lote_id);

CREATE TABLE dispensacoes (
  id BIGSERIAL PRIMARY KEY,
  data_dispensa TIMESTAMP NOT NULL DEFAULT NOW(),
  responsavel TEXT,                          -- opcional (3.5)
  paciente_id BIGINT NOT NULL REFERENCES pacientes(id) ON DELETE RESTRICT,
  lote_id BIGINT NOT NULL REFERENCES lotes(id) ON DELETE RESTRICT,
  dosagem TEXT,
  nome_comercial TEXT,
  quantidade_informada NUMERIC(12,3) NOT NULL CHECK (quantidade_informada > 0),
  quantidade_base NUMERIC(12,3) NOT NULL CHECK (quantidade_base > 0),
  unidade unidade_contagem NOT NULL,
  numero_receita TEXT                        -- obrigatório quando com prescrição (trigger)
);

CREATE INDEX idx_dispensacoes_lote ON dispensacoes (lote_id);
CREATE INDEX idx_dispensacoes_data ON dispensacoes (data_dispensa);

-- =====================
-- Triggers: conversão de quantidade e validações
-- =====================
-- Converte quantidade informada para quantidade base da unidade do medicamento na ENTRADA
CREATE OR REPLACE FUNCTION fn_calc_quantidade_base_entrada()
RETURNS TRIGGER LANGUAGE plpgsql AS
$$
DECLARE
  unid_base unidade_contagem;
BEGIN
  SELECT m.unidade_base INTO unid_base
  FROM lotes l JOIN medicamentos m ON m.id = l.medicamento_id
  WHERE l.id = NEW.lote_id;

  IF unid_base IS NULL THEN
    RAISE EXCEPTION 'Unidade base não definida para o medicamento do lote %', NEW.lote_id;
  END IF;

  IF NEW.unidade = unid_base THEN
    NEW.quantidade_base := NEW.quantidade_informada;
  ELSE
    IF NEW.unidades_por_embalagem IS NULL THEN
      RAISE EXCEPTION 'Informe unidades_por_embalagem para converter % em %', NEW.unidade, unid_base;
    ELSE
      NEW.quantidade_base := NEW.quantidade_informada * NEW.unidades_por_embalagem;
    END IF;
  END IF;

  RETURN NEW;
END;
$$;

CREATE TRIGGER trg_calc_quantidade_base_entrada
BEFORE INSERT OR UPDATE ON entradas
FOR EACH ROW EXECUTE FUNCTION fn_calc_quantidade_base_entrada();

-- Converte quantidade informada para quantidade base na DISPENSAÇÃO (usa última referência de embalagem)
CREATE OR REPLACE FUNCTION fn_calc_quantidade_base_dispensacao()
RETURNS TRIGGER LANGUAGE plpgsql AS
$$
DECLARE
  unid_base unidade_contagem;
  fator NUMERIC(12,3);
BEGIN
  SELECT m.unidade_base INTO unid_base
  FROM lotes l JOIN medicamentos m ON m.id = l.medicamento_id
  WHERE l.id = NEW.lote_id;

  IF unid_base IS NULL THEN
    RAISE EXCEPTION 'Unidade base não definida para o medicamento do lote %', NEW.lote_id;
  END IF;

  IF NEW.unidade = unid_base THEN
    NEW.quantidade_base := NEW.quantidade_informada;
  ELSE
    SELECT en.unidades_por_embalagem
    INTO fator
    FROM entradas en
    WHERE en.lote_id = NEW.lote_id AND en.unidade = NEW.unidade
    ORDER BY en.id DESC
    LIMIT 1;

    IF fator IS NULL THEN
      RAISE EXCEPTION 'Não foi possível converter unidade % para unidade base % no lote %', NEW.unidade, unid_base, NEW.lote_id;
    END IF;

    NEW.quantidade_base := NEW.quantidade_informada * fator;
  END IF;

  RETURN NEW;
END;
$$;

CREATE TRIGGER trg_calc_quantidade_base_dispensacao
BEFORE INSERT OR UPDATE ON dispensacoes
FOR EACH ROW EXECUTE FUNCTION fn_calc_quantidade_base_dispensacao();

-- Impede dispensação acima do saldo e de lote vencido; exige receita quando necessário (3.4, 3.5)
CREATE OR REPLACE FUNCTION fn_check_saldo_e_validade_dispensacao()
RETURNS TRIGGER LANGUAGE plpgsql AS
$$
DECLARE
  saldo_atual NUMERIC(12,3);
  validade_lote DATE;
  forma forma_retirada_tipo;
BEGIN
  SELECT l.validade, m.forma_retirada INTO validade_lote, forma
  FROM lotes l JOIN medicamentos m ON m.id = l.medicamento_id
  WHERE l.id = NEW.lote_id;

  IF validade_lote < CURRENT_DATE THEN
    RAISE EXCEPTION 'Lote % vencido (validade=%). Dispensação bloqueada.', NEW.lote_id, validade_lote;
  END IF;

  SELECT COALESCE(SUM(quantidade_base), 0) INTO saldo_atual FROM entradas WHERE lote_id = NEW.lote_id;
  saldo_atual := saldo_atual - COALESCE((SELECT SUM(quantidade_base) FROM dispensacoes WHERE lote_id = NEW.lote_id), 0);

  IF NEW.quantidade_base > GREATEST(saldo_atual, 0) THEN
    RAISE EXCEPTION 'Saldo insuficiente para o lote %, saldo=%, pedido(base)=%', NEW.lote_id, saldo_atual, NEW.quantidade_base;
  END IF;

  IF forma = 'com_prescricao' AND (NEW.numero_receita IS NULL OR LENGTH(TRIM(NEW.numero_receita)) = 0) THEN
    RAISE EXCEPTION 'Número de receita obrigatório para medicamento com prescrição.';
  END IF;

  RETURN NEW;
END;
$$;

CREATE TRIGGER trg_check_saldo_e_validade_dispensacao
BEFORE INSERT OR UPDATE ON dispensacoes
FOR EACH ROW EXECUTE FUNCTION fn_check_saldo_e_validade_dispensacao();

-- =====================
-- Views de Estoque e Alertas
-- =====================
CREATE OR REPLACE VIEW vw_estoque_por_lote AS
SELECT 
  l.id AS lote_id,
  m.id AS medicamento_id,
  m.nome AS medicamento,
  m.codigo,
  m.generico,
  m.tarja,
  m.forma_retirada,
  m.forma_fisica,
  m.apresentacao,
  m.unidade_base,
  m.dosagem_valor,
  m.dosagem_unidade,
  l.data_fabricacao,
  l.validade,
  l.validade_mes,
  COALESCE((SELECT SUM(e.quantidade_base) FROM entradas e WHERE e.lote_id = l.id), 0) AS quantidade_entrada,
  COALESCE((SELECT SUM(d.quantidade_base) FROM dispensacoes d WHERE d.lote_id = l.id), 0) AS quantidade_saida,
  COALESCE((SELECT SUM(e.quantidade_base) FROM entradas e WHERE e.lote_id = l.id), 0)
    - COALESCE((SELECT SUM(d.quantidade_base) FROM dispensacoes d WHERE d.lote_id = l.id), 0) AS quantidade_disponivel,
  (l.validade - CURRENT_DATE) AS dias_para_vencimento,
  CASE 
    WHEN l.validade < CURRENT_DATE THEN 'Bloquear dispensação'
    WHEN l.validade <= (CURRENT_DATE + INTERVAL '30 days') THEN 'Próximo de vencer'
    ELSE 'OK'
  END AS status
FROM lotes l
JOIN medicamentos m ON m.id = l.medicamento_id;

CREATE OR REPLACE VIEW vw_estoque_por_medicamento AS
SELECT 
  m.id AS medicamento_id,
  m.nome,
  m.codigo,
  m.unidade_base,
  m.dosagem_valor,
  m.dosagem_unidade,
  m.tarja,
  m.generico,
  COALESCE(e.total_entrada, 0) AS quantidade_entrada,
  COALESCE(d.total_saida, 0) AS quantidade_saida,
  COALESCE(e.total_entrada, 0) - COALESCE(d.total_saida, 0) AS quantidade_disponivel,
  m.limite_minimo AS limite_minimo,
  CASE WHEN (COALESCE(e.total_entrada, 0) - COALESCE(d.total_saida, 0)) <= m.limite_minimo THEN TRUE ELSE FALSE END AS alerta_minimo,
  CASE WHEN (COALESCE(e.total_entrada, 0) - COALESCE(d.total_saida, 0)) < 10 THEN TRUE ELSE FALSE END AS alerta_menos_que_10_unidades,
  CASE WHEN e.total_entrada IS NOT NULL AND e.total_entrada > 0 AND (COALESCE(e.total_entrada, 0) - COALESCE(d.total_saida, 0)) <= (0.20 * e.total_entrada) THEN TRUE ELSE FALSE END AS alerta_menos_que_20_porcento
FROM medicamentos m
LEFT JOIN (
  SELECT l.medicamento_id, SUM(en.quantidade_base) AS total_entrada
  FROM lotes l
  JOIN entradas en ON en.lote_id = l.id
  GROUP BY l.medicamento_id
) e ON e.medicamento_id = m.id
LEFT JOIN (
  SELECT l.medicamento_id, SUM(di.quantidade_base) AS total_saida
  FROM lotes l
  JOIN dispensacoes di ON di.lote_id = l.id
  GROUP BY l.medicamento_id
) d ON d.medicamento_id = m.id;

-- Alertas de validade (<=30 dias ou vencidos)
CREATE OR REPLACE VIEW vw_alerta_validade AS
SELECT *
FROM vw_estoque_por_lote
WHERE status IN ('Próximo de vencer', 'Bloquear dispensação');

-- Alerta do início do mês: validade do lote coincide com o mês atual (3.1)
CREATE OR REPLACE VIEW vw_alerta_validade_mes_atual AS
SELECT *
FROM vw_estoque_por_lote
WHERE validade_mes = DATE_TRUNC('month', CURRENT_DATE)::date;

-- Alertas de estoque baixo (mínimo, <10 unidades, <=20% do estoque inicial)
CREATE OR REPLACE VIEW vw_alerta_estoque_baixo AS
SELECT *
FROM vw_estoque_por_medicamento
WHERE alerta_minimo = TRUE
   OR alerta_menos_que_10_unidades = TRUE
   OR alerta_menos_que_20_porcento = TRUE;

-- =====================
-- Funções de Arquivamento (Exclusão lógica) por falta de estoque (3.6)
-- =====================
CREATE OR REPLACE FUNCTION fn_arquivar_lote_se_sem_saldo_ou_vencido(p_lote_id BIGINT)
RETURNS BOOLEAN LANGUAGE plpgsql AS
$$
DECLARE
  saldo NUMERIC(12,3);
  vencido BOOLEAN;
BEGIN
  SELECT 
    COALESCE((SELECT SUM(e.quantidade_base) FROM entradas e WHERE e.lote_id = l.id), 0)
    - COALESCE((SELECT SUM(d.quantidade_base) FROM dispensacoes d WHERE d.lote_id = l.id), 0),
    (l.validade < CURRENT_DATE)
  INTO saldo, vencido
  FROM lotes l
  WHERE l.id = p_lote_id
  FOR UPDATE;

  IF NOT FOUND THEN
    RAISE EXCEPTION 'Lote % não encontrado', p_lote_id;
  END IF;

  IF saldo <= 0 OR vencido THEN
    UPDATE lotes SET ativo = FALSE WHERE id = p_lote_id;
    RETURN TRUE;
  ELSE
    RETURN FALSE;
  END IF;
END;
$$;

CREATE OR REPLACE FUNCTION fn_arquivar_medicamento_se_sem_saldo(p_medicamento_id BIGINT)
RETURNS BOOLEAN LANGUAGE plpgsql AS
$$
DECLARE
  saldo NUMERIC(12,3);
BEGIN
  SELECT 
    COALESCE((SELECT SUM(e.quantidade_base) FROM entradas e JOIN lotes l2 ON l2.id = e.lote_id WHERE l2.medicamento_id = m.id), 0)
    - COALESCE((SELECT SUM(d.quantidade_base) FROM dispensacoes d JOIN lotes l3 ON l3.id = d.lote_id WHERE l3.medicamento_id = m.id), 0)
  INTO saldo
  FROM medicamentos m
  WHERE m.id = p_medicamento_id
  FOR UPDATE;

  IF NOT FOUND THEN
    RAISE EXCEPTION 'Medicamento % não encontrado', p_medicamento_id;
  END IF;

  IF saldo <= 0 THEN
    UPDATE medicamentos SET ativo = FALSE WHERE id = p_medicamento_id;
    RETURN TRUE;
  ELSE
    RETURN FALSE;
  END IF;
END;
$$;

-- =====================
-- Índices adicionais
-- =====================
CREATE INDEX idx_pacientes_cpf ON pacientes (cpf);
CREATE INDEX idx_fornecedores_tipo ON fornecedores (tipo);

-- =====================
-- Usuarios e Controle de Acesso (RBAC)
-- =====================
CREATE EXTENSION IF NOT EXISTS pgcrypto;

CREATE TABLE usuarios (
  id BIGSERIAL PRIMARY KEY,
  nome TEXT NOT NULL,
  celular TEXT,
  email TEXT NOT NULL UNIQUE,
  login TEXT NOT NULL UNIQUE,
  senha_hash TEXT NOT NULL,
  datacadastro TIMESTAMP NOT NULL DEFAULT NOW(),
  ultimoacesso TIMESTAMP,
  ativo BOOLEAN NOT NULL DEFAULT TRUE
);

-- Hash automático de senha (preserva se já vier em formato hash começando com '$')
CREATE OR REPLACE FUNCTION fn_hash_senha_usuarios()
RETURNS TRIGGER LANGUAGE plpgsql AS
$$
BEGIN
  IF NEW.senha_hash IS NULL OR LENGTH(TRIM(NEW.senha_hash)) = 0 THEN
    RAISE EXCEPTION 'Senha não pode ser vazia';
  END IF;

  IF POSITION('$' IN NEW.senha_hash) = 1 THEN
    RETURN NEW; -- já é hash
  ELSE
    NEW.senha_hash := crypt(NEW.senha_hash, gen_salt('bf'));
    RETURN NEW;
  END IF;
END;
$$;

CREATE TRIGGER trg_hash_senha_usuarios
BEFORE INSERT OR UPDATE OF senha_hash ON usuarios
FOR EACH ROW EXECUTE FUNCTION fn_hash_senha_usuarios();

CREATE TABLE permissoes (
  id BIGSERIAL PRIMARY KEY,
  codigo TEXT NOT NULL UNIQUE,
  nome TEXT NOT NULL
);

CREATE TABLE papeis (
  id BIGSERIAL PRIMARY KEY,
  nome TEXT NOT NULL UNIQUE,
  descricao TEXT
);

CREATE TABLE papeis_permissoes (
  id BIGSERIAL PRIMARY KEY,
  papel_id BIGINT NOT NULL REFERENCES papeis(id) ON DELETE CASCADE,
  permissao_id BIGINT NOT NULL REFERENCES permissoes(id) ON DELETE CASCADE,
  UNIQUE (papel_id, permissao_id)
);

CREATE TABLE usuarios_papeis (
  id BIGSERIAL PRIMARY KEY,
  usuario_id BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
  papel_id BIGINT NOT NULL REFERENCES papeis(id) ON DELETE CASCADE,
  UNIQUE (usuario_id, papel_id)
);

CREATE TABLE usuarios_permissoes (
  id BIGSERIAL PRIMARY KEY,
  usuario_id BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
  permissao_id BIGINT NOT NULL REFERENCES permissoes(id) ON DELETE CASCADE,
  UNIQUE (usuario_id, permissao_id)
);

-- Permissões efetivas do usuário (diretas + via papel)
CREATE OR REPLACE VIEW vw_usuarios_permissoes_efetivas AS
SELECT u.id AS usuario_id, p.codigo AS codigo_permissao
FROM usuarios u
JOIN usuarios_permissoes up ON up.usuario_id = u.id
JOIN permissoes p ON p.id = up.permissao_id
UNION
SELECT u.id AS usuario_id, p.codigo AS codigo_permissao
FROM usuarios u
JOIN usuarios_papeis ur ON ur.usuario_id = u.id
JOIN papeis_permissoes rp ON rp.papel_id = ur.papel_id
JOIN permissoes p ON p.id = rp.permissao_id;

-- Checagem de permissão considerando usuário ativo
CREATE OR REPLACE FUNCTION fn_usuario_tem_permissao(p_usuario_id BIGINT, p_codigo TEXT)
RETURNS BOOLEAN LANGUAGE plpgsql AS
$$
DECLARE
  v_ativo BOOLEAN;
  v_tem BOOLEAN;
BEGIN
  SELECT ativo INTO v_ativo FROM usuarios WHERE id = p_usuario_id;
  IF v_ativo IS DISTINCT FROM TRUE THEN
    RETURN FALSE;
  END IF;

  SELECT EXISTS (
    SELECT 1 FROM vw_usuarios_permissoes_efetivas v
    WHERE v.usuario_id = p_usuario_id AND v.codigo_permissao = p_codigo
  ) INTO v_tem;

  RETURN COALESCE(v_tem, FALSE);
END;
$$;

-- Índices úteis
CREATE INDEX idx_usuarios_login ON usuarios (login);
CREATE INDEX idx_usuarios_email ON usuarios (email);
CREATE INDEX idx_usuarios_ultimoacesso ON usuarios (ultimoacesso);
CREATE INDEX idx_usuarios_ativo ON usuarios (ativo);

-- Seeds opcionais de permissões básicas
INSERT INTO permissoes (codigo, nome) VALUES
  ('acesso_sistema', 'Pode acessar o sistema'),
  ('relatorios', 'Acesso a relatórios'),
  ('entradas', 'Acesso às telas de entradas'),
  ('saidas', 'Acesso às telas de saídas'),
  ('estoque', 'Acesso ao estoque')
ON CONFLICT (codigo) DO NOTHING;

-- =====================
-- Validação de CPF
-- =====================
CREATE OR REPLACE FUNCTION fn_cpf_valido(cpf_input TEXT)
RETURNS BOOLEAN LANGUAGE plpgsql IMMUTABLE AS
$$
DECLARE
  cleaned TEXT;
  i INT;
  sum1 INT := 0;
  sum2 INT := 0;
  d10 INT;
  d11 INT;
  digit INT;
  rem INT;
  check1 INT;
  check2 INT;
BEGIN
  IF cpf_input IS NULL THEN
    RETURN FALSE;
  END IF;

  cleaned := regexp_replace(cpf_input, '[^0-9]', '', 'g');

  IF length(cleaned) <> 11 THEN
    RETURN FALSE;
  END IF;

  -- rejeita CPFs com todos os dígitos iguais
  IF cleaned = repeat(substr(cleaned,1,1), 11) THEN
    RETURN FALSE;
  END IF;

  -- primeiro dígito verificador
  FOR i IN 1..9 LOOP
    digit := CAST(substr(cleaned, i, 1) AS INT);
    sum1 := sum1 + digit * (11 - i);
  END LOOP;

  rem := sum1 % 11;
  IF rem < 2 THEN
    check1 := 0;
  ELSE
    check1 := 11 - rem;
  END IF;

  -- segundo dígito verificador
  sum2 := 0;
  FOR i IN 1..9 LOOP
    digit := CAST(substr(cleaned, i, 1) AS INT);
    sum2 := sum2 + digit * (12 - i);
  END LOOP;
  sum2 := sum2 + check1 * 2;

  rem := sum2 % 11;
  IF rem < 2 THEN
    check2 := 0;
  ELSE
    check2 := 11 - rem;
  END IF;

  d10 := CAST(substr(cleaned, 10, 1) AS INT);
  d11 := CAST(substr(cleaned, 11, 1) AS INT);

  RETURN (check1 = d10 AND check2 = d11);
END;
$$;

DO $$
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM pg_constraint WHERE conname = 'ck_pacientes_cpf_valido'
  ) THEN
    ALTER TABLE pacientes
      ADD CONSTRAINT ck_pacientes_cpf_valido
      CHECK (fn_cpf_valido(cpf));
  END IF;
END $$;

-- Fim do schema