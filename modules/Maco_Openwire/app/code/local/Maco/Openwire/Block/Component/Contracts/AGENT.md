# Block/Component/Contracts — AGENT

## 🎯 Objetivo
Definir contratos (interfaces) que componentes podem implementar para suporte a modos de render (por exemplo `data` mode) e para garantir contratos claros API/implementação.

## 📄 Arquivos
- `ComponentInterface.php` — (interface base para componentes) — define métodos esperados para interoperabilidade.
- `DataProvider.php` — contrato para componentes que suportam o modo `data` (ex.: `getDataPayload`, `getHtmlPayload`).

## 🛠 Como usar
- Implemente `DataProvider` quando seu componente puder retornar um payload estruturado (útil para SPAs ou atualizações parciais no cliente).
- Assegure que `ComponentRunner` e o controller reconheçam a implementação para escolher o modo correto.

---
> Mantém a separação de preocupações e facilita testes unitários.