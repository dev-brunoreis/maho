# Block/Component/Trait — AGENT

## 🎯 Propósito
Traits fornecem comportamentos reutilizáveis que os componentes podem incorporar: alias e identificação (`Reactive`), persistência de estado (`Stateful`), polling (`Polling`) e autorização (`Authorizes`).

## 📄 Traits existentes
- `Reactive.php` — gera alias (`openwire_component/{name}`) e fornece `getPollIntervalMs()` padrão.
- `Stateful.php` — integra com `Model/State/SessionStore` para carregar/persistir estado automaticamente.
- `Polling.php` — utilitário para componentes que fazem polling periódico.
- `Authorizes.php` — helpers para políticas de ação (ainda pode ser placeholder).

## ⚙️ Como usar
- Inclua o trait no `Block` do componente: `use Maco_Openwire_Block_Component_Trait_Reactive;`.
- Se o componente precisar persistir estado entre requests, use `Stateful` e declare chaves de estado no `dehydrate()`/`hydrate()`.

## 💡 Observações
- Traits mantêm o componente simples e evitam duplicação; atualize os testes ao alterar comportamento de um trait.

---
> Tip: Leia `Counter` e `LegacyWrapper` como exemplos de uso de traits.