# 1) O que é o MVP do Maco_Openwire

## Objetivo do MVP (definição de pronto)

No MVP, o **Maco_Openwire** deve permitir:

1. Declarar um **Block “componente”** no layout/template do Magento.
2. Renderizar esse Block normalmente (SSR) e também permitir **re-render via AJAX**.
3. Manter **estado**:

   * **Stateless** (estado vem do request) **e**
   * **Stateful** (estado persistido via sessão), habilitados por **trait**.
4. Fazer **binding e reatividade** com **Alpine.js** (sem depender de build step).
5. Suportar **3 gatilhos de update**:

   * `submit`
   * `onchange` (com debounce)
   * `polling` (intervalo configurável)
6. Rodar em **frontend e adminhtml**, respeitando **ACL nativa**.
7. Ter um ciclo de vida mínimo padronizado: `mount`, `hydrate`, `action`, `render`, `dehydrate`.

> Entrega “business-ready”: você consegue criar um componente tipo **Counter**, **Filtro de grid**, **Form reativo**, e ele se comporta bem em loja e admin.

---

# 2) Escopo do MVP: o que entra e o que NÃO entra

## Entra no MVP

* Endpoint AJAX para atualização de componente
* Contratos (interfaces), Base Abstract Component, Traits
* Serialização/Hidratação de estado
* Segurança: form_key/CSRF, ACL, allowlist de actions
* Runtime JS com Alpine (init, send, patch HTML, debounce, polling)
* 2 componentes demo (um para frontend e um para admin)
* Trilha de testes completa: Unit + Feature + Browser(E2E), dividida em PHP e JS

## Não entra (por agora)

* “Diff DOM inteligente” estilo morphing avançado (mVP pode fazer replace do “body” do componente)
* Websockets / realtime push
* Uploads complexos, streaming, progress
* Sistema de filas / jobs
* “Devtools” completo (pode ter logging básico)

---

# 3) Arquitetura em alto nível (visão executiva)

## Fluxo (resumo)

1. Magento renderiza a página normalmente.
2. Um componente Openwire aparece como um **wrapper** com metadados (alias, id, config).
3. Alpine.js inicializa um controller JS do Openwire.
4. Interação do usuário dispara `send()` com:

   * `component` (alias do block)
   * `component_id`
   * `action` (método no block)
   * `payload` (dados)
   * `state` (se aplicável)
   * `form_key`/token
5. Controller Magento valida ACL/CSRF, instancia o block, hidrata estado, executa action, renderiza HTML novamente e devolve JSON.
6. JS aplica “patch” no DOM e mantém o Alpine vivo.

---

# 4) Estrutura de arquivos do módulo (MVP)

```
app/etc/modules/Maco_Openwire.xml

app/code/local/Maco/Openwire/etc/config.xml
app/code/local/Maco/Openwire/etc/adminhtml.xml   (opcional, se preferir separar ACL)
app/code/local/Maco/Openwire/Helper/Data.php

app/code/local/Maco/Openwire/Controller/AjaxController.php
app/code/local/Maco/Openwire/controllers/AjaxController.php                (frontend)
app/code/local/Maco/Openwire/controllers/Adminhtml/OpenwireController.php  (admin)

app/code/local/Maco/Openwire/Block/Component/Abstract.php
app/code/local/Maco/Openwire/Block/Component/Contracts/*.php
app/code/local/Maco/Openwire/Block/Component/Traits/*.php

app/code/local/Maco/Openwire/Model/State/StoreInterface.php
app/code/local/Maco/Openwire/Model/State/SessionStore.php
app/code/local/Maco/Openwire/Model/Security/RequestValidator.php
app/code/local/Maco/Openwire/Model/Security/ActionPolicy.php

design/frontend/base/default/template/maco/openwire/*.phtml
design/adminhtml/default/default/template/maco/openwire/*.phtml

js/maco/openwire/openwire.js
js/maco/openwire/alpine.min.js (ou carregamento externo controlado via layout)
```

---

# 5) Contratos e Core: o que implementar

## 5.1 Interfaces (Contracts)

**O que implementar**

* `Maco_Openwire_Block_Component_Contract_ComponentInterface`

  * `mount(array $props = []) : void`
  * `hydrate(array $state) : void`
  * `dehydrate() : array`
  * `renderPayload() : array` (html + state + meta)
* `Maco_Openwire_Model_State_StoreInterface`

  * `load(string $componentId) : array`
  * `save(string $componentId, array $state) : void`
  * `forget(string $componentId) : void`

**O que testar (PHP Unit)**

* Interface compliance via classes fake
* `renderPayload()` sempre retorna estrutura mínima `{html, state, meta}`

---

## 5.2 Classe abstrata de componente (novo padrão)

**O que implementar**

* `Maco_Openwire_Block_Component_Abstract` extendendo `Mage_Core_Block_Template`
* Responsabilidades:

  * gerar/guardar `component_id`
  * gerenciar `props`
  * aplicar lifecycle padrão (`mount → hydrate → action → render → dehydrate`)
  * expor “metadata” para o frontend (poll interval, flags, etc.)
  * “action allowlist” (por padrão, só métodos explicitamente permitidos)

**Resultado esperado**

* Qualquer novo componente pode herdar essa abstract e ganhar o “framework” de graça.

**O que testar**

* (PHP Unit) `component_id` é estável por request e respeita override
* (PHP Unit) allowlist bloqueia execução de métodos não autorizados
* (PHP Unit) lifecycle chama na ordem correta quando acionado pelo controller

---

## 5.3 Traits (para legado e composição)

Aqui é onde você ganha “flexibilidade corporativa” e retrofit elegante.

### Trait: `Reactive`

**O que implementar**

* Helpers para gerar atributos/data-* no HTML:

  * `data-openwire-component="alias"`
  * `data-openwire-id="component_id"`
  * `data-openwire-config="json"`
* Helper para retornar o “x-data” Alpine com init do Openwire.

**Testes**

* (PHP Unit) `getOpenwireConfig()` gera JSON válido e com defaults

### Trait: `Stateful` (persistência configurável)

**O que implementar**

* `isStateful(): bool`
* `getStateStore(): StoreInterface` (default SessionStore)
* `loadState()` e `persistState()`

**Testes**

* (PHP Unit) SessionStore salva e carrega corretamente com chave namespaceada
* (PHP Feature) estado persiste entre duas chamadas AJAX usando mesmo component_id

### Trait: `Polling`

**O que implementar**

* `getPollIntervalMs(): ?int`
* `shouldPoll(): bool`

**Testes**

* (PHP Unit) defaults (null) e override por componente

### Trait: `Authorizes` (ACL)

**O que implementar**

* `authorize(): bool` baseado em:

  * area (admin vs frontend)
  * sessão
  * ACL resource (admin) quando aplicável

**Testes**

* (PHP Feature) request admin sem permissão → 403
* (PHP Feature) request frontend público permitido quando componente assim definir

---

# 6) Camada de Segurança (MVP sem brechas óbvias)

## 6.1 RequestValidator

**O que implementar**

* Validar:

  * `form_key` (frontend)
  * secret key/admin session (adminhtml)
  * payload JSON mínimo
  * tamanho máximo do payload
* Validar assinatura opcional do state (MVP pode começar sem HMAC e evoluir; mas recomendo já entrar com HMAC simples)

**O que testar**

* (PHP Unit) rejeita payload sem `component`/`action`
* (PHP Feature) rejeita request sem `form_key` no frontend
* (PHP Feature) rejeita request admin sem sessão

## 6.2 ActionPolicy (allowlist)

**O que implementar**

* Um componente deve declarar explicitamente:

  * `protected $_openwireAllowedActions = ['increment','decrement'];`
* Bloquear:

  * métodos mágicos
  * métodos do core (`toHtml`, `setTemplate`, etc.)
  * qualquer método que não esteja allowlisted

**Testes**

* (PHP Unit) método fora da allowlist não executa
* (PHP Feature) tentativa de chamar `setTemplate` retorna erro controlado (400/403)

---

# 7) Controller AJAX (frontend e admin)

## 7.1 Frontend: `maco_openwire/ajax/update`

**O que implementar**

* Controller `Maco_Openwire_AjaxController::updateAction()`
* Pipeline:

  1. parse JSON
  2. validate request
  3. create block por alias
  4. set component_id/props
  5. hydrate state (se stateful ou state enviado)
  6. execute action (se houver)
  7. render `html` (ideal: render apenas “body” do componente, não o wrapper)
  8. dehydrate + persist state (se stateful)
  9. return JSON

**Resultado necessário**

* Uma chamada POST retorna:

  * `html` atualizado
  * `state` atualizado
  * `meta` (ex: poll interval, timestamp)

## 7.2 Adminhtml: `adminhtml/openwire/update`

**O que implementar**

* Controller `Maco_Openwire_Adminhtml_OpenwireController`
* Mesmo pipeline, mas:

  * valida sessão admin
  * valida ACL (`_isAllowed`)
  * respeita secret keys do admin routing

**Testes**

* (PHP Feature) admin update com usuário autorizado → 200
* (PHP Feature) admin update sem ACL → 403

---

# 8) Runtime JS + Alpine.js (mVP)

## 8.1 openwire.js (core)

**O que implementar**
Um singleton/namespace:

* `window.Openwire = { bootstrap(), component(el), send(), patch(), debounce(), startPolling() }`

### Comportamentos obrigatórios (MVP)

1. **Bootstrap automático**

   * Ao carregar a página, encontra `[data-openwire-component]`
   * Inicializa Alpine controller em cada root
2. **send(action, payload)**

   * POST JSON pro endpoint correto
3. **patch(html)**

   * Atualiza apenas a região “body” do componente, sem destruir o root Alpine
4. **onchange**

   * binding de inputs com debounce e envio incremental
5. **submit**

   * intercepta submit e envia payload
6. **polling**

   * se `pollIntervalMs` vier no config, inicia timer e chama update periódico

> Nota de engenharia: o patch deve atualizar um container interno tipo `[data-openwire-body]` para não “matar” o `x-data`.

**O que testar (JS Unit)**

* `Openwire.debounce()` funciona (chama 1 vez com burst de eventos)
* `Openwire.send()` monta payload corretamente
* `Openwire.patch()` troca apenas body e mantém root intacto (teste via DOM em jsdom)

---

## 8.2 Integração com Alpine (contrato de componente)

**O que implementar**

* Um helper padrão:

  * `x-data="Openwire.component($el)"`
  * `x-init="init()"`

**Testes (JS Feature / integração leve)**

* Inicialização detecta elemento com data attrs e cria “state store” local
* Ao receber resposta JSON, atualiza `state` e re-renderiza body

---

# 9) Como será “um componente” no padrão final (MVP-ready)

## PHP (Block)

* Novo: herda `Maco_Openwire_Block_Component_Abstract`
* Legado: qualquer `Mage_Core_Block_Template` pode virar reativo adicionando traits + contrato mínimo

### Exemplo de contrato final (mental model)

* `mount($props)` para iniciar
* `hydrate($state)` para carregar estado
* `action()` para mutar
* `dehydrate()` para devolver estado
* template `.phtml` renderiza usando `$this->getState()` ou properties

## Template (phtml)

* wrapper root com:

  * `data-openwire-component`
  * `data-openwire-id`
  * `data-openwire-config`
  * `x-data`, `x-init`
* body interno com `data-openwire-body`

---

# 10) Plano passo a passo do MVP (com To-do + Expected Outcome + Testes)

Abaixo, cada etapa é “projeto-fechada”: você sabe exatamente o que codar, o que validar e qual resultado precisa sair.

---

## Etapa 1 — Bootstrap do módulo e roteamento

### To-do

* [ ] Criar `app/etc/modules/Maco_Openwire.xml`
* [ ] Criar `etc/config.xml` com:

  * routers frontend (`maco_openwire`)
  * admin router (`adminhtml`)
* [ ] Criar helper `Maco_Openwire_Helper_Data`
* [ ] Adicionar layout update para incluir `openwire.js` e Alpine (frontend e admin)

### Resultado esperado

* Módulo habilitado, sem conflicts no cache/config
* URL do endpoint existe (mesmo que retorne placeholder)

### Testes

**PHP Feature**

* [ ] GET/POST no endpoint retorna 200/405 conforme definido (ao menos roteia)

**Browser (E2E)**

* [ ] Página de teste carrega `openwire.js` e `Alpine` sem erro no console

---

## Etapa 2 — Contracts + Abstract Component + Traits base

### To-do

* [ ] Implementar interfaces (ComponentInterface, StoreInterface)
* [ ] Criar `Component_Abstract`
* [ ] Criar traits:

  * [ ] `Reactive`
  * [ ] `Stateful`
  * [ ] `Polling`
  * [ ] `Authorizes`
* [ ] Criar `SessionStore`

### Resultado esperado

* Você consegue instanciar um componente e obter config + payload consistente

### Testes

**PHP Unit**

* [ ] `SessionStore` salva/carrega/forget
* [ ] `Reactive` gera config JSON válido
* [ ] `Component_Abstract` respeita allowlist e lifecycle “mockado”

---

## Etapa 3 — Segurança: validator + action policy

### To-do

* [ ] Implementar `RequestValidator`
* [ ] Implementar `ActionPolicy`
* [ ] Padronizar response de erro (JSON com `error_code`, `message`)

### Resultado esperado

* Requests malformados ou não autorizados são bloqueados antes de tocar no componente

### Testes

**PHP Unit**

* [ ] Validator rejeita payload incompleto
* [ ] ActionPolicy bloqueia métodos perigosos

**PHP Feature**

* [ ] Frontend sem form_key → 403
* [ ] Admin sem sessão/ACL → 403

---

## Etapa 4 — Controller AJAX (frontend + admin)

### To-do

* [ ] Implementar `AjaxController::updateAction()` (frontend)
* [ ] Implementar `Adminhtml_OpenwireController::updateAction()` (admin)
* [ ] Implementar pipeline completo de lifecycle:

  * parse → validate → createBlock → mount/hydrate → action → render → dehydrate → persist → respond

### Resultado esperado

* POST retorna `{ html, state, meta }`
* Componentes conseguem ser atualizados sem refresh

### Testes

**PHP Feature**

* [ ] request executa action allowlisted e devolve HTML atualizado
* [ ] stateful persiste state entre 2 requests com mesmo component_id

---

## Etapa 5 — Runtime JS (Openwire) + Alpine wiring

### To-do

* [ ] Implementar `js/maco/openwire/openwire.js`
* [ ] Implementar:

  * [ ] bootstrap: detectar roots
  * [ ] send: montar payload e postar
  * [ ] patch: atualizar somente body
  * [ ] onchange debounce
  * [ ] submit intercept
  * [ ] polling loop
* [ ] Integrar com Alpine via `x-data="Openwire.component($el)"`

### Resultado esperado

* UI reativa funcionando em cima do HTML server-rendered do Magento

### Testes

**JS Unit**

* [ ] debounce funciona
* [ ] payload builder inclui component/id/action/state/form_key
* [ ] patch atualiza body sem destruir root

**Browser (E2E)**

* [ ] clique em botão dispara request e atualiza DOM
* [ ] onchange dispara request com debounce
* [ ] polling dispara request no intervalo e atualiza UI

---

## Etapa 6 — Componentes demo (2 entregas estratégicas)

### To-do

* [ ] Componente Frontend: `Counter`

  * increment/decrement
  * stateful via trait
* [ ] Componente Admin: `QuickFilter` (ex: filtro de grid fake ou listagem simples)

  * onchange + submit
  * ACL obrigatório

### Resultado esperado

* “Showcase” completo do framework rodando nos 2 mundos (loja e admin)

### Testes

**PHP Feature**

* [ ] Counter atualiza state
* [ ] Admin component bloqueia sem ACL

**Browser (E2E)**

* [ ] Frontend Counter: clicar “+” incrementa sem refresh
* [ ] Admin QuickFilter: mudar select atualiza listagem sem refresh

---

# 11) Matriz de Testes do MVP (3 tipos × 2 stacks)

## A) PHP — Unit / Feature / Browser

### PHP Unit (núcleo)

* SessionStore save/load/forget
* RequestValidator validações (payload mínimo, form_key)
* ActionPolicy allowlist
* Lifecycle order (mock component)
* Traits: config/poll/state

### PHP Feature (integração Magento)

* Endpoint frontend atualiza componente
* Endpoint admin bloqueia sem ACL
* Stateful: duas requisições preservam state
* Stateless: state vem do request e não “vaza” pra sessão

### Browser(E2E) com foco Magento real

* Counter em frontend atualiza DOM
* Admin componente respeita login/ACL
* onchange com debounce
* polling funciona (assert de múltiplas chamadas)

---

## B) JS — Unit / Feature / Browser

### JS Unit (Node + jsdom)

* debounce
* payload builder
* patch body
* scheduler de polling start/stop

### JS Feature (integração leve)

* Openwire.component(el) cria controller com state
* send() atualiza state após “mock fetch”
* onchange dispara send com debounce

### Browser(E2E) (Cypress/Playwright)

* fluxo completo no Magento:

  * inicializa
  * dispara request
  * atualiza HTML
  * preserva comportamento Alpine

---

# 12) “Checklist de Pronto” do MVP (go/no-go)

Você considera o MVP aprovado quando:

* [ ] Um componente no frontend atualiza sem refresh (submit + onchange + polling)
* [ ] Um componente no admin atualiza sem refresh e respeita ACL
* [ ] Stateful funciona e é habilitado por trait
* [ ] Stateless funciona e não depende de sessão
* [ ] Qualquer action fora da allowlist é bloqueada
* [ ] form_key/segurança mínima está enforced
* [ ] Suite de testes roda cobrindo Unit/Feature/E2E (PHP e JS)
