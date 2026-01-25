<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

/**
 * Template Compiler for OpenWire.
 *
 * Compiles declarative HTML templates with OpenWire directives into operational
 * HTML with Alpine.js attributes and data bindings. Handles event directives (@click),
 * text bindings ({{ variable }}), and component root directives (openwire="alias").
 *
 * @package Maco_Openwire_Model_Template
 * @author Maco Studios
 * @copyright 2026 Maco Studios
 * @license MIT
 * @link https://github.com/maco-studios/openwire
 */
class Maco_Openwire_Model_Template_Compiler
{
    /**
     * Compile declarative HTML to operational HTML.
     *
     * Transforms OpenWire template syntax into browser-executable HTML by:
     * - Converting @event directives to data-ow:event attributes
     * - Replacing {{ variable }} bindings with actual values (if component provided)
     * - Expanding openwire="alias" to full component attributes and config (if component provided)
     *
     * @param string $html The declarative HTML template
     * @param Maco_Openwire_Block_Component_Abstract|null $component The component instance for data binding (null for legacy)
     * @return string The compiled operational HTML
     */
    public function compile(string $html, ?Maco_Openwire_Block_Component_Abstract $component = null): string
    {
        // Delegate to the structured Template Engine (lexer->parser->transform->render pipeline)
        $engine = new Maco_Openwire_Model_Template_Engine();
        return $engine->compile($html, $component);
    }

    /**
     * Compile @event directives.
     *
     * Converts OpenWire event directives like @click="action" into
     * data-ow:click="action" attributes that the JavaScript runtime can handle.
     *
     * @param string $html The HTML containing event directives
     * @return string HTML with compiled event attributes
     */
    private function compileEventDirectives(string $html): string
    {
        // Match @event="action" patterns
        $pattern = '/@(\w+)\s*=\s*"([^"]+)"/';

        return preg_replace_callback($pattern, function ($matches) {
            $event = $matches[1];
            $action = $matches[2];

            return sprintf('data-ow:%s="%s"', $event, $action);
        }, $html);
    }

    /**
     * Compile {{ variable }} text bindings.
     *
     * Replaces mustache-style variable bindings with actual values from the component's
     * data store, properly escaped for HTML output.
     *
     * @param string $html The HTML containing text bindings
     * @param Maco_Openwire_Block_Component_Abstract $component The component for data retrieval
     * @return string HTML with resolved variable values
     */
    private function compileTextBindings(string $html, Maco_Openwire_Block_Component_Abstract $component): string
    {
        // Match {{ variable }} patterns
        $pattern = '/\{\{\s*(\w+)\s*\}\}/';

        return preg_replace_callback($pattern, function ($matches) use ($component) {
            $variable = $matches[1];
            $value = $component->getData($variable) ?? '';
            return htmlspecialchars((string) $value, ENT_QUOTES);
        }, $html);
    }

    /**
     * Compile openwire="alias" root directive.
     *
     * Expands the openwire root directive into full component attributes including
     * component alias, unique ID, configuration JSON, and Alpine.js initialization.
     *
     * @param string $html The HTML containing the root directive
     * @param Maco_Openwire_Block_Component_Abstract $component The component instance
     * @return string HTML with expanded component attributes
     */
    private function compileRootDirective(string $html, Maco_Openwire_Block_Component_Abstract $component): string
    {
        // Match openwire="alias" patterns and replace with full attributes
        $pattern = '/\s*openwire\s*=\s*"([^"]+)"/';

        $config = $component->getOpenwireConfig();
        $componentAlias = $config['component'];
        $id = $config['id'];

        return preg_replace_callback($pattern, function ($matches) use ($config, $id, $componentAlias) {
            return sprintf(' data-ow-component="%s" data-ow-id="%s" data-ow-config=\'%s\' x-data="{}"', $componentAlias, $id, json_encode($config));
        }, $html);
    }
}
