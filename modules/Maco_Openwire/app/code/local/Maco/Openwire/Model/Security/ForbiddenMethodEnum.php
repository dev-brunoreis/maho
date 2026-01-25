<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Forbidden Method Enum.
 *
 * Defines methods that are forbidden to be called on components for security reasons.
 * These include core PHP magic methods and Magento template methods that should not
 * be exposed to user-initiated actions.
 *
 * @package Maco_Openwire_Model_Security
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
enum Maco_Openwire_Model_Security_ForbiddenMethodEnum: string
{
    case CONSTRUCT = '__construct';
    case DESTRUCT = '__destruct';
    case TO_HTML = 'toHtml';
    case SET_TEMPLATE = 'setTemplate';
    case GET_TEMPLATE = 'getTemplate';
    case SET_DATA = 'setData';
    case GET_DATA = 'getData';
}
