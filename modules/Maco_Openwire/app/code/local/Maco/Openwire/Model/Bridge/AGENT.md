# Model/Bridge — AGENT

## 🎯 Objetivo
Implementar uma camada "bridge" que representa um fluxo bem-definido para requests de componentes, permitindo modo `html` ou `data` e separando responsabilidades do controller.

## 📄 Arquivos-chave
- `ComponentRunner.php` — orquestra a lifecycle: resolve → authorize → mount → hydrate → validate → run action → dehydrate → persist → render.
- `ModeResolver.php` — decide se a resposta deve ser `html` ou `data`.
- `Request.php` / `Response.php` — objetos para transportar dados no pipeline.

## 🛠 Como utilizar
- Construa um `Request` e chame `ComponentRunner::run($request)` para um processamento consistente e testável.
- Use `ModeResolver` para adicionar heurísticas de render (por usuário, cabeçalho, ou props).

## 🔍 Observações de segurança
- `authorize()` no runner é placeholder; implemente checks baseados em ACL quando necessário.

---
> Esse módulo facilita adicionar observability e testes de integração sem tocar no controller.