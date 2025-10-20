# Sistema de Gestão da Lista de Espera de Creches

Aplicação web em PHP 8.2+ seguindo MVC para gestão da lista de espera das creches municipais de São José do Rio Preto/SP, alinhada à LDB, ECA, Lei 13.257/2016, legislações municipais e à LGPD.

## Recursos Principais
- Autenticação via Google OAuth2 restrita ao domínio `educacao.riopreto.sp.gov.br`.
- Cadastro completo de crianças com anexos obrigatórios e opcionais.
- Cálculo de pontuação automática baseado em critérios configuráveis.
- Geolocalização via CEP (ViaCEP) e coordenadas (geocode.maps.co) para cálculo de distância.
- Painel com indicadores, gráficos e listagens com DataTables.
- Módulo de relatórios com exportação CSV, Excel (PhpSpreadsheet) e PDF (Dompdf).
- Logs de auditoria e controle de acesso por perfil (admin e cadastro).
- Upload seguro (10 MB, PDF/JPG/PNG) e proteção CSRF.

## Requisitos
- PHP 8.2+
- Composer
- MySQL 8+

## Instalação
```bash
composer install
cp config/config.php config/config.local.php # opcional para sobreescrever variáveis
```

Configure as variáveis de ambiente:
```
APP_BASE_URL=https://seusistema
DB_DSN="mysql:host=localhost;dbname=creches;charset=utf8mb4"
DB_USER=usuario
DB_PASSWORD=senha
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=https://seusistema/login.php
GOOGLE_HOSTED_DOMAIN=educacao.riopreto.sp.gov.br
PASSWORD_PEPPER=chave-secreta
```

Importe o esquema de banco:
```bash
mysql -u root -p creches < database/schema.sql
```

Configure o virtual host apontando para `public/`.

### Login alternativo (fallback)
Caso o OAuth esteja indisponível, o sistema aceita autenticação com usuário e senha cadastrados na tabela `users`.

1. Defina a variável `PASSWORD_PEPPER` no ambiente com uma chave secreta forte.
2. Gere o hash da senha somando a senha desejada com o pepper:

   ```bash
   php -r "echo password_hash('SUA_SENHA' . 'SUA_CHAVE_PEPPER', PASSWORD_DEFAULT), PHP_EOL;"
   ```

   > Substitua `SUA_CHAVE_PEPPER` pelo mesmo valor configurado em `PASSWORD_PEPPER`.

3. Cadastre o usuário (ou atualize um existente) executando no MySQL:

   ```sql
   INSERT INTO users (name, email, role, password_hash) VALUES
   ('Administrador', 'admin@educacao.riopreto.sp.gov.br', 'admin', 'HASH_GERADO');
   ```

Após o cadastro, o formulário de fallback na página de login ficará habilitado.

### Checklist para Google OAuth
A página de login exibe automaticamente as etapas pendentes (como client ID, secret, redirect URI e extensões PHP) quando a autenticação Google não está totalmente configurada. Ajuste as variáveis `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI` e `GOOGLE_HOSTED_DOMAIN` para remover os avisos.

## Segurança e LGPD
- Consentimento explícito exibido na tela de login.
- Todas as operações registradas em `logs`.
- Exportação de dados pessoais via relatórios.
- Uso de prepared statements, tokens CSRF e sanitização básica.

## Licença
Software desenvolvido para uso interno da Secretaria Municipal de Educação de São José do Rio Preto/SP.
