# Security

OpenWire provides built-in security features to protect your components from common web vulnerabilities. Learn how to build secure reactive applications.

## 🛡️ Built-in Protections

### CSRF Protection

OpenWire automatically includes CSRF tokens in all AJAX requests:

```php
// Automatic CSRF token generation
$formKey = Mage::getSingleton('core/session')->getFormKey();

// Included in every request
{
    "id": "ow_1234567890",
    "calls": [{"method": "increment", "params": []}],
    "updates": {},
    "form_key": "abc123def456"
}
```

### Action Whitelisting

Components must explicitly declare allowed actions:

```php
class SecureComponent extends Maco_Openwire_Block_Component_Abstract
{
    // Only these methods can be called from frontend
    protected $_openwireAllowedActions = [
        'increment',
        'updateName',
        'submitForm'
    ];

    // ❌ This method cannot be called (not in whitelist)
    public function dangerousMethod()
    {
        // This would be blocked
    }

    // ✅ This method can be called
    public function increment()
    {
        $count = $this->getData('count');
        $this->setData('count', $count + 1);
    }
}
```

### Input Validation

Always validate user input before processing:

```php
public function updateEmail($email = null)
{
    // Sanitize input
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Validate format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->setData('errors.email', 'Invalid email format');
        return;
    }

    // Check length
    if (strlen($email) > 254) {
        $this->setData('errors.email', 'Email too long');
        return;
    }

    $this->setData('email', $email);
    $this->setData('errors.email', null);
}
```

## 🔒 Authorization

### Role-Based Access

```php
class AdminComponent extends Maco_Openwire_Block_Component_Abstract
{
    public function mount($params = [])
    {
        // Check permissions on mount
        if (!$this->isAdmin()) {
            throw new Exception('Access denied');
        }
    }

    public function deleteUser($userId)
    {
        // Double-check permissions
        if (!$this->isAdmin()) {
            $this->setData('error', 'Access denied');
            return;
        }

        // Proceed with deletion...
    }

    protected function isAdmin()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer && $customer->getIsAdmin();
    }
}
```

### Method-Level Permissions

```php
class ContentComponent extends Maco_Openwire_Block_Component_Abstract
{
    protected $_openwireAllowedActions = [
        'view',      // Anyone can view
        'edit',      // Must be owner or admin
        'delete'     // Only admin
    ];

    public function edit($contentId)
    {
        if (!$this->canEdit($contentId)) {
            $this->setData('error', 'Permission denied');
            return;
        }

        // Proceed with edit...
    }

    public function delete($contentId)
    {
        if (!$this->canDelete($contentId)) {
            $this->setData('error', 'Permission denied');
            return;
        }

        // Proceed with deletion...
    }

    protected function canEdit($contentId)
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $content = $this->getContent($contentId);

        return $customer &&
               ($customer->getId() == $content->getCustomerId() || $customer->getIsAdmin());
    }

    protected function canDelete($contentId)
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer && $customer->getIsAdmin();
    }
}
```

## 🧹 Input Sanitization

### HTML Content

```php
public function updateComment($comment = null)
{
    // Strip HTML tags
    $comment = strip_tags($comment);

    // Or allow limited HTML
    $comment = Mage::helper('core')->stripTags($comment, '<p><br><strong><em>');

    // Escape for safe display
    $comment = htmlspecialchars($comment, ENT_QUOTES, 'UTF-8');

    $this->setData('comment', $comment);
}
```

### SQL Injection Prevention

```php
public function searchProducts($query = null)
{
    // Use parameterized queries
    $query = trim($query);
    if (empty($query)) {
        $this->setData('results', []);
        return;
    }

    // Safe database query
    $collection = Mage::getModel('catalog/product')->getCollection()
        ->addAttributeToFilter('name', ['like' => '%' . $query . '%'])
        ->addAttributeToSelect(['name', 'price']);

    $results = [];
    foreach ($collection as $product) {
        $results[] = [
            'id' => $product->getId(),
            'name' => htmlspecialchars($product->getName()),
            'price' => $product->getPrice()
        ];
    }

    $this->setData('results', $results);
}
```

### File Upload Security

```php
public function uploadFile($fileData = null)
{
    if (!$fileData || !isset($fileData['tmp_name'])) {
        $this->setData('error', 'No file uploaded');
        return;
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $fileType = mime_content_type($fileData['tmp_name']);

    if (!in_array($fileType, $allowedTypes)) {
        $this->setData('error', 'Invalid file type');
        return;
    }

    // Validate file size (max 5MB)
    if ($fileData['size'] > 5 * 1024 * 1024) {
        $this->setData('error', 'File too large');
        return;
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
        $this->setData('uploadedFile', $safeName);
    } else {
        $this->setData('error', 'Upload failed');
    }
}
```

## 🔐 Secure Communication

### HTTPS Enforcement

```php
class SecureComponent extends Maco_Openwire_Block_Component_Abstract
{
    public function mount($params = [])
    {
        // Redirect to HTTPS if not secure
        if (!Mage::app()->getRequest()->isSecure()) {
            $url = Mage::getUrl('', ['_secure' => true]);
            Mage::app()->getResponse()->setRedirect($url);
            return;
        }
    }
}
```

### Request Rate Limiting

```php
class RateLimitedComponent extends Maco_Openwire_Block_Component_Abstract
{
    protected $_requestCounts = [];

    public function increment()
    {
        $customerId = $this->getCustomerId();
        $key = $customerId . '_' . date('Y-m-d-H-i');

        if (!isset($this->_requestCounts[$key])) {
            $this->_requestCounts[$key] = 0;
        }

        $this->_requestCounts[$key]++;

        // Limit: 10 requests per minute
        if ($this->_requestCounts[$key] > 10) {
            $this->setData('error', 'Too many requests. Please wait.');
            return;
        }

        // Proceed with increment...
        $count = $this->getData('count');
        $this->setData('count', $count + 1);
    }

    protected function getCustomerId()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        return $customer ? $customer->getId() : 'guest_' . session_id();
    }
}
```

## 🚨 Error Handling

### Secure Error Messages

```php
public function processPayment($paymentData = null)
{
    try {
        // Validate payment data
        if (!$this->validatePaymentData($paymentData)) {
            $this->setData('error', 'Invalid payment information');
            return;
        }

        // Process payment
        $result = $this->chargeCard($paymentData);

        if ($result['success']) {
            $this->setData('success', 'Payment processed successfully');
            $this->setData('transactionId', $result['transaction_id']);
        } else {
            // Don't expose internal error details
            $this->setData('error', 'Payment failed. Please try again.');
            // Log actual error for debugging
            Mage::log('Payment failed: ' . $result['error'], Zend_Log::ERR, 'payment.log');
        }

    } catch (Exception $e) {
        // Never expose exception details to user
        $this->setData('error', 'An error occurred. Please try again later.');
        // Log full exception for debugging
        Mage::logException($e);
    }
}
```

### Logging Security Events

```php
public function login($credentials = null)
{
    $ip = Mage::app()->getRequest()->getClientIp();
    $userAgent = Mage::app()->getRequest()->getHeader('User-Agent');

    try {
        // Attempt login
        $result = $this->authenticate($credentials);

        if ($result['success']) {
            $this->setData('loggedIn', true);
            Mage::log("Successful login: {$credentials['username']} from {$ip}", null, 'security.log');
        } else {
            $this->setData('error', 'Invalid credentials');
            Mage::log("Failed login attempt: {$credentials['username']} from {$ip}", Zend_Log::WARN, 'security.log');
        }

    } catch (Exception $e) {
        Mage::log("Login error for {$credentials['username']} from {$ip}: " . $e->getMessage(), Zend_Log::ERR, 'security.log');
        $this->setData('error', 'Login failed. Please try again.');
    }
}
```

## 🛡️ XSS Prevention

### Template Escaping

```php
protected function _toHtml()
{
    $userInput = $this->getData('userInput');

    // Automatic escaping in templates
    $html = '<div openwire="user-content">
        <p>User said: {{ userInput }}</p>
    </div>';

    // Manual escaping when needed
    $html = '<div openwire="manual-escape">
        <p>User said: ' . htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8') . '</p>
    </div>';

    $compiler = Mage::getModel('openwire/template_compiler');
    return $compiler->compile($html, $this);
}
```

### Content Security Policy

```php
// In your layout XML
<reference name="head">
    <block type="core/text" name="csp_meta">
        <action method="setText">
            <text><![CDATA[
<meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';">
            ]]></text>
        </action>
    </block>
</reference>
```

## 🔍 Security Auditing

### Component Security Checklist

- [ ] CSRF tokens included in all forms
- [ ] Action methods whitelisted
- [ ] User input validated and sanitized
- [ ] Database queries use parameterized statements
- [ ] File uploads validated (type, size, content)
- [ ] Error messages don't leak sensitive information
- [ ] HTTPS enforced for sensitive operations
- [ ] Rate limiting implemented for public actions
- [ ] Security events logged appropriately

### Security Testing

```php
// Unit test for input validation
it('validates email input', function () {
    $component = new SecureComponent();
    $component->updateEmail('invalid-email');

    expect($component->getData('errors.email'))->toBe('Invalid email format');
});

it('prevents unauthorized access', function () {
    $component = new AdminComponent();
    // Mock non-admin user
    // Test that deleteUser throws exception or sets error
});

it('sanitizes HTML input', function () {
    $component = new ContentComponent();
    $component->updateComment('<script>alert("xss")</script>Hello');

    expect($component->getData('comment'))->toBe('Hello');
});
```

## 📚 Next Steps

- **[Components](../components/)**: Building secure components
- **[State Management](../state-management.md)**: Secure state handling
- **[API Reference](../api/)**: Security-related methods
- **[Examples](../examples/)**: Security patterns

---

<p align="center">
  <strong>🛡️ Build secure applications!</strong><br>
  <a href="../components/">🧩 Learn Components</a> •
  <a href="../examples/">💡 View Examples</a> •
  <a href="../api/security.md">📚 Security API</a>
</p>