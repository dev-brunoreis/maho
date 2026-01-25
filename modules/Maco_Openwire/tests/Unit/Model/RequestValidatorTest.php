<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

use Maco_Openwire_Model_Security_RequestValidator;

// Stub classes for Mage
if (!class_exists('Mage', false)) {
    class Mage
    {
        public static function app()
        {
            return new MockApp();
        }
        public static function getSingleton($name)
        {
            return new MockSession();
        }
    }
}
if (!class_exists('MockApp', false)) {
    class MockApp
    {
        public function getStore()
        {
            return new MockStore();
        }
    }
}
if (!class_exists('MockStore', false)) {
    class MockStore
    {
        public function isAdmin()
        {
            return false;
        }
    }
}
if (!class_exists('MockSession', false)) {
    class MockSession
    {
        private static $data = [];
        public function getFormKey()
        {
            return "test_form_key";
        }
        public function getData($key)
        {
            return isset(self::$data[$key]) ? self::$data[$key] : null;
        }
        public function setData($key, $value)
        {
            self::$data[$key] = $value;
        }
        public function unsetData($key)
        {
            unset(self::$data[$key]);
        }
    }
}

beforeEach(function () {
    // No need for eval, stubs defined above
});

it('validates valid payload', function () {
    $validator = new Maco_Openwire_Model_Security_RequestValidator();
    $payload = [
        'component' => 'test/component',
        'form_key' => 'test_form_key_123',
        'calls' => [
            ['method' => 'testMethod', 'params' => []]
        ]
    ];

    expect(fn () => $validator->validate($payload))->not->toThrow(Exception::class);
});

it('throws exception for invalid payload', function () {
    $validator = new Maco_Openwire_Model_Security_RequestValidator();
    $payload = 'invalid';

    expect(fn () => $validator->validate($payload))->toThrow(Exception::class);
});

it('throws exception for missing component', function () {
    $validator = new Maco_Openwire_Model_Security_RequestValidator();
    $payload = [
        'form_key' => 'test_form_key',
        'calls' => [
            ['method' => 'testMethod', 'params' => []]
        ]
    ];

    expect(fn () => $validator->validate($payload))->toThrow(Exception::class);
});

it('throws exception for call without method', function () {
    $validator = new Maco_Openwire_Model_Security_RequestValidator();
    $payload = [
        'component' => 'test/component',
        'form_key' => 'test_form_key',
        'calls' => [
            ['params' => []]
        ]
    ];

    expect(fn () => $validator->validate($payload))->toThrow(Exception::class);
});

it('throws exception for invalid form key', function () {
    $validator = new Maco_Openwire_Model_Security_RequestValidator();
    $payload = [
        'component' => 'test/component',
        'form_key' => 'invalid_key',
        'calls' => [
            ['method' => 'testMethod', 'params' => []]
        ]
    ];

    expect(fn () => $validator->validate($payload))->toThrow(Exception::class);
});
