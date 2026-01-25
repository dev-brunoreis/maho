# js/openwire/tests — AGENT

## 🎯 Objetivo
Cobertura de testes unitários para o runtime JavaScript/TypeScript.

## 🧪 Test files
Arquivos: `event-handler.test.ts`, `ajax-client.test.ts`, `dom-patcher.test.ts`, etc. — cada teste foca em pequenas unidades do runtime.

## ⚙️ Como executar
- `npm test` — roda `vitest` e executa os testes listados.

## 📌 Boas práticas
- Mantenha os testes isolados (mock de fetch/DOM) e rápidos.
- Ao alterar payloads, atualize os testes de `event-handler` e `ajax-client`.

---
> Observação: use `jsdom` para emular o DOM nos testes.