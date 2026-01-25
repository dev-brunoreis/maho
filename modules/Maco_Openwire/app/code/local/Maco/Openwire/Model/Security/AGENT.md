# Model/Security — AGENT

## 🎯 Objetivo
Centralizar validação e políticas de segurança para requests OpenWire, protegendo contra CSRF, payloads malformados e chamadas de ação não autorizadas.

## 📄 Arquivos-chave
- `RequestValidator.php` — valida estrutura do payload, campos obrigatórios e `form_key` para frontends.
- `ActionPolicy.php`, `ForbiddenMethodEnum.php` — utilitários para regras de ação (ex.: permitir/negara execução remota de métodos).

## 🛡 Como integrar
- Sempre chame `RequestValidator->validate($payload)` no começo do fluxo (o `UpdateController` já faz isso).
- Ao expor novas ações, atualize as allowlists e adicione testes para casos de negação.

## 🔧 Extensibilidade
- Para regras mais complexas, conecte a ACL do Magento ou escreva um `Policy` que cheque o usuário/rol.

---
> Segurança é crítica: adicione testes para qualquer mudança que exponha nova superfície de execução remota.