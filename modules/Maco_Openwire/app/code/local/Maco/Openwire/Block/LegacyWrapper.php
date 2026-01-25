<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Legacy Wrapper Block for OpenWire.
 *
 * Wraps existing Magento blocks to enable progressive reactivity without refactoring.
 * Provides allowlist-based action policies and compiled template rendering.
 *
 * @package Maco_Openwire_Block
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
class Maco_Openwire_Block_LegacyWrapper extends Maco_Openwire_Block_Component_Abstract
{
    use Maco_Openwire_Block_Component_Trait_Reactive;
    /**
     * Component alias
     *
     * @var string
     */
    protected string $componentAlias;

    /**
     * Component reference
     *
     * @var Maco_Openwire_Model_ComponentRef|null
     */
    private ?Maco_Openwire_Model_ComponentRef $componentRef = null;

    /**
     * Allowed actions for this legacy block
     *
     * @var array
     */
    protected $_openwireAllowedActions = ['refresh', 'load'];

    /**
     * Set the wrapped block.
     *
     * @param Mage_Core_Block_Abstract $block
     * @return self
     */
    public function setWrappedBlock(Mage_Core_Block_Abstract $block): self
    {
        $this->wrappedBlock = $block;
        return $this;
    }

    /**
     * Set component ref.
     *
     * @param Maco_Openwire_Model_ComponentRef $ref
     * @return self
     */
    public function setComponentRef(Maco_Openwire_Model_ComponentRef $ref): self
    {
        $this->componentRef = $ref;
        $this->componentAlias = $ref->getAlias();
        $this->_alias = $ref->getAlias(); // For Trait_Reactive
        return $this;
    }

    /**
     * Get allowed actions.
     *
     * @return array
     */
    public function getAllowedActions(): array
    {
        return $this->_openwireAllowedActions;
    }

    /**
     * Check if stateful (legacy wrapper is not).
     *
     * @return bool
     */
    public function isStateful(): bool
    {
        return false;
    }

    /**
     * Get ID.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->componentRef ? $this->componentRef->getId() : 'legacy_wrapper';
    }

    /**
     * Render the wrapped block's HTML, compiling if necessary.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->wrappedBlock) {
            return '';
        }

        $html = $this->wrappedBlock->_toHtml();

        // Check if HTML contains OpenWire directives
        if (strpos($html, '@click') !== false || strpos($html, 'openwire=') !== false) {
            // Compile the HTML
            $compiler = Mage::getModel('openwire/template_compiler');
            $html = $compiler->compile($html);
        }

        return $html;
    }

    /**
     * Execute action (for allowlisted actions).
     *
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws Exception
     */
    public function executeAction($method, $params = [])
    {
        if (!in_array($method, $this->_openwireAllowedActions)) {
            throw new Exception("Action '$method' not allowed");
        }

        if ($method === 'refresh') {
            // For refresh, do nothing special, just re-render
            return;
        }

        if ($method === 'load') {
            // Handle load action, e.g., set product_id
            if (is_array($params) && isset($params[0]['product_id'])) {
                $this->setData('product_id', $params[0]['product_id']);
                // If wrapped block has setProductId or something
                if ($this->wrappedBlock && method_exists($this->wrappedBlock, 'setProductId')) {
                    $this->wrappedBlock->setProductId($params[0]['product_id']);
                }
            }
            return;
        }

        // For other actions, try on wrapper or block
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], $params);
        } elseif (method_exists($this->wrappedBlock, $method)) {
            return call_user_func_array([$this->wrappedBlock, $method], $params);
        } else {
            throw new Exception("Action '$method' not implemented");
        }
    }

    /**
     * Mount (placeholder).
     *
     * @param array $props
     */
    public function mount(array $props = []): void
    {
        parent::mount($props);
        // Pass props to wrapped block if it has setData
        if ($this->wrappedBlock && method_exists($this->wrappedBlock, 'setData')) {
            foreach ($props as $key => $value) {
                $this->wrappedBlock->setData($key, $value);
            }
        }
    }

    /**
     * Hydrate (placeholder).
     *
     * @param array $state
     */
    public function hydrate(array $state): void
    {
        // No-op for legacy
    }

    /**
     * Dehydrate.
     *
     * @return array
     */
    public function dehydrate(): array
    {
        return [];
    }
}