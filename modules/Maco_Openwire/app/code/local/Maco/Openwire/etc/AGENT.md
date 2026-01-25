# etc — AGENT

## 🎯 Objetivo
Contém arquivos de config do módulo Magento (`config.xml`) que registram routers, models, helpers e dependências do módulo.

## 📄 Arquivo principal
- `config.xml` — declara resources, routers, modelos, helpers e registra o controller `openwire/update`.

## ⚙️ Como modificar
- Atualize `config.xml` para registrar novas classes, observers, rotas e configurações de XML.
- Após mudanças em `config.xml`, limpe cache do Magento (`var/cache`) para que as mudanças tenham efeito.

## 📝 Considerações
- Mantenha namespacing consistente (`openwire/...`) e atualize `tests/bootstrap.php` se mocks de model forem adicionados.

---
> Sempre valide o XML (syntax/structure) antes de implantar em ambientes.