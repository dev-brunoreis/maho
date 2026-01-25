# Getting Started

Welcome! This guide will walk you through creating your first OpenWire component. We'll build a simple counter that demonstrates reactive updates without page refreshes.

## 🎯 What You'll Build

A counter component with:
- ✅ Increment/decrement buttons
- ✅ Live count display
- ✅ State persistence across requests
- ✅ Zero JavaScript required

## 📁 Project Setup

First, ensure you have:
1. [OpenWire installed](installation.md)
2. A custom Magento module (or create one)

For this tutorial, we'll assume you have a module called `YourModule`.

## 🧩 Step 1: Create the Component Class

Create the component class:

```php
<?php
// app/code/local/YourModule/Openwire/Block/Component/Counter.php

class YourModule_Openwire_Block_Component_Counter extends Maco_Openwire_Block_Component_Abstract
{
    // Enable reactive behavior
    use Maco_Openwire_Block_Component_Trait_Reactive;

    // Enable automatic state persistence
    use Maco_Openwire_Block_Component_Trait_Stateful;

    // Define allowed actions (security)
    protected $_openwireAllowedActions = ['increment', 'decrement'];

    /**
     * Initialize component with default state
     */
    public function mount($params = [])
    {
        parent::mount($params);
        $this->setData('count', $params['count'] ?? 0);
    }

    /**
     * Increment the counter
     */
    public function increment()
    {
        $currentCount = (int) $this->getData('count');
        $this->setData('count', $currentCount + 1);
    }

    /**
     * Decrement the counter
     */
    public function decrement()
    {
        $currentCount = (int) $this->getData('count');
        $this->setData('count', $currentCount - 1);
    }

    /**
     * Render the component HTML
     */
    protected function _toHtml()
    {
        // Declarative HTML with reactive syntax
        $declarativeHtml = '<div openwire="counter">
            <h3>My Counter</h3>
            <div class="counter-controls">
                <button @click="decrement">-</button>
                <span class="count">{{ count }}</span>
                <button @click="increment">+</button>
            </div>
            <p>Count: {{ count }}</p>
        </div>';

        // Compile to operational HTML
        $compiler = Mage::getModel('openwire/template_compiler');
        return $compiler->compile($declarativeHtml, $this);
    }
}
```

## 🎨 Step 2: Create the Template

Create the layout template:

```php
<?php
// app/code/local/YourModule/Openwire/Block/Component/Counter.php (add this method)

public function getTemplate()
{
    return 'yourmodule/counter.phtml';
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

## ⚙️ Step 3: Register the Component

Register your component in Magento's configuration:

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

## 📄 Step 4: Add to Layout

Add the component to a page layout:

```xml
<!-- app/design/frontend/base/default/layout/yourmodule.xml -->
<layout>
    <yourmodule_index_index>
        <reference name="content">
            <block type="yourmodule_component/counter" name="counter" template="yourmodule/counter.phtml">
                <action method="setCount"><count>5</count></action>
            </block>
        </reference>
    </yourmodule_index_index>
</layout>
```

## 🎉 Step 5: Test It!

1. **Clear Magento cache**
   ```bash
   rm -rf var/cache/*
   php shell/indexer.php reindexall
   ```

2. **Visit your page**
   - Go to your module's page (e.g., `/yourmodule/`)
   - You should see the counter with buttons

3. **Test the reactivity**
   - Click the `+` and `-` buttons
   - The count should update without page refreshes!
   - The count persists across page reloads

## 🔍 Understanding What Happened

### Component Lifecycle

1. **Page Load**: Magento creates your component block
2. **Template Render**: The template includes OpenWire attributes
3. **Bootstrapper**: JavaScript initializes the component
4. **User Interaction**: Click triggers AJAX request
5. **Backend Processing**: Your `increment()` method runs
6. **State Persistence**: Count is saved to session
7. **DOM Update**: Page updates without refresh

### Declarative HTML

Your template used special syntax:

```html
<!-- @click tells OpenWire to call the method when clicked -->
<button @click="increment">+</button>

<!-- {{ variable }} displays component data -->
<span>{{ count }}</span>

<!-- openwire="name" marks the component root -->
<div openwire="counter">...</div>
```

This gets compiled to:

```html
<button data-ow:click="increment">+</button>
<span>5</span>
<div data-ow-component="yourmodule_component/counter" ...>...</div>
```

## 🎨 Adding Styles

Make it look better with CSS:

```html
<!-- app/design/frontend/base/default/template/yourmodule/counter.phtml -->
<div data-ow-component="<?php echo $this->getComponentAlias(); ?>"
     data-ow-id="<?php echo $this->getId(); ?>"
     data-ow-config='<?php echo json_encode($this->getOpenwireConfig()); ?>'
     x-data="{}"
     class="openwire-counter">

    <div data-ow-body>
        <?php echo $this->_toHtml(); ?>
    </div>
</div>

<style>
.openwire-counter {
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    max-width: 300px;
    margin: 20px auto;
}

.counter-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin: 20px 0;
}

.counter-controls button {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: #007cba;
    color: white;
    font-size: 18px;
    cursor: pointer;
}

.counter-controls button:hover {
    background: #005a87;
}

.count {
    font-size: 24px;
    font-weight: bold;
    min-width: 50px;
}
</style>
```

## 🚀 Next Steps

Now that you have a working component, explore:

### 🔧 Component Features

- **[State Management](state-management.md)**: Learn about automatic persistence
- **[Security](security.md)**: Add authorization and validation
- **[Events](components/events.md)**: Handle forms, inputs, and more

### 📚 Advanced Topics

- **[Component Types](components/)**: Basic, stateful, and authorized components
- **[Template Engine](templates/)**: Advanced declarative syntax
- **[API Reference](../api/)**: Complete method reference

### 💡 More Examples

- **[Form Handling](../examples/form.md)**: Contact forms with validation
- **[Shopping Cart](../examples/cart.md)**: Complex stateful components
- **[Real-time Search](../examples/search.md)**: Debounced input handling

## 🆘 Having Issues?

Common problems and solutions:

### Component Not Loading
- Check PHP error logs for class loading issues
- Verify block type in layout XML
- Ensure module is enabled

### Buttons Not Working
- Check browser console for JavaScript errors
- Verify actions are in `$_openwireAllowedActions`
- Ensure AJAX endpoint is accessible

### State Not Persisting
- Confirm component uses `Stateful` trait
- Check session storage permissions
- Verify component ID consistency

### Template Not Found
- Check file paths and permissions
- Verify template method returns correct path
- Clear Magento cache

---

<p align="center">
  <strong>🎉 Congratulations on your first OpenWire component!</strong><br>
  <a href="components/">📚 Learn More About Components</a> •
  <a href="../examples/">💡 View More Examples</a> •
  <a href="templates/">🎨 Master Templates</a>
</p>