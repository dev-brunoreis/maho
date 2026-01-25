# Vuewire (PDP) — Guia rápido de DX

Este guia documenta **como estruturamos mountpoints em `.phtml`** e **como consumimos isso no Vue** no escopo de `Catalog/Product` (PDP).

## Conceitos

- **Mountpoint**: elemento raiz renderizado pelo Magento (PHTML) que monta um componente Vue.
- **Contrato**: shape de `data-props` + slots esperados por aquele componente.
- **Slots (PHTML)**: conteúdo HTML de blocos Magento filho, exposto como `<slot name="...">...</slot>` e consumido no Vue.

## Mountpoint (PHTML)

Um mountpoint deve sempre ter:

- `data-ui="vue:<RegistryKey>"`: chave que mapeia para o componente em `skin/.../js/bridge/registry.js`.
- `data-props='{"...": "..."}'`: JSON com props do contrato.
- `openwire="..."` (opcional): nome do “backend component” usado por `useOpenwire()` quando o Vue precisar chamar ações (endpoint OpenWire).
- `<slot name="...">...</slot>` (opcional): HTML de blocos filhos.

Exemplo (PDP):

```html
<div
  openwire="catalog/product_view"
  data-ui="vue:Catalog/Product/View"
  data-contract="catalog/product_view@1"
  data-props='{"product": 123, "isSaleable": true, "...": "..."}'
>
  <slot name="price">...</slot>
  <slot name="media">...</slot>
</div>
```

### Regras de slots

- Use slots nomeados para qualquer HTML legado gerado pelo Magento que o Vue precisa posicionar.
- Se um slot existe no PHTML mas o Vue não renderiza (`<slot name="..."/>` ou `v-html`), **ele some** (o Vue substitui o conteúdo do mountpoint). Em modo dev, o runtime avisa no console.

## Runtime (`js/main.js`)

O runtime:

- Faz `JSON.parse(data-props)` e passa isso como props do componente.
- Extrai `<slot name="...">...</slot>` do mountpoint e:
  - injeta em `props.slots[name]` (HTML string)
  - cria Vue slots com wrapper `display: contents` para minimizar impacto de markup
- Em dev, adiciona warnings quando:
  - falta `data-contract`
  - havia conteúdo de slot no PHTML mas o Vue não renderizou

## Contratos (TypeScript)

Contratos vivem em:

- `skin/frontend/maco/vuewire/js/contracts/`

Exemplos:

- `contracts/catalog/product/view.ts`
- `contracts/catalog/product/view/options/type/*.ts`

Padrões:

- `...Contract` (string) e `...PropsV1` (interface)
- Slots são tipados quando útil, e sempre existe `slots?: Record<string,string>` como fallback.

## Vue (componentes)

Padrão recomendado:

- Sempre usar `<script setup lang="ts">`
- Usar `defineProps<...>()` com o contrato correspondente
- Preferir `<slot name="..."/>` para HTML do Magento
- Usar `v-html` apenas quando necessário

Helpers:

- `components/_shared/RawHtmlSlot.vue`: renderiza `props.slots[name]` com `display: contents`
- `components/_shared/MaybeSlot.vue`: usa Vue slot se existir, senão cai para `RawHtmlSlot`

## Custom Options (PDP)

Decisão atual: **Magento renderiza o HTML de cada option**, e o Vue apenas organiza/posiciona consumindo via slots.

O mountpoint `catalog/product/view/options.phtml` agora emite:

- `data-props.options = [{id}]`
- `<slot name="option-{id}">...HTML do Magento...</slot>`

E o Vue `Options.vue` consome:

- `<slot :name="'option-' + option.id" />`

## Checklist para criar um novo mountpoint

- Definir `data-ui` e registrar no `bridge/registry.js`.
- Criar contrato `@1` em `contracts/...`.
- Garantir `data-props` com chaves estáveis.
- Se houver HTML de blocos filhos: expor via `<slot name="...">...</slot>` e consumir no Vue.
- (Opcional) Definir `data-contract="...@1"` para DX.

