# Model — AGENT

## 🎯 Propósito
Contém modelos que suportam a execução dos componentes no servidor: orquestração de requests, segurança, estado e compilação de templates.

## 📁 Submódulos importantes
- `Bridge/` — abstração para requests/response e runner que implementa o fluxo completo do componente.
- `Template/` — compilador de templates declarativos para atributos operacionais usados pelo runtime JS.
- `State/` — implementação de armazenamento (session-store) para `Stateful` components.
- `Security/` — validação de payloads e regras de autorização.

## 🤝 Integração
- `Bridge/ComponentRunner` pode ser usado para centralizar lógica quando expor APIs além do controller padrão.
- `Template_Compiler` é consumido pelos blocos (`Counter`, `LegacyWrapper`) ao gerar HTML.

## ✅ Testes
- Testes unitários cobrem `Template_Compiler` e fluxo de `ComponentRunner`.

---
> Dica: use `Bridge` quando quiser transformar o fluxo em uma API (modo html/data) e facilitar instrumentação e logs.