# Template Engine

OpenWire's template engine lets you write declarative HTML that gets compiled into reactive components. No JavaScript required!

## 🎨 Basic Syntax

### Event Directives

```html
<!-- Declarative (what you write) -->
<button @click="increment">+</button>
<input @change="updateName" />
<form @submit="saveForm">

<!-- Compiled (what gets rendered) -->
<button data-ow:click="increment">+</button>
<input data-ow:change="updateName" />
<form data-ow:submit="saveForm">
```

### Variable Binding

```html
<!-- Declarative -->
<h1>Hello {{ name }}!</h1>
<p>Count: {{ count }}</p>
<div class="{{ isActive ? 'active' : 'inactive' }}">

<!-- Compiled -->
<h1>Hello John!</h1>
<p>Count: 5</p>
<div class="active">
```

### Component Root

```html
<!-- Declarative -->
<div openwire="counter">
    <button @click="increment">{{ count }}</button>
</div>

<!-- Compiled -->
<div data-ow-component="yourmodule_component/counter"
     data-ow-id="ow_1234567890"
     data-ow-config='{"component":"yourmodule_component/counter",...}'
     x-data="{}">
    <button data-ow:click="increment">5</button>
</div>
```

## 🎯 Supported Events

### Click Events

```html
<button @click="increment">+1</button>
<a @click="navigate" href="#">Link</a>
```

### Form Events

```html
<input @change="updateValue" />
<textarea @input="updateText"></textarea>
<select @change="updateSelection">
    <option value="1">Option 1</option>
</select>
<form @submit="submitForm">
    <!-- form fields -->
</form>
```

### Focus Events

```html
<input @focus="showHelp" @blur="hideHelp" />
```

## 🔧 Advanced Features

### Conditional Rendering

```php
protected function _toHtml()
{
    $isLoggedIn = $this->getData('isLoggedIn');
    $userName = $this->getData('userName');

    $html = '<div openwire="user-panel">';

    if ($isLoggedIn) {
        $html .= '<p>Welcome back, {{ userName }}!</p>';
        $html .= '<button @click="logout">Logout</button>';
    } else {
        $html .= '<button @click="login">Login</button>';
    }

    $html .= '</div>';

    $compiler = Mage::getModel('openwire/template_compiler');
    return $compiler->compile($html, $this);
}
```

### Dynamic Attributes

```php
protected function _toHtml()
{
    $isDisabled = $this->getData('isDisabled');
    $maxLength = $this->getData('maxLength');
    $cssClass = $this->getData('hasError') ? 'error' : 'valid';

    $disabled = $isDisabled ? ' disabled="disabled"' : '';
    $maxlength = $maxLength ? ' maxlength="' . $maxLength . '"' : '';

    $html = '<div openwire="form-field">
        <input type="text"
               class="' . $cssClass . '"
               @input="validateInput"' .
               $disabled .
               $maxlength . ' />
    </div>';

    $compiler = Mage::getModel('openwire/template_compiler');
    return $compiler->compile($html, $this);
}
```

### Loops and Complex Logic

```php
protected function _toHtml()
{
    $items = $this->getData('items');
    $html = '<div openwire="item-list">';

    if (!empty($items)) {
        $html .= '<ul>';
        foreach ($items as $index => $item) {
            $html .= '<li>';
            $html .= '<span>{{ items.' . $index . '.name }}</span>';
            $html .= '<button @click="removeItem" data-index="' . $index . '">Remove</button>';
            $html .= '</li>';
        }
        $html .= '</ul>';
    } else {
        $html .= '<p>No items yet.</p>';
    }

    $html .= '<button @click="addItem">Add Item</button>';
    $html .= '</div>';

    $compiler = Mage::getModel('openwire/template_compiler');
    return $compiler->compile($html, $this);
}
```

## 🎭 Template Patterns

### Form Components

```php
protected function _toHtml()
{
    $errors = $this->getData('errors');
    $nameError = $errors['name'] ?? '';

    $html = '<div openwire="contact-form">
        <form @submit="submit">
            <div class="field">
                <label>Name:</label>
                <input type="text"
                       @input="updateName"
                       value="{{ name }}" />
                ' . (!empty($nameError) ? '<span class="error">{{ errors.name }}</span>' : '') . '
            </div>

            <div class="field">
                <label>Message:</label>
                <textarea @input="updateMessage">{{ message }}</textarea>
            </div>

            <button type="submit" ' . ($this->getData('isSubmitting') ? 'disabled' : '') . '>
                {{ isSubmitting ? "Sending..." : "Send Message" }}
            </button>
        </form>
    </div>';

    $compiler = Mage::getModel('openwire/template_compiler');
    return $compiler->compile($html, $this);
}
```

### Loading States

```php
protected function _toHtml()
{
    $isLoading = $this->getData('isLoading');
    $results = $this->getData('results');

    $html = '<div openwire="search">
        <input type="text"
               @input="search"
               placeholder="Search..."
               value="{{ query }}" />

        ' . ($isLoading ? '<div class="loading">Searching...</div>' : '') . '

        <div class="results">
            {{ results.length > 0 ? "Found " + results.length + " results" : "No results" }}
        </div>
    </div>';

    $compiler = Mage::getModel('openwire/template_compiler');
    return $compiler->compile($html, $this);
}
```

### Modal/Dialog Components

```php
protected function _toHtml()
{
    $isOpen = $this->getData('isOpen');

    $html = '<div openwire="modal">';

    if ($isOpen) {
        $html .= '<div class="modal-overlay" @click="close">
            <div class="modal-content" @click.stop>
                <h3>{{ title }}</h3>
                <p>{{ message }}</p>
                <div class="modal-actions">
                    <button @click="confirm">OK</button>
                    <button @click="close">Cancel</button>
                </div>
            </div>
        </div>';
    }

    $html .= '<button @click="open">Open Modal</button>';
    $html .= '</div>';

    $compiler = Mage::getModel('openwire/template_compiler');
    return $compiler->compile($html, $this);
}
```

## 🔧 Template Compilation

### How It Works

1. **Parse**: Find `@event` and `{{ variable }}` patterns
2. **Transform**: Convert to `data-ow:event` attributes
3. **Interpolate**: Replace `{{ variable }}` with actual values
4. **Root Processing**: Add component metadata to root element

### Compilation Process

```php
// Your component method
protected function _toHtml()
{
    $template = '<div openwire="my-component">
        <button @click="doAction">{{ buttonText }}</button>
    </div>';

    // Compile the template
    $compiler = Mage::getModel('openwire/template_compiler');
    return $compiler->compile($template, $this);
}
```

### Custom Compilation

```php
class Custom_Compiler extends Maco_Openwire_Model_Template_Compiler
{
    protected function compileCustomDirective($html)
    {
        // Add custom @directives
        return preg_replace('/@custom="([^"]+)"/', 'data-custom="$1"', $html);
    }
}
```

## 🎨 Styling Components

### CSS Integration

```html
<div openwire="styled-component" class="my-component">
    <div data-ow-body>
        <!-- Your component HTML -->
    </div>
</div>

<style>
.my-component {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
}

.my-component button {
    background: #007cba;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
}

.my-component button:hover {
    background: #005a87;
}
</style>
```

### Dynamic Classes

```php
protected function _toHtml()
{
    $status = $this->getData('status');
    $cssClass = match($status) {
        'success' => 'alert-success',
        'error' => 'alert-error',
        default => 'alert-info'
    };

    $html = '<div openwire="alert" class="alert ' . $cssClass . '">
        <p>{{ message }}</p>
        <button @click="dismiss">×</button>
    </div>';

    $compiler = Mage::getModel('openwire/template_compiler');
    return $compiler->compile($html, $this);
}
```

## 🐛 Troubleshooting

### Common Issues

#### Variables Not Displaying
```php
// ❌ Wrong - variable not accessible
$this->setData('count', 5);
// Template: {{ count }} → displays nothing

// ✅ Correct - use getData()
$this->setData('count', 5);
// Template: {{ count }} → displays "5"
```

#### Events Not Firing
```php
// ❌ Wrong - method not in allowed actions
protected $_openwireAllowedActions = ['otherMethod'];

// ✅ Correct - include all callable methods
protected $_openwireAllowedActions = ['increment', 'otherMethod'];
```

#### Template Not Compiling
```php
// ❌ Wrong - missing compiler call
return '<div openwire="test">{{ value }}</div>';

// ✅ Correct - compile the template
$compiler = Mage::getModel('openwire/template_compiler');
return $compiler->compile('<div openwire="test">{{ value }}</div>', $this);
```

## 📚 Next Steps

- **[Component Guide](../components/)**: Learn about component lifecycle
- **[State Management](../state-management.md)**: Automatic data persistence
- **[Examples](../examples/)**: Real template patterns
- **[API Reference](../api/)**: Template compiler methods

---

<p align="center">
  <strong>🎨 Master the template engine!</strong><br>
  <a href="../components/">🧩 Learn Components</a> •
  <a href="../examples/">💡 View Examples</a> •
  <a href="../api/compiler.md">📚 Compiler API</a>
</p>