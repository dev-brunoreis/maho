<p align="center">
  <img src=".github/assets/img/logo.svg" alt="OpenWire Logo" width="200"/>
</p>

# OpenWire: Reactive Components for Magento 1

<p align="center">
  <img src=".github/assets/img/logo.svg" alt="OpenWire Logo" width="200"/>
</p>

[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg)](https://phpstan.org/)
[![Pest](https://img.shields.io/badge/Pest-enabled-brightgreen.svg)](https://pestphp.com/)
[![Magento 1](https://img.shields.io/badge/Magento-1.9+-blue.svg)](https://magento.com/)

OpenWire is a revolutionary Magento 1 module that brings modern reactive component architecture to the legacy Magento 1 platform. Inspired by Laravel Livewire and Magento 2's Magewire, OpenWire enables developers to build dynamic, interactive user interfaces without writing JavaScript.

## ✨ Features

- 🚀 **Zero JavaScript Required** - Build interactive UIs with PHP only
- 🔄 **Reactive Components** - Automatic AJAX updates without page refreshes
- 🎨 **Declarative Templates** - Clean, semantic HTML with `@click`, `{{ variables }}`
- 💾 **Stateful Components** - Automatic state persistence across requests
- 🔒 **Security First** - Built-in CSRF protection and action validation
- 🧪 **Thoroughly Tested** - 100% test coverage with Pest PHP
- 📱 **Modern Frontend** - TypeScript-powered runtime with Vite build system
- 🏗️ **Magento Native** - Seamless integration with Magento 1 architecture

## 📋 Table of Contents

- [Architecture Overview](#architecture-overview)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Component System](#component-system)
- [Template Engine](#template-engine)
- [State Management](#state-management)
- [Event Handling](#event-handling)
- [Security](#security)
- [Testing](#testing)
- [Development](#development)
- [API Reference](#api-reference)
- [Examples](#examples)
- [Contributing](#contributing)

## 🏛️ Architecture Overview

OpenWire consists of three main layers:

### Backend (PHP)
- **Component Classes**: Extend `Maco_Openwire_Block_Component_Abstract`
- **Template Compiler**: Transforms declarative HTML to operational attributes
- **State Management**: Session-based persistence for stateful components
- **Security Layer**: Request validation and action authorization

### Frontend (TypeScript/JavaScript)
- **Bootstrapper**: Initializes OpenWire components on page load
- **Event Handler**: Captures user interactions and sends AJAX requests
- **DOM Patcher**: Updates page content without full refreshes
- **AJAX Client**: Handles communication with Magento backend

### Communication Protocol
```json
// Request Payload
{
  "id": "ow_1234567890",
  "component": "openwire_component/counter",
  "calls": [{"method": "increment", "params": []}],
  "initial_state": {"count": 0},
  "form_key": "abc123def456"
}

// Response Payload
{
  "html": "<button data-ow:click=\"increment\">Count: 1</button>",
  "state": {"count": 1},
  "meta": {"pollIntervalMs": null}
}
```

## 🚀 Installation

### Requirements
- Magento 1.9+
- PHP 7.4+
- Composer
- Node.js 16+ (for development)

### Setup

1. **Clone and Install**
```bash
git clone https://github.com/maco-studios/openwire.git
cd openwire
composer install
npm install
```

2. **Deploy to Magento**
```bash
# Copy module files
cp -r app/code/local/Maco /path/to/magento/app/code/local/
cp -r app/design /path/to/magento/app/design/
cp -r js /path/to/magento/js/

# Enable module
echo "Maco_Openwire" >> /path/to/magento/app/etc/modules/Maco_Openwire.xml
```

3. **Build Frontend Assets**
```bash
npm run build
```

4. **Clear Magento Cache**
```bash
# Via admin panel or command line
php shell/indexer.php reindexall
rm -rf var/cache/*
```

## ⚡ Quick Start

### 1. Create a Component

```php
<?php
// app/code/local/YourModule/Openwire/Block/Component/Counter.php
class YourModule_Openwire_Block_Component_Counter extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;
    use Maco_Openwire_Block_Component_Trait_Stateful;

    protected $_openwireAllowedActions = ['increment', 'decrement'];

    public function mount($params = [])
    {
        parent::mount($params);
        $this->setData('count', $params['count'] ?? 0);
    }

    public function increment()
    {
        $this->setData('count', $this->getData('count') + 1);
    }

    public function decrement()
    {
        $this->setData('count', $this->getData('count') - 1);
    }

    protected function _toHtml()
    {
        $declarativeHtml = '<div openwire="counter">
            <button @click="decrement">-</button>
            <span>{{ count }}</span>
            <button @click="increment">+</button>
        </div>';

        $compiler = Mage::getModel('openwire/template_compiler');
        return $compiler->compile($declarativeHtml, $this);
    }
}
```

### 2. Create a Template

```php
<?php
// app/code/local/YourModule/Openwire/Block/Component/Counter.php (continued)
class YourModule_Openwire_Block_Component_Counter extends Maco_Openwire_Block_Component_Abstract
{
    // ... component code ...

    public function getTemplate()
    {
        return 'yourmodule/counter.phtml';
    }
}
```

```html
<!-- app/design/frontend/base/default/template/yourmodule/counter.phtml -->
<div data-ow-component="<?php echo $this->getComponentAlias(); ?>"
     data-ow-id="<?php echo $this->getId(); ?>"
     data-ow-config='<?php echo json_encode($this->getOpenwireConfig()); ?>'
     x-data="{}">
    <div data-ow-body>
        <?php echo $this->_toHtml(); ?>
    </div>
</div>
```

### 3. Register the Component

```xml
<!-- app/code/local/YourModule/etc/config.xml -->
<config>
    <global>
        <blocks>
            <yourmodule_component>
                <class>YourModule_Openwire_Block_Component</class>
            </yourmodule_component>
        </blocks>
    </global>
</config>
```

### 4. Use in Layout

```xml
<!-- app/design/frontend/base/default/layout/yourmodule.xml -->
<layout>
    <yourmodule_index_index>
        <reference name="content">
            <block type="yourmodule_component/counter" name="counter" template="yourmodule/counter.phtml" />
        </reference>
    </yourmodule_index_index>
</layout>
```

## 🧩 Component System

### Component Types

#### Basic Components
```php
class Example_Basic extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;

    protected $_openwireAllowedActions = ['doSomething'];

    public function doSomething()
    {
        // Action logic
    }
}
```

#### Stateful Components
```php
class Example_Stateful extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;
    use Maco_Openwire_Block_Component_Trait_Stateful;

    protected $_openwireAllowedActions = ['updateData'];

    public function mount($params = [])
    {
        parent::mount($params);
        $this->setData('items', []);
    }

    public function updateData($newData)
    {
        $this->setData('items', $newData);
        // State automatically persisted
    }
}
```

#### Components with Authorization
```php
class Example_Secure extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;
    use Maco_Openwire_Block_Component_Trait_Authorizes;

    protected $_openwireAllowedActions = ['adminOnlyAction'];

    public function adminOnlyAction()
    {
        $this->authorize('admin'); // Throws exception if not authorized
        // Admin-only logic
    }
}
```

### Component Lifecycle

1. **Instantiation**: Component created via `Mage::app()->getLayout()->createBlock()`
2. **Mount**: `mount()` called with props from layout/template
3. **Hydration**: For AJAX requests, state loaded from session
4. **Action Execution**: User-triggered methods executed
5. **Rendering**: `renderPayload()` returns HTML + state + metadata
6. **Persistence**: State saved to session for stateful components

## 🎨 Template Engine

OpenWire includes a powerful declarative template engine that compiles semantic HTML into operational attributes.

### Syntax

#### Event Directives
```html
<!-- Declarative -->
<button @click="increment">+1</button>
<input @change="updateValue" />
<form @submit="saveForm">

<!-- Compiled -->
<button data-ow:click="increment">+1</button>
<input data-ow:change="updateValue" />
<form data-ow:submit="saveForm">
```

#### Variable Binding
```html
<!-- Declarative -->
<span>Hello {{ name }}!</span>
<div class="{{ isActive ? 'active' : 'inactive' }}">

<!-- Compiled -->
<span>Hello John!</span>
<div class="active">
```

#### Component Root
```html
<!-- Declarative -->
<div openwire="counter">
    <button @click="increment">{{ count }}</button>
</div>

<!-- Compiled -->
<div data-ow-component="openwire_component/counter"
     data-ow-id="ow_1234567890"
     data-ow-config='{"component":"openwire_component/counter",...}'
     x-data="{}">
    <button data-ow:click="increment">5</button>
</div>
```

### Advanced Features

#### Conditional Rendering
```php
protected function _toHtml()
{
    $count = $this->getData('count');
    $html = '<div openwire="counter">';

    if ($count > 0) {
        $html .= '<button @click="decrement">-</button>';
    }

    $html .= '<span>{{ count }}</span>';
    $html .= '<button @click="increment">+</button>';
    $html .= '</div>';

    $compiler = Mage::getModel('openwire/template_compiler');
    return $compiler->compile($html, $this);
}
```

#### Dynamic Attributes
```php
protected function _toHtml()
{
    $isDisabled = $this->getData('isDisabled');
    $disabledAttr = $isDisabled ? ' disabled="disabled"' : '';

    $html = '<div openwire="form">
        <input type="text" @change="updateValue"' . $disabledAttr . ' />
    </div>';

    $compiler = Mage::getModel('openwire/template_compiler');
    return $compiler->compile($html, $this);
}
```

## 💾 State Management

### Automatic State Persistence

Stateful components automatically persist their state across requests using PHP sessions.

```php
class Example_Cart extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Stateful;

    public function mount($params = [])
    {
        parent::mount($params);
        // Initialize cart if empty
        if (!$this->getData('items')) {
            $this->setData('items', []);
        }
    }

    public function addItem($productId, $quantity = 1)
    {
        $items = $this->getData('items');
        $items[$productId] = ($items[$productId] ?? 0) + $quantity;
        $this->setData('items', $items);
        // State automatically saved to session
    }

    public function removeItem($productId)
    {
        $items = $this->getData('items');
        unset($items[$productId]);
        $this->setData('items', $items);
    }
}
```

### State Storage

State is stored using the `Maco_Openwire_Model_State_SessionStore` class:

```php
// Automatic storage key: 'openwire_state_' . $componentId
// Example: 'openwire_state_ow_1234567890'
```

### Hydration & Dehydration

- **Hydration**: Loading state from storage into component data
- **Dehydration**: Extracting component data for storage

```php
// Custom hydration/dehydration (rarely needed)
public function hydrate(array $state)
{
    // Custom logic before setting state
    $processedState = $this->processState($state);
    parent::hydrate($processedState);
}

public function dehydrate()
{
    $state = parent::dehydrate();
    // Custom logic before saving state
    return $this->cleanState($state);
}
```

## 🎯 Event Handling

### Supported Events

- `@click` - Click events on any element
- `@change` - Input change events
- `@submit` - Form submission events
- `@blur` - Input blur events
- `@focus` - Input focus events

### Event Parameters

```php
public function handleClick($elementId)
{
    // $elementId is passed from frontend
}

public function updateValue($newValue)
{
    // Input value passed automatically
}

public function saveForm($formData)
{
    // Form data as associative array
}
```

### Debounced Events

Change events are automatically debounced (300ms) to prevent excessive requests:

```html
<input @change="search" placeholder="Search..." />
```

### Event Modifiers (Future)

```html
<!-- Planned for future versions -->
<button @click.prevent="submit">Submit</button>
<button @click.stop="increment">+1</button>
```

## 🔒 Security

### CSRF Protection

All requests include Magento's form key:

```javascript
// Automatic inclusion
const payload = {
    form_key: getFormKey(),
    // ... other data
};
```

### Action Validation

Components define allowed actions:

```php
class Secure_Component extends Maco_Openwire_Block_Component_Abstract
{
    protected $_openwireAllowedActions = ['safeAction'];

    public function safeAction()
    {
        // This is allowed
    }

    public function dangerousAction()
    {
        // This will throw an exception
    }
}
```

### Request Validation

All requests are validated server-side:

```php
$validator = new Maco_Openwire_Model_Security_RequestValidator();
$validator->validate($payload);
```

### Authorization Trait

```php
class Admin_Component extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Authorizes;

    public function adminAction()
    {
        $this->authorize('admin');
        // Only admins can execute this
    }
}
```

## 🧪 Testing

OpenWire includes comprehensive test suites with 100% coverage.

### Running Tests

```bash
# Run all tests
composer test

# Run specific test suite
composer test -- --filter=CounterTest

# Run with coverage
composer test -- --coverage
```

### Component Testing

```php
<?php
// tests/Unit/Component/CounterTest.php
it('increments count', function () {
    $counter = new Maco_Openwire_Block_Component_Counter();
    $counter->mount(['count' => 5]);
    $counter->increment();
    expect($counter->getData('count'))->toBe(6);
});

it('renders HTML with correct attributes', function () {
    $counter = new Maco_Openwire_Block_Component_Counter();
    $counter->mount(['count' => 3]);

    $html = $counter->toHtml();

    expect($html)->toContain('data-ow-component="openwire_component/counter"');
    expect($html)->toContain('data-ow:click="increment"');
    expect($html)->toContain('<span>3</span>');
});
```

### Template Compiler Testing

```php
it('compiles @click directive correctly', function () {
    $compiler = new Maco_Openwire_Model_Template_Compiler();
    $html = '<button @click="increment">+1</button>';

    $counter = new Maco_Openwire_Block_Component_Counter();
    $result = $compiler->compile($html, $counter);

    expect($result)->toContain('data-ow:click="increment"');
    expect($result)->not()->toContain('@click="increment"');
});
```

## 🛠️ Development

### Project Structure

```
openwire/
├── app/code/local/Maco/Openwire/
│   ├── Block/Component/
│   │   ├── Abstract.php
│   │   ├── Trait/
│   │   │   ├── Reactive.php
│   │   │   ├── Stateful.php
│   │   │   └── Authorizes.php
│   │   └── Counter.php
│   ├── controllers/
│   │   ├── UpdateController.php
│   │   └── Adminhtml/
│   ├── Model/
│   │   ├── Template/
│   │   │   └── Compiler.php
│   │   ├── State/
│   │   │   └── SessionStore.php
│   │   ├── Security/
│   │   │   ├── RequestValidator.php
│   │   │   └── ActionPolicy.php
│   │   └── Component/
│   │       └── Factory.php
│   └── Helper/
│       └── Data.php
├── app/design/frontend/base/default/
│   ├── layout/
│   │   └── openwire.xml
│   └── template/
│       └── openwire/
│           └── counter.phtml
├── js/openwire/src/
│   ├── bootstrapper.ts
│   ├── event-handler.ts
│   ├── dom-patcher.ts
│   ├── ajax-client.ts
│   ├── response-handler.ts
│   ├── types.ts
│   └── utils/
├── tests/
│   ├── Unit/
│   ├── Feature/
│   └── Browser/
├── composer.json
├── package.json
├── phpstan.neon
├── rector.php
└── vite.config.js
```

### Development Workflow

```bash
# Install dependencies
composer install
npm install

# Start development server
npm run dev

# Build for production
npm run build

# Run tests
composer test

# Code quality checks
composer lint    # PHP CS Fixer + PHPStan
composer fix     # Auto-fix code style
```

### Code Quality Tools

- **PHPStan**: Static analysis for type safety
- **PHP CS Fixer**: Code style enforcement
- **Rector**: Automated code refactoring
- **Pest**: Modern PHP testing framework
- **Vitest**: Frontend testing (planned)

## 📚 API Reference

### Component Methods

#### Lifecycle Methods
- `mount(array $props)` - Initialize component with props
- `hydrate(array $state)` - Load state from storage
- `dehydrate()` - Extract state for storage
- `renderPayload()` - Return response data

#### Data Methods
- `setData($key, $value)` - Set component data
- `getData($key = null)` - Get component data
- `hasData($key)` - Check if data exists

#### Configuration Methods
- `getId()` - Get unique component ID
- `getComponentAlias()` - Get Magento block alias
- `getOpenwireConfig()` - Get component configuration
- `isStateful()` - Check if component persists state

### Template Compiler

```php
$compiler = Mage::getModel('openwire/template_compiler');
$compiledHtml = $compiler->compile($declarativeHtml, $component);
```

### State Store Interface

```php
interface Maco_Openwire_Model_State_StoreInterface
{
    public function load($componentId);
    public function save($componentId, $state);
    public function forget($componentId);
}
```

## 💡 Examples

### Counter Component

[See: `app/code/local/Maco/Openwire/Block/Component/Counter.php`]

A complete example with increment/decrement functionality and state persistence.

### Form Handling

```php
class Contact_Form extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;

    protected $_openwireAllowedActions = ['submit'];

    public function mount($params = [])
    {
        parent::mount($params);
        $this->setData([
            'name' => '',
            'email' => '',
            'message' => '',
            'submitted' => false
        ]);
    }

    public function submit($formData)
    {
        // Validate and process form
        $this->setData('submitted', true);
        // Send email, save to database, etc.
    }

    protected function _toHtml()
    {
        $html = '<div openwire="contact-form">';

        if (!$this->getData('submitted')) {
            $html .= '
                <form @submit="submit">
                    <input @change="setData(\'name\', $event.target.value)" placeholder="Name" />
                    <input @change="setData(\'email\', $event.target.value)" placeholder="Email" />
                    <textarea @change="setData(\'message\', $event.target.value)" placeholder="Message"></textarea>
                    <button type="submit">Send</button>
                </form>
            ';
        } else {
            $html .= '<p>Thank you for your message!</p>';
        }

        $html .= '</div>';

        $compiler = Mage::getModel('openwire/template_compiler');
        return $compiler->compile($html, $this);
    }
}
```

### Shopping Cart

```php
class Shopping_Cart extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;
    use Maco_Openwire_Block_Component_Trait_Stateful;

    protected $_openwireAllowedActions = ['addItem', 'removeItem', 'updateQuantity'];

    public function mount($params = [])
    {
        parent::mount($params);
        if (!$this->getData('items')) {
            $this->setData('items', []);
        }
    }

    public function addItem($productId, $quantity = 1)
    {
        $items = $this->getData('items');
        $items[$productId] = ($items[$productId] ?? 0) + $quantity;
        $this->setData('items', $items);
    }

    public function getTotal()
    {
        $items = $this->getData('items');
        $total = 0;
        foreach ($items as $productId => $quantity) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $total += $product->getPrice() * $quantity;
        }
        return $total;
    }
}
```

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Setup

```bash
git clone https://github.com/maco-studios/openwire.git
cd openwire
composer install
npm install
npm run dev
```

### Code Standards

- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation
- Use meaningful commit messages

### Testing

```bash
# Run full test suite
composer test

# Run with coverage
composer test -- --coverage --min=80

# Run specific tests
composer test -- --filter=ComponentTest
```

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE.md) file for details.

## 🙏 Acknowledgments

- **Laravel Livewire** - Inspiration for the reactive component pattern
- **Magewire** - Magento 2 implementation that inspired this Magento 1 version
- **Magento Community** - For keeping the platform alive and relevant

## 📞 Support

- 📖 [Documentation](https://maco-studios.github.io/openwire/)
- 🐛 [Issue Tracker](https://github.com/maco-studios/openwire/issues)
- 💬 [Discussions](https://github.com/maco-studios/openwire/discussions)
- 📧 [Email Support](mailto:support@maco-studios.com)

---

<p align="center">
  <strong>Built with ❤️ for the Magento 1 community</strong>
</p>
