# Model/State — AGENT

## 🎯 Objetivo
Fornecer abstrações para persistência do estado de componentes entre requests. Atualmente usa sessão do Magento.

## 📄 Arquivos-chave
- `SessionStore.php` — implementa `load`, `save`, `forget` usando `core/session`.
- `StoreInterface.php` — interface para permitir swaps futuros (Redis, DB).

## ⚙️ Como usar
- `Trait_Stateful` chama o store para `load`/`save` automaticamente se o componente for stateful.
- Para migrar o store, implemente `StoreInterface` e registre o modelo adequado.

## 🧪 Testes
- Verificar que estados são corretamente recuperados e sobrescritos em fluxos de múltiplas requisições.

---
> Observação: sessão pode não ser adequada para escalabilidade horizontal — considere uma store externa para clusters.