# Block — AGENT

## 🎯 Propósito
Contém blocos Magento que representam componentes reativos (componentes nativos OpenWire e wrappers para blocos legados).

## 🔎 Componentes de destaque
- `Component/Abstract.php` — base para componentes com API: `mount()`, `hydrate()`, `dehydrate()`, `executeAction()`, `_toHtml()`.
- `LegacyWrapper.php` — adapta blocos legados para o fluxo OpenWire, compila templates se detecta diretivas.
- `Component/Counter.php` — exemplo de componente demonstrando `increment`/`decrement` e uso do compilador de templates.

## 🛠 Como contribuir
- Novos componentes devem expor um alias (via trait `Reactive`) e declarar ações permitidas em `$openwireAllowedActions`.
- Evite métodos públicos que não estejam na whitelist para ações remotas.

## 🔐 Segurança
- O controller central valida ações permitidas antes de invocar `executeAction()`.

---
> Dica: use o `Counter` como referência para novos componentes simples.