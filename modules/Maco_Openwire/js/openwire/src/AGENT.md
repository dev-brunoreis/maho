# js/openwire/src — AGENT

## 🎯 Objetivo
Código cliente que executa o runtime OpenWire: captura eventos do DOM, forma payloads, envia atualizações via AJAX e aplica respostas (HTML patch / data merge).

## 🧩 Arquivos principais
- `event-handler.ts` — captura `click`, `change`, `input`, `submit` com atributos `data-ow:*` e envia payloads.
- `ajax-client.ts` — abstração de comunicação (POST para `/openwire/update/index`).
- `response-handler.ts` / `dom-patcher.ts` — aplica as respostas retornadas ao DOM.
- `poller.ts` / `debouncer.ts` — utilitários de timing.
- `index.ts` / `bootstrapper.ts` — ponto de entrada para inicializar o runtime.

## ⚙️ Build & Test
- `npm run dev` — dev server (vite).
- `npm run build` — gera `dist` para incluir no Magento.
- `npm test` — roda `vitest`.

## 🔄 Integração com PHP
- O runtime espera `data-ow-config` com `initialState`; envia `initial_state` uma vez por componente.
- A resposta do servidor deve seguir `{ html?, data?, state?, meta? }`.

## 📝 Boas práticas
- Atualize os testes unitários quando alterar a assinatura do payload.
- Mantenha compatibilidade entre `data-ow` e `data-openwire` (há compatibilidade parcial implementada).

---
> Dica: use o `EventHandler` e `ResponseHandler` como pontos para instrumentação (telemetria/logs).