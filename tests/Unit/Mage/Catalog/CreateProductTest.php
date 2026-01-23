<?php

it('creates a simple product in the database', function () {
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    $sku = 'pest-test-sku-rollback';

    $existing = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
    expect($existing)->toBeFalse();

    $entityType = Mage::getModel('eav/entity_type')->loadByCode('catalog_product');
    $attributeSetId = (int) $entityType->getDefaultAttributeSetId();
    expect($attributeSetId)->toBeGreaterThan(0);

    $websiteIds = Mage::getModel('core/website')->getCollection()->getAllIds();
    expect($websiteIds)->not->toBeEmpty();

    $product = Mage::getModel('catalog/product');
    $product->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID);
    $product->setWebsiteIds($websiteIds);
    $product->setAttributeSetId($attributeSetId);
    $product->setTypeId('simple');
    $product->setSku($sku);
    $product->setName('Pest Test Product');
    $product->setPrice(9.99);
    $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
    $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
    $product->setTaxClassId(0);
    $product->setStockData([
        'use_config_manage_stock' => 0,
        'manage_stock' => 1,
        'is_in_stock' => 1,
        'qty' => 10,
    ]);
    $product->save();

    expect((int) $product->getId())->toBeGreaterThan(0);

    $reloaded = Mage::getModel('catalog/product')->load($product->getId());
    expect((string) $reloaded->getSku())->toBe($sku);
});

it('rolls back database changes between tests', function () {
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

    $sku = 'pest-test-sku-rollback';
    $existing = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
    expect($existing)->toBeFalse();
});
