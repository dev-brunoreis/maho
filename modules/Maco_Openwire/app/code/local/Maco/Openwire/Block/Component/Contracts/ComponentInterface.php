<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Component Interface for OpenWire.
 *
 * Defines the contract for OpenWire components, ensuring they support mounting with props,
 * hydrating and dehydrating state, and rendering payloads for reactive updates.
 *
 * @package Maco_Openwire_Block_Component_Contracts
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
interface Maco_Openwire_Block_Component_Contracts_ComponentInterface
{
    /**
     * Mount the component with initial props.
     *
     * This method is called to initialize the component with external data or configuration
     * passed from the parent context or template.
     *
     * @param array $props Associative array of initial properties
     * @return void
     */
    public function mount(array $props = []);

    /**
     * Hydrate the component with state.
     *
     * Restores the component's internal state from a previously dehydrated state array,
     * typically during AJAX requests to maintain component state across interactions.
     *
     * @param array $state Associative array of state data
     * @return void
     */
    public function hydrate(array $state);

    /**
     * Dehydrate the component state.
     *
     * Extracts the current state of the component into an array for serialization,
     * typically for storage or transmission in AJAX responses.
     *
     * @return array Associative array representing the component's current state
     */
    public function dehydrate();

    /**
     * Render the payload for AJAX response.
     *
     * Generates the complete payload to be sent back to the client after an action,
     * including rendered HTML, current state, and any metadata like polling intervals.
     *
     * @return array Payload array with 'html', 'state', and 'meta' keys
     */
    public function renderPayload();
}
