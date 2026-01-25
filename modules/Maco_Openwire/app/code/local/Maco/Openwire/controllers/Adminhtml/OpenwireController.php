<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Admin OpenWire Controller.
 *
 * Handles OpenWire AJAX requests in the Magento admin area. Similar to the frontend
 * UpdateController but includes admin-specific authorization checks and ACL validation.
 * Processes component updates with additional security measures for admin context.
 *
 * @package Maco_Openwire_Adminhtml
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
class Maco_Openwire_Adminhtml_OpenwireController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Check ACL.
     *
     * Verifies admin user permissions for OpenWire system access.
     *
     * @return bool True if admin has permission, false otherwise
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/openwire');
    }

    /**
     * Update action.
     *
     * Admin endpoint for OpenWire AJAX requests. Similar to frontend update but includes
     * component-level authorization checks in addition to request validation.
     * Processes admin component updates with enhanced security for backend operations.
     *
     * @return void
     */
    public function updateAction()
    {
        try {
            $payload = json_decode($this->getRequest()->getRawBody(), true);
            if (!$payload) {
                throw new Exception('Invalid JSON');
            }

            // Validate request (admin has session check)
            $validator = Mage::getModel('openwire/security_requestValidator');
            $validator->validate($payload);

            // Create component
            $componentAlias = $payload['component'];
            $component = $this->_createComponent($componentAlias, $payload);

            // Check authorization
            if (!$component->authorize()) {
                throw new Exception('Unauthorized');
            }

            // Mount and hydrate
            $component->mount($payload['props'] ?? []);
            if (isset($payload['initial_state'])) {
                $component->hydrate($payload['initial_state']);
            } elseif ($component->isStateful()) {
                $component->loadState();
            }

            // Execute actions
            if (isset($payload['calls'])) {
                foreach ($payload['calls'] as $call) {
                    $component->executeAction($call['method'], $call['params'] ?? []);
                }
            }

            // Render
            $response = $component->renderPayload();

            // Persist state if stateful
            if ($component->isStateful()) {
                $component->persistState();
            }

            $this->getResponse()->setHeader('Content-Type', 'application/json');
            $this->getResponse()->setBody(json_encode($response));

        } catch (Exception $e) {
            $this->_errorResponse($e->getMessage());
        }
    }

    /**
     * Create component instance.
     *
     * Instantiates a component block from the Magento layout using the provided alias.
     * Validates that the created block is a valid OpenWire component and sets the ID if provided.
     *
     * @param string $alias The component alias (block class name)
     * @param array $payload The request payload containing component data
     * @return Maco_Openwire_Block_Component_Abstract The instantiated component
     * @throws Exception If component cannot be created or is invalid
     */
    protected function _createComponent($alias, $payload)
    {
        $component = Mage::app()->getLayout()->createBlock($alias);
        if (!$component) {
            throw new Exception(sprintf("Component '%s' not found", $alias));
        }
        if (!$component instanceof Maco_Openwire_Block_Component_Abstract) {
            throw new Exception(sprintf("Component '%s' is not a valid OpenWire component", $alias));
        }
        if (isset($payload['id'])) {
            $component->setId($payload['id']);
        }

        return $component;
    }

    /**
     * Send error response.
     *
     * Returns a JSON error response with HTTP 400 status when request processing fails.
     *
     * @param string $message The error message to return
     * @return void
     */
    protected function _errorResponse($message)
    {
        $this->getResponse()->setHttpResponseCode(Maco_Openwire_Model_HttpStatusEnum::BAD_REQUEST->value);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->setBody(json_encode(['error' => $message]));
    }
}
