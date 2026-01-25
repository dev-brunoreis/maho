<?php

it('Access the product page', function () {
    Mage::app();

    $product = Mage::getModel('catalog/product')->getCollection()
        ->addAttributeToSelect(['name'])
        ->setPageSize(1)
        ->getFirstItem();
    if (!$product->getId()) {
        throw new Exception('No product found');
    }

    $page = visit(url('catalog/product/view/id/' . (int) $product->getId()));
    $page->assertSee($product->getName());
    $page->screenshot();
});
