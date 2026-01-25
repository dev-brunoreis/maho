<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Bridge Response DTO for OpenWire.
 *
 * Standardizes outgoing bridge responses with a fixed schema.
 * Ensures consistent structure for frontend consumption.
 *
 * @package Maco_Openwire_Model_Bridge
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
class Maco_Openwire_Model_Bridge_Response
{
    /**
     * Success flag
     *
     * @var bool
     */
    private bool $ok;

    /**
     * Render mode: 'html' or 'data'
     *
     * @var string
     */
    private string $mode;

    /**
     * HTML content (null for data mode)
     *
     * @var string|null
     */
    private ?string $html;

    /**
     * Data payload (null for html mode)
     *
     * @var array|null
     */
    private ?array $data;

    /**
     * Component state
     *
     * @var array
     */
    private array $state;

    /**
     * Metadata (timestamps, component_id, checksum, poll interval, etc.)
     *
     * @var array
     */
    private array $meta;

    /**
     * Errors array
     *
     * @var array
     */
    private array $errors;

    /**
     * Constructor.
     *
     * @param bool $ok
     * @param string $mode
     * @param string|null $html
     * @param array|null $data
     * @param array $state
     * @param array $meta
     * @param array $errors
     */
    public function __construct(
        bool $ok,
        string $mode,
        ?string $html,
        ?array $data,
        array $state = [],
        array $meta = [],
        array $errors = []
    ) {
        $this->ok = $ok;
        $this->mode = $mode;
        $this->html = $html;
        $this->data = $data;
        $this->state = $state;
        $this->meta = $meta;
        $this->errors = $errors;
    }

    /**
     * Create success response.
     *
     * @param string $mode
     * @param string|null $html
     * @param array|null $data
     * @param array $state
     * @param array $meta
     * @return self
     */
    public static function success(
        string $mode,
        ?string $html,
        ?array $data,
        array $state = [],
        array $meta = []
    ): self {
        return new self(true, $mode, $html, $data, $state, $meta, []);
    }

    /**
     * Create error response.
     *
     * @param array $errors
     * @param array $meta
     * @return self
     */
    public static function error(array $errors, array $meta = []): self
    {
        return new self(false, 'html', null, null, [], $meta, $errors);
    }

    /**
     * Serialize to array for JSON encoding.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'ok' => $this->ok,
            'mode' => $this->mode,
            'html' => $this->html,
            'data' => $this->data,
            'state' => $this->state,
            'meta' => $this->meta,
            'errors' => $this->errors,
        ];
    }

    // Getters

    public function isOk(): bool
    {
        return $this->ok;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function getState(): array
    {
        return $this->state;
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
