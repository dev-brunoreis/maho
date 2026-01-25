<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Bridge Request DTO for OpenWire.
 *
 * Normalizes and validates incoming bridge requests with a fixed schema.
 * Provides type-safe access to request fields and enforces required structures.
 *
 * @package Maco_Openwire_Model_Bridge
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
class Maco_Openwire_Model_Bridge_Request
{
    /**
     * Component alias (e.g., 'catalog/product_list') or ref string
     *
     * @var string
     */
    private string $component;

    /**
     * Parsed component reference
     *
     * @var Maco_Openwire_Model_ComponentRef|null
     */
    private ?Maco_Openwire_Model_ComponentRef $componentRef;

    /**
     * Component instance ID
     *
     * @var string|null
     */
    private ?string $componentId;

    /**
     * Action to execute (null for render/refresh)
     *
     * @var string|null
     */
    private ?string $action;

    /**
     * Calls array (alternative to action)
     *
     * @var array
     */
    private array $calls;

    /**
     * User input payload
     *
     * @var array
     */
    private array $payload;

    /**
     * Client-side state or state token
     *
     * @var array
     */
    private array $state;

    /**
     * Initial props
     *
     * @var array
     */
    private array $props;

    /**
     * Metadata (ui, mode_preference, trace_id, etc.)
     *
     * @var array
     */
    private array $meta;

    /**
     * Security data (form_key, signatures, etc.)
     *
     * @var array
     */
    private array $security;

    /**
     * Context data (store_id, currency, customer_group - optional)
     *
     * @var array
     */
    private array $context;

    /**
     * Constructor.
     *
     * @param array $data Raw request data
     * @throws InvalidArgumentException If required fields are missing or invalid
     */
    public function __construct(array $data)
    {
        $this->validateAndNormalize($data);
    }

    /**
     * Validate and normalize input data.
     *
     * @param array $data
     * @throws InvalidArgumentException
     */
    private function validateAndNormalize(array $data): void
    {
        // Required: component
        if (!isset($data['component']) || !is_string($data['component'])) {
            throw new InvalidArgumentException('Missing or invalid component');
        }
        $this->component = $data['component'];

        // Parse component ref if applicable
        if (strpos($this->component, ':') !== false) {
            try {
                $this->componentRef = Maco_Openwire_Model_ComponentRef::fromString($this->component);
            } catch (InvalidArgumentException $e) {
                throw new InvalidArgumentException('Invalid component ref: ' . $e->getMessage());
            }
        } else {
            $this->componentRef = null;
        }

        // Optional: component_id
        $componentId = $data['component_id'] ?? null;
        if ($componentId !== null && !is_string($componentId)) {
            throw new InvalidArgumentException('Invalid component_id');
        }
        $this->componentId = $componentId;

        // Optional: action
        $action = $data['action'] ?? null;
        if ($action !== null && !is_string($action)) {
            throw new InvalidArgumentException('Invalid action');
        }
        $this->action = $action;

        // Optional: calls (alternative to action)
        $calls = $data['calls'] ?? [];
        if (!is_array($calls)) {
            throw new InvalidArgumentException('Invalid calls');
        }
        $this->calls = $calls;

        // If no action but calls, set action to first call's method
        if (!$this->action && !empty($this->calls)) {
            $firstCall = $this->calls[0];
            if (isset($firstCall['method'])) {
                $this->action = $firstCall['method'];
                $this->payload = $firstCall['params'] ?? [];
            }
        }

        // Optional: payload (default empty array)
        $payload = $data['payload'] ?? [];
        if (!is_array($payload)) {
            throw new InvalidArgumentException('Invalid payload');
        }
        $this->payload = $payload;

        // Optional: state (default empty array)
        $state = $data['state'] ?? [];
        if (!is_array($state)) {
            throw new InvalidArgumentException('Invalid state');
        }
        $this->state = $state;

        // Optional: props (default empty array)
        $props = $data['props'] ?? [];
        if (!is_array($props)) {
            throw new InvalidArgumentException('Invalid props');
        }
        $this->props = $props;

        // Optional: meta (default empty array)
        $meta = $data['meta'] ?? [];
        if (!is_array($meta)) {
            throw new InvalidArgumentException('Invalid meta');
        }
        $this->meta = $meta;

        // Optional: security (default empty array)
        $security = $data['security'] ?? [];
        if (!is_array($security)) {
            throw new InvalidArgumentException('Invalid security');
        }
        $this->security = $security;

        // Optional: context (default empty array)
        $context = $data['context'] ?? [];
        if (!is_array($context)) {
            throw new InvalidArgumentException('Invalid context');
        }
        $this->context = $context;

        // Size limits for DoS protection
        $this->enforceSizeLimits();
    }

    /**
     * Enforce size limits on payload and state.
     *
     * @throws InvalidArgumentException If limits exceeded
     */
    private function enforceSizeLimits(): void
    {
        $maxSize = 1024 * 1024; // 1MB
        $maxDepth = 10;

        if ($this->getArraySize($this->payload) > $maxSize) {
            throw new InvalidArgumentException('Payload too large');
        }
        if ($this->getArrayDepth($this->payload) > $maxDepth) {
            throw new InvalidArgumentException('Payload too deep');
        }

        if ($this->getArraySize($this->state) > $maxSize) {
            throw new InvalidArgumentException('State too large');
        }
        if ($this->getArrayDepth($this->state) > $maxDepth) {
            throw new InvalidArgumentException('State too deep');
        }
    }

    /**
     * Get approximate size of array in bytes.
     *
     * @param array $array
     * @return int
     */
    private function getArraySize(array $array): int
    {
        return strlen(json_encode($array));
    }

    /**
     * Get maximum depth of array.
     *
     * @param array $array
     * @param int $depth
     * @return int
     */
    private function getArrayDepth(array $array, int $depth = 0): int
    {
        $maxDepth = $depth;
        foreach ($array as $value) {
            if (is_array($value)) {
                $maxDepth = max($maxDepth, $this->getArrayDepth($value, $depth + 1));
            }
        }
        return $maxDepth;
    }

    // Getters

    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * Get parsed component reference.
     *
     * @return Maco_Openwire_Model_ComponentRef|null
     */
    public function getComponentRef(): ?Maco_Openwire_Model_ComponentRef
    {
        return $this->componentRef;
    }

    public function getComponentId(): ?string
    {
        return $this->componentId;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * Get calls array.
     *
     * @return array
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getState(): array
    {
        return $this->state;
    }

    public function getProps(): array
    {
        return $this->props;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getSecurity(): array
    {
        return $this->security;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get mode preference from meta.
     *
     * @return string|null 'html' or 'data'
     */
    public function getModePreference(): ?string
    {
        return $this->meta['mode_preference'] ?? null;
    }

    /**
     * Get trace ID from meta.
     *
     * @return string|null
     */
    public function getTraceId(): ?string
    {
        return $this->meta['trace_id'] ?? null;
    }
}
