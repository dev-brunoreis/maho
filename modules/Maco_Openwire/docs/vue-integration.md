# Vue + OpenWire Integration Guide

This guide explains how to build a complete Magento 1 theme using Vue.js components powered by OpenWire's reactive backend. The **Maco_Vuewire** theme demonstrates this integration pattern.

## 🎯 Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    Magento Template (PHP)                    │
│  <div openwire="component" data-ui="vue:ComponentName">      │
│       data-props='{"product": {...}}'>                       │
│  </div>                                                       │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        │ HTML rendered with attributes
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              Vue Bootstrapper (JavaScript)                    │
│  • Scans for [data-ui^="vue:"] elements                      │
│  • Lazy loads Vue components from registry                   │
│  • Mounts components with props and OpenWire context         │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        │ Component interactions
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              OpenWire Bridge (JavaScript)                     │
│  • Provides useOpenwire() composable                         │
│  • Handles AJAX calls to OpenWire backend                   │
│  • Manages CSRF tokens and request formatting                │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        │ AJAX requests
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              OpenWire Backend (PHP)                          │
│  • Component classes handle business logic                   │
│  • State management and persistence                         │
│  • Returns updated HTML/state                                │
└─────────────────────────────────────────────────────────────┘
```

## 📁 Project Structure

```
Maco_Vuewire/
├── app/design/frontend/maco/vuewire/
│   ├── template/              # PHP templates
│   │   └── catalog/product/
│   │       ├── view.phtml     # Product view template
│   │       └── list.phtml     # Product list template
│   └── layout/
│       ├── local.xml          # Theme layout config
│       └── openwire.xml       # OpenWire JS inclusion
│
└── skin/frontend/maco/vuewire/
    └── js/
        ├── main.js            # Vue bootstrapper
        ├── bridge/
        │   ├── registry.js    # Component registry
        │   ├── mount.js       # useOpenwire composable
        │   └── openwire-client.js  # AJAX client
        └── components/
            ├── CatalogProductView.vue
            └── CatalogProductList.vue
```

## 🚀 Step-by-Step Implementation

### Step 1: Create PHP Template

Your Magento template renders a container with OpenWire and Vue attributes:

```php
<?php
// app/design/frontend/maco/vuewire/template/catalog/product/view.phtml
$product = Mage::registry('current_product')->getData();
?>
<div openwire="catalog/product_view"
     data-ui="vue:CatalogProductView"
     data-openwire="catalog/product_view"
     data-props='<?= json_encode(['product' => $product]) ?>'>
</div>
```

**Key Attributes:**
- `openwire="catalog/product_view"` - OpenWire component identifier
- `data-ui="vue:CatalogProductView"` - Vue component to mount
- `data-openwire="catalog/product_view"` - OpenWire component name for bridge
- `data-props` - JSON-encoded data passed to Vue component

### Step 2: Register Vue Component

Add your component to the registry for lazy loading:

```javascript
// skin/frontend/maco/vuewire/js/bridge/registry.js
export default {
  CatalogProductView: () => import('../components/CatalogProductView.vue'),
  CatalogProductList: () => import('../components/CatalogProductList.vue')
}
```

### Step 3: Create Vue Component

Build your Vue component using the OpenWire bridge:

```vue
<template>
  <div class="product-view">
    <h1>{{ product.name }}</h1>
    <p>{{ product.price }}</p>
    <button @click="addToCart">Add to Cart</button>
  </div>
</template>

<script setup>
import { ref, inject } from 'vue'
import { useOpenwire } from '../bridge/mount.js'

// Get props and OpenWire bridge
const { call, props } = useOpenwire()
const product = ref(props.product || {})

// Call OpenWire action
const addToCart = async () => {
  const result = await call('addToCart', {
    product_id: product.value.entity_id
  })

  if (result.html) {
    // Handle response - update UI, show message, etc.
    console.log('Added to cart!')
  }
}
</script>
```

### Step 4: Create OpenWire Backend Component

Implement the PHP component that handles the logic:

```php
<?php
// app/code/local/YourModule/Openwire/Block/Component/Catalog/Product/View.php

class YourModule_Openwire_Block_Component_Catalog_Product_View
    extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;
    use Maco_Openwire_Block_Component_Trait_Stateful;

    protected $_openwireAllowedActions = ['addToCart', 'updateQuantity'];

    public function mount($params = [])
    {
        parent::mount($params);
        $productId = $params['product_id'] ?? null;

        if ($productId) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $this->setData('product', $product->getData());
        }
    }

    public function addToCart($productId)
    {
        $cart = Mage::getSingleton('checkout/cart');
        $product = Mage::getModel('catalog/product')->load($productId);

        try {
            $cart->addProduct($product, ['qty' => 1]);
            $cart->save();

            $this->setData('cartMessage', 'Product added to cart!');
            $this->setData('cartCount', $cart->getItemsCount());
        } catch (Exception $e) {
            $this->setData('error', $e->getMessage());
        }
    }

    protected function _toHtml()
    {
        // Return minimal HTML - Vue handles the UI
        return '<div data-openwire-mount></div>';
    }
}
```

## 🔧 The Bridge Layer

### useOpenwire Composable

The bridge provides a composable that connects Vue to OpenWire:

```javascript
// skin/frontend/maco/vuewire/js/bridge/mount.js
import { inject } from 'vue'
import client from './openwire-client.js'

export function useOpenwire() {
  const props = inject('props', {})
  const openwire = inject('openwire', '')

  const call = (action, payload, state) => {
    return client.call(openwire, action, payload, state)
  }

  return { call, props }
}
```

### OpenWire Client

Handles AJAX communication with the backend:

```javascript
// skin/frontend/maco/vuewire/js/bridge/openwire-client.js
export class OpenwireClient {
  getFormKey() {
    return window.FORM_KEY ||
           document.querySelector('input[name="form_key"]')?.value;
  }

  async call(component, action, payload, state) {
    const updatePayload = {
      id: 'vuewire_' + Date.now(),
      component: component,
      calls: [{
        method: action,
        params: [payload]
      }],
      updates: state || {}
    };

    const formKey = this.getFormKey();
    if (formKey) {
      updatePayload.form_key = formKey;
    }

    const response = await fetch('/openwire/update/index', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(updatePayload)
    });

    return response.json();
  }
}
```

### Bootstrapper

The main entry point that mounts Vue components:

```javascript
// skin/frontend/maco/vuewire/js/main.js
import { createApp } from 'vue'
import registry from './bridge/registry.js'

document.addEventListener('DOMContentLoaded', () => {
  const elements = document.querySelectorAll('[data-ui^="vue:"]')

  elements.forEach(el => {
    const ui = el.getAttribute('data-ui')
    const componentName = ui.split(':')[1]
    const componentPromise = registry[componentName]

    if (componentPromise) {
      componentPromise().then(component => {
        const app = createApp(component.default)
        const props = JSON.parse(el.getAttribute('data-props') || '{}')
        const openwire = el.getAttribute('data-openwire')

        // Provide context to all child components
        app.provide('props', props)
        app.provide('openwire', openwire)

        app.mount(el)
      })
    }
  })
})
```

## 🎨 Complete Example: Product View

### PHP Template

```php
<?php
$product = Mage::registry('current_product')->getData();
?>
<div openwire="catalog/product_view"
     data-ui="vue:CatalogProductView"
     data-openwire="catalog/product_view"
     data-props='<?= json_encode(['product' => $product]) ?>'>
</div>
```

### Vue Component

```vue
<template>
  <div class="bg-white">
    <div class="product-header">
      <h1>{{ product.name }}</h1>
      <p class="price">{{ formatPrice(product.price) }}</p>
    </div>

    <div class="product-images">
      <img v-for="image in product.images"
           :key="image.id"
           :src="image.url"
           :alt="product.name">
    </div>

    <form @submit.prevent="addToCart">
      <button type="submit" :disabled="isLoading">
        {{ isLoading ? 'Adding...' : 'Add to Cart' }}
      </button>
    </form>

    <div v-if="message" class="message">{{ message }}</div>
  </div>
</template>

<script setup>
import { ref, inject } from 'vue'
import { useOpenwire } from '../bridge/mount.js'

const { call, props } = useOpenwire()
const product = ref(props.product || {})
const isLoading = ref(false)
const message = ref('')

const formatPrice = (price) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(price)
}

const addToCart = async () => {
  isLoading.value = true
  message.value = ''

  try {
    const result = await call('addToCart', {
      product_id: product.value.entity_id,
      qty: 1
    })

    if (result.html) {
      message.value = 'Product added to cart!'
      // Optionally update product state from response
      if (result.state) {
        Object.assign(product.value, result.state.product)
      }
    }
  } catch (error) {
    message.value = 'Error adding to cart'
    console.error(error)
  } finally {
    isLoading.value = false
  }
}
</script>
```

### OpenWire Component

```php
<?php
class Maco_Openwire_Block_Component_Catalog_Product_View
    extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;
    use Maco_Openwire_Block_Component_Trait_Stateful;

    protected $_openwireAllowedActions = ['addToCart'];

    public function mount($params = [])
    {
        parent::mount($params);
        $productId = Mage::registry('current_product')->getId();

        if ($productId) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $this->setData('product', $product->getData());
        }
    }

    public function addToCart($productId, $qty = 1)
    {
        $cart = Mage::getSingleton('checkout/cart');
        $product = Mage::getModel('catalog/product')->load($productId);

        try {
            $cart->addProduct($product, ['qty' => $qty]);
            $cart->save();

            $this->setData('cartMessage', 'Added to cart!');
            $this->setData('cartCount', $cart->getItemsCount());
        } catch (Exception $e) {
            $this->setData('error', $e->getMessage());
        }
    }

    protected function _toHtml()
    {
        // Minimal HTML - Vue renders the UI
        return '<div data-openwire-mount></div>';
    }
}
```

## 🔄 Data Flow

1. **Initial Load:**
   - PHP template renders container with `data-ui` and `data-props`
   - Vue bootstrapper finds element and lazy loads component
   - Component mounts with initial props from PHP

2. **User Interaction:**
   - User clicks button in Vue component
   - Component calls `call('addToCart', {...})`
   - Bridge sends AJAX request to OpenWire backend

3. **Backend Processing:**
   - OpenWire component receives request
   - Executes `addToCart()` method
   - Updates component state
   - Returns response with updated state/HTML

4. **UI Update:**
   - Vue component receives response
   - Updates reactive state
   - UI re-renders automatically

## 🎯 Benefits of This Pattern

### Separation of Concerns
- **PHP**: Business logic, data fetching, Magento integration
- **Vue**: UI rendering, user interactions, client-side reactivity
- **OpenWire**: State management, AJAX communication, security

### Developer Experience
- ✅ Modern Vue 3 with Composition API
- ✅ TypeScript support (optional)
- ✅ Hot module replacement in development
- ✅ Component lazy loading
- ✅ Tailwind CSS integration

### Performance
- ✅ Lazy-loaded components
- ✅ Minimal initial bundle size
- ✅ Server-side rendering of initial data
- ✅ Efficient state updates

## 🛠️ Development Setup

### 1. Install Dependencies

```bash
cd modules/Maco_Vuewire
npm install
```

### 2. Development Mode

```bash
npm run dev
```

Vite will watch for changes and hot-reload components.

### 3. Production Build

```bash
npm run build
```

Compiles Vue components and assets to `skin/frontend/maco/vuewire/js/dist/`.

### 4. Include Assets in Layout

```xml
<!-- app/design/frontend/maco/vuewire/layout/local.xml -->
<layout version="0.1.0">
  <default>
    <reference name="head">
      <action method="addItem">
        <type>skin_css</type>
        <name>js/dist/app.css</name>
      </action>
      <action method="addItem">
        <type>skin_js</type>
        <name>js/dist/app.js</name>
      </action>
    </reference>
  </default>
</layout>
```

## 📚 Best Practices

### 1. Keep Templates Minimal

```php
<?php
// ✅ Good - minimal template
$product = Mage::registry('current_product')->getData();
?>
<div openwire="catalog/product_view"
     data-ui="vue:CatalogProductView"
     data-props='<?= json_encode(['product' => $product]) ?>'>
</div>
```

### 2. Use OpenWire for Business Logic

```php
// ✅ Good - business logic in OpenWire component
public function addToCart($productId)
{
    $cart = Mage::getSingleton('checkout/cart');
    // ... cart logic
}
```

### 3. Keep Vue Components Focused on UI

```vue
<!-- ✅ Good - UI-focused component -->
<template>
  <button @click="addToCart" :disabled="isLoading">
    Add to Cart
  </button>
</template>
```

### 4. Handle Loading States

```vue
<script setup>
const isLoading = ref(false)

const addToCart = async () => {
  isLoading.value = true
  try {
    await call('addToCart', {...})
  } finally {
    isLoading.value = false
  }
}
</script>
```

### 5. Error Handling

```vue
<script setup>
const error = ref(null)

const addToCart = async () => {
  error.value = null
  try {
    await call('addToCart', {...})
  } catch (e) {
    error.value = 'Failed to add to cart'
  }
}
</script>
```

## 🐛 Troubleshooting

### Component Not Mounting

- Check browser console for errors
- Verify `data-ui` attribute format: `vue:ComponentName`
- Ensure component is registered in `registry.js`
- Check that `app.js` is loaded in layout

### OpenWire Calls Failing

- Verify OpenWire endpoint: `/openwire/update/index`
- Check CSRF token is included
- Ensure action is in `$_openwireAllowedActions`
- Check PHP error logs

### Props Not Available

- Verify `data-props` is valid JSON
- Check component uses `inject('props')`
- Ensure props are provided in bootstrapper

## 📖 Next Steps

- **[Components Guide](./components/)** - Learn OpenWire component patterns
- **[Templates Guide](./templates/)** - OpenWire template syntax
- **[Examples](./examples/)** - More integration examples
- **[API Reference](./api/)** - Complete API documentation

---

<p align="center">
  <strong>🎨 Build modern Magento themes with Vue + OpenWire!</strong><br>
  <a href="./components/">🧩 Learn Components</a> •
  <a href="./examples/">💡 View Examples</a> •
  <a href="./api/">📚 API Reference</a>
</p>
