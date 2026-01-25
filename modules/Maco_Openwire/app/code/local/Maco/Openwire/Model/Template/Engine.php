<?php

declare(strict_types=1);

/**
 * Simple orchestration engine for template compilation.
 * Delegates to transformers to keep compatibility while enabling a pluggable pipeline.
 */
class Maco_Openwire_Model_Template_Engine
{
    public function compile(string $html, ?Maco_Openwire_Block_Component_Abstract $component = null): string
    {
        // Event directives are independent from component instance
        $html = (new Maco_Openwire_Model_Template_Transformer_EventTransformer())->transform($html);

        // If component provided, run component-aware transforms
        if ($component) {
            $html = (new Maco_Openwire_Model_Template_Transformer_MustacheTransformer())->transform($html, $component);
            $html = (new Maco_Openwire_Model_Template_Transformer_RootDirectiveTransformer())->transform($html, $component);
        }

        return $html;
    }
}
