### Como funciona (alto nível)

### Objetivos

- **Remover `local.xml` do fluxo** (bootstrap/instalação não dependem de `app/etc/local.xml`)
- **`.env` não contém install date**: o estado “instalado” é determinado **consultando o banco**
- **Configs internas ficam no banco** (`core_config_data`), não em arquivo

### Onde cada coisa fica

- **Credenciais/DB connection**: arquivo `.env` (gerado no install)
  - `MAHO_DB_ENGINE` (`sqlite|mysql|pgsql`)
  - `MAHO_DB_HOST`, `MAHO_DB_NAME`, `MAHO_DB_USER`, `MAHO_DB_PASS`, `MAHO_DB_PREFIX`
- **Instalação / estado**: tabela `core_config_data`
  - `global/install/date` → define se a aplicação está instalada
  - `global/crypt/key` → chave de criptografia

### Bootstrap de DB via `MAHO_DB_*`

O Maho carrega a configuração base (`app/etc/config.xml`) e depois aplica overrides vindos do ambiente:

- Patch `0004`: adiciona a leitura de `MAHO_DB_*` e ignora `local.xml`.
- Patch `0007`: garante que esses overrides são aplicados **antes** de qualquer operação que possa abrir uma conexão com DB (ex.: cache lock/config cache), e também:
  - para `sqlite`, força `initStatements` vazio (evita `SET NAMES utf8`)
  - para `sqlite`, normaliza `MAHO_DB_NAME` relativo para `var/db/<nome>`

### Como o “installed” é detectado

- Patch `0005`: `Mage::isInstalled()` consulta diretamente o DB (PDO) buscando `global/install/date` em `core_config_data`.
  - Isso evita depender de `Mage::app()`/config totalmente carregada para decidir se está instalado.

### Como o install grava os dados

- Patch `0006`: o install passa a:
  - **gerar/atualizar `.env`** com `MAHO_DB_*` (somente credenciais)
  - **persistir** `global/install/date` e `global/crypt/key` no `core_config_data`
  - não escrever `local.xml`

### CLI e carregamento do `.env`

- Patch `0008`: o script `maho` (CLI) carrega `.env` **bem no início**, sem depender de `symfony/dotenv`.
  - Resultado: comandos como `./maho config:get ...` funcionam imediatamente após instalar, porque as variáveis `MAHO_DB_*` já estão no processo.

