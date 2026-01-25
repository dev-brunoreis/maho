<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

use Maco_Openwire_Model_State_SessionStore;

// Stub classes for Mage
if (!class_exists('Mage', false)) {
    class Mage
    {
        public static function getSingleton($name)
        {
            return new MockSession();
        }
    }
}
if (!class_exists('MockSession', false)) {
    class MockSession
    {
        private static $data = [];
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
        public function getFormKey()
        {
            return "test_form_key";
        }
    }
}

beforeEach(function () {
    // No need for eval, stubs defined above
});

it('saves and loads state', function () {
    $store = new Maco_Openwire_Model_State_SessionStore();
    $componentId = 'test_component';
    $state = ['count' => 10, 'name' => 'test'];

    $store->save($componentId, $state);
    $loadedState = $store->load($componentId);

    expect($loadedState)->toBe($state);
});

it('returns empty array for non-existent component', function () {
    $store = new Maco_Openwire_Model_State_SessionStore();
    $componentId = 'non_existent';

    $state = $store->load($componentId);

    expect($state)->toBe([]);
});

it('forgets state', function () {
    $store = new Maco_Openwire_Model_State_SessionStore();
    $componentId = 'test_component';
    $state = ['count' => 5];

    $store->save($componentId, $state);
    expect($store->load($componentId))->toBe($state);

    $store->forget($componentId);
    expect($store->load($componentId))->toBe([]);
});
