### SQLite: instalação e uso

### Pré-requisitos

- PHP (compatível com o projeto)
- Extensão `pdo_sqlite` habilitada
- Composer

### Instalar dependências (com patches)

```bash
composer install --ignore-platform-req=ext-sodium
```

### Instalar (SQLite)

Este comando:
- cria o arquivo SQLite em `var/db/<db_name>` (quando `db_name` é relativo)
- gera `.env` com `MAHO_DB_*`
- grava `global/install/date` e `global/crypt/key` no `core_config_data`

```bash
rm -f .env
rm -rf var/db var/cache var/session

./maho install -n --force \
  --license_agreement_accepted yes \
  --locale en_US --timezone UTC --default_currency USD \
  --db_engine sqlite --db_name test-install.sqlite \
  --url http://localhost/ --use_secure 0 --secure_base_url http://localhost/ --use_secure_admin 0 \
  --admin_lastname Test --admin_firstname Test --admin_email test@example.com \
  --admin_username admin --admin_password "AdminPassword123!"
```

### Validar que `.env` não tem install date

```bash
grep -i "INSTALL_DATE" .env && exit 1 || echo "OK: no INSTALL_DATE"
```

### Validar que a instalação está no DB (via CLI)

```bash
./maho config:get global/install/date
./maho config:get global/crypt/key
```

### Onde fica o arquivo SQLite

- Se você passar `--db_name test-install.sqlite`, o caminho efetivo fica:
  - `var/db/test-install.sqlite`
- Se você passar um caminho absoluto, ele é usado como está:
  - `--db_name /tmp/maho.sqlite`

