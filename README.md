### Hammer (Maho starter)

Este repositório é um *starter* do **Maho** com um fluxo de instalação/configuração que:

- **não usa `local.xml`**
- usa **`.env` somente para credenciais/DB connection** (`MAHO_DB_*`)
- usa o **banco (`core_config_data`)** para configs internas como:
  - `global/install/date` (define se está instalado)
  - `global/crypt/key`
- aplica mudanças no `mahocommerce/maho` via **patches do Composer** (`cweagans/composer-patches`)

### Documentação

- `docs/README.md`
- `docs/how-it-works.md`
- `docs/sqlite.md`
- `docs/patches.md`

### Pré-requisitos

- PHP (compatível com o projeto)
- Extensões: `pdo` e **`pdo_sqlite`** (para SQLite)
- Composer

### Instalar dependências (com patches)

```bash
composer install --ignore-platform-req=ext-sodium
```

### Quickstart (SQLite)

Instala do zero usando SQLite, gera `.env`, e grava `global/install/date` + `global/crypt/key` no DB.

```bash
rm -f .env
rm -rf var/db var/cache var/session

./maho install -n --force \
  --license_agreement_accepted yes \
  --locale en_US --timezone UTC --default_currency USD \
  --db_engine sqlite --db_name test-install.sqlite \
  --url http://maho.test/ --use_secure 0 --secure_base_url http://maho.test/ --use_secure_admin 0 \
  --admin_lastname Test --admin_firstname Test --admin_email test@example.com \
  --admin_username admin --admin_password "admin1234@admin"
```

### Validações rápidas

`.env` **não** deve conter install date:

```bash
grep -i "INSTALL_DATE" .env && exit 1 || echo "OK: no INSTALL_DATE"
```

Ler valores gravados no DB via CLI:

```bash
./maho config:get global/install/date
./maho config:get global/crypt/key
```
