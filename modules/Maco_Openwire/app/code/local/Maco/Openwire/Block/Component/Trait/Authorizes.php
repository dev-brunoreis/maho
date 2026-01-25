<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Authorizes Trait for OpenWire Components.
 *
 * Provides authorization capabilities for components, allowing control over
 * whether a component can be rendered or interacted with based on user permissions
 * or context (e.g., admin area access).
 *
 * @package Maco_Openwire_Block_Component_Trait
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
trait Maco_Openwire_Block_Component_Trait_Authorizes
{
    /**
     * Authorize the component access.
     *
     * Determines whether the current user/context is allowed to access or interact
     * with this component. Override this method in components that require authorization.
     *
     * @return bool True if access is granted, false otherwise
     */
    public function authorize()
    {
        // Default: allow all
        return true;
    }

    /**
     * Check if in admin area.
     *
     * Utility method to determine if the current request is in the Magento admin area.
     * Useful for authorization logic that differs between frontend and admin contexts.
     *
     * @return bool True if in admin area, false if on frontend
     */
    protected function isAdminArea()
    {
        return Mage::app()->getStore()->isAdmin();
    }
}
