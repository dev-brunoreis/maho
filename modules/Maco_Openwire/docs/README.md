# OpenWire Documentation

Welcome to OpenWire, a revolutionary reactive component framework for Magento 1!

## 📖 What is OpenWire?

OpenWire brings modern web development patterns to Magento 1 by enabling **reactive components** - dynamic, interactive UI elements that update automatically without page refreshes. Inspired by Laravel Livewire and Magento 2's Magewire, OpenWire lets you build sophisticated user interfaces using **PHP only**.

### ✨ Key Features

- 🚀 **Zero JavaScript Required** - Build interactive UIs with PHP
- 🎨 **Vue.js Integration** - Use Vue components with OpenWire backend (see [Vue Integration Guide](vue-integration.md))
- 🔄 **Reactive Components** - Automatic AJAX updates
- 🎨 **Declarative Templates** - Clean HTML with `@click`, `{{ variables }}`
- 💾 **Stateful Components** - Automatic state persistence
- 🔒 **Security First** - CSRF protection and action validation
- 🧪 **Thoroughly Tested** - 100% test coverage

### 🎯 What Problem Does It Solve?

Traditional Magento 1 development requires:
- Complex JavaScript for dynamic interactions
- Manual AJAX endpoint creation
- State management across requests
- Security considerations for each endpoint

OpenWire simplifies this to:
```php
class Counter extends Maco_Openwire_Block_Component_Abstract
{
    public function increment()
    {
        $this->setData('count', $this->getData('count') + 1);
    }
}
```

## 🚀 Quick Start (5 minutes)

1. **Install OpenWire** → [Installation Guide](installation.md)
2. **Create Your First Component** → [Getting Started](getting-started.md)
3. **Build Something Amazing!** → [Examples](examples/)

## 📚 Documentation Guide

| Section | Description | Time to Read |
|---------|-------------|--------------|
| **[Installation](installation.md)** | Setup OpenWire in your Magento 1 store | 5 min |
| **[Getting Started](getting-started.md)** | Your first reactive component | 10 min |
| **[Components](components/)** | Component types and lifecycle | 15 min |
| **[Templates](templates/)** | Declarative HTML syntax | 10 min |
| **[Vue Integration](vue-integration.md)** | Build themes with Vue.js + OpenWire | 20 min |
| **[State Management](state-management.md)** | Automatic state persistence | 8 min |
| **[Security](security.md)** | CSRF, validation, authorization | 10 min |
| **[API Reference](api/)** | Complete API documentation | Reference |
| **[Examples](examples/)** | Real-world implementations | Reference |

## 🏗️ Architecture at a Glance

```
┌─────────────────┐    AJAX    ┌─────────────────┐
│   Frontend      │◄──────────►│   Backend       │
│   (TypeScript)  │            │   (PHP)         │
│                 │            │                 │
│ • Event Handler │            │ • Component     │
│ • DOM Patcher   │            │ • Template      │
│ • Bootstrapper  │            │ • State Store   │
└─────────────────┘            └─────────────────┘
```

**Frontend**: Captures user interactions, sends AJAX requests
**Backend**: Processes actions, renders updates, manages state

## 💡 Example: Counter Component

```php
<?php
class MyModule_Counter extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;
    use Maco_Openwire_Block_Component_Trait_Stateful;

    protected $_openwireAllowedActions = ['increment'];

    public function mount($params = [])
    {
        parent::mount($params);
        $this->setData('count', 0);
    }

    public function increment()
    {
        $this->setData('count', $this->getData('count') + 1);
    }

    protected function _toHtml()
    {
        $html = '<div openwire="counter">
            <button @click="increment">+</button>
            <span>{{ count }}</span>
        </div>';

        $compiler = Mage::getModel('openwire/template_compiler');
        return $compiler->compile($html, $this);
    }
}
```

**Result**: A button that increments a counter without page refreshes!

## 🎉 Why OpenWire?

| Traditional Magento 1 | With OpenWire |
|----------------------|----------------|
| Complex JavaScript/AJAX | PHP-only development |
| Manual state management | Automatic persistence |
| Security concerns per endpoint | Built-in security |
| Page refreshes for updates | Seamless reactivity |
| Complex form handling | Declarative interactions |

## 🤝 Community & Support

- 📖 **Documentation**: You're reading it!
- 🐛 **Issues**: [GitHub Issues](https://github.com/maco-studios/openwire/issues)
- 💬 **Discussions**: [GitHub Discussions](https://github.com/maco-studios/openwire/discussions)
- 📧 **Email**: support@maco-studios.com

## 📋 Requirements

- **Magento**: 1.9+
- **PHP**: 7.4+
- **Composer**: For dependency management
- **Node.js**: 16+ (development only)

---

<p align="center">
  <strong>Ready to modernize your Magento 1 store?</strong><br>
  <a href="installation.md">🚀 Get Started</a> •
  <a href="getting-started.md">📚 Learn More</a> •
  <a href="examples/">💡 See Examples</a>
</p>
