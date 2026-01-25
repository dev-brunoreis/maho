# Magento 1 Module Development Guide

This document defines conventions, patterns, and validation steps for building and maintaining Magento 1 modules.  
It enforces code quality, maintainability, and test reliability using **PestPHP** and standard Magento 1 practices.

---

## 🧱 Project Overview

This repository defines **custom Magento 1 modules** developed with:
- Strict MVC layering
- Dependency isolation (no global scope abuse)
- Comprehensive tests via **PestPHP**
- Version-controlled XML configurations
- Strict code review and QA checkpoints

---

## ⚙️ Development Workflow

Each module should follow the Magento 1 structure:

```

app/
code/
local/
Vendor/
ModuleName/
Block/
controllers/
etc/
Helper/
Model/
sql/
etc/modules/
Vendor_ModuleName.xml

````

**Checkpoints before commit:**
1. All new code under `app/code/local/Vendor/ModuleName/`
2. Config XML valid and loaded
3. Block/Model naming conventions respected
4. PSR-0 compliant autoloading
5. Module enabled in `app/etc/modules/`

---

## 🧩 Module Definition Example

### `app/etc/modules/Vendor_ModuleName.xml`
```xml
<?xml version="1.0"?>
<config>
  <modules>
    <Vendor_ModuleName>
      <active>true</active>
      <codePool>local</codePool>
    </Vendor_ModuleName>
  </modules>
</config>
````

**Validation checklist:**

* ✅ File name matches module name
* ✅ `codePool` matches directory path
* ✅ Only one `<active>` declaration

---

## 🏗️ Configuration (config.xml)

### `app/code/local/Vendor/ModuleName/etc/config.xml`

```xml
<?xml version="1.0"?>
<config>
  <modules>
    <Vendor_ModuleName>
      <version>1.0.0</version>
    </Vendor_ModuleName>
  </modules>
  <global>
    <models>
      <vendor_modulename>
        <class>Vendor_ModuleName_Model</class>
      </vendor_modulename>
    </models>
    <helpers>
      <vendor_modulename>
        <class>Vendor_ModuleName_Helper</class>
      </vendor_modulename>
    </helpers>
  </global>
</config>
```

**Validation checklist:**

* ✅ Root `<config>` tag present
* ✅ Proper XML nesting
* ✅ Model and helper namespaces match directory names

---

## 🧠 Coding Standards

### General Principles

* Follow **PSR-1** and **PSR-12**
* Avoid global Mage calls inside models
* Do not use static helper calls in loops
* Limit methods to one responsibility
* Document public methods with `@return`, `@param`, `@throws`

### PHP Example

```php
class Vendor_ModuleName_Model_Item extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('vendor_modulename/item');
    }

    public function calculateDiscount($price)
    {
        if ($price <= 0) {
            throw new InvalidArgumentException('Invalid price');
        }

        return $price * 0.9;
    }
}
```

---

## 🧪 Testing with PestPHP

Magento 1 doesn’t natively support PestPHP, but we integrate it using `composer` and bootstrap loading:

### `tests/Pest.php`

```php
<?php
require __DIR__ . '/../app/Mage.php';
Mage::app('admin');
```

### Example Test

```php
<?php

use Vendor\ModuleName\Model\Item;

it('applies a 10% discount', function () {
    $item = Mage::getModel('vendor_modulename/item');
    expect($item->calculateDiscount(100))->toBe(90.0);
});

it('throws for invalid price', function () {
    $item = Mage::getModel('vendor_modulename/item');
    $item->calculateDiscount(0);
})->throws(InvalidArgumentException::class);
```

**Test Checkpoints:**

* ✅ Test suite runs with `./vendor/bin/pest`
* ✅ Covers business logic methods
* ✅ Covers exceptions and edge cases
* ✅ Uses Magento bootstrap, not mocks
* ✅ 80%+ coverage for core classes

---

## 🧩 Frontend Block Example

```php
class Vendor_ModuleName_Block_Widget extends Mage_Core_Block_Template
{
    protected $_template = 'vendor/module/widget.phtml';

    public function getCustomerName()
    {
        return Mage::getSingleton('customer/session')->getCustomer()->getName();
    }
}
```

**Checkpoints:**

* ✅ `_template` path correct
* ✅ Block loaded via layout XML
* ✅ No direct DB calls
* ✅ Methods return display-safe values

---

## 🧭 Layout Configuration

### `app/design/frontend/base/default/layout/vendor_module.xml`

```xml
<layout version="0.1.0">
  <default>
    <reference name="content">
      <block type="vendor_modulename/widget" name="vendor.widget" template="vendor/module/widget.phtml"/>
    </reference>
  </default>
</layout>
```

**Validation:**

* ✅ Layout loads in Magento Admin → System → Configuration → Advanced
* ✅ Block name matches layout handle
* ✅ Template file exists

---

## 🧾 Helper Class Example

```php
class Vendor_ModuleName_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function formatPrice($price)
    {
        return Mage::helper('core')->currency($price, true, false);
    }
}
```

**Checkpoints:**

* ✅ Class extends `Mage_Core_Helper_Abstract`
* ✅ Namespaced correctly under `Vendor_ModuleName_Helper`
* ✅ Methods stateless and pure

---

## 🔍 Deployment & Validation

### Before Commit

* [ ] Module XML validated with `xmllint`
* [ ] No usage of deprecated Mage core methods
* [ ] `config.xml` version bumped if schema changes
* [ ] `pest` tests pass
* [ ] No direct `$_GET`, `$_POST`, or `$_REQUEST` usage

### Continuous Validation

Add to `composer.json`:

```json
"scripts": {
  "test": "pest",
  "lint": "phpcs --standard=PSR12 app/code/local/Vendor"
}
```

---

## 🧱 Example Pest Test Directory Layout

```
tests/
  Pest.php
  Unit/
    ItemTest.php
    HelperDataTest.php
  Integration/
    LayoutRenderTest.php
```

Each module feature must have:

* 1 unit test file per model/helper
* 1 integration test per layout or observer
* Optional functional test for controller routes

---

## 🛠️ Observer Example

```php
class Vendor_ModuleName_Model_Observer
{
    public function onSalesOrderPlaceAfter(Varien_Event_Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        Mage::log("Order placed: " . $order->getIncrementId(), null, 'orders.log');
    }
}
```

### Event Config (`config.xml`)

```xml
<global>
  <events>
    <sales_order_place_after>
      <observers>
        <vendor_modulename_observer>
          <type>singleton</type>
          <class>vendor_modulename/observer</class>
          <method>onSalesOrderPlaceAfter</method>
        </vendor_modulename_observer>
      </observers>
    </sales_order_place_after>
  </events>
</global>
```

**Checkpoint:**

* ✅ Observer method uses typed `Varien_Event_Observer`
* ✅ Logs or executes safe, idempotent operations
* ✅ No hardcoded paths or external connections

---

## 🚫 What to Avoid

* ❌ Global `Mage::getSingleton('core/resource')` in loops
* ❌ SQL inside blocks or controllers
* ❌ Mixed HTML/PHP in templates
* ❌ Direct file writes in production
* ❌ Inline configuration in PHP

---

## 🧭 Final Validation Checklist

| Category   | Validation                 | Status |
| ---------- | -------------------------- | ------ |
| Structure  | Module in `app/code/local` | ✅      |
| XML Config | Valid & loaded             | ✅      |
| Models     | Extend proper core class   | ✅      |
| Helpers    | Stateless & namespaced     | ✅      |
| Blocks     | No logic in templates      | ✅      |
| Observers  | Event-safe                 | ✅      |
| Tests      | PestPHP coverage >80%      | ✅      |
| Style      | PSR-12 compliant           | ✅      |

---

## ✅ Example Command Suite

```bash
composer install
vendor/bin/pest
vendor/bin/phpcs --standard=PSR12 app/code/local/Vendor
vendor/bin/phpstan analyse app/code/local/Vendor
```

---

## 🔒 Best Practices Summary

* Always commit with passing tests (`pest`)
* Use event-observer pattern instead of rewrites
* Keep XML minimal and versioned
* Encapsulate logic in models, not blocks
* Validate every XML file on pre-commit
* Prefer dependency injection (when possible via constructor)
* Keep tests deterministic and independent

---

> 🧭 **Use this file as your agent guide** — every new module or refactor must conform to these rules.
> Treat it as both a **checklist** and a **training document** for consistent Magento 1 development quality.
