<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

use Maco_Openwire_Model_Template_Compiler;

// Stub for Mage_Core_Block_Template
if (!class_exists('Mage_Core_Block_Template', false)) {
    class Mage_Core_Block_Template
    {
        protected $data = [];
        protected $template;

        public function __construct()
        {
            $this->data = [];
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

        public function getData($key = "")
        {
            if ($key === "") {
                return $this->data;
            }
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }

        public function setTemplate($template)
        {
            $this->template = $template;
            return $this;
        }

        public function getTemplate()
        {
            return $this->template;
        }

        public function toHtml()
        {
            return $this->_toHtml();
        }

        protected function _toHtml()
        {
            return '';
        }
    }
}

// Stub for Mage
if (!class_exists('Mage', false)) {
    class Mage
    {
        public static function getModel($modelName)
        {
            if ($modelName === 'openwire/template_compiler') {
                return new Maco_Openwire_Model_Template_Compiler();
            }
            return null;
        }
    }
}

// Stub interface
if (!interface_exists('Maco_Openwire_Block_Component_Contract_ComponentInterface', false)) {
    interface Maco_Openwire_Block_Component_Contract_ComponentInterface
    {
        public function mount(array $props = []);
        public function getId();
    }
}

beforeEach(function () {

    // No need for eval anymore, stubs are defined above
});

it('class exists', function () {
    expect(class_exists('Maco_Openwire_Block_Component_Counter'))->toBeTrue();
});

it('decrements count', function () {
    $counter = new Maco_Openwire_Block_Component_Counter();
    $counter->mount(['count' => 5]);
    $counter->decrement();
    expect($counter->getData('count'))->toBe(4);
});

it('has allowed actions', function () {
    $counter = new Maco_Openwire_Block_Component_Counter();
    $reflection = new ReflectionClass($counter);
    $property = $reflection->getProperty('openwireAllowedActions');
    $property->setAccessible(true);
    $allowedActions = $property->getValue($counter);
    expect($allowedActions)->toContain('increment');
    expect($allowedActions)->toContain('decrement');
});

it('mounts with default count', function () {
    $counter = new Maco_Openwire_Block_Component_Counter();
    $counter->mount([]);
    expect($counter->getData('count'))->toBe(0);
});

it('mounts with specified count', function () {
    $counter = new Maco_Openwire_Block_Component_Counter();
    $counter->mount(['count' => 10]);
    expect($counter->getData('count'))->toBe(10);
});

it('renders HTML with correct attributes', function () {
    $counter = new Maco_Openwire_Block_Component_Counter();
    $counter->mount(['count' => 3]);

    $html = $counter->toHtml();

    expect($html)->toContain('data-ow-component="openwire_component/counter"');
    expect($html)->toContain('data-ow-id="');
    expect($html)->toContain('data-ow:click="decrement"');
    expect($html)->toContain('data-ow:click="increment"');
    expect($html)->toContain('<span>3</span>');
});

it('returns correct component alias', function () {
    $counter = new Maco_Openwire_Block_Component_Counter();
    $reflection = new ReflectionClass($counter);
    $method = $reflection->getMethod('getComponentAlias');
    $method->setAccessible(true);
    $alias = $method->invoke($counter);
    expect($alias)->toBe('openwire_component/counter');
});

it('is stateful', function () {
    $counter = new Maco_Openwire_Block_Component_Counter();
    expect($counter->isStateful())->toBeTrue();
});

it('has no poll interval by default', function () {
    $counter = new Maco_Openwire_Block_Component_Counter();
    $reflection = new ReflectionClass($counter);
    $method = $reflection->getMethod('getPollIntervalMs');
    $method->setAccessible(true);
    $interval = $method->invoke($counter);
    expect($interval)->toBeNull();
});
