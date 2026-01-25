<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Counter Action Enum.
 *
 * Defines the allowed actions for the Counter component.
 * Provides type-safe constants for counter operations.
 *
 * @package Maco_Openwire_Block_Component
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
enum Maco_Openwire_Block_Component_CounterActionEnum: string
{
    case INCREMENT = 'increment';
    case DECREMENT = 'decrement';
}
