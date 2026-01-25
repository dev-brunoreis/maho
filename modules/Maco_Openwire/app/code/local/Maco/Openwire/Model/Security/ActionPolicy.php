<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Action Policy for OpenWire.
 *
 * Handles security validation for component actions, ensuring that only explicitly
 * allowed methods can be executed and preventing calls to sensitive or internal methods.
 * This prevents unauthorized access and potential security vulnerabilities.
 *
 * @package Maco_Openwire_Model_Security
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
class Maco_Openwire_Model_Security_ActionPolicy
{
    /**
     * Check if action is allowed.
     *
     * Verifies that the requested method is in the component's list of allowed actions,
     * providing a whitelist-based security mechanism.
     *
     * @param string $method The method name to check
     * @param array $allowedActions List of allowed method names
     * @return bool True if the method is allowed, false otherwise
     */
    public function isAllowed($method, $allowedActions)
    {
        return in_array($method, $allowedActions);
    }

    /**
     * Validate method name.
     *
     * Ensures the method name is safe to call by checking against a list of forbidden
     * methods (constructors, destructors, core template methods) and preventing calls
     * to private/protected methods (starting with underscore).
     *
     * @param string $method The method name to validate
     * @return bool True if the method name is valid and safe to call
     */
    public function isValidMethod($method)
    {
        // Prevent calling core methods
        return Maco_Openwire_Model_Security_ForbiddenMethodEnum::tryFrom($method) === null && !preg_match('/^_/', $method);
    }
}
