# State Management

OpenWire automatically handles component state persistence across requests. No sessions, no databases - just reactive data that survives page reloads.

## 🔄 How It Works

### Automatic Hydration

```php
class MyComponent extends Maco_Openwire_Block_Component_Abstract
{
    public function mount($params = [])
    {
        // Initial state from URL params or defaults
        $this->setData('query', $params['q'] ?? '');
        $this->setData('results', []);
        $this->setData('isLoading', false);
    }

    public function search()
    {
        $this->setData('isLoading', true);
        // Perform search...
        $this->setData('results', $searchResults);
        $this->setData('isLoading', false);
    }
}
```

### State Persistence

```html
<!-- Template -->
<div openwire="search-component">
    <input @input="updateQuery" value="{{ query }}" />
    <button @click="search" :disabled="isLoading">
        {{ isLoading ? 'Searching...' : 'Search' }}
    </button>

    <div class="results">
        <!-- Results render here -->
    </div>
</div>
```

### Request Flow

1. **Initial Load**: Component mounts with initial state
2. **User Action**: Event triggers method call
3. **State Update**: Component data changes
4. **Re-render**: Template reflects new state
5. **Persistence**: State survives page reloads

## 📊 Data Types

### Primitive Values

```php
public function mount($params = [])
{
    $this->setData('count', 0);           // Integer
    $this->setData('name', 'John');       // String
    $this->setData('isActive', true);     // Boolean
    $this->setData('price', 29.99);       // Float
}
```

### Arrays and Objects

```php
public function mount($params = [])
{
    $this->setData('items', [
        ['id' => 1, 'name' => 'Item 1', 'price' => 10.00],
        ['id' => 2, 'name' => 'Item 2', 'price' => 15.00]
    ]);

    $this->setData('user', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'preferences' => ['theme' => 'dark']
    ]);
}
```

### Complex State

```php
public function mount($params = [])
{
    $this->setData('form', [
        'name' => '',
        'email' => '',
        'message' => '',
        'errors' => []
    ]);

    $this->setData('ui', [
        'isSubmitting' => false,
        'showModal' => false,
        'activeTab' => 'general'
    ]);
}
```

## 🔄 State Updates

### Direct Updates

```php
public function increment()
{
    $current = $this->getData('count');
    $this->setData('count', $current + 1);
}

public function toggle()
{
    $current = $this->getData('isVisible');
    $this->setData('isVisible', !$current);
}
```

### Batch Updates

```php
public function resetForm()
{
    $this->setData('form', [
        'name' => '',
        'email' => '',
        'message' => '',
        'errors' => []
    ]);
}
```

### Computed Properties

```php
public function getTotal()
{
    $items = $this->getData('items');
    return array_sum(array_column($items, 'price'));
}

public function getIsValid()
{
    $form = $this->getData('form');
    return !empty($form['name']) && !empty($form['email']);
}
```

## 🎯 Event Handling

### Form Inputs

```php
public function updateName($value = null)
{
    $this->setData('form.name', $value);
}

public function updateEmail($value = null)
{
    $this->setData('form.email', $value);
}
```

### Array Operations

```php
public function addItem()
{
    $items = $this->getData('items');
    $items[] = ['id' => uniqid(), 'name' => 'New Item', 'price' => 0.00];
    $this->setData('items', $items);
}

public function removeItem($index)
{
    $items = $this->getData('items');
    unset($items[$index]);
    $this->setData('items', array_values($items));
}

public function updateItem($index, $field, $value)
{
    $items = $this->getData('items');
    $items[$index][$field] = $value;
    $this->setData('items', $items);
}
```

## 🔒 State Validation

### Client-Side Validation

```php
public function updateEmail($value = null)
{
    $this->setData('form.email', $value);

    // Basic validation
    $errors = $this->getData('form.errors');
    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email address';
    } else {
        unset($errors['email']);
    }

    $this->setData('form.errors', $errors);
}
```

### Server-Side Validation

```php
public function submitForm()
{
    $form = $this->getData('form');

    // Server validation
    $errors = [];
    if (empty($form['name'])) {
        $errors['name'] = 'Name is required';
    }
    if (empty($form['email'])) {
        $errors['email'] = 'Email is required';
    }

    if (!empty($errors)) {
        $this->setData('form.errors', $errors);
        return;
    }

    // Process form...
    $this->setData('form.errors', []);
    $this->setData('isSubmitted', true);
}
```

## 🔄 Advanced Patterns

### State Machines

```php
class OrderComponent extends Maco_Openwire_Block_Component_Abstract
{
    const STATE_CART = 'cart';
    const STATE_CHECKOUT = 'checkout';
    const STATE_PAYMENT = 'payment';
    const STATE_CONFIRMATION = 'confirmation';

    public function mount($params = [])
    {
        $this->setData('state', self::STATE_CART);
        $this->setData('cart', []);
        $this->setData('order', null);
    }

    public function addToCart($productId)
    {
        $cart = $this->getData('cart');
        $cart[] = $productId;
        $this->setData('cart', $cart);
    }

    public function proceedToCheckout()
    {
        if (empty($this->getData('cart'))) {
            return;
        }
        $this->setData('state', self::STATE_CHECKOUT);
    }

    public function placeOrder()
    {
        // Process payment...
        $this->setData('state', self::STATE_CONFIRMATION);
        $this->setData('order', ['id' => '12345', 'total' => 99.99]);
    }
}
```

### Optimistic Updates

```php
public function toggleFavorite($itemId)
{
    $favorites = $this->getData('favorites');

    // Optimistic update
    $wasFavorited = in_array($itemId, $favorites);
    if ($wasFavorited) {
        $favorites = array_diff($favorites, [$itemId]);
    } else {
        $favorites[] = $itemId;
    }

    $this->setData('favorites', $favorites);

    try {
        // Make API call
        $result = $this->saveFavorite($itemId, !$wasFavorited);

        if (!$result['success']) {
            // Revert on failure
            if ($wasFavorited) {
                $favorites[] = $itemId;
            } else {
                $favorites = array_diff($favorites, [$itemId]);
            }
            $this->setData('favorites', $favorites);
        }
    } catch (Exception $e) {
        // Revert on error
        if ($wasFavorited) {
            $favorites[] = $itemId;
        } else {
            $favorites = array_diff($favorites, [$itemId]);
        }
        $this->setData('favorites', $favorites);
    }
}
```

### Debounced Updates

```php
public function updateSearchQuery($value = null)
{
    $this->setData('query', $value);

    // Cancel previous debounce
    if (isset($this->_searchTimeout)) {
        clearTimeout($this->_searchTimeout);
    }

    // Debounce search
    $this->_searchTimeout = setTimeout(function() {
        $this->performSearch();
    }, 300);
}

public function performSearch()
{
    $this->setData('isSearching', true);

    $results = $this->searchProducts($this->getData('query'));

    $this->setData('results', $results);
    $this->setData('isSearching', false);
}
```

## 🔧 State Debugging

### Logging State Changes

```php
public function setData($key, $value = null)
{
    if (Mage::getIsDeveloperMode()) {
        Mage::log("State change: {$key} = " . json_encode($value), null, 'openwire.log');
    }

    return parent::setData($key, $value);
}
```

### State Inspection

```php
public function debugState()
{
    $state = $this->getData();
    Mage::log('Current state: ' . json_encode($state), null, 'openwire.log');
    return $state;
}
```

## 🐛 Common Issues

### State Not Persisting

```php
// ❌ Wrong - using private properties
private $_count = 0;

// ✅ Correct - use setData/getData
$this->setData('count', 0);
```

### Race Conditions

```php
// ❌ Wrong - multiple rapid updates
public function increment()
{
    $this->setData('count', $this->getData('count') + 1);
}

// ✅ Correct - atomic updates
public function increment()
{
    $count = $this->getData('count');
    $this->setData('count', $count + 1);
}
```

### Memory Leaks

```php
// ❌ Wrong - accumulating data
public function addLog($message)
{
    $logs = $this->getData('logs');
    $logs[] = $message;
    $this->setData('logs', $logs); // Logs grow indefinitely
}

// ✅ Correct - limit data size
public function addLog($message)
{
    $logs = $this->getData('logs');
    $logs[] = $message;
    if (count($logs) > 100) {
        $logs = array_slice($logs, -100); // Keep last 100
    }
    $this->setData('logs', $logs);
}
```

## 📚 Next Steps

- **[Templates](../templates/)**: Reactive HTML rendering
- **[Security](../security.md)**: State validation and protection
- **[Examples](../examples/)**: Real state management patterns
- **[API Reference](../api/)**: State methods and properties

---

<p align="center">
  <strong>🔄 Master reactive state!</strong><br>
  <a href="../templates/">🎨 Learn Templates</a> •
  <a href="../examples/">💡 View Examples</a> •
  <a href="../api/state.md">📚 State API</a>
</p>