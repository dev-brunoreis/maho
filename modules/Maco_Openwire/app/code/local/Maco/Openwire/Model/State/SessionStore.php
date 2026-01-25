<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Session-based State Store.
 *
 * Implements state persistence using Magento's core session storage.
 * Component states are stored in the user's session with a prefixed key
 * to avoid conflicts with other session data.
 *
 * @package Maco_Openwire_Model_State
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
class Maco_Openwire_Model_State_SessionStore implements Maco_Openwire_Model_State_StoreInterface
{
    public const SESSION_KEY_PREFIX = 'openwire_state_';

    /**
     * Load state for a component.
     *
     * Retrieves the persisted state for the given component ID from the session.
     * Returns an empty array if no state exists.
     *
     * @param string $componentId Unique identifier for the component
     * @return array The component's persisted state data
     */
    public function load($componentId)
    {
        $session = Mage::getSingleton('core/session');
        $key = self::SESSION_KEY_PREFIX . $componentId;
        return $session->getData($key) ?: [];
    }

    /**
     * Save state for a component.
     *
     * Persists the component's state data to the session storage.
     *
     * @param string $componentId Unique identifier for the component
     * @param array $state The state data to persist
     * @return void
     */
    public function save($componentId, $state)
    {
        $session = Mage::getSingleton('core/session');
        $key = self::SESSION_KEY_PREFIX . $componentId;
        $session->setData($key, $state);
    }

    /**
     * Forget state for a component.
     *
     * Removes the persisted state for the given component ID from the session.
     * Useful for cleanup or resetting component state.
     *
     * @param string $componentId Unique identifier for the component
     * @return void
     */
    public function forget($componentId)
    {
        $session = Mage::getSingleton('core/session');
        $key = self::SESSION_KEY_PREFIX . $componentId;
        $session->unsetData($key);
    }
}
