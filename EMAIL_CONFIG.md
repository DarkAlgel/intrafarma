# Configuração de Email - IntraFarma

## Desenvolvimento (Atual)
Atualmente, o sistema está configurado para salvar emails em logs. Os emails são salvos em `storage/logs/laravel.log`.

## Produção - Opções de Configuração

### Opção 1: Gmail SMTP (Recomendado para pequenos projetos)

1. **Ative a verificação em duas etapas na sua conta Google**
2. **Gere uma senha de app**:
   - Acesse: https://myaccount.google.com/apppasswords
   - Crie uma senha de app para "Mail"
   - Use essa senha no lugar da sua senha normal

3. **Configure o .env** (descomente e altere as linhas):
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seuemail@gmail.com
MAIL_PASSWORD=sua_senha_de_app_aqui
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seuemail@gmail.com
MAIL_FROM_NAME="IntraFarma Sistema"
```

### Opção 2: Mailgun (Recomendado para projetos maiores)
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=seu_dominio
MAILGUN_SECRET=sua_chave_secreta
MAIL_FROM_ADDRESS=contato@seudominio.com
MAIL_FROM_NAME="IntraFarma Sistema"
```

### Opção 3: Mailtrap (Para testes)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu_usuario_mailtrap
MAIL_PASSWORD=sua_senha_mailtrap
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=teste@example.com
MAIL_FROM_NAME="IntraFarma Teste"
```

## Como configurar

1. **Edite o arquivo `.env`**
2. **Comente as linhas de desenvolvimento** (com `#`)
3. **Descomente as linhas de produção** (remova `#`)
4. **Clear cache** após alterar:
```bash
php artisan config:clear
php artisan cache:clear
```

## Testar o email

Após configurar, teste o envio de email:
```bash
php artisan tinker
```

```php
use Illuminate\Support\Facades\Mail;
Mail::raw('Teste de email', function ($message) {
    $message->to('seu_email@example.com')
             ->subject('Teste IntraFarma');
});
```

## Segurança importante

- **Nunca commite credenciais reais** no repositório
- **Use variáveis de ambiente** em produção
- **Configure DKIM e SPF** no seu domínio
- **Monitore os logs** de email regularmente

## Suporte

Se precisar de ajuda com a configuração, verifique:
- Logs em `storage/logs/laravel.log`
- Documentação do Laravel: https://laravel.com/docs/mail
- Status do serviço de email escolhido