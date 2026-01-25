<?php

declare(strict_types=1);

/**
 * Transform @event directives into data-ow:event attributes.
 */
class Maco_Openwire_Model_Template_Transformer_EventTransformer
{
    public function transform(string $html): string
    {
        // Support both single and double quoted attribute values
        $pattern = '/@(\w+)\s*=\s*("|\')([^\"\']*?)\2/';

        return preg_replace_callback($pattern, function ($matches) {
            $event = $matches[1];
            $action = $matches[3];

            // Always emit double-quoted attribute for consistency
            return sprintf('data-ow:%s="%s"', $event, $action);
        }, $html);
    }
}
