<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

// Mock Mage class for testing
if (!class_exists('Mage', false)) {
    class Mage
    {
        private static $singletons = [];
        private static $app;

        public static function getSingleton($name)
        {
            if (!isset(self::$singletons[$name])) {
                self::$singletons[$name] = new MockSession();
            }
            return self::$singletons[$name];
        }

        public static function app()
        {
            if (!self::$app) {
                self::$app = new MockApp();
            }
            return self::$app;
        }

        public static function getModel($name)
        {
            // Return appropriate mock models
            switch ($name) {
                case 'openwire/template_compiler':
                    return new MockTemplateCompiler();
                default:
                    return new stdClass();
            }
        }

        public static function log($message, $level = null, $file = null, $forceLog = false)
        {
            // Mock logging - do nothing
        }

        public static function getBaseDir($type = 'base')
        {
            return '/tmp';
        }

        public static function helper($name)
        {
            return new stdClass();
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
        private $data = [];

        public function getData($key = null)
        {
            if ($key === null) {
                return $this->data;
            }
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }

        public function setData($key, $value = null)
        {
            if (is_array($key)) {
                $this->data = array_merge($this->data, $key);
            } else {
                $this->data[$key] = $value;
            }
        }

        public function unsetData($key)
        {
            unset($this->data[$key]);
        }

        public function getFormKey()
        {
            return 'test_form_key_123';
        }
    }
}

if (!class_exists('MockTemplateCompiler', false)) {
    class MockTemplateCompiler
    {
        public function compile($html, $component)
        {
            // Simple mock compilation - just return the HTML with some basic processing
            $html = str_replace('@click="increment"', 'data-ow:click="increment"', $html);
            $html = str_replace('@click="decrement"', 'data-ow:click="decrement"', $html);

            // Replace variables with actual component data
            $count = $component->getData('count') ?? 5;
            $html = str_replace('{{ count }}', $count, $html);

            $html = str_replace('openwire="counter"', 'data-ow-component="openwire_component/counter" data-ow-id="ow_test" data-ow-config="{}"', $html);
            return $html;
        }
    }
}
class Varien_Object
{
    private $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function getData($key = '')
    {
        if ($key === '') {
            return $this->data;
        }
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    public function hasData($key = '')
    {
        return isset($this->data[$key]);
    }

    public function getId()
    {
        return $this->getData('id');
    }

    public function setId($id)
    {
        return $this->setData('id', $id);
    }
}
