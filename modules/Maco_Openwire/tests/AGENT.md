# tests — AGENT

## 🎯 Objetivo
Cobrir comportamento de componentes, compilador de templates, e fluxo de requests (unit / integration) usando Pest para PHP e Vitest para o JS.

## 🧪 Como executar
- PHP tests: `composer test` (Pest)
- JS tests: `npm test` (Vitest)

## 📌 Convenções
- Use `tests/Unit` para unidades, `tests/Feature`/`Browser` para integrações.
- Mocks de `template_compiler` e outros serviços são definidos em `tests/bootstrap.php`.

## ✍️ Ao adicionar testes
- Garanta cobertura para casos de sucesso e erros (ex.: validação de payload, ações não permitidas, parsing do compilador).

---
> Dica: testes são executados no CI; mantenha tempo de execução baixo e mocks adequados.