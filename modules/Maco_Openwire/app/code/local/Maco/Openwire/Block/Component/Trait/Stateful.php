<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Stateful Trait for OpenWire Components.
 *
 * Enables state persistence across requests for components. Stateful components
 * automatically save and restore their state using a configurable store (default: session).
 * This allows components to maintain their state between page loads and AJAX interactions.
 *
 * @package Maco_Openwire_Block_Component_Trait
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
trait Maco_Openwire_Block_Component_Trait_Stateful
{
    /**
     * @var Maco_Openwire_Model_State_StoreInterface State store instance for persistence
     */
    protected $stateStore;

    /**
     * Check if component is stateful.
     *
     * Indicates that this component maintains state across requests.
     *
     * @return bool Always returns true for stateful components
     */
    public function isStateful()
    {
        return true;
    }

    /**
     * Get state store.
     *
     * Retrieves the store used for persisting component state. Defaults to SessionStore
     * if not explicitly set.
     *
     * @return Maco_Openwire_Model_State_StoreInterface The state store instance
     */
    public function getStateStore()
    {
        if (!$this->stateStore) {
            $this->stateStore = Mage::getModel('openwire/state_sessionStore');
        }
        return $this->stateStore;
    }

    /**
     * Set state store.
     *
     * Allows injection of a custom state store implementation for testing or
     * alternative persistence mechanisms.
     *
     * @param Maco_Openwire_Model_State_StoreInterface $store The state store to use
     * @return void
     */
    public function setStateStore(Maco_Openwire_Model_State_StoreInterface $store)
    {
        $this->stateStore = $store;
    }

    /**
     * Load state from store.
     *
     * Restores the component's state from the configured store using the component's ID.
     * This is typically called during component initialization.
     *
     * @return void
     */
    public function loadState()
    {
        $state = $this->getStateStore()->load($this->getId());
        $this->hydrate($state);
    }

    /**
     * Persist state to store.
     *
     * Saves the current component state to the configured store for later retrieval.
     * This is typically called after state-changing actions.
     *
     * @return void
     */
    public function persistState()
    {
        $state = $this->dehydrate();
        $this->getStateStore()->save($this->getId(), $state);
    }
}
