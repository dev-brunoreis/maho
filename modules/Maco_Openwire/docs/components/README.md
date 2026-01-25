# Components Guide

Components are the heart of OpenWire. Learn how to create different types of components and understand their lifecycle.

## 🧩 Component Types

### Basic Components

The simplest form - reactive but stateless:

```php
class Example_Basic extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;

    protected $_openwireAllowedActions = ['greet'];

    public function mount($params = [])
    {
        parent::mount($params);
        $this->setData('name', $params['name'] ?? 'World');
    }

    public function greet()
    {
        // This action can be called from frontend
        return "Hello, " . $this->getData('name') . "!";
    }
}
```

### Stateful Components

Components that automatically persist their state:

```php
class Example_Cart extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;
    use Maco_Openwire_Block_Component_Trait_Stateful;

    protected $_openwireAllowedActions = ['addItem'];

    public function mount($params = [])
    {
        parent::mount($params);
        // Initialize state if empty
        if (!$this->getData('items')) {
            $this->setData('items', []);
        }
    }

    public function addItem($productId)
    {
        $items = $this->getData('items');
        $items[] = $productId;
        $this->setData('items', $items);
        // State automatically saved!
    }
}
```

### Authorized Components

Components with access control:

```php
class Example_Admin extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;
    use Maco_Openwire_Block_Component_Trait_Authorizes;

    protected $_openwireAllowedActions = ['adminAction'];

    public function adminAction()
    {
        // Check permissions before executing
        $this->authorize('admin');

        // Only admins can reach here
        $this->setData('message', 'Admin action completed!');
    }
}
```

## 🏗️ Tier 2: Full Component Evolution

For strategic blocks you control, evolve to full OpenWire components by applying traits. This provides complete lifecycle, state management, and advanced features without rewriting logic.

### Applying Traits to Legacy Blocks

1. **Extend Abstract**: Make your block extend `Maco_Openwire_Block_Component_Abstract`
2. **Add Traits**: Include relevant traits for desired capabilities
3. **Define Actions**: Set `$_openwireAllowedActions` for security
4. **Implement Lifecycle**: Override `mount()`, `hydrate()`, etc. as needed

### Trait Guidelines

- **Reactive**: Required for all components - enables frontend communication
- **Stateful**: Add for automatic session persistence
- **Polling**: Add for periodic updates (e.g., live data)
- **Authorizes**: Add for admin/permission checks

### Example: Adapting a Legacy Template

For a standard Magento block template, add `openwire` attribute and directives:

**Before:**
```php
<?php $product = Mage::registry('current_product'); ?>
<div class="product-view">
    <h1><?php echo $product->getName(); ?></h1>
    <p><?php echo $product->getDescription(); ?></p>
</div>
```

**After (Tier 0 - Reactive Shell):**
```php
<?php $product = Mage::registry('current_product'); ?>
<div openwire="catalog/product_view" class="product-view">
    <h1><?php echo $product->getName(); ?></h1>
    <p><?php echo $product->getDescription(); ?></p>
    <button @click="refresh">Refresh View</button>
</div>
```

**After (Tier 1 - State Assist):**
```php
<?php $product = Mage::registry('current_product'); ?>
<div openwire="catalog/product_view" class="product-view">
    <h1><?php echo $product->getName(); ?></h1>
    <p><?php echo $product->getDescription(); ?></p>
    <input @input="updateQuantity" type="number" value="1" />
    <button @click="addToCart">Add to Cart</button>
</div>
```

The `openwire` attribute enables compilation, and `@click`/`@input` directives make it reactive without changing the block class.

### Backward Compatibility

- Existing block methods remain unchanged
- Template rendering can stay the same
- Add OpenWire features incrementally

## 🔄 Component Lifecycle

Understanding when methods are called:

```
1. Instantiation ──► 2. mount() ──► 3. hydrate() ──► 4. Action ──► 5. Render ──► 6. Persist
       │                     │                      │                      │                     │
       └─ Magento creates     └─ Initialize data    └─ Load saved state   └─ User triggered    └─ Return HTML
          component block                              (stateful only)                              (stateful only)
```

### 1. Instantiation
```php
// Magento creates your component
$component = Mage::app()->getLayout()->createBlock('yourmodule_component/example');
```

### 2. Mount
```php
public function mount($params = [])
{
    parent::mount($params);
    // Initialize component data
    $this->setData('initialized', true);
}
```

### 3. Hydration (Stateful Only)
```php
// Automatic - loads from session
// Component state is restored
```

### 4. Action Execution
```php
public function doSomething($param1, $param2)
{
    // Validate action is allowed (automatic)
    // Execute your logic
    $this->setData('result', $param1 + $param2);
}
```

### 5. Rendering
```php
protected function _toHtml()
{
    // Return compiled HTML
    return $compiler->compile($template, $this);
}
```

### 6. State Persistence (Stateful Only)
```php
// Automatic - saves to session
```

## 📊 Data Management

### Setting and Getting Data

```php
// Set single value
$this->setData('name', 'John');

// Set multiple values
$this->setData([
    'email' => 'john@example.com',
    'age' => 30
]);

// Get single value
$name = $this->getData('name');

// Get all data
$allData = $this->getData();

// Check if data exists
if ($this->hasData('email')) {
    // Data exists
}
```

### Data Types

OpenWire supports all PHP data types:

```php
public function mount($params = [])
{
    $this->setData([
        'string' => 'Hello World',
        'number' => 42,
        'boolean' => true,
        'array' => ['a', 'b', 'c'],
        'object' => (object) ['key' => 'value']
    ]);
}
```

## 🎯 Actions and Methods

### Defining Allowed Actions

```php
class Secure_Component extends Maco_Openwire_Block_Component_Abstract
{
    // Only these methods can be called from frontend
    protected $_openwireAllowedActions = [
        'safeMethod1',
        'safeMethod2'
    ];

    public function safeMethod1()
    {
        // ✅ Can be called
    }

    public function dangerousMethod()
    {
        // ❌ Will throw exception if called
    }
}
```

### Action Parameters

Actions automatically receive parameters from the frontend:

```php
public function updateProfile($userId, $data)
{
    // $userId and $data passed from frontend
    $user = Mage::getModel('customer/customer')->load($userId);

    foreach ($data as $key => $value) {
        $user->setData($key, $value);
    }

    $user->save();
    $this->setData('updated', true);
}
```

### Return Values

Actions can return data to the frontend:

```php
public function calculateTotal($items)
{
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    $this->setData('total', $total);
    return ['total' => $total, 'tax' => $total * 0.1];
}
```

## 🔧 Advanced Features

### Custom Hydration/Dehydration

```php
class Complex_Component extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Stateful;

    public function hydrate(array $state)
    {
        // Custom logic before setting state
        $processedState = $this->validateState($state);
        parent::hydrate($processedState);
    }

    public function dehydrate()
    {
        $state = parent::dehydrate();
        // Custom logic before saving
        return $this->cleanState($state);
    }
}
```

### Component Dependencies

```php
class Dependent_Component extends Maco_Openwire_Block_Component_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        // Initialize dependencies
        $this->_productModel = Mage::getModel('catalog/product');
    }

    public function loadProduct($productId)
    {
        $product = $this->_productModel->load($productId);
        $this->setData('product', $product->getData());
    }
}
```

### Error Handling

```php
public function processOrder($orderData)
{
    try {
        // Process order logic
        $this->validateOrder($orderData);
        $order = $this->createOrder($orderData);

        $this->setData('orderId', $order->getId());
        $this->setData('status', 'success');

    } catch (Exception $e) {
        $this->setData('error', $e->getMessage());
        $this->setData('status', 'error');

        // Log error for debugging
        Mage::logException($e);
    }
}
```

## 🎨 Component Registration

### Block Configuration

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

### Layout Integration

```xml
<!-- app/design/frontend/base/default/layout/yourmodule.xml -->
<layout>
    <yourmodule_page>
        <reference name="content">
            <!-- Basic component -->
            <block type="yourmodule_component/basic" name="basic_component" />

            <!-- Component with parameters -->
            <block type="yourmodule_component/advanced" name="advanced_component">
                <action method="setConfig">
                    <param1>value1</param1>
                    <param2>value2</param2>
                </action>
            </block>
        </reference>
    </yourmodule_page>
</layout>
```

## 🧪 Testing Components

### Unit Tests

```php
<?php
// tests/Unit/Component/CounterTest.php

it('increments counter', function () {
    $counter = new YourModule_Counter();
    $counter->mount(['count' => 5]);
    $counter->increment();

    expect($counter->getData('count'))->toBe(6);
});

it('is stateful', function () {
    $counter = new YourModule_Counter();
    expect($counter->isStateful())->toBeTrue();
});
```

### Integration Tests

```php
it('persists state across requests', function () {
    // Simulate AJAX request flow
    $component = new Stateful_Component();

    // First request
    $component->mount();
    $component->addItem('product1');
    $component->persistState();

    // Second request (new instance)
    $component2 = new Stateful_Component();
    $component2->mount();
    $component2->loadState();

    expect($component2->getData('items'))->toContain('product1');
});
```

## 📚 Next Steps

- **[Template Engine](../templates/)**: Learn declarative HTML syntax
- **[State Management](../state-management.md)**: Deep dive into persistence
- **[Security](../security.md)**: Authorization and validation
- **[Examples](../examples/)**: Real-world component patterns

---

<p align="center">
  <strong>Ready to build more complex components?</strong><br>
  <a href="../templates/">🎨 Learn Templates</a> •
  <a href="../examples/">💡 View Examples</a> •
  <a href="../api/">📚 API Reference</a>
</p>