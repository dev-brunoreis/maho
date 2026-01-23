<?php

use Tests\TestCase;

it('writes .env without install date', function () {
    $envPath = rtrim((string) MAHO_ROOT_DIR, '/\\') . '/.env';

    expect(is_file($envPath))->toBeTrue();

    $contents = file_get_contents($envPath);
    expect($contents)->not->toBeFalse();

    // Should contain DB creds (engine/name) but not INSTALL_DATE
    expect(stripos($contents, 'MAHO_DB_ENGINE'))->not->toBeFalse();
    expect(stripos($contents, 'MAHO_DB_NAME'))->not->toBeFalse();
    expect(stripos($contents, 'INSTALL_DATE'))->toBeFalse();
});

it('persists install date in database and model works', function () {
    $installDate = Mage::getModel('core/install_date');

    expect($installDate)->not->toBeNull();
    expect($installDate->exists())->toBeTrue();

    $date = $installDate->get();
    expect($date)->not->toBeFalse();
    expect(strtotime($date))->not->toBeFalse();
});

it('applyDbEnv applies MAHO_DB_* to config for sqlite relative db name', function () {
    // Backup env
    $backup = [];
    foreach (['MAHO_DB_ENGINE', 'MAHO_DB_NAME', 'MAHO_DB_PREFIX', 'MAHO_DB_INIT_STATEMENTS'] as $k) {
        $backup[$k] = $_ENV[$k] ?? ($_SERVER[$k] ?? null);
    }

    // Use a relative sqlite DB name to force normalization
    $_ENV['MAHO_DB_ENGINE'] = 'sqlite';
    $_ENV['MAHO_DB_NAME'] = 'test-db.sqlite';
    $_ENV['MAHO_DB_PREFIX'] = 'pref_';
    $_ENV['MAHO_DB_INIT_STATEMENTS'] = 'SET NAMES utf8';

    try {
        // Invoke the protected applyDbEnv() without doing full reinit (avoids DB/DDL during transaction)
        $config = Mage::getConfig();
        $ref = new ReflectionMethod($config, 'applyDbEnv');
        $ref->invoke($config);

        $dbname = (string) $config->getNode('global/resources/default_setup/connection/dbname');
        $init = (string) $config->getNode('global/resources/default_setup/connection/initStatements');
        $prefix = (string) $config->getNode('global/resources/db/table_prefix');

        // DB name should be normalized under MAHO_ROOT_DIR/var/db/test-db.sqlite
        $expected = rtrim((string) MAHO_ROOT_DIR, '/\\') . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'db' . DIRECTORY_SEPARATOR . 'test-db.sqlite';
        expect($dbname)->toBe($expected);

        // For sqlite, initStatements must be empty
        expect($init)->toBe('');

        // Table prefix must be set
        expect($prefix)->toBe('pref_');
    } finally {
        // Restore env
        foreach ($backup as $k => $v) {
            if ($v === null) {
                unset($_ENV[$k], $_SERVER[$k]);
            } else {
                $_ENV[$k] = $v;
                $_SERVER[$k] = $v;
            }
        }

        // Re-apply original env to config to avoid leaving test state modified
        $ref->invoke($config);
    }
});

it('Mage::isInstalled() is based on DB, not an INSTALL_DATE env var', function () {
    // Ensure it's installed first (make sure DB has an install date)
    Mage::getModel('core/install_date')->save();
    $mageRef = new ReflectionClass('Mage');
    $prop = $mageRef->getProperty('_isInstalled');
    $prop->setValue(null, null);
    expect(Mage::isInstalled())->toBeTrue();

    // Remove install date from DB
    $resource = Mage::getSingleton('core/resource');
    $conn = $resource->getConnection('core_write');
    $table = $resource->getTableName('core/config_data');
    $conn->delete($table, ['path = ?' => 'global/install/date']);

    // Set a fake INSTALL_DATE in env (should be ignored by isInstalled)
    $_ENV['INSTALL_DATE'] = date('c');
    $_SERVER['INSTALL_DATE'] = $_ENV['INSTALL_DATE'];
    putenv('INSTALL_DATE=' . $_ENV['INSTALL_DATE']);

    // Reset Mage::isInstalled() cache
    $mageRef = new ReflectionClass('Mage');
    $prop = $mageRef->getProperty('_isInstalled');
    $prop->setValue(null, null);

    // Now isInstalled() should be false (DB-driven)
    expect(Mage::isInstalled())->toBeFalse();

    // Cleanup
    unset($_ENV['INSTALL_DATE'], $_SERVER['INSTALL_DATE']);
    putenv('INSTALL_DATE');

    // Re-save install date to keep test isolation
    Mage::getModel('core/install_date')->save();
});
