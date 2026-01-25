<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

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

// Stub interface
if (!interface_exists('Maco_Openwire_Block_Component_Contract_ComponentInterface', false)) {
    interface Maco_Openwire_Block_Component_Contract_ComponentInterface
    {
        public function mount(array $props = []);
        public function getId();
    }
}

beforeEach(function () {
    // No need for eval, stubs defined above
});

it('component abstract has correct interface', function () {
    // Test that the abstract class exists and has expected methods
    expect(class_exists('Maco_Openwire_Block_Component_Abstract'))->toBeTrue();
    /** @phpstan-ignore-next-line */
    expect(method_exists('Maco_Openwire_Block_Component_Abstract', 'mount'))->toBeTrue();
    /** @phpstan-ignore-next-line */
    expect(method_exists('Maco_Openwire_Block_Component_Abstract', 'hydrate'))->toBeTrue();
    /** @phpstan-ignore-next-line */
    expect(method_exists('Maco_Openwire_Block_Component_Abstract', 'dehydrate'))->toBeTrue();
    /** @phpstan-ignore-next-line */
    expect(method_exists('Maco_Openwire_Block_Component_Abstract', 'renderPayload'))->toBeTrue();
    /** @phpstan-ignore-next-line */
    expect(method_exists('Maco_Openwire_Block_Component_Abstract', 'executeAction'))->toBeTrue();
});

it('component abstract has allowed actions property', function () {
    $reflection = new ReflectionClass('Maco_Openwire_Block_Component_Abstract');
    expect($reflection->hasProperty('openwireAllowedActions'))->toBeTrue();
});
