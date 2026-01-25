<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Component Runner for OpenWire Bridge.
 *
 * Orchestrates the complete lifecycle of component execution for bridge requests:
 * resolve, authorize, mount, hydrate, validate, run action, dehydrate, persist, render.
 *
 * Centralizes business logic and reduces controller complexity.
 *
 * @package Maco_Openwire_Model_Bridge
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
class Maco_Openwire_Model_Bridge_ComponentRunner
{
    /**
     * @var Maco_Openwire_Model_Bridge_ModeResolver
     */
    private Maco_Openwire_Model_Bridge_ModeResolver $modeResolver;

    /**
     * Constructor.
     *
     * @param Maco_Openwire_Model_Bridge_ModeResolver $modeResolver
     */
    public function __construct(Maco_Openwire_Model_Bridge_ModeResolver $modeResolver)
    {
        $this->modeResolver = $modeResolver;
    }

    /**
     * Run a bridge request.
     *
     * Executes the full component lifecycle and returns a standardized response.
     *
     * @param Maco_Openwire_Model_Bridge_Request $request
     * @return Maco_Openwire_Model_Bridge_Response
     * @throws Exception On errors
     */
    public function run(Maco_Openwire_Model_Bridge_Request $request): Maco_Openwire_Model_Bridge_Response
    {
        $traceId = $request->getTraceId() ?: uniqid('trace_');

        try {
            // 1. Resolve component
            $component = $this->resolveComponent($request);
            $isLegacy = $request->getComponentRef() && $request->getComponentRef()->isLegacy();

            // 2. Authorize (placeholder - implement ACL later)
            $this->authorize($request, $component, $isLegacy);

            // 3. Mount (only for fresh mounts, not actions, and not legacy)
            if (!$request->getAction() && !$isLegacy) {
                $component->mount($request->getProps());
            }

            // 4. Hydrate state (only for components)
            if (!$isLegacy) {
                if ($request->getState()) {
                    $component->hydrate($request->getState());
                } elseif ($component->isStateful()) {
                    $component->loadState();
                }
            }

            // 5. Validate payload (placeholder)
            $this->validatePayload($request->getPayload());

            // 6. Run action if specified (only for components)
            if ($request->getAction()) {
                if ($isLegacy) {
                    throw new Exception("Actions not supported for legacy blocks yet");
                }
                $component->executeAction($request->getAction(), $request->getPayload());
            }

            // 7. Dehydrate state (only for components)
            $state = $isLegacy ? [] : $component->dehydrate();

            // 8. Persist state if stateful (only for components)
            if (!$isLegacy && $component->isStateful()) {
                $component->persistState();
            }

            // 9. Render based on mode
            $mode = $this->modeResolver->resolve($request, $component);
            $renderResult = $this->render($component, $mode, $isLegacy);

            // 10. Build response
            $meta = [
                'component_id' => $request->getComponentRef() ? $request->getComponentRef()->getId() : $request->getComponent(),
                'trace_id' => $traceId,
                'timestamp' => time(),
            ];

            if ($mode === 'html') {
                return Maco_Openwire_Model_Bridge_Response::success(
                    $mode,
                    $renderResult['html'],
                    null,
                    $state,
                    $meta
                );
            } else {
                return Maco_Openwire_Model_Bridge_Response::success(
                    $mode,
                    null,
                    $renderResult['data'],
                    $state,
                    $meta
                );
            }

        } catch (Exception $e) {
            return Maco_Openwire_Model_Bridge_Response::error(
                [['code' => 'COMPONENT_ERROR', 'message' => $e->getMessage()]],
                ['trace_id' => $traceId, 'timestamp' => time()]
            );
        }
    }

    /**
     * Resolve component from request.
     *
     * @param Maco_Openwire_Model_Bridge_Request $request
     * @return Mage_Core_Block_Abstract
     * @throws Exception
     */
    private function resolveComponent(Maco_Openwire_Model_Bridge_Request $request): Mage_Core_Block_Abstract
    {
        $componentRef = $request->getComponentRef();
        if ($componentRef) {
            return $componentRef->resolve();
        } else {
            // Legacy: assume it's a component alias, try to create it
            $block = Mage::app()->getLayout()->createBlock($request->getComponent());
            if (!$block) {
                throw new Exception(sprintf("Block '%s' not found", $request->getComponent()));
            }
            // If it's already an OpenWire component, use it directly
            if ($block instanceof Maco_Openwire_Block_Component_Abstract) {
                return $block;
            } else {
                // Wrap legacy block in LegacyWrapper
                $wrapper = Mage::app()->getLayout()->createBlock('openwire/legacyWrapper');
                $wrapper->setWrappedBlock($block);
                $ref = new Maco_Openwire_Model_ComponentRef('legacy', $request->getComponent());
                $wrapper->setComponentRef($ref);
                return $wrapper;
            }
        }
    }

    /**
     * Authorize the request (placeholder).
     *
     * @param Maco_Openwire_Model_Bridge_Request $request
     * @param Mage_Core_Block_Abstract $component
     * @param bool $isLegacy
     * @throws Exception
     */
    private function authorize(Maco_Openwire_Model_Bridge_Request $request, Mage_Core_Block_Abstract $component, bool $isLegacy): void
    {
        // TODO: Implement ACL checks
        // For now, just check if action is allowed
        if ($request->getAction()) {
            if ($isLegacy) {
                // For legacy, deny actions for now
                throw new Exception("Actions not allowed for legacy blocks");
            } else {
                if (!in_array($request->getAction(), $component->getAllowedActions())) {
                    throw new Exception(sprintf("Action '%s' not allowed", $request->getAction()));
                }
            }
        }
    }

    /**
     * Validate payload (placeholder).
     *
     * @param array $payload
     * @throws Exception
     */
    private function validatePayload(array $payload): void
    {
        // TODO: Implement payload validation rules
    }

    /**
     * Render component based on mode.
     *
     * @param Mage_Core_Block_Abstract $component
     * @param string $mode
     * @param bool $isLegacy
     * @return array ['html' => string|null, 'data' => array|null]
     * @throws Exception
     */
    private function render(Mage_Core_Block_Abstract $component, string $mode, bool $isLegacy): array
    {
        if ($isLegacy) {
            // For legacy, only html mode
            return [
                'html' => $component->_toHtml(),
                'data' => null,
            ];
        }

        if ($mode === 'data') {
            if (!$component instanceof Maco_Openwire_Block_Component_Contracts_DataProvider) {
                throw new Exception("Component does not support data mode");
            }
            return [
                'html' => $component->getHtmlPayload(),
                'data' => $component->getDataPayload(),
            ];
        } else {
            // html mode
            return [
                'html' => $component->renderPayload()['html'] ?? $component->_toHtml(),
                'data' => null,
            ];
        }
    }
}
