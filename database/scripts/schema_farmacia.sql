-- DROP SCHEMA public;

CREATE SCHEMA public AUTHORIZATION pg_database_owner;

COMMENT ON SCHEMA public IS 'standard public schema';

-- DROP TYPE public."estado_item";

CREATE TYPE public."estado_item" AS ENUM (
	'novo',
	'lacrado',
	'aberto',
	'avariado');

-- DROP TYPE public."forma_fisica_tipo";

CREATE TYPE public."forma_fisica_tipo" AS ENUM (
	'solida',
	'pastosa',
	'liquida',
	'gasosa');

-- DROP TYPE public."forma_retirada_tipo";

CREATE TYPE public."forma_retirada_tipo" AS ENUM (
	'MIP',
	'com_prescricao');

-- DROP TYPE public."fornecedor_tipo";

CREATE TYPE public."fornecedor_tipo" AS ENUM (
	'doacao',
	'compra',
	'parceria',
	'outros');

-- DROP TYPE public."tarja_tipo";

CREATE TYPE public."tarja_tipo" AS ENUM (
	'sem_tarja',
	'tarja_amarela',
	'tarja_vermelha',
	'tarja_preta');

-- DROP TYPE public."unidade_contagem";

CREATE TYPE public."unidade_contagem" AS ENUM (
	'comprimido',
	'capsula',
	'dragea',
	'sache',
	'ampola',
	'frasco',
	'caixa',
	'ml',
	'g',
	'unidade',
	'aerosol',
	'xarope',
	'solucao');

-- DROP SEQUENCE classes_terapeuticas_id_seq;

CREATE SEQUENCE classes_terapeuticas_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE dispensacoes_id_seq;

CREATE SEQUENCE dispensacoes_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE entradas_id_seq;

CREATE SEQUENCE entradas_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE failed_jobs_id_seq;

CREATE SEQUENCE failed_jobs_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE fornecedores_id_seq;

CREATE SEQUENCE fornecedores_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE jobs_id_seq;

CREATE SEQUENCE jobs_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE laboratorios_id_seq;

CREATE SEQUENCE laboratorios_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE lotes_id_seq;

CREATE SEQUENCE lotes_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE medicamentos_id_seq;

CREATE SEQUENCE medicamentos_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE migrations_id_seq;

CREATE SEQUENCE migrations_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 2147483647
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE pacientes_id_seq;

CREATE SEQUENCE pacientes_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE papeis_id_seq;

CREATE SEQUENCE papeis_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE papeis_permissoes_id_seq;

CREATE SEQUENCE papeis_permissoes_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE permissoes_id_seq;

CREATE SEQUENCE permissoes_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE users_id_seq;

CREATE SEQUENCE users_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE usuarios_id_seq;

CREATE SEQUENCE usuarios_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE usuarios_papeis_id_seq;

CREATE SEQUENCE usuarios_papeis_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;
-- DROP SEQUENCE usuarios_permissoes_id_seq;

CREATE SEQUENCE usuarios_permissoes_id_seq
	INCREMENT BY 1
	MINVALUE 1
	MAXVALUE 9223372036854775807
	START 1
	CACHE 1
	NO CYCLE;-- public."cache" definition

-- Drop table

-- DROP TABLE "cache";

CREATE TABLE "cache" (
	"key" varchar(255) NOT NULL,
	value text NOT NULL,
	expiration int4 NOT NULL,
	CONSTRAINT cache_pkey PRIMARY KEY (key)
);


-- public.cache_locks definition

-- Drop table

-- DROP TABLE cache_locks;

CREATE TABLE cache_locks (
	"key" varchar(255) NOT NULL,
	"owner" varchar(255) NOT NULL,
	expiration int4 NOT NULL,
	CONSTRAINT cache_locks_pkey PRIMARY KEY (key)
);


-- public.classes_terapeuticas definition

-- Drop table

-- DROP TABLE classes_terapeuticas;

CREATE TABLE classes_terapeuticas (
	id bigserial NOT NULL,
	codigo_classe int2 NOT NULL,
	nome text NOT NULL,
	CONSTRAINT classes_terapeuticas_codigo_classe_key UNIQUE (codigo_classe),
	CONSTRAINT classes_terapeuticas_nome_key UNIQUE (nome),
	CONSTRAINT classes_terapeuticas_pkey PRIMARY KEY (id)
);


-- public.failed_jobs definition

-- Drop table

-- DROP TABLE failed_jobs;

CREATE TABLE failed_jobs (
	id bigserial NOT NULL,
	"uuid" varchar(255) NOT NULL,
	"connection" text NOT NULL,
	queue text NOT NULL,
	payload text NOT NULL,
	"exception" text NOT NULL,
	failed_at timestamp(0) DEFAULT CURRENT_TIMESTAMP NOT NULL,
	CONSTRAINT failed_jobs_pkey PRIMARY KEY (id),
	CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid)
);


-- public.fornecedores definition

-- Drop table

-- DROP TABLE fornecedores;

CREATE TABLE fornecedores (
	id bigserial NOT NULL,
	nome text NOT NULL,
	tipo public."fornecedor_tipo" DEFAULT 'doacao'::fornecedor_tipo NOT NULL,
	contato text NULL,
	CONSTRAINT fornecedores_pkey PRIMARY KEY (id)
);
CREATE INDEX idx_fornecedores_tipo ON public.fornecedores USING btree (tipo);


-- public.job_batches definition

-- Drop table

-- DROP TABLE job_batches;

CREATE TABLE job_batches (
	id varchar(255) NOT NULL,
	"name" varchar(255) NOT NULL,
	total_jobs int4 NOT NULL,
	pending_jobs int4 NOT NULL,
	failed_jobs int4 NOT NULL,
	failed_job_ids text NOT NULL,
	"options" text NULL,
	cancelled_at int4 NULL,
	created_at int4 NOT NULL,
	finished_at int4 NULL,
	CONSTRAINT job_batches_pkey PRIMARY KEY (id)
);


-- public.jobs definition

-- Drop table

-- DROP TABLE jobs;

CREATE TABLE jobs (
	id bigserial NOT NULL,
	queue varchar(255) NOT NULL,
	payload text NOT NULL,
	attempts int2 NOT NULL,
	reserved_at int4 NULL,
	available_at int4 NOT NULL,
	created_at int4 NOT NULL,
	CONSTRAINT jobs_pkey PRIMARY KEY (id)
);
CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


-- public.laboratorios definition

-- Drop table

-- DROP TABLE laboratorios;

CREATE TABLE laboratorios (
	id bigserial NOT NULL,
	nome text NOT NULL,
	CONSTRAINT laboratorios_nome_key UNIQUE (nome),
	CONSTRAINT laboratorios_pkey PRIMARY KEY (id)
);


-- public.migrations definition

-- Drop table

-- DROP TABLE migrations;

CREATE TABLE migrations (
	id serial4 NOT NULL,
	migration varchar(255) NOT NULL,
	batch int4 NOT NULL,
	CONSTRAINT migrations_pkey PRIMARY KEY (id)
);


-- public.pacientes definition

-- Drop table

-- DROP TABLE pacientes;

CREATE TABLE pacientes (
	id bigserial NOT NULL,
	nome text NOT NULL,
	cpf text NOT NULL,
	telefone text NULL,
	cidade text NULL,
	CONSTRAINT ck_pacientes_cpf_valido CHECK (fn_cpf_valido(cpf)),
	CONSTRAINT pacientes_cpf_key UNIQUE (cpf),
	CONSTRAINT pacientes_pkey PRIMARY KEY (id)
);
CREATE INDEX idx_pacientes_cpf ON public.pacientes USING btree (cpf);


-- public.papeis definition

-- Drop table

-- DROP TABLE papeis;

CREATE TABLE papeis (
	id bigserial NOT NULL,
	nome text NOT NULL,
	descricao text NULL,
	CONSTRAINT papeis_nome_key UNIQUE (nome),
	CONSTRAINT papeis_pkey PRIMARY KEY (id)
);


-- public.password_reset_tokens definition

-- Drop table

-- DROP TABLE password_reset_tokens;

CREATE TABLE password_reset_tokens (
	email varchar(255) NOT NULL,
	"token" varchar(255) NOT NULL,
	created_at timestamp(0) NULL,
	CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email)
);


-- public.permissoes definition

-- Drop table

-- DROP TABLE permissoes;

CREATE TABLE permissoes (
	id bigserial NOT NULL,
	codigo text NOT NULL,
	nome text NOT NULL,
	CONSTRAINT permissoes_codigo_key UNIQUE (codigo),
	CONSTRAINT permissoes_pkey PRIMARY KEY (id)
);


-- public.sessions definition

-- Drop table

-- DROP TABLE sessions;

CREATE TABLE sessions (
	id varchar(255) NOT NULL,
	user_id int8 NULL,
	ip_address varchar(45) NULL,
	user_agent text NULL,
	payload text NOT NULL,
	last_activity int4 NOT NULL,
	CONSTRAINT sessions_pkey PRIMARY KEY (id)
);
CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);
CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


-- public.users definition

-- Drop table

-- DROP TABLE users;

CREATE TABLE users (
	id bigserial NOT NULL,
	"name" varchar(255) NOT NULL,
	email varchar(255) NOT NULL,
	email_verified_at timestamp(0) NULL,
	"password" varchar(255) NOT NULL,
	remember_token varchar(100) NULL,
	created_at timestamp(0) NULL,
	updated_at timestamp(0) NULL,
	CONSTRAINT users_email_unique UNIQUE (email),
	CONSTRAINT users_pkey PRIMARY KEY (id)
);


-- public.usuarios definition

-- Drop table

-- DROP TABLE usuarios;

CREATE TABLE usuarios (
	id bigserial NOT NULL,
	nome text NOT NULL,
	celular text NULL,
	email text NOT NULL,
	login text NOT NULL,
	senha_hash text NOT NULL,
	datacadastro timestamp DEFAULT now() NOT NULL,
	ultimoacesso timestamp NULL,
	ativo bool DEFAULT true NOT NULL,
	CONSTRAINT usuarios_email_key UNIQUE (email),
	CONSTRAINT usuarios_login_key UNIQUE (login),
	CONSTRAINT usuarios_pkey PRIMARY KEY (id)
);
CREATE INDEX idx_usuarios_ativo ON public.usuarios USING btree (ativo);
CREATE INDEX idx_usuarios_email ON public.usuarios USING btree (email);
CREATE INDEX idx_usuarios_login ON public.usuarios USING btree (login);
CREATE INDEX idx_usuarios_ultimoacesso ON public.usuarios USING btree (ultimoacesso);

-- Table Triggers

create trigger trg_hash_senha_usuarios before
insert
    or
update
    of senha_hash on
    public.usuarios for each row execute function fn_hash_senha_usuarios();


-- public.medicamentos definition

-- Drop table

-- DROP TABLE medicamentos;

CREATE TABLE medicamentos (
	id bigserial NOT NULL,
	codigo text NOT NULL,
	nome text NOT NULL,
	laboratorio_id int8 NULL,
	classe_terapeutica_id int8 NOT NULL,
	tarja public."tarja_tipo" NOT NULL,
	forma_retirada public."forma_retirada_tipo" NOT NULL,
	forma_fisica public."forma_fisica_tipo" NOT NULL,
	apresentacao public."unidade_contagem" NOT NULL,
	unidade_base public."unidade_contagem" NOT NULL,
	dosagem_valor numeric(12, 3) NOT NULL,
	dosagem_unidade text NOT NULL,
	generico bool DEFAULT false NOT NULL,
	limite_minimo numeric(12, 3) DEFAULT 0 NOT NULL,
	serial_por_classe int4 NOT NULL,
	ativo bool DEFAULT true NOT NULL,
	CONSTRAINT medicamentos_codigo_key UNIQUE (codigo),
	CONSTRAINT medicamentos_pkey PRIMARY KEY (id),
	CONSTRAINT medicamentos_classe_terapeutica_id_fkey FOREIGN KEY (classe_terapeutica_id) REFERENCES classes_terapeuticas(id) ON DELETE RESTRICT,
	CONSTRAINT medicamentos_laboratorio_id_fkey FOREIGN KEY (laboratorio_id) REFERENCES laboratorios(id) ON DELETE RESTRICT
);
CREATE INDEX idx_medicamentos_classe ON public.medicamentos USING btree (classe_terapeutica_id);
CREATE INDEX idx_medicamentos_generico ON public.medicamentos USING btree (generico);
CREATE INDEX idx_medicamentos_tarja ON public.medicamentos USING btree (tarja);

-- Table Triggers

create trigger trg_set_serial_por_classe before
insert
    on
    public.medicamentos for each row execute function fn_set_serial_por_classe();


-- public.papeis_permissoes definition

-- Drop table

-- DROP TABLE papeis_permissoes;

CREATE TABLE papeis_permissoes (
	id bigserial NOT NULL,
	papel_id int8 NOT NULL,
	permissao_id int8 NOT NULL,
	CONSTRAINT papeis_permissoes_papel_id_permissao_id_key UNIQUE (papel_id, permissao_id),
	CONSTRAINT papeis_permissoes_pkey PRIMARY KEY (id),
	CONSTRAINT papeis_permissoes_papel_id_fkey FOREIGN KEY (papel_id) REFERENCES papeis(id) ON DELETE CASCADE,
	CONSTRAINT papeis_permissoes_permissao_id_fkey FOREIGN KEY (permissao_id) REFERENCES permissoes(id) ON DELETE CASCADE
);


-- public.usuarios_papeis definition

-- Drop table

-- DROP TABLE usuarios_papeis;

CREATE TABLE usuarios_papeis (
	id bigserial NOT NULL,
	usuario_id int8 NOT NULL,
	papel_id int8 NOT NULL,
	CONSTRAINT usuarios_papeis_pkey PRIMARY KEY (id),
	CONSTRAINT usuarios_papeis_usuario_id_papel_id_key UNIQUE (usuario_id, papel_id),
	CONSTRAINT usuarios_papeis_papel_id_fkey FOREIGN KEY (papel_id) REFERENCES papeis(id) ON DELETE CASCADE,
	CONSTRAINT usuarios_papeis_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);


-- public.usuarios_permissoes definition

-- Drop table

-- DROP TABLE usuarios_permissoes;

CREATE TABLE usuarios_permissoes (
	id bigserial NOT NULL,
	usuario_id int8 NOT NULL,
	permissao_id int8 NOT NULL,
	CONSTRAINT usuarios_permissoes_pkey PRIMARY KEY (id),
	CONSTRAINT usuarios_permissoes_usuario_id_permissao_id_key UNIQUE (usuario_id, permissao_id),
	CONSTRAINT usuarios_permissoes_permissao_id_fkey FOREIGN KEY (permissao_id) REFERENCES permissoes(id) ON DELETE CASCADE,
	CONSTRAINT usuarios_permissoes_usuario_id_fkey FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);


-- public.lotes definition

-- Drop table

-- DROP TABLE lotes;

CREATE TABLE lotes (
	id bigserial NOT NULL,
	medicamento_id int8 NOT NULL,
	data_fabricacao date NOT NULL,
	validade date NOT NULL,
	validade_mes date GENERATED ALWAYS AS (make_date(date_part('year'::text, validade)::integer, date_part('month'::text, validade)::integer, 1)) STORED NULL,
	nome_comercial text NULL,
	ativo bool DEFAULT true NOT NULL,
	observacao text NULL,
	CONSTRAINT lotes_medicamento_id_validade_mes_key UNIQUE (medicamento_id, validade_mes),
	CONSTRAINT lotes_pkey PRIMARY KEY (id),
	CONSTRAINT lotes_medicamento_id_fkey FOREIGN KEY (medicamento_id) REFERENCES medicamentos(id) ON DELETE RESTRICT
);
CREATE INDEX idx_lotes_validade ON public.lotes USING btree (validade);
CREATE INDEX idx_lotes_validade_mes ON public.lotes USING btree (validade_mes);


-- public.dispensacoes definition

-- Drop table

-- DROP TABLE dispensacoes;

CREATE TABLE dispensacoes (
	id bigserial NOT NULL,
	data_dispensa timestamp DEFAULT now() NOT NULL,
	responsavel text NULL,
	paciente_id int8 NOT NULL,
	lote_id int8 NOT NULL,
	dosagem text NULL,
	nome_comercial text NULL,
	quantidade_informada numeric(12, 3) NOT NULL,
	quantidade_base numeric(12, 3) NOT NULL,
	unidade public."unidade_contagem" NOT NULL,
	numero_receita text NULL,
	CONSTRAINT dispensacoes_pkey PRIMARY KEY (id),
	CONSTRAINT dispensacoes_quantidade_base_check CHECK ((quantidade_base > (0)::numeric)),
	CONSTRAINT dispensacoes_quantidade_informada_check CHECK ((quantidade_informada > (0)::numeric)),
	CONSTRAINT dispensacoes_lote_id_fkey FOREIGN KEY (lote_id) REFERENCES lotes(id) ON DELETE RESTRICT,
	CONSTRAINT dispensacoes_paciente_id_fkey FOREIGN KEY (paciente_id) REFERENCES pacientes(id) ON DELETE RESTRICT
);
CREATE INDEX idx_dispensacoes_data ON public.dispensacoes USING btree (data_dispensa);
CREATE INDEX idx_dispensacoes_lote ON public.dispensacoes USING btree (lote_id);

-- Table Triggers

create trigger trg_calc_quantidade_base_dispensacao before
insert
    or
update
    on
    public.dispensacoes for each row execute function fn_calc_quantidade_base_dispensacao();
create trigger trg_check_saldo_e_validade_dispensacao before
insert
    or
update
    on
    public.dispensacoes for each row execute function fn_check_saldo_e_validade_dispensacao();


-- public.entradas definition

-- Drop table

-- DROP TABLE entradas;

CREATE TABLE entradas (
	id bigserial NOT NULL,
	data_entrada date DEFAULT CURRENT_DATE NOT NULL,
	fornecedor_id int8 NOT NULL,
	lote_id int8 NOT NULL,
	numero_lote_fornecedor text NOT NULL,
	quantidade_informada numeric(12, 3) NOT NULL,
	quantidade_base numeric(12, 3) NOT NULL,
	unidade public."unidade_contagem" NOT NULL,
	unidades_por_embalagem numeric(12, 3) NULL,
	estado public."estado_item" NULL,
	observacao text NULL,
	CONSTRAINT entradas_lote_id_numero_lote_fornecedor_key UNIQUE (lote_id, numero_lote_fornecedor),
	CONSTRAINT entradas_pkey PRIMARY KEY (id),
	CONSTRAINT entradas_quantidade_base_check CHECK ((quantidade_base > (0)::numeric)),
	CONSTRAINT entradas_quantidade_informada_check CHECK ((quantidade_informada > (0)::numeric)),
	CONSTRAINT entradas_fornecedor_id_fkey FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id) ON DELETE RESTRICT,
	CONSTRAINT entradas_lote_id_fkey FOREIGN KEY (lote_id) REFERENCES lotes(id) ON DELETE RESTRICT
);
CREATE INDEX idx_entradas_lote ON public.entradas USING btree (lote_id);

-- Table Triggers

create trigger trg_calc_quantidade_base_entrada before
insert
    or
update
    on
    public.entradas for each row execute function fn_calc_quantidade_base_entrada();


-- public.vw_alerta_estoque_baixo source

CREATE OR REPLACE VIEW vw_alerta_estoque_baixo
AS SELECT medicamento_id,
    nome,
    codigo,
    unidade_base,
    dosagem_valor,
    dosagem_unidade,
    tarja,
    generico,
    quantidade_entrada,
    quantidade_saida,
    quantidade_disponivel,
    limite_minimo,
    alerta_minimo,
    alerta_menos_que_10_unidades,
    alerta_menos_que_20_porcento
   FROM vw_estoque_por_medicamento
  WHERE alerta_minimo = true OR alerta_menos_que_10_unidades = true OR alerta_menos_que_20_porcento = true;


-- public.vw_alerta_validade source

CREATE OR REPLACE VIEW vw_alerta_validade
AS SELECT lote_id,
    medicamento_id,
    medicamento,
    codigo,
    generico,
    tarja,
    forma_retirada,
    forma_fisica,
    apresentacao,
    unidade_base,
    dosagem_valor,
    dosagem_unidade,
    data_fabricacao,
    validade,
    validade_mes,
    quantidade_entrada,
    quantidade_saida,
    quantidade_disponivel,
    dias_para_vencimento,
    status
   FROM vw_estoque_por_lote
  WHERE status = ANY (ARRAY['Próximo de vencer'::text, 'Bloquear dispensação'::text]);


-- public.vw_alerta_validade_mes_atual source

CREATE OR REPLACE VIEW vw_alerta_validade_mes_atual
AS SELECT lote_id,
    medicamento_id,
    medicamento,
    codigo,
    generico,
    tarja,
    forma_retirada,
    forma_fisica,
    apresentacao,
    unidade_base,
    dosagem_valor,
    dosagem_unidade,
    data_fabricacao,
    validade,
    validade_mes,
    quantidade_entrada,
    quantidade_saida,
    quantidade_disponivel,
    dias_para_vencimento,
    status
   FROM vw_estoque_por_lote
  WHERE validade_mes = date_trunc('month'::text, CURRENT_DATE::timestamp with time zone)::date;


-- public.vw_estoque_por_lote source

CREATE OR REPLACE VIEW vw_estoque_por_lote
AS SELECT l.id AS lote_id,
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
    COALESCE(( SELECT sum(e.quantidade_base) AS sum
           FROM entradas e
          WHERE e.lote_id = l.id), 0::numeric) AS quantidade_entrada,
    COALESCE(( SELECT sum(d.quantidade_base) AS sum
           FROM dispensacoes d
          WHERE d.lote_id = l.id), 0::numeric) AS quantidade_saida,
    COALESCE(( SELECT sum(e.quantidade_base) AS sum
           FROM entradas e
          WHERE e.lote_id = l.id), 0::numeric) - COALESCE(( SELECT sum(d.quantidade_base) AS sum
           FROM dispensacoes d
          WHERE d.lote_id = l.id), 0::numeric) AS quantidade_disponivel,
    l.validade - CURRENT_DATE AS dias_para_vencimento,
        CASE
            WHEN l.validade < CURRENT_DATE THEN 'Bloquear dispensação'::text
            WHEN l.validade <= (CURRENT_DATE + '30 days'::interval) THEN 'Próximo de vencer'::text
            ELSE 'OK'::text
        END AS status
   FROM lotes l
     JOIN medicamentos m ON m.id = l.medicamento_id;


-- public.vw_estoque_por_medicamento source

CREATE OR REPLACE VIEW vw_estoque_por_medicamento
AS SELECT m.id AS medicamento_id,
    m.nome,
    m.codigo,
    m.unidade_base,
    m.dosagem_valor,
    m.dosagem_unidade,
    m.tarja,
    m.generico,
    COALESCE(e.total_entrada, 0::numeric) AS quantidade_entrada,
    COALESCE(d.total_saida, 0::numeric) AS quantidade_saida,
    COALESCE(e.total_entrada, 0::numeric) - COALESCE(d.total_saida, 0::numeric) AS quantidade_disponivel,
    m.limite_minimo,
        CASE
            WHEN (COALESCE(e.total_entrada, 0::numeric) - COALESCE(d.total_saida, 0::numeric)) <= m.limite_minimo THEN true
            ELSE false
        END AS alerta_minimo,
        CASE
            WHEN (COALESCE(e.total_entrada, 0::numeric) - COALESCE(d.total_saida, 0::numeric)) < 10::numeric THEN true
            ELSE false
        END AS alerta_menos_que_10_unidades,
        CASE
            WHEN e.total_entrada IS NOT NULL AND e.total_entrada > 0::numeric AND (COALESCE(e.total_entrada, 0::numeric) - COALESCE(d.total_saida, 0::numeric)) <= (0.20 * e.total_entrada) THEN true
            ELSE false
        END AS alerta_menos_que_20_porcento
   FROM medicamentos m
     LEFT JOIN ( SELECT l.medicamento_id,
            sum(en.quantidade_base) AS total_entrada
           FROM lotes l
             JOIN entradas en ON en.lote_id = l.id
          GROUP BY l.medicamento_id) e ON e.medicamento_id = m.id
     LEFT JOIN ( SELECT l.medicamento_id,
            sum(di.quantidade_base) AS total_saida
           FROM lotes l
             JOIN dispensacoes di ON di.lote_id = l.id
          GROUP BY l.medicamento_id) d ON d.medicamento_id = m.id;


-- public.vw_usuarios_permissoes_efetivas source

CREATE OR REPLACE VIEW vw_usuarios_permissoes_efetivas
AS SELECT u.id AS usuario_id,
    p.codigo AS codigo_permissao
   FROM usuarios u
     JOIN usuarios_permissoes up ON up.usuario_id = u.id
     JOIN permissoes p ON p.id = up.permissao_id
UNION
 SELECT u.id AS usuario_id,
    p.codigo AS codigo_permissao
   FROM usuarios u
     JOIN usuarios_papeis ur ON ur.usuario_id = u.id
     JOIN papeis_permissoes rp ON rp.papel_id = ur.papel_id
     JOIN permissoes p ON p.id = rp.permissao_id;



-- DROP FUNCTION public.fn_arquivar_lote_se_sem_saldo_ou_vencido(int8);

CREATE OR REPLACE FUNCTION public.fn_arquivar_lote_se_sem_saldo_ou_vencido(p_lote_id bigint)
 RETURNS boolean
 LANGUAGE plpgsql
AS $function$
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
$function$
;

-- DROP FUNCTION public.fn_arquivar_medicamento_se_sem_saldo(int8);

CREATE OR REPLACE FUNCTION public.fn_arquivar_medicamento_se_sem_saldo(p_medicamento_id bigint)
 RETURNS boolean
 LANGUAGE plpgsql
AS $function$
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
$function$
;

-- DROP FUNCTION public.fn_calc_quantidade_base_dispensacao();

CREATE OR REPLACE FUNCTION public.fn_calc_quantidade_base_dispensacao()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
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
$function$
;

-- DROP FUNCTION public.fn_calc_quantidade_base_entrada();

CREATE OR REPLACE FUNCTION public.fn_calc_quantidade_base_entrada()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
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
$function$
;

-- DROP FUNCTION public.fn_check_saldo_e_validade_dispensacao();

CREATE OR REPLACE FUNCTION public.fn_check_saldo_e_validade_dispensacao()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
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
$function$
;

-- DROP FUNCTION public.fn_cpf_valido(text);

CREATE OR REPLACE FUNCTION public.fn_cpf_valido(cpf_input text)
 RETURNS boolean
 LANGUAGE plpgsql
 IMMUTABLE
AS $function$
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
$function$
;

-- DROP FUNCTION public.fn_hash_senha_usuarios();

CREATE OR REPLACE FUNCTION public.fn_hash_senha_usuarios()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
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
$function$
;

-- DROP FUNCTION public.fn_set_serial_por_classe();

CREATE OR REPLACE FUNCTION public.fn_set_serial_por_classe()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
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
$function$
;

-- DROP FUNCTION public.fn_usuario_tem_permissao(int8, text);

CREATE OR REPLACE FUNCTION public.fn_usuario_tem_permissao(p_usuario_id bigint, p_codigo text)
 RETURNS boolean
 LANGUAGE plpgsql
AS $function$
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
$function$
;