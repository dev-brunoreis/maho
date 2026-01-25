# controllers — AGENT

## 🎯 Propósito
Contém os controllers que expõem endpoints HTTP para o módulo. O principal é o `UpdateController` que processa solicitações AJAX do runtime JS.

## 📌 Arquivo principal
- `UpdateController.php` — recebe payloads JSON, valida com `RequestValidator`, instancia/resolve componente, monta/hidrata, executa ações, persiste estado e responde com JSON `{ html, state, meta }`.

## 🚨 Regras importantes
- Sempre usar validação de payload e checar `form_key` para evitar CSRF (feito por `RequestValidator`).
- Tratar exceptions e retornar código 400 com mensagem de erro clara (o controller já faz isso via `_errorResponse`).

## 🛠 Ao adicionar endpoints
- Mantenha convenção de resposta uniformizada (`html`/`data` + `state` + `meta`).
- Escreva testes de integração para fluxos de sucesso e erro.

---
> Nota: o controller aceita tanto componentes OpenWire quanto blocos legados (envia para `LegacyWrapper`).