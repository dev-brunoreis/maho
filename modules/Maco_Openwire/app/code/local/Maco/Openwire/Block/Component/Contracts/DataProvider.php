<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Data Provider Contract for OpenWire Components.
 *
 * Components that implement this interface can provide structured data payloads
 * for client-driven rendering (mode=data), in addition to HTML rendering.
 *
 * @package Maco_Openwire_Block_Component_Contracts
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
interface Maco_Openwire_Block_Component_Contracts_DataProvider
{
    /**
     * Get data payload for client-driven rendering.
     *
     * Returns structured data that the frontend can use to render the component
     * without server-side HTML generation. This enables frameworks like React/Vue
     * to take full control of the UI.
     *
     * @return array Structured data payload
     */
    public function getDataPayload(): array;

    /**
     * Get HTML payload for server-driven rendering (optional).
     *
     * If implemented, provides the HTML representation of the component.
     * If not implemented, falls back to _toHtml() or other rendering methods.
     *
     * @return string|null HTML string or null if not supported
     */
    public function getHtmlPayload(): ?string;
}
