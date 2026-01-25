# Examples

Real-world OpenWire component examples. Copy, modify, and learn from these patterns.

## 📚 Table of Contents

- [Counter](./counter/) - Basic reactive counter
- [Todo List](./todo/) - CRUD operations with arrays
- [Search](./search/) - Real-time search with debouncing
- [Form Validation](./form/) - Multi-step form with validation
- [Shopping Cart](./cart/) - E-commerce cart functionality
- [Modal Dialog](./modal/) - Dynamic modal with state
- [Data Table](./table/) - Sortable, filterable table
- [File Upload](./upload/) - Secure file upload component

## 🔢 Counter Component

Simple reactive counter demonstrating basic state management.

### PHP Component (`app/code/local/Maco/Openwire/Block/Component/Counter.php`)

```php
<?php

class Maco_Openwire_Block_Component_Counter extends Maco_Openwire_Block_Component_Abstract
{
    protected $_openwireAllowedActions = ['increment', 'decrement', 'reset'];

    public function mount($params = [])
    {
        $this->setData('count', (int)($params['start'] ?? 0));
        $this->setData('step', (int)($params['step'] ?? 1));
    }

    public function increment()
    {
        $count = $this->getData('count');
        $step = $this->getData('step');
        $this->setData('count', $count + $step);
    }

    public function decrement()
    {
        $count = $this->getData('count');
        $step = $this->getData('step');
        $this->setData('count', $count - $step);
    }

    public function reset()
    {
        $this->setData('count', 0);
    }

    public function getTemplate()
    {
        return 'openwire/counter.phtml';
    }
}
```

### Template (`app/design/frontend/base/default/template/openwire/counter.phtml`)

```html
<div openwire="counter" class="counter-component">
    <div class="counter-display">
        <span class="count">{{ count }}</span>
    </div>

    <div class="counter-controls">
        <button @click="decrement" class="btn btn-secondary">-</button>
        <button @click="reset" class="btn btn-warning">Reset</button>
        <button @click="increment" class="btn btn-primary">+</button>
    </div>

    <div class="counter-settings">
        <label>Step size:</label>
        <select @change="updateStep">
            <option value="1" {{ step == 1 ? 'selected' : '' }}>1</option>
            <option value="5" {{ step == 5 ? 'selected' : '' }}>5</option>
            <option value="10" {{ step == 10 ? 'selected' : '' }}>10</option>
        </select>
    </div>
</div>
```

### Usage in Layout

```xml
<block type="openwire/component" name="counter">
    <action method="setComponent"><component>counter</component></action>
    <action method="setParams">
        <params>
            <start>10</start>
            <step>5</step>
        </params>
    </action>
</block>
```

### CSS Styling

```css
.counter-component {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    max-width: 300px;
    margin: 0 auto;
}

.counter-display {
    margin-bottom: 20px;
}

.count {
    font-size: 48px;
    font-weight: bold;
    color: #333;
}

.counter-controls {
    margin-bottom: 20px;
}

.btn {
    padding: 10px 20px;
    margin: 0 5px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.btn-primary { background: #007cba; color: white; }
.btn-secondary { background: #6c757d; color: white; }
.btn-warning { background: #ffc107; color: black; }

.counter-settings {
    font-size: 14px;
}
```

## ✅ Todo List Component

Demonstrates array manipulation and CRUD operations.

### PHP Component

```php
<?php

class Maco_Openwire_Block_Component_Todo extends Maco_Openwire_Block_Component_Abstract
{
    protected $_openwireAllowedActions = [
        'addTodo', 'toggleTodo', 'deleteTodo', 'clearCompleted', 'updateFilter'
    ];

    public function mount($params = [])
    {
        $this->setData('todos', []);
        $this->setData('filter', 'all'); // all, active, completed
        $this->setData('newTodo', '');
    }

    public function addTodo()
    {
        $newTodo = trim($this->getData('newTodo'));
        if (empty($newTodo)) {
            return;
        }

        $todos = $this->getData('todos');
        $todos[] = [
            'id' => uniqid('todo_'),
            'text' => $newTodo,
            'completed' => false,
            'created_at' => time()
        ];

        $this->setData('todos', $todos);
        $this->setData('newTodo', '');
    }

    public function toggleTodo($todoId)
    {
        $todos = $this->getData('todos');
        foreach ($todos as &$todo) {
            if ($todo['id'] === $todoId) {
                $todo['completed'] = !$todo['completed'];
                break;
            }
        }
        $this->setData('todos', $todos);
    }

    public function deleteTodo($todoId)
    {
        $todos = $this->getData('todos');
        $todos = array_filter($todos, function($todo) use ($todoId) {
            return $todo['id'] !== $todoId;
        });
        $this->setData('todos', array_values($todos));
    }

    public function clearCompleted()
    {
        $todos = $this->getData('todos');
        $todos = array_filter($todos, function($todo) {
            return !$todo['completed'];
        });
        $this->setData('todos', array_values($todos));
    }

    public function updateFilter($filter)
    {
        $validFilters = ['all', 'active', 'completed'];
        if (in_array($filter, $validFilters)) {
            $this->setData('filter', $filter);
        }
    }

    public function getFilteredTodos()
    {
        $todos = $this->getData('todos');
        $filter = $this->getData('filter');

        switch ($filter) {
            case 'active':
                return array_filter($todos, function($todo) {
                    return !$todo['completed'];
                });
            case 'completed':
                return array_filter($todos, function($todo) {
                    return $todo['completed'];
                });
            default:
                return $todos;
        }
    }

    public function getStats()
    {
        $todos = $this->getData('todos');
        $total = count($todos);
        $completed = count(array_filter($todos, function($todo) {
            return $todo['completed'];
        }));
        $active = $total - $completed;

        return [
            'total' => $total,
            'active' => $active,
            'completed' => $completed
        ];
    }

    public function getTemplate()
    {
        return 'openwire/todo.phtml';
    }
}
```

### Template

```html
<div openwire="todo" class="todo-app">
    <h1>Todo App</h1>

    <!-- Add Todo Form -->
    <form @submit="addTodo" class="add-todo">
        <input type="text"
               @input="updateNewTodo"
               value="{{ newTodo }}"
               placeholder="What needs to be done?"
               class="todo-input" />
        <button type="submit" class="add-btn">Add Todo</button>
    </form>

    <!-- Todo List -->
    <div class="todo-list">
        {{ filteredTodos.length > 0 ? '' : '<p class="empty">No todos yet</p>' }}

        {{ filteredTodos.map((todo, index) => `
            <div class="todo-item ${todo.completed ? 'completed' : ''}">
                <input type="checkbox"
                       @change="toggleTodo"
                       data-todo-id="${todo.id}"
                       ${todo.completed ? 'checked' : ''} />

                <span class="todo-text">${todo.text}</span>

                <button @click="deleteTodo"
                        data-todo-id="${todo.id}"
                        class="delete-btn">×</button>
            </div>
        `).join('') }}
    </div>

    <!-- Filters and Stats -->
    <div class="todo-footer">
        <div class="stats">
            {{ stats.total }} total, {{ stats.active }} active, {{ stats.completed }} completed
        </div>

        <div class="filters">
            <button @click="updateFilter" data-filter="all"
                    class="filter-btn ${filter === 'all' ? 'active' : ''}">All</button>
            <button @click="updateFilter" data-filter="active"
                    class="filter-btn ${filter === 'active' ? 'active' : ''}">Active</button>
            <button @click="updateFilter" data-filter="completed"
                    class="filter-btn ${filter === 'completed' ? 'active' : ''}">Completed</button>
        </div>

        <button @click="clearCompleted" class="clear-btn">
            Clear Completed
        </button>
    </div>
</div>
```

## 🔍 Real-time Search

Demonstrates debounced search with loading states.

### PHP Component

```php
<?php

class Maco_Openwire_Block_Component_Search extends Maco_Openwire_Block_Component_Abstract
{
    protected $_openwireAllowedActions = ['search', 'selectResult'];

    public function mount($params = [])
    {
        $this->setData('query', $params['q'] ?? '');
        $this->setData('results', []);
        $this->setData('isLoading', false);
        $this->setData('selectedResult', null);
    }

    public function search($query = null)
    {
        $this->setData('query', trim($query));
        $this->setData('isLoading', true);

        // Simulate search delay
        sleep(1);

        $results = $this->performSearch($query);
        $this->setData('results', $results);
        $this->setData('isLoading', false);
    }

    public function selectResult($resultId)
    {
        $results = $this->getData('results');
        foreach ($results as $result) {
            if ($result['id'] == $resultId) {
                $this->setData('selectedResult', $result);
                break;
            }
        }
    }

    protected function performSearch($query)
    {
        if (empty($query)) {
            return [];
        }

        // Mock search results
        $allResults = [
            ['id' => 1, 'title' => 'Product A', 'category' => 'Electronics'],
            ['id' => 2, 'title' => 'Product B', 'category' => 'Books'],
            ['id' => 3, 'title' => 'Product C', 'category' => 'Clothing'],
            // ... more results
        ];

        return array_filter($allResults, function($result) use ($query) {
            return stripos($result['title'], $query) !== false ||
                   stripos($result['category'], $query) !== false;
        });
    }

    public function getTemplate()
    {
        return 'openwire/search.phtml';
    }
}
```

### Template

```html
<div openwire="search" class="search-component">
    <div class="search-input-container">
        <input type="text"
               @input="updateQuery"
               value="{{ query }}"
               placeholder="Search products..."
               class="search-input" />

        <div class="search-icon">🔍</div>
    </div>

    <!-- Loading indicator -->
    {{ isLoading ? '<div class="loading">Searching...</div>' : '' }}

    <!-- Search results -->
    <div class="search-results">
        {{ results.length > 0 ? `
            <div class="results-header">
                Found ${results.length} result(s)
            </div>
        ` : '' }}

        {{ results.map(result => `
            <div class="result-item" @click="selectResult" data-result-id="${result.id}">
                <div class="result-title">${result.title}</div>
                <div class="result-category">${result.category}</div>
            </div>
        `).join('') }}

        {{ query && !isLoading && results.length === 0 ? '
            <div class="no-results">No results found for "${query}"</div>
        ' : '' }}
    </div>

    <!-- Selected result details -->
    {{ selectedResult ? `
        <div class="selected-result">
            <h3>Selected: ${selectedResult.title}</h3>
            <p>Category: ${selectedResult.category}</p>
        </div>
    ` : '' }}
</div>
```

## 📝 Form Validation

Multi-step form with comprehensive validation.

### PHP Component

```php
<?php

class Maco_Openwire_Block_Component_ContactForm extends Maco_Openwire_Block_Component_Abstract
{
    protected $_openwireAllowedActions = [
        'updateField', 'nextStep', 'prevStep', 'submitForm'
    ];

    public function mount($params = [])
    {
        $this->setData('currentStep', 1);
        $this->setData('totalSteps', 3);

        $this->setData('form', [
            'name' => '',
            'email' => '',
            'phone' => '',
            'message' => '',
            'newsletter' => false
        ]);

        $this->setData('errors', []);
        $this->setData('isSubmitting', false);
        $this->setData('submitted', false);
    }

    public function updateField($field, $value)
    {
        $form = $this->getData('form');
        $form[$field] = $value;
        $this->setData('form', $form);

        // Clear field error on change
        $errors = $this->getData('errors');
        unset($errors[$field]);
        $this->setData('errors', $errors);
    }

    public function nextStep()
    {
        if ($this->validateCurrentStep()) {
            $currentStep = $this->getData('currentStep');
            if ($currentStep < $this->getData('totalSteps')) {
                $this->setData('currentStep', $currentStep + 1);
            }
        }
    }

    public function prevStep()
    {
        $currentStep = $this->getData('currentStep');
        if ($currentStep > 1) {
            $this->setData('currentStep', $currentStep - 1);
        }
    }

    public function submitForm()
    {
        if ($this->validateAll()) {
            $this->setData('isSubmitting', true);

            // Simulate form processing
            sleep(2);

            // Send email or save to database
            $this->processFormSubmission();

            $this->setData('submitted', true);
            $this->setData('isSubmitting', false);
        }
    }

    protected function validateCurrentStep()
    {
        $currentStep = $this->getData('currentStep');
        $form = $this->getData('form');
        $errors = $this->getData('errors');

        switch ($currentStep) {
            case 1:
                if (empty($form['name'])) {
                    $errors['name'] = 'Name is required';
                }
                break;

            case 2:
                if (empty($form['email'])) {
                    $errors['email'] = 'Email is required';
                } elseif (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'Invalid email format';
                }
                break;

            case 3:
                if (empty($form['message'])) {
                    $errors['message'] = 'Message is required';
                } elseif (strlen($form['message']) < 10) {
                    $errors['message'] = 'Message must be at least 10 characters';
                }
                break;
        }

        $this->setData('errors', $errors);
        return empty($errors);
    }

    protected function validateAll()
    {
        // Validate all steps
        for ($step = 1; $step <= $this->getData('totalSteps'); $step++) {
            $this->setData('currentStep', $step);
            if (!$this->validateCurrentStep()) {
                return false;
            }
        }
        return true;
    }

    protected function processFormSubmission()
    {
        $form = $this->getData('form');

        // Send email
        $mail = Mage::getModel('core/email');
        $mail->setToName('Admin');
        $mail->setToEmail(Mage::getStoreConfig('trans_email/ident_general/email'));
        $mail->setFromEmail($form['email']);
        $mail->setFromName($form['name']);
        $mail->setSubject('Contact Form Submission');
        $mail->setBody($form['message']);
        $mail->send();
    }

    public function getTemplate()
    {
        return 'openwire/contact-form.phtml';
    }
}
```

## 🛒 Shopping Cart

E-commerce cart with add/remove/update functionality.

### PHP Component

```php
<?php

class Maco_Openwire_Block_Component_Cart extends Maco_Openwire_Block_Component_Abstract
{
    protected $_openwireAllowedActions = [
        'addToCart', 'removeFromCart', 'updateQuantity', 'clearCart', 'applyCoupon'
    ];

    public function mount($params = [])
    {
        $this->setData('items', []);
        $this->setData('coupon', '');
        $this->setData('discount', 0);
    }

    public function addToCart($productId, $quantity = 1)
    {
        $items = $this->getData('items');
        $product = $this->getProduct($productId);

        if (!$product) {
            $this->setData('error', 'Product not found');
            return;
        }

        // Check if item already in cart
        $found = false;
        foreach ($items as &$item) {
            if ($item['id'] == $productId) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $items[] = [
                'id' => $productId,
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'quantity' => $quantity,
                'image' => $product->getImageUrl()
            ];
        }

        $this->setData('items', $items);
        $this->setData('error', null);
    }

    public function removeFromCart($productId)
    {
        $items = $this->getData('items');
        $items = array_filter($items, function($item) use ($productId) {
            return $item['id'] != $productId;
        });
        $this->setData('items', array_values($items));
    }

    public function updateQuantity($productId, $quantity)
    {
        $quantity = max(0, (int)$quantity);

        if ($quantity == 0) {
            $this->removeFromCart($productId);
            return;
        }

        $items = $this->getData('items');
        foreach ($items as &$item) {
            if ($item['id'] == $productId) {
                $item['quantity'] = $quantity;
                break;
            }
        }
        $this->setData('items', $items);
    }

    public function clearCart()
    {
        $this->setData('items', []);
        $this->setData('coupon', '');
        $this->setData('discount', 0);
    }

    public function applyCoupon($code)
    {
        $code = strtoupper(trim($code));

        // Mock coupon validation
        $coupons = [
            'SAVE10' => 10,
            'SAVE20' => 20
        ];

        if (isset($coupons[$code])) {
            $this->setData('coupon', $code);
            $this->setData('discount', $coupons[$code]);
            $this->setData('error', null);
        } else {
            $this->setData('error', 'Invalid coupon code');
        }
    }

    public function getSubtotal()
    {
        $items = $this->getData('items');
        return array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
        }, $items));
    }

    public function getDiscountAmount()
    {
        $subtotal = $this->getSubtotal();
        $discountPercent = $this->getData('discount');
        return $subtotal * ($discountPercent / 100);
    }

    public function getTotal()
    {
        return $this->getSubtotal() - $this->getDiscountAmount();
    }

    public function getItemCount()
    {
        $items = $this->getData('items');
        return array_sum(array_column($items, 'quantity'));
    }

    protected function getProduct($productId)
    {
        return Mage::getModel('catalog/product')->load($productId);
    }

    public function getTemplate()
    {
        return 'openwire/cart.phtml';
    }
}
```

## 🎯 Modal Dialog

Dynamic modal with state management.

### PHP Component

```php
<?php

class Maco_Openwire_Block_Component_Modal extends Maco_Openwire_Block_Component_Abstract
{
    protected $_openwireAllowedActions = [
        'openModal', 'closeModal', 'confirmAction'
    ];

    public function mount($params = [])
    {
        $this->setData('isOpen', false);
        $this->setData('title', '');
        $this->setData('message', '');
        $this->setData('confirmText', 'OK');
        $this->setData('cancelText', 'Cancel');
        $this->setData('type', 'info'); // info, warning, danger
        $this->setData('callback', null);
    }

    public function openModal($config = [])
    {
        $this->setData('isOpen', true);
        $this->setData('title', $config['title'] ?? 'Confirm Action');
        $this->setData('message', $config['message'] ?? 'Are you sure?');
        $this->setData('confirmText', $config['confirmText'] ?? 'OK');
        $this->setData('cancelText', $config['cancelText'] ?? 'Cancel');
        $this->setData('type', $config['type'] ?? 'info');
        $this->setData('callback', $config['callback'] ?? null);
    }

    public function closeModal()
    {
        $this->setData('isOpen', false);
        $this->setData('callback', null);
    }

    public function confirmAction()
    {
        $callback = $this->getData('callback');
        if ($callback) {
            // Execute callback (in real app, this might trigger other actions)
            $this->executeCallback($callback);
        }
        $this->closeModal();
    }

    protected function executeCallback($callback)
    {
        // In a real implementation, you might have a registry of callbacks
        // or use a more sophisticated callback system
        Mage::log('Executing callback: ' . $callback, null, 'modal.log');
    }

    public function getTemplate()
    {
        return 'openwire/modal.phtml';
    }
}
```

### Template

```html
<div openwire="modal">
    <!-- Modal trigger buttons (for demo) -->
    <div class="modal-triggers">
        <button @click="openInfoModal" class="btn btn-info">Info Modal</button>
        <button @click="openWarningModal" class="btn btn-warning">Warning Modal</button>
        <button @click="openDangerModal" class="btn btn-danger">Danger Modal</button>
    </div>

    <!-- Modal overlay -->
    {{ isOpen ? `
        <div class="modal-overlay" @click="closeModal">
            <div class="modal-content ${type}" @click.stop>
                <div class="modal-header">
                    <h3 class="modal-title">${title}</h3>
                    <button @click="closeModal" class="modal-close">&times;</button>
                </div>

                <div class="modal-body">
                    <p>${message}</p>
                </div>

                <div class="modal-footer">
                    <button @click="closeModal" class="btn btn-secondary">
                        ${cancelText}
                    </button>
                    <button @click="confirmAction" class="btn btn-${type === 'danger' ? 'danger' : 'primary'}">
                        ${confirmText}
                    </button>
                </div>
            </div>
        </div>
    ` : '' }}
</div>
```

## 📊 Data Table

Sortable, filterable table with pagination.

### PHP Component

```php
<?php

class Maco_Openwire_Block_Component_DataTable extends Maco_Openwire_Block_Component_Abstract
{
    protected $_openwireAllowedActions = [
        'sortBy', 'filterBy', 'changePage', 'changePageSize'
    ];

    public function mount($params = [])
    {
        $this->setData('data', $this->getSampleData());
        $this->setData('sortColumn', 'name');
        $this->setData('sortDirection', 'asc');
        $this->setData('filterText', '');
        $this->setData('currentPage', 1);
        $this->setData('pageSize', 10);
    }

    public function sortBy($column)
    {
        $currentColumn = $this->getData('sortColumn');
        $currentDirection = $this->getData('sortDirection');

        if ($column === $currentColumn) {
            $newDirection = $currentDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $newDirection = 'asc';
        }

        $this->setData('sortColumn', $column);
        $this->setData('sortDirection', $newDirection);
        $this->setData('currentPage', 1); // Reset to first page
    }

    public function filterBy($text)
    {
        $this->setData('filterText', trim($text));
        $this->setData('currentPage', 1); // Reset to first page
    }

    public function changePage($page)
    {
        $totalPages = $this->getTotalPages();
        $page = max(1, min($page, $totalPages));
        $this->setData('currentPage', $page);
    }

    public function changePageSize($size)
    {
        $validSizes = [5, 10, 25, 50];
        if (in_array($size, $validSizes)) {
            $this->setData('pageSize', $size);
            $this->setData('currentPage', 1);
        }
    }

    public function getFilteredData()
    {
        $data = $this->getData('data');
        $filterText = $this->getData('filterText');

        if (empty($filterText)) {
            return $data;
        }

        return array_filter($data, function($row) use ($filterText) {
            return stripos($row['name'], $filterText) !== false ||
                   stripos($row['email'], $filterText) !== false ||
                   stripos($row['department'], $filterText) !== false;
        });
    }

    public function getSortedData()
    {
        $data = $this->getFilteredData();
        $sortColumn = $this->getData('sortColumn');
        $sortDirection = $this->getData('sortDirection');

        usort($data, function($a, $b) use ($sortColumn, $sortDirection) {
            $aVal = $a[$sortColumn];
            $bVal = $b[$sortColumn];

            if ($sortDirection === 'asc') {
                return $aVal <=> $bVal;
            } else {
                return $bVal <=> $aVal;
            }
        });

        return $data;
    }

    public function getPaginatedData()
    {
        $data = $this->getSortedData();
        $pageSize = $this->getData('pageSize');
        $currentPage = $this->getData('currentPage');

        $offset = ($currentPage - 1) * $pageSize;
        return array_slice($data, $offset, $pageSize);
    }

    public function getTotalPages()
    {
        $filteredCount = count($this->getFilteredData());
        $pageSize = $this->getData('pageSize');
        return ceil($filteredCount / $pageSize);
    }

    public function getPaginationInfo()
    {
        $currentPage = $this->getData('currentPage');
        $totalPages = $this->getTotalPages();
        $pageSize = $this->getData('pageSize');
        $totalItems = count($this->getFilteredData());

        $startItem = ($currentPage - 1) * $pageSize + 1;
        $endItem = min($currentPage * $pageSize, $totalItems);

        return [
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'pageSize' => $pageSize,
            'totalItems' => $totalItems,
            'startItem' => $startItem,
            'endItem' => $endItem
        ];
    }

    protected function getSampleData()
    {
        return [
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'department' => 'Engineering'],
            ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'department' => 'Marketing'],
            // ... more sample data
        ];
    }

    public function getTemplate()
    {
        return 'openwire/data-table.phtml';
    }
}
```

## 📁 File Upload

Secure file upload with validation and progress.

### PHP Component

```php
<?php

class Maco_Openwire_Block_Component_FileUpload extends Maco_Openwire_Block_Component_Abstract
{
    protected $_openwireAllowedActions = ['uploadFile', 'removeFile'];

    public function mount($params = [])
    {
        $this->setData('files', []);
        $this->setData('isUploading', false);
        $this->setData('uploadProgress', 0);
        $this->setData('maxFileSize', 5 * 1024 * 1024); // 5MB
        $this->setData('allowedTypes', ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']);
    }

    public function uploadFile($fileData = null)
    {
        if (!$fileData || !isset($fileData['tmp_name'])) {
            $this->setData('error', 'No file uploaded');
            return;
        }

        // Validate file size
        if ($fileData['size'] > $this->getData('maxFileSize')) {
            $this->setData('error', 'File too large (max 5MB)');
            return;
        }

        // Validate file type
        $allowedTypes = $this->getData('allowedTypes');
        $fileType = mime_content_type($fileData['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $this->setData('error', 'Invalid file type');
            return;
        }

        $this->setData('isUploading', true);
        $this->setData('uploadProgress', 0);

        // Simulate upload progress
        for ($i = 0; $i <= 100; $i += 10) {
            $this->setData('uploadProgress', $i);
            usleep(50000); // 0.05 seconds
        }

        // Generate safe filename
        $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
        $safeName = uniqid('upload_') . '.' . $extension;

        // Move to secure location
        $uploadDir = Mage::getBaseDir('media') . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . $safeName;
        if (move_uploaded_file($fileData['tmp_name'], $destination)) {
            $files = $this->getData('files');
            $files[] = [
                'name' => $fileData['name'],
                'safe_name' => $safeName,
                'size' => $fileData['size'],
                'type' => $fileType,
                'uploaded_at' => time()
            ];

            $this->setData('files', $files);
            $this->setData('error', null);
        } else {
            $this->setData('error', 'Upload failed');
        }

        $this->setData('isUploading', false);
        $this->setData('uploadProgress', 0);
    }

    public function removeFile($safeName)
    {
        $files = $this->getData('files');
        $files = array_filter($files, function($file) use ($safeName) {
            return $file['safe_name'] !== $safeName;
        });

        $this->setData('files', array_values($files));

        // Delete physical file
        $filePath = Mage::getBaseDir('media') . '/uploads/' . $safeName;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    public function getTemplate()
    {
        return 'openwire/file-upload.phtml';
    }
}
```

---

<p align="center">
  <strong>💡 Copy these examples and build amazing components!</strong><br>
  <a href="../getting-started.md">🚀 Getting Started</a> •
  <a href="../components/">🧩 Component Guide</a> •
  <a href="../templates/">🎨 Template Guide</a>
</p>