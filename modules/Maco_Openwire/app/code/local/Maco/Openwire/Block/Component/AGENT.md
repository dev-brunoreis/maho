# Block/Component — AGENT

## 🎯 Objetivo
Implementar componentes reativos que exportam estado e ações ao runtime JavaScript, permitindo atualizações AJAX sem reload.

## 🧩 Arquivos-chave
- `Abstract.php` — API central (ID, mount, hydrate, dehydrate, renderPayload, getOpenwireConfig).
- `Contracts/ComponentInterface.php` e `Contracts/DataProvider.php` — contratos para componentes (ex.: data/html mode).
- `Counter.php` — exemplo prático.
- `Trait/Reactive.php`, `Trait/Stateful.php`, `Trait/Polling.php`, `Trait/Authorizes.php` — traits de comportamento.

## 🧭 Como criar um componente
1. Estenda `Maco_Openwire_Block_Component_Abstract`.
2. Use `Trait_Reactive` para alias e comportamento padrão.
3. Declare `protected $openwireAllowedActions` com os métodos que podem ser chamados via AJAX.
4. Implemente `mount($props)` e `_toHtml()` (usar `Template_Compiler` para templates declarativos).

## ⚙️ Modos de render (HTML vs DATA)
- Componentes podem suportar `data` mode implementando `DataProvider` e retornando `getDataPayload()` / `getHtmlPayload()`.

---
> Segurança: mantenha `openwireAllowedActions` restrito e documente novos métodos públicos.