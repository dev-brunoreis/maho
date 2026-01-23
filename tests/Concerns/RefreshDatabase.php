<?php

namespace Tests\Concerns;

use RuntimeException;

trait RefreshDatabase
{
    private static bool $mahoInstalledOnce = false;

    /** @var \Maho\Db\Adapter\AdapterInterface[] */
    private array $testDbConnections = [];

    protected function refreshDatabaseSetUp(): void
    {
        $this->installMahoOncePerProcess();
        $this->beginDatabaseTransaction();
    }

    protected function refreshDatabaseTearDown(): void
    {
        $this->rollbackDatabaseTransaction();
    }

    private function installMahoOncePerProcess(): void
    {
        if (self::$mahoInstalledOnce) {
            return;
        }

        // Ensure installer writes .env into a temp dir (creds remain in phpunit.xml).
        if (!defined('MAHO_ROOT_DIR')) {
            $tmpRoot = rtrim((string) sys_get_temp_dir(), '/\\') . '/hammer-tests';
            if (!is_dir($tmpRoot)) {
                @mkdir($tmpRoot, 0750, true);
            }
            define('MAHO_ROOT_DIR', $tmpRoot);
        }
        if (!defined('MAHO_PUBLIC_DIR')) {
            define('MAHO_PUBLIC_DIR', MAHO_ROOT_DIR . '/public');
        }

        // Sanity check: required env vars must exist (set in phpunit.xml).
        $engine = $_ENV['MAHO_DB_ENGINE'] ?? $_SERVER['MAHO_DB_ENGINE'] ?? getenv('MAHO_DB_ENGINE');
        $name = $_ENV['MAHO_DB_NAME'] ?? $_SERVER['MAHO_DB_NAME'] ?? getenv('MAHO_DB_NAME');
        if (strtolower((string) $engine) !== 'sqlite' || (string) $name !== ':memory:') {
            throw new RuntimeException('Tests require MAHO_DB_ENGINE=sqlite and MAHO_DB_NAME=:memory: (configure in phpunit.xml).');
        }

        // Fresh app boot (only once): after this we keep the same connection alive for :memory:.
        \Mage::reset();
        \Mage::app();

        $console = new \Mage_Install_Model_Installer_Console();

        $argv = [
            'install',
            '--license_agreement_accepted=yes',
            '--locale=en_US',
            '--timezone=UTC',
            '--default_currency=USD',
            '--db_engine=sqlite',
            '--db_name=:memory:',
            '--db_prefix=',
            '--url=http://localhost/',
            '--use_secure=0',
            '--secure_base_url=http://localhost/',
            '--use_secure_admin=0',
            '--admin_lastname=Test',
            '--admin_firstname=Test',
            '--admin_email=test@example.com',
            '--admin_username=admin',
            '--admin_password=AdminPassword123!',
        ];

        if ($console->setArgs($argv) === false) {
            throw new RuntimeException('Failed to initialize installer args: ' . implode('; ', $console->getErrors()));
        }
        if ($console->init(\Mage::app()) === false) {
            throw new RuntimeException('Failed to init installer: ' . implode('; ', $console->getErrors()));
        }
        if ($console->install() === false) {
            throw new RuntimeException('Failed to install Maho test DB: ' . implode('; ', $console->getErrors()));
        }

        self::$mahoInstalledOnce = true;
    }

    private function beginDatabaseTransaction(): void
    {
        $resource = \Mage::getSingleton('core/resource');
        $write = $resource->getConnection(\Mage_Core_Model_Resource::DEFAULT_WRITE_RESOURCE);
        $read = $resource->getConnection(\Mage_Core_Model_Resource::DEFAULT_READ_RESOURCE);

        // With SQLite :memory:, core_read uses <use> default_setup so both should be the same object.
        if ($read === $write) {
            $write->beginTransaction();
            $this->testDbConnections = [$write];
            return;
        }

        // Fallback (unexpected): start transaction on both.
        $write->beginTransaction();
        $read->beginTransaction();
        $this->testDbConnections = [$write, $read];
    }

    private function rollbackDatabaseTransaction(): void
    {
        if ($this->testDbConnections === []) {
            return;
        }

        try {
            foreach ($this->testDbConnections as $conn) {
                try {
                    $conn->rollBack();
                } catch (\Throwable $e) {
                    // Best-effort rollback; keep original test failure if any.
                }
            }
        } finally {
            $this->testDbConnections = [];
        }
    }
}

