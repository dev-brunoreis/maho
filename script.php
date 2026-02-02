<?php

define('MAHO_ROOT_DIR', __DIR__);
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/product_maker.php';

Mage::register('isSecureArea', true, true);
Mage::app('admin');

if (!Mage::isInstalled()) {
    fwrite(STDERR, 'Maho is not installed. Run ./maho install first.' . PHP_EOL);
    exit(1);
}

$output = function (string $msg): void {
    echo $msg . PHP_EOL;
};

try {
    // 1. Disable all caches
    $resource = Mage::getSingleton('core/resource');
    $db = $resource->getConnection('core_write');
    $cacheOptionTable = $resource->getTableName('core/cache_option');
    $db->query(sprintf('UPDATE %s SET value = 0', $cacheOptionTable));
    $output('Caches disabled.');

    // 2. Set all indexers to "Update on Save" (real_time)
    $indexer = Mage::getSingleton('index/indexer');
    foreach ($indexer->getProcessesCollection() as $process) {
        /** @var Mage_Index_Model_Process $process */
        if ($process->getIndexer()->isVisible()) {
            $process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)->save();
        }
    }
    $output('Indexers set to Update on Save.');

    // 3. Set theme maco/vuewire via design_change
    $designChangeTable = $resource->getTableName('core/design_change');
    $db->delete($designChangeTable);
    foreach (Mage::app()->getStores() as $store) {
        $db->insert($designChangeTable, [
            'store_id' => $store->getId(),
            'design'   => 'maco/vuewire',
            'date_from' => null,
            'date_to'   => null,
        ]);
    }
    $output('Theme set to maco/vuewire (design_change).');

    // 4. Enable logs
    Mage::getModel('core/config')->saveConfig('dev/log/active', '1', 'default', 0);
    $output('Logs enabled.');

    // 5. Create default product if missing
    create_default_product_if_missing($output);

    // 6. Reindex all indexers
    $indexCollection = Mage::getResourceModel('index/process_collection');
    foreach ($indexCollection as $process) {
        /** @var Mage_Index_Model_Process $process */
        if ($process->isLocked()) {
            $process->unlock();
        }
        $process->reindexEverything();
    }
    $output('Indexers reindexed.');

    // 7. Clear all caches
    Mage::app()->getCache()->clean();
    $output('Caches cleared.');

    $output('Done.');
} catch (Throwable $e) {
    fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
