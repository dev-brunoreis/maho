<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Update Controller for OpenWire.
 *
 * Handles AJAX update requests for OpenWire components. Processes incoming payloads,
 * validates requests, instantiates components, executes actions, and returns updated
 * HTML and state. This is the core endpoint for reactive component interactions.
 *
 * @package Maco_Openwire
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
class Maco_Openwire_UpdateController extends Mage_Core_Controller_Front_Action
{
    /**
     * Update action.
     *
     * Main endpoint for OpenWire AJAX requests. Processes the request payload by:
     * - Validating the request structure and security
     * - Instantiating the target component
     * - Mounting with props and hydrating state
     * - Executing requested actions
     * - Rendering and returning the updated payload
     * - Persisting state for stateful components
     *
     * @return void
     */
    public function indexAction()
    {
        try {
            $payload = json_decode($this->getRequest()->getRawBody(), true);
            if (!$payload) {
                throw new Exception('Invalid JSON');
            }

            // Validate request
            $validator = Mage::getModel('openwire/security_requestValidator');
            $validator->validate($payload);

            // Create component
            $componentAlias = $payload['component'];
            $component = $this->_createComponent($componentAlias, $payload);

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

        // If it's already an OpenWire component, use it
        if ($component instanceof Maco_Openwire_Block_Component_Abstract) {
            if (isset($payload['id'])) {
                $component->setId($payload['id']);
            }
            return $component;
        }

        // For legacy blocks, wrap in LegacyWrapper
        $wrapper = Mage::app()->getLayout()->createBlock('openwire/legacyWrapper');
        $wrapper->setWrappedBlock($component);
        $ref = new Maco_Openwire_Model_ComponentRef('legacy', $alias);
        $wrapper->setComponentRef($ref);
        if (isset($payload['id'])) {
            $wrapper->setId($payload['id']);
        }
        return $wrapper;
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
