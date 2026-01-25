<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Request Validator for OpenWire.
 *
 * Validates incoming AJAX request payloads to ensure they contain required fields
 * and are properly structured. Includes CSRF protection via form key validation
 * for frontend requests to prevent unauthorized actions.
 *
 * @package Maco_Openwire_Model_Security
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
class Maco_Openwire_Model_Security_RequestValidator
{
    /**
     * Validate request payload.
     *
     * Performs comprehensive validation of the AJAX request payload including:
     * - Payload structure (must be array)
     * - Required 'component' field
     * - Valid 'calls' array with 'method' fields
     * - CSRF protection via form key for frontend requests
     *
     * @param mixed $payload The request payload to validate
     * @return bool True if validation passes
     * @throws Exception If validation fails with descriptive error message
     */
    public function validate($payload)
    {
        if (!is_array($payload)) {
            throw new Exception('Invalid payload');
        }

        if (!isset($payload['component'])) {
            throw new Exception('Missing component');
        }

        if (isset($payload['calls'])) {
            foreach ($payload['calls'] as $call) {
                if (!isset($call['method'])) {
                    throw new Exception('Missing method in call');
                }
            }
        }

        // Validate form_key if in frontend
        if (!Mage::app()->getStore()->isAdmin()) {
            $formKey = $payload['form_key'] ?? $payload['security']['form_key'] ?? null;
            if (!$formKey || Mage::getSingleton('core/session')->getFormKey() !== $formKey) {
                throw new Exception('Invalid form key');
            }
        }

        return true;
    }
}
