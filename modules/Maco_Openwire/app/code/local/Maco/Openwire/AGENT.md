# Módulo OpenWire — AGENT

## 🎯 Objetivo do diretório
Contém a implementação do módulo Magento responsável por componentes reativos no servidor: componentes, wrappers para blocos legados, validação de requests, armazenamento de estado e o compilador de templates.

## 📁 Principais arquivos/classes
- `controllers/UpdateController.php` — endpoint principal `/openwire/update/index` que processa payloads AJAX.
- `Block/Component/Abstract.php` — base para componentes reativos (mount, hydrate, executeAction, renderPayload).
- `Block/LegacyWrapper.php` — wrapper para blocos legados que adiciona comportamento OpenWire.
- `Model/Template/Compiler.php` — compila templates declarativos (`@click`, `{{ var }}`, `openwire="alias"`) para atributos operacionais.
- `Model/Bridge/ComponentRunner.php` — orquestra requests para modo bridge (resolve → mount → action → render).
- `Model/Security/RequestValidator.php` — valida payload e CSRF.
- `Model/State/SessionStore.php` — persistência de estado por sessão.

## ⚙️ Como interagir / estender
- Para criar um componente, estenda `Maco_Openwire_Block_Component_Abstract`, implemente `mount()` e `_toHtml()`, e declare `$openwireAllowedActions`.
- Use traits (`Trait_Reactive`, `Trait_Stateful`) para comportamento padrão (alias, polling, persistência).
- Para compatibilidade com blocos legados, deixe o sistema envolver o bloco com `LegacyWrapper` (ex.: via `ComponentRef`).

## 🧪 Testes & Validação
- Tests PHP: `composer test` (Pest).
- Há testes unitários para `Template_Compiler`, `Component` e `Bridge`.

## 🔧 Integração com frontend
- O JS runtime procura elementos com `data-ow-id`, `data-ow-component` e envia payloads para o controller de update.
- O compilador gera `data-ow-config` com `initialState` quando o componente é stateful.

---
> Nota: mantenha validações no `RequestValidator` em dia ao adicionar novos eventos ou cargas úteis para evitar superfícies de ataque CSRF/ACL.