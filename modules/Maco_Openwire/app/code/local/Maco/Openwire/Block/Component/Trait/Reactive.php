<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Reactive Trait for OpenWire Components.
 *
 * Provides core reactivity features for components, including automatic alias generation
 * for component identification and default polling behavior. Components using this trait
 * can respond to user interactions and update their state reactively.
 *
 * @package Maco_Openwire_Block_Component_Trait
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
trait Maco_Openwire_Block_Component_Trait_Reactive
{
    /**
     * Get component alias.
     *
     * Generates a unique alias for the component based on its class name,
     * used for identification in AJAX requests and template compilation.
     * The alias follows the format 'openwire_component/{lowercase_class_name}'.
     *
     * @return string Component alias for identification
     */
    protected function getComponentAlias()
    {
        // Default to class name without namespace
        $class = get_class($this);
        $parts = explode('_', $class);
        return 'openwire_component/' . strtolower(end($parts));
    }

    /**
     * Get poll interval in milliseconds.
     *
     * Defines the polling interval for reactive updates. By default, reactive components
     * do not poll; override this method to enable periodic updates.
     *
     * @return int|null Milliseconds between polls, null means no polling
     */
    protected function getPollIntervalMs()
    {
        return null;
    }
}
