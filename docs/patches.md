### Patches (Composer) — como funciona e como usar

### Onde estão os patches

- Pasta: `patches/`
- Aplicados via `cweagans/composer-patches`
- Declarados em `composer.json` em:
  - `extra.patches.mahocommerce/maho`

Patches relevantes para o fluxo SQLite/DB-backed install date:

- `0001` / `0005`: `Mage::isInstalled()` usando DB (sem `local.xml`)
- `0002`: adiciona model/service para install date
- `0003`: installer persiste install date no DB
- `0004`: config bootstrap pelo `MAHO_DB_*` (ignora `local.xml`)
- `0006`: installer gera `.env` (somente credenciais) e persiste configs internas em `core_config_data`
- `0007`: aplica env overrides cedo + corrige `initStatements` para SQLite
- `0008`: CLI carrega `.env` cedo (sem `symfony/dotenv`)

### Validar que patches foram aplicados

Reinstale o vendor e observe a etapa “Patching mahocommerce/maho”:

```bash
rm -f patches.lock.json
rm -rf vendor/mahocommerce/maho
composer install --ignore-platform-req=ext-sodium
```

Opcional: confirme que arquivos patchados existem/contêm as mudanças esperadas:

```bash
rg -n "applyDbEnv\\(\\)" vendor/mahocommerce/maho/app/code/core/Mage/Core/Model/Config.php
rg -n "maho_load_env_file" vendor/mahocommerce/maho/maho
```

### Atualizar/regenerar um patch

O fluxo usado aqui é “git dentro do vendor”:

1. Reinstale/recupere o vendor limpo
2. Entre em `vendor/mahocommerce/maho/`
3. `git init` e commit baseline
4. Faça a mudança
5. `git diff` para gerar o patch em `patches/`
6. Referencie o patch no `composer.json`

Exemplo (modelo):

```bash
rm -rf vendor/mahocommerce/maho
composer install --ignore-platform-req=ext-sodium

cd vendor/mahocommerce/maho
rm -rf .git
git init
git add .
git commit -m "baseline"

# (edite arquivos...)

git diff > ../../../patches/NNNN-minha-mudanca.patch
```

