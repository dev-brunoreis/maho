<?php

declare(strict_types=1);

/**
 * Transform mustache bindings {{ var }} into escaped values from component.
 */
class Maco_Openwire_Model_Template_Transformer_MustacheTransformer
{
    public function transform(string $html, Maco_Openwire_Block_Component_Abstract $component): string
    {
        $pattern = '/\{\{\s*(\w+)\s*\}\}/';

        return preg_replace_callback($pattern, function ($matches) use ($component) {
            $variable = $matches[1];
            $value = $component->getData($variable) ?? '';

            return htmlspecialchars((string) $value, ENT_QUOTES);
        }, $html);
    }
}
