<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Mode Resolver for OpenWire Bridge.
 *
 * Determines the render mode ('html' or 'data') based on request preferences,
 * component defaults, and global configuration.
 *
 * @package Maco_Openwire_Model_Bridge
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
class Maco_Openwire_Model_Bridge_ModeResolver
{
    /**
     * Default global mode
     */
    private const DEFAULT_MODE = 'html';

    /**
     * Valid modes
     */
    private const VALID_MODES = ['html', 'data'];

    /**
     * Resolve the render mode for a request.
     *
     * Priority order:
     * 1. Request meta.mode_preference
     * 2. Component default mode (if component)
     * 3. Global default (html)
     *
     * @param Maco_Openwire_Model_Bridge_Request $request
     * @param mixed $component
     * @return string 'html' or 'data'
     */
    public function resolve(
        Maco_Openwire_Model_Bridge_Request $request,
        $component
    ): string {
        // 1. Check request preference
        $modePreference = $request->getModePreference();
        if ($modePreference && in_array($modePreference, self::VALID_MODES, true)) {
            return $modePreference;
        }

        // 2. Check component default (only for OpenWire components)
        if ($component instanceof Maco_Openwire_Block_Component_Abstract) {
            $componentDefault = $component->getDefaultMode();
            if ($componentDefault && in_array($componentDefault, self::VALID_MODES, true)) {
                return $componentDefault;
            }
        }

        // 3. Global default
        return self::DEFAULT_MODE;
    }

    /**
     * Get global default mode.
     *
     * @return string
     */
    public function getGlobalDefault(): string
    {
        return self::DEFAULT_MODE;
    }

    /**
     * Check if mode is valid.
     *
     * @param string $mode
     * @return bool
     */
    public function isValidMode(string $mode): bool
    {
        return in_array($mode, self::VALID_MODES, true);
    }
}
