# Installation Guide

This guide will get OpenWire installed and running on your Magento 1 store in under 10 minutes.

## 📋 Prerequisites

Before installing OpenWire, ensure your system meets these requirements:

### System Requirements
- **Magento**: 1.9.0.0 or higher
- **PHP**: 7.4.0 or higher
- **Composer**: Latest stable version
- **Node.js**: 16.0.0 or higher (for development builds)
- **Git**: For cloning the repository

### Magento Setup
- Magento must be installed and running
- File permissions must allow writing to Magento directories
- Cache management access (admin panel or command line)

## 🚀 Installation Steps

### Step 1: Download OpenWire

```bash
# Clone the repository
git clone https://github.com/maco-studios/openwire.git
cd openwire

# Install PHP dependencies
composer install

# Install Node.js dependencies (for building frontend assets)
npm install
```

### Step 2: Deploy to Magento

Copy the OpenWire files to your Magento installation:

```bash
# Assuming Magento is installed at /var/www/magento
MAGENTO_ROOT="/var/www/magento"

# Copy module code
cp -r app/code/local/Maco $MAGENTO_ROOT/app/code/local/

# Copy design templates
cp -r app/design $MAGENTO_ROOT/

# Copy JavaScript assets
cp -r js $MAGENTO_ROOT/
```

### Step 3: Enable the Module

Enable OpenWire in your Magento configuration:

```bash
# Option A: Via admin panel
# 1. Go to System > Configuration > Advanced > Advanced
# 2. Find "Maco_Openwire" and set to "Enabled"

# Option B: Via configuration file
echo '<?xml version="1.0"?>
<config>
    <modules>
        <Maco_Openwire>
            <active>true</active>
            <codePool>local</codePool>
        </Maco_Openwire>
    </modules>
</config>' > $MAGENTO_ROOT/app/etc/modules/Maco_Openwire.xml
```

### Step 4: Build Frontend Assets

Compile the TypeScript/JavaScript assets:

```bash
# Build for production
npm run build

# The built files will be in js/openwire/dist/
```

### Step 5: Clear Magento Cache

Clear all Magento caches to ensure the module is recognized:

```bash
# Option A: Via admin panel
# System > Cache Management > Select All > Submit

# Option B: Via command line
cd $MAGENTO_ROOT
php shell/indexer.php reindexall
rm -rf var/cache/*
rm -rf var/session/*  # Optional: Clear sessions
```

### Step 6: Verify Installation

Test that OpenWire is working:

1. **Check Module Status**
   ```bash
   # In Magento admin: System > Configuration > Advanced > Advanced
   # Look for "Maco_Openwire" - should show as enabled
   ```

2. **Test Component Creation**
   ```php
   // Create a test file in your Magento root
   <?php
   require_once 'app/Mage.php';
   Mage::app();

   $component = Mage::app()->getLayout()->createBlock('openwire_component/counter');
   if ($component) {
       echo "✅ OpenWire is working!";
   } else {
       echo "❌ OpenWire installation failed";
   }
   ```

3. **Check Frontend Assets**
   - Open your browser's developer tools
   - Check that `js/openwire/dist/openwire.js` loads without errors

## 🔧 Configuration

### Basic Configuration

OpenWire works out-of-the-box with default settings. For advanced configuration:

```xml
<!-- app/code/local/Maco/Openwire/etc/config.xml -->
<config>
    <global>
        <openwire>
            <!-- Custom state store class -->
            <state_store>Maco_Openwire_Model_State_SessionStore</state_store>

            <!-- Custom template compiler -->
            <template_compiler>Maco_Openwire_Model_Template_Compiler</template_compiler>

            <!-- Security settings -->
            <security>
                <enable_csrf>true</enable_csrf>
                <max_request_size>1048576</max_request_size>
            </security>
        </openwire>
    </global>
</config>
```

### Environment-Specific Settings

```xml
<!-- For production -->
<production>
    <openwire>
        <debug>false</debug>
        <cache>true</cache>
    </openwire>
</production>

<!-- For development -->
<development>
    <openwire>
        <debug>true</debug>
        <cache>false</cache>
    </openwire>
</development>
```

## 🐛 Troubleshooting

### Common Issues

#### Module Not Showing in Admin
**Problem**: OpenWire doesn't appear in System > Configuration > Advanced
**Solution**:
1. Check file permissions on `app/etc/modules/Maco_Openwire.xml`
2. Clear Magento cache: `rm -rf var/cache/*`
3. Reindex: `php shell/indexer.php reindexall`
4. Check PHP error logs for module loading issues

#### JavaScript Assets Not Loading
**Problem**: `openwire.js` returns 404
**Solution**:
1. Verify files exist: `ls -la js/openwire/dist/`
2. Check file permissions
3. Rebuild assets: `npm run build`
4. Clear browser cache

#### Components Not Working
**Problem**: Components render but don't respond to interactions
**Solution**:
1. Check browser console for JavaScript errors
2. Verify AJAX endpoint: `/openwire/update/index`
3. Check PHP error logs for component instantiation errors
4. Ensure component actions are in `$_openwireAllowedActions`

#### Template Compilation Errors
**Problem**: Components show raw `@click` instead of compiled attributes
**Solution**:
1. Verify template compiler is loaded
2. Check component `_toHtml()` method calls compiler
3. Ensure component implements proper interface

### Debug Mode

Enable debug mode for detailed logging:

```xml
<!-- app/etc/local.xml -->
<config>
    <global>
        <openwire>
            <debug>true</debug>
        </openwire>
    </global>
</config>
```

Debug information will be logged to `var/log/openwire.log`.

## 📦 Upgrading

To upgrade OpenWire to a newer version:

```bash
# Backup your custom components
cp -r app/code/local/YourModule app/code/local/YourModule.backup

# Update OpenWire
cd openwire
git pull origin main
composer install
npm install
npm run build

# Deploy updated files
cp -r app/code/local/Maco /path/to/magento/app/code/local/
cp -r js /path/to/magento/

# Clear caches
cd /path/to/magento
rm -rf var/cache/*
php shell/indexer.php reindexall

# Restore custom components if needed
# cp -r app/code/local/YourModule.backup/* app/code/local/YourModule/
```

## 🎯 Next Steps

Once OpenWire is installed:

1. **[Create Your First Component](getting-started.md)** - Learn the basics
2. **[Explore Examples](../examples/)** - See real implementations
3. **[Read the Component Guide](components/)** - Master advanced features

## 🆘 Need Help?

- 📖 Check the [troubleshooting section](#-troubleshooting) above
- 🐛 [Open an issue](https://github.com/maco-studios/openwire/issues) on GitHub
- 💬 [Ask the community](https://github.com/maco-studios/openwire/discussions)
- 📧 Email: support@maco-studios.com

---

<p align="center">
  <strong>Installation complete!</strong><br>
  <a href="getting-started.md">🚀 Create Your First Component</a> •
  <a href="../examples/">💡 View Examples</a>
</p>