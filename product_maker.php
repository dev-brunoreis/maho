<?php

function create_default_product_if_missing(?callable $output = null): bool
{
    $output = $output ?? function (string $msg): void {
        echo $msg . PHP_EOL;
    };

    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

    $sku = 'simples';
    $existing = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
    if ($existing) {
        $output('Product with SKU "' . $sku . '" already exists (ID: ' . $existing->getId() . ').');
        return false;
    }

    $entityType = Mage::getModel('eav/entity_type')->loadByCode('catalog_product');
    $attributeSetId = (int) $entityType->getDefaultAttributeSetId();
    $websiteIds = Mage::getModel('core/website')->getCollection()->getAllIds();

    $product = Mage::getModel('catalog/product');
    $product->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID);
    $product->setWebsiteIds($websiteIds);
    $product->setAttributeSetId($attributeSetId);
    $product->setTypeId('simple');
    $product->setSku($sku);
    $product->setName('simples');
    $product->setPrice(100);
    $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
    $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
    $product->setTaxClassId(0);
    $product->setStockData([
        'use_config_manage_stock' => 0,
        'manage_stock' => 1,
        'is_in_stock' => 1,
        'qty' => 100,
    ]);
    $product->save();

    $output('Product created: ' . $product->getName() . ' (ID: ' . $product->getId() . ', SKU: ' . $product->getSku() . ')');
    $output('  Price: ' . $product->getPrice() . ', Stock: 100, Status: Enabled.');
    return true;
}

// Run standalone when executed as script (not when required)
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    define('MAHO_ROOT_DIR', __DIR__);
    require __DIR__ . '/vendor/autoload.php';

    Mage::register('isSecureArea', true, true);
    Mage::app('admin');

    if (!Mage::isInstalled()) {
        fwrite(STDERR, 'Maho is not installed. Run ./maho install first.' . PHP_EOL);
        exit(1);
    }

    try {
        create_default_product_if_missing();
    } catch (Throwable $e) {
        fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
}
