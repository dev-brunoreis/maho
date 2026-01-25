<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * State Store Interface.
 *
 * Defines the contract for state storage implementations. Allows different
 * persistence mechanisms (session, database, cache, etc.) for component state.
 * Implementations must provide methods to load, save, and forget component states.
 *
 * @package Maco_Openwire_Model_State
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
interface Maco_Openwire_Model_State_StoreInterface
{
    /**
     * Load state for a component.
     *
     * Retrieves the persisted state data for the specified component.
     * Should return an empty array if no state exists.
     *
     * @param string $componentId Unique identifier for the component
     * @return array The component's state data
     */
    public function load($componentId);

    /**
     * Save state for a component.
     *
     * Persists the component's state data for later retrieval.
     *
     * @param string $componentId Unique identifier for the component
     * @param array $state The state data to persist
     * @return void
     */
    public function save($componentId, $state);

    /**
     * Forget state for a component.
     *
     * Removes any persisted state data for the specified component.
     *
     * @param string $componentId Unique identifier for the component
     * @return void
     */
    public function forget($componentId);
}
