# Helper — AGENT

## 🎯 Objetivo
Helpers fornecem utilitários e funções auxiliares reutilizáveis pelo módulo (por exemplo, acesso a configurações e conveniências para views).

## 📄 Arquivos
- `Data.php` — implementação de helper central (acesso a config, utilitários para templates).

## 🛠 Como usar
- Use `Mage::helper('openwire')` para acessar funções utilitárias em blocos, controllers e templates.

## 💡 Observações
- Mantenha helpers pequenos e focados; lógica complexa pertence a modelos ou serviços.

---
> Dica: escreva testes para helpers que contenham lógica não trivial.