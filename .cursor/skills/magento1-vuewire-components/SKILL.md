---
name: magento1-vuewire-components
description: Create or extend Magento 1 Vuewire theme components (phtml + Vue + contract + registry) following the Maco_Vuewire pattern. Use when the user asks to create web components for specific Magento 1 parts (e.g. checkout cart, header, footer).
---

# Magento 1 Vuewire components

**Project:** We use [Maho](https://github.com/MahoCommerce/maho) — the modern PHP 8.3+ ecommerce platform (Magento 1–compatible). Vuewire components live in the `Maco_Vuewire` module within this codebase.

**Implementation:** Use only the Maho codebase and project docs for block/template APIs and behaviour. Do not rely on OpenMage or external Magento 1 core documentation.

**Maho default theme:** When looking up the default theme (layout XML, template paths, block names, page structure), use the official Maho default theme: [app/design/frontend/base/default](https://github.com/MahoCommerce/maho/tree/main/app/design/frontend/base/default).

When the user asks to create web components for a specific Magento 1 area (e.g. "crie o componente do carrinho", "faça o bloco do header"), follow the **Maco_Vuewire** pattern. Apply the project rule in `.cursor/rules/magento1-vuewire-components.mdc` (it applies to `**/Maco_Vuewire/**/*.{phtml,vue,ts}` and `registry.js`).

## What to produce

For each new Magento 1 block/template the user requests:

1. **PHTML** – In `modules/Maco_Vuewire/app/design/frontend/maco/vuewire/template/` using the standard Magento 1 template path (e.g. `checkout/cart/item/default.phtml`). Root `<div>` with `openwire`, `data-ui="vue:..."`, `data-props`, and optional `<slot name="...">` for child blocks.
2. **Contract** – In `modules/Maco_Vuewire/skin/frontend/maco/vuewire/js/contracts/` with path in lowercase; nested leaf like `options/wrapper/bottom` → file `options/wrapper_bottom.ts`. Export contract constant, slot types if any, and `XxxPropsV1`. Add/update `index.ts` exports.
3. **Vue component** – In `modules/Maco_Vuewire/skin/frontend/maco/vuewire/js/components/` with path in PascalCase (e.g. `Checkout/Cart/Item/Default.vue`). Use `defineProps<XxxPropsV1>()` from the contract; use `<slot name="...">` or MaybeSlot/RawHtmlSlot as appropriate; use `$t()` for labels. **Do not add styling**: no `<style>`, no scoped CSS, no component-specific CSS; components use the same class names as the original Magento templates so the theme/skin CSS applies.
4. **Registry** – Add an entry in `modules/Maco_Vuewire/skin/frontend/maco/vuewire/js/bridge/registry.js`: key = PascalCase path (same as `data-ui` without `vue:`), value = `() => import('../components/...')`.

**Layout XML:** Do not add or change layout XML (e.g. `local.xml`, `openwire.xml`) when creating a new component. The theme resolves templates by path; no `<reference name="…">` or `setTemplate` is required unless the user explicitly asks for it.

## Naming quick reference

- **Openwire id**: snake_case path, e.g. `checkout/cart_item_default`.
- **data-ui**: `vue:Checkout/Cart/Item/Default`.
- **Contract file**: lowercase path; for `a/b/c/d.phtml` use `a/b/c_d.ts` (last two segments with `_` when nested).

Always keep Magento 1 standard template paths; the theme base is `app/design/frontend/maco/vuewire/template/` under the module.
