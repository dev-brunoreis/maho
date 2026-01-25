<?php

declare(strict_types=1);

/**
 * Transform openwire="alias" into component attributes (data-ow-component, data-ow-id, data-ow-config, x-data)
 */
class Maco_Openwire_Model_Template_Transformer_RootDirectiveTransformer
{
    public function transform(string $html, Maco_Openwire_Block_Component_Abstract $component): string
    {
        $pattern = '/\s*openwire\s*=\s*("|\')([^"\']+)\1/';

        $config = $component->getOpenwireConfig();
        $componentAlias = $config['component'] ?? '';
        $id = $config['id'] ?? '';

        return preg_replace_callback($pattern, function ($matches) use ($config, $id, $componentAlias) {
            return sprintf(' data-ow-component="%s" data-ow-id="%s" data-ow-config=\'%s\' x-data="{}"', $componentAlias, $id, json_encode($config));
        }, $html);
    }
}
