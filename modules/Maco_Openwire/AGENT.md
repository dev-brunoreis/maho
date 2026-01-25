# OpenWire — AGENT

## 🎯 Resumo rápido
OpenWire traz componentes reativos ao Magento 1: componentes PHP que expõem estado e ações, e um runtime JS que envia atualizações via AJAX para aplicar patches no DOM sem recarregar a página.

---

## ⚡ Quick start (usar localmente)
- Instalar dependências JS: `npm install`
- Rodar dev (vite): `npm run dev` (desenvolvimento do runtime)
- Build JS para produção: `npm run build` (gera `js/openwire/dist`)
- Rodar testes JS: `npm test` (Vitest)
- Rodar testes PHP: `composer test` (Pest)
- Lint & fix: `composer lint` / `composer fix`

> Dica: sempre rode a suíte de testes antes de abrir PRs.

---

## 📦 O que contém o repositório
- `app/code/local/Maco/Openwire/` — implementação PHP: componentes, wrappers legados, controller (`UpdateController`), validação e store de estado.
- `js/openwire/src/` — runtime TypeScript que captura eventos DOM e envia payloads (`data-ow-*`).
- `tests/` — testes PHP (Pest) e JS (Vitest).
- `docs/` — guias e exemplos (instalação, arquitetura, state management).

---

## 🧭 Como funciona (fluxo básico)
1. O runtime detecta um evento em um elemento com `data-ow-id`/`data-ow-component` e lê atributos `data-ow:*` (ex.: `data-ow:click`).
2. Forma um payload JSON: `{ id, component, calls: [{ method, params }], initial_state?, props? }` e envia POST para `/openwire/update/index`.
3. `UpdateController` valida (`RequestValidator`), instancia o componente (ou envolve bloco legado), monta/hidrata, executa ações, renderiza e persiste estado se necessário.
4. O servidor responde com `{ html?, data?, state?, meta? }` e o runtime aplica o patch no DOM ou mescla dados.

---

## ✍️ Criando um componente (checklist)
- Crie uma classe que estenda `Maco_Openwire_Block_Component_Abstract`.
- Use `Maco_Openwire_Block_Component_Trait_Reactive` para alias automático (ou implemente `getComponentAlias()`).
- Declare `protected $openwireAllowedActions = ['action1', ...];` (whitelist de ações remotas).
- Implemente `mount(array $props = [])` para inicializar props.
- Implemente `_toHtml()` retornando HTML declarativo (pode usar `Template_Compiler` para compilar diretivas `@click` e `{{ var }}`).
- Se suportar `data` mode, implemente `Maco_Openwire_Block_Component_Contracts_DataProvider` (`getDataPayload()`, `getHtmlPayload()`).
- Adicione testes unitários e de integração (Pest).

---

## 📡 Payload & atributos (runtime)
- Atributos de template gerados pelo compilador:
  - `data-ow-id` — id único do componente
  - `data-ow-component` — alias do componente (ex.: `openwire_component/counter`)
  - `data-ow-config` — JSON com `initialState`, `stateful`, `pollIntervalMs`
  - `data-ow:*` — eventos compilados ex.: `data-ow:click="increment"`

- Exemplo de payload de clique:
```json
{ "id": "ow_123", "component": "openwire_component/counter", "calls": [{ "method": "increment", "params": [] }], "initial_state": { "count": 0 } }
```

---

## 🔐 Segurança & permissões
- Sempre valide payloads no servidor com `Maco_Openwire_Model_Security_RequestValidator` (já usado no `UpdateController`).
- Proteja contra CSRF: o validator checa `form_key` para requests do frontend.
- Mantenha `openwireAllowedActions` restrito; novas ações remotas devem ser revisadas por segurança e testes.

---

## 🧪 Testes e CI
- PHP: `composer test` (Pest) — unit + integration.
- JS: `npm test` (Vitest) — runtime em `js/openwire/tests`.
- Adicione casos para cenários de erro (ações não permitidas, payload malformado, form_key inválida).

---

## 🛠️ Desenvolvimento & debug
- Runtime: use `npm run dev` e abra console do navegador; o `EventHandler` e `ResponseHandler` são pontos-chave para logs.
- Server: reproduza requests com curl ou Insomnia para `/openwire/update/index`.
- Verifique `data-ow-config` no DOM para confirmar `initialState` e se o componente é `stateful`.

---

## ✍️ Contribuição
- Abra PRs para features ou correções; inclua testes e descrição clara de mudanças.
- Siga normas de style (psr-12 para PHP) e rodar `composer lint` antes de submeter.
- Adicione/atualize o AGENT.md do diretório afetado explicando o comportamento e pontos de integração.

---

## 📚 Onde encontrar mais detalhes
- Veja AGENT.md específicos por diretório (ex.: `app/code/local/Maco/Openwire/Block/AGENT.md`, `js/openwire/src/AGENT.md`, `app/code/local/Maco/Openwire/Model/Bridge/AGENT.md`) para instruções granulares.

---

> Se precisar, posso: gerar exemplos de componentes, adicionar snippets de debug para o runtime ou abrir um PR com alterações sugeridas. ✨
