# API Reference

Complete reference for OpenWire's PHP and JavaScript APIs. Everything you need to build advanced components.

## 📚 Table of Contents

- [Component API](./components.md) - Block classes and lifecycle
- [Template API](./templates.md) - Template compilation and directives
- [State API](./state.md) - Data management and persistence
- [Security API](./security.md) - Protection and validation methods
- [JavaScript API](./javascript.md) - Frontend runtime and events

## 🧩 Component Classes

### Maco_Openwire_Block_Component_Abstract

Base class for all OpenWire components.

#### Properties

| Property | Type | Description |
|----------|------|-------------|
| `$_openwireAllowedActions` | `array` | Whitelisted methods callable from frontend |
| `$_openwireComponentName` | `string` | Component identifier (auto-generated) |
| `$_openwireId` | `string` | Unique instance ID |

#### Methods

##### Lifecycle Methods

```php
public function mount(array $params = []): void
```
Called when component is first loaded. Initialize state here.

```php
public function hydrate(): void
```
Called when component state is restored from persistence.

```php
public function dehydrate(): array
```
Return state to persist across requests.

##### Data Methods

```php
public function setData(string $key, mixed $value): self
public function getData(string $key = null): mixed
```
Set/get component data. Supports dot notation for nested access.

```php
public function hasData(string $key): bool
```
Check if data key exists.

```php
public function unsetData(string $key): self
```
Remove data key.

##### Action Methods

```php
public function callAction(string $method, array $params = []): mixed
```
Execute a whitelisted action method.

##### Template Methods

```php
public function getTemplate(): string
```
Return template path (e.g., `'openwire/counter.phtml'`).

```php
protected function _toHtml(): string
```
Render component HTML. Override for custom rendering.

##### Utility Methods

```php
public function getId(): string
```
Get unique component instance ID.

```php
public function getComponentName(): string
```
Get component class name.

## 🏗️ Core Models

### Maco_Openwire_Model_Component_Resolver

Resolves component classes from names.

```php
public function resolve(string $componentName): string
```
Convert component name to full class name.

```php
public function getComponentClass(string $componentName): string
```
Get component class for name.

### Maco_Openwire_Model_Component_Factory

Creates component instances.

```php
public function create(string $componentName, array $params = []): Maco_Openwire_Block_Component_Abstract
```
Create and initialize component instance.

### Maco_Openwire_Model_Template_Compiler

Compiles template directives.

```php
public function compile(string $html, Maco_Openwire_Block_Component_Abstract $component): string
```
Compile template with component data.

```php
public function compileDirectives(string $html): string
```
Convert `@event` to `data-ow:event` attributes.

```php
public function interpolateVariables(string $html, array $data): string
```
Replace `{{ variable }}` with values.

## 🎨 Template Directives

### Event Directives

| Directive | Description | Example |
|-----------|-------------|---------|
| `@click` | Click handler | `@click="increment"` |
| `@submit` | Form submit | `@submit="saveForm"` |
| `@input` | Input change | `@input="updateValue"` |
| `@change` | Select/radio change | `@change="updateSelection"` |
| `@focus` | Focus event | `@focus="showHelp"` |
| `@blur` | Blur event | `@blur="hideHelp"` |

### Special Attributes

| Attribute | Description | Example |
|-----------|-------------|---------|
| `openwire` | Component root | `<div openwire="counter">` |
| `data-ow:*` | Compiled directives | `data-ow:click="increment"` |

### Variable Interpolation

```php
{{ variable }}           // Simple variable
{{ object.property }}    // Nested property
{{ array.0.name }}       // Array access
{{ user?.name }}         // Safe navigation
{{ count > 0 ? 'Yes' : 'No' }}  // Ternary
```

## 🔄 State Management

### Data Persistence

```php
// Automatic persistence
$this->setData('count', 5);  // Survives page reloads

// Manual persistence
public function dehydrate(): array
{
    return [
        'count' => $this->getData('count'),
        'timestamp' => time()
    ];
}
```

### State Validation

```php
public function setData($key, $value = null)
{
    // Custom validation logic
    if ($key === 'email' && $value) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email');
        }
    }

    return parent::setData($key, $value);
}
```

## 🛡️ Security Features

### CSRF Protection

```php
// Automatic CSRF token
$formKey = $this->getFormKey();

// Manual verification
if (!$this->validateFormKey($request->getParam('form_key'))) {
    throw new Exception('Invalid form key');
}
```

### Action Whitelisting

```php
protected $_openwireAllowedActions = [
    'increment',
    'updateName',
    'submitForm'
];

// Only these methods can be called from AJAX
```

### Input Sanitization

```php
public function updateComment($comment = null)
{
    $comment = strip_tags($comment);  // Remove HTML
    $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');  // Escape
    $this->setData('comment', $comment);
}
```

## 📡 AJAX Communication

### Request Format

```json
{
    "id": "ow_1234567890",
    "calls": [
        {
            "method": "increment",
            "params": []
        }
    ],
    "updates": {
        "count": 6
    },
    "form_key": "abc123def456"
}
```

### Response Format

```json
{
    "html": "<div data-ow-component=\"counter\">...</div>",
    "state": {
        "count": 6
    },
    "effects": []
}
```

## 🔧 JavaScript Runtime

### Event Handling

```typescript
// Automatic event binding
document.addEventListener('click', (e) => {
    const method = e.target.getAttribute('data-ow:click');
    if (method) {
        sendUpdate({
            id: componentId,
            calls: [{ method, params: [] }]
        });
    }
});
```

### State Updates

```typescript
function handleResponse(response, element) {
    // Update DOM
    element.innerHTML = response.html;

    // Update state
    componentState = response.state;

    // Execute effects
    response.effects.forEach(effect => effect());
}
```

## 🧪 Testing APIs

### Component Testing

```php
use Tests\TestCase;

it('increments counter', function () {
    $counter = Mage::getModel('openwire/component_factory')
        ->create('counter', ['count' => 5]);

    $counter->increment();

    expect($counter->getData('count'))->toBe(6);
});
```

### Template Testing

```php
it('compiles template correctly', function () {
    $compiler = Mage::getModel('openwire/template_compiler');
    $component = new TestComponent();

    $html = $compiler->compile('<button @click="test">{{ value }}</button>', $component);

    expect($html)->toContain('data-ow:click="test"');
    expect($html)->toContain('test value');
});
```

## 📋 Configuration

### Module Configuration

```xml
<!-- app/etc/modules/Maco_Openwire.xml -->
<config>
    <modules>
        <Maco_Openwire>
            <active>true</active>
            <codePool>local</codePool>
        </Maco_Openwire>
    </modules>
</config>
```

### System Configuration

```xml
<!-- app/code/local/Maco/Openwire/etc/config.xml -->
<config>
    <modules>
        <Maco_Openwire>
            <version>1.0.0</version>
        </Maco_Openwire>
    </modules>

    <global>
        <blocks>
            <openwire>
                <class>Maco_Openwire_Block</class>
            </openwire>
        </blocks>

        <models>
            <openwire>
                <class>Maco_Openwire_Model</class>
            </openwire>
        </models>
    </global>
</config>
```

## 🚀 Performance Tips

### Component Optimization

```php
// Lazy load heavy data
public function loadExpensiveData()
{
    if ($this->getData('dataLoaded')) {
        return $this->getData('expensiveData');
    }

    $data = $this->fetchExpensiveData();
    $this->setData('expensiveData', $data);
    $this->setData('dataLoaded', true);

    return $data;
}
```

### Template Optimization

```php
// Cache compiled templates
protected function _toHtml()
{
    $cacheKey = 'openwire_template_' . $this->getComponentName();

    if (!$html = Mage::app()->getCache()->load($cacheKey)) {
        $html = $this->compileTemplate();
        Mage::app()->getCache()->save($html, $cacheKey, [], 3600);
    }

    return $html;
}
```

## 🐛 Debugging

### Enable Debug Mode

```php
// In index.php or bootstrap
Mage::setIsDeveloperMode(true);

// Log OpenWire events
Mage::log('Component created: ' . $component->getComponentName(), null, 'openwire.log');
```

### Debug Methods

```php
// Dump component state
public function debug()
{
    return [
        'id' => $this->getId(),
        'name' => $this->getComponentName(),
        'data' => $this->getData(),
        'allowed_actions' => $this->_openwireAllowedActions
    ];
}
```

---

<p align="center">
  <strong>📚 Complete API reference!</strong><br>
  <a href="./components.md">🧩 Component API</a> •
  <a href="./templates.md">🎨 Template API</a> •
  <a href="./javascript.md">💻 JavaScript API</a>
</p>