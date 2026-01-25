# Model/Template — AGENT

## 🎯 Objetivo
Compilar templates declarativos escritos pelos desenvolvedores (`@click`, `{{ var }}`, `openwire="alias"`) em HTML operacional consumido pelo runtime JavaScript (atributos `data-ow:*`, `data-ow-config`, `x-data`).

## 📄 Arquivo principal
- `Compiler.php` — transforma diretivas em atributos: `@event` -> `data-ow:event`, `{{ var }}` -> valor escapado, `openwire="alias"` -> `data-ow-*` com configuração JSON.

## 🛠 Como usar
- Chame o compiler em `_toHtml()` de componentes ou no `LegacyWrapper` quando detectadas diretivas.
- Para testes: há testes unitários cobrindo eventos, bindings e root directive (ver `tests/Unit/Model/TemplateCompilerTest.php`).

## 🧩 Considerações
- O compilador atual faz substituições simples; cuidado ao introduzir sintaxes complexas (ex.: expressões JS dentro de bindings).

---
> Ao estender, mantenha compatibilidade retroativa com templates legacy.