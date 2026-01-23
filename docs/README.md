### Docs

Este projeto é um *starter* do Maho com **instalação baseada em SQLite** (ou outros engines) usando:

- **`.env` apenas com credenciais/connection settings** (`MAHO_DB_*`)
- **`core_config_data`** para configs internas (ex.: `global/install/date`, `global/crypt/key`)
- **sem `local.xml`** no fluxo
- **patches via Composer** para alterar o fluxo padrão do `mahocommerce/maho`

### Índice

- [`docs/how-it-works.md`](how-it-works.md): arquitetura do bootstrap (DB env, install date no DB, sem `local.xml`)
- [`docs/sqlite.md`](sqlite.md): como instalar/rodar usando SQLite (passo-a-passo)
- [`docs/patches.md`](patches.md): como os patches são aplicados e como atualizar/regenerar

