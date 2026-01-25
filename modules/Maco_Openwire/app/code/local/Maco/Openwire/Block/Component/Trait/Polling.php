<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Polling Trait for OpenWire Components.
 *
 * Enables automatic periodic updates for components by defining a polling interval.
 * Components using this trait will automatically refresh their state at regular intervals,
 * useful for displaying real-time data or status updates.
 *
 * @package Maco_Openwire_Block_Component_Trait
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
trait Maco_Openwire_Block_Component_Trait_Polling
{
    /**
     * Get poll interval in milliseconds.
     *
     * Defines how often the component should automatically refresh its state.
     * Override this method to customize the polling frequency.
     *
     * @return int|null Milliseconds between polls, or null to disable polling
     */
    public function getPollIntervalMs()
    {
        return 5000; // Default 5 seconds
    }

    /**
     * Check if polling is enabled.
     *
     * Determines whether the component should perform automatic polling based on
     * the configured poll interval.
     *
     * @return bool True if polling is enabled (interval > 0), false otherwise
     */
    public function shouldPoll()
    {
        return $this->getPollIntervalMs() > 0;
    }
}
