<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Component Reference for OpenWire.
 *
 * Represents a reference to a component or legacy block that can be resolved and rendered.
 * Enables progressive reactivity by allowing legacy blocks to be treated as components.
 *
 * @package Maco_Openwire_Model
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
class Maco_Openwire_Model_ComponentRef
{
    /**
     * Reference type: 'component' or 'legacy'
     *
     * @var string
     */
    private string $type;

    /**
     * Component alias or block name
     *
     * @var string
     */
    private string $alias;

    /**
     * Layout handle (for legacy blocks)
     *
     * @var string|null
     */
    private ?string $handle;

    /**
     * Instance key (for multiple instances)
     *
     * @var string|null
     */
    private ?string $instanceKey;

    /**
     * Constructor.
     *
     * @param string $type 'component' or 'legacy'
     * @param string $alias Component alias or block name
     * @param string|null $handle Layout handle
     * @param string|null $instanceKey Instance key
     */
    public function __construct(string $type, string $alias, ?string $handle = null, ?string $instanceKey = null)
    {
        $this->type = $type;
        $this->alias = $alias;
        $this->handle = $handle;
        $this->instanceKey = $instanceKey;
    }

    /**
     * Create from string representation.
     *
     * @param string $refString e.g., 'component:catalog/product_list' or 'legacy:catalog/product_list:main'
     * @return self
     */
    public static function fromString(string $refString): self
    {
        $parts = explode(':', $refString, 4);
        if (count($parts) < 2) {
            throw new InvalidArgumentException('Invalid component ref string');
        }

        $type = $parts[0];
        $alias = $parts[1];
        $handle = $parts[2] ?? null;
        $instanceKey = $parts[3] ?? null;

        return new self($type, $alias, $handle, $instanceKey);
    }

    /**
     * Get deterministic ID.
     *
     * @return string
     */
    public function getId(): string
    {
        $id = $this->type . ':' . $this->alias;
        if ($this->handle) {
            $id .= ':' . $this->handle;
        }
        if ($this->instanceKey) {
            $id .= ':' . $this->instanceKey;
        }
        return $id;
    }

    /**
     * Resolve to a block instance.
     *
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    public function resolve(): Mage_Core_Block_Abstract
    {
        if ($this->type === 'component') {
            $block = Mage::app()->getLayout()->createBlock($this->alias);
            if (!$block) {
                throw new Exception(sprintf("Component '%s' not found", $this->alias));
            }
            if (!$block instanceof Maco_Openwire_Block_Component_Abstract) {
                throw new Exception(sprintf("Component '%s' is not a valid OpenWire component", $this->alias));
            }
            return $block;
        } elseif ($this->type === 'legacy') {
            // For legacy, wrap in LegacyWrapper
            $wrappedBlock = Mage::app()->getLayout()->createBlock($this->alias);
            if (!$wrappedBlock) {
                throw new Exception(sprintf("Legacy block '%s' not found", $this->alias));
            }

            $wrapper = Mage::app()->getLayout()->createBlock('openwire/legacyWrapper');
            $wrapper->setWrappedBlock($wrappedBlock);
            $wrapper->setComponentRef($this);
            return $wrapper;
        } else {
            throw new Exception(sprintf("Unknown component type '%s'", $this->type));
        }
    }

    /**
     * Check if this is a legacy block.
     *
     * @return bool
     */
    public function isLegacy(): bool
    {
        return $this->type === 'legacy';
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Get handle.
     *
     * @return string|null
     */
    public function getHandle(): ?string
    {
        return $this->handle;
    }

    /**
     * Get instance key.
     *
     * @return string|null
     */
    public function getInstanceKey(): ?string
    {
        return $this->instanceKey;
    }
}