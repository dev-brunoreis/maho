<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

use Maco_Openwire_Block_Component_Counter;

it('compiles @click directive correctly', function () {
    $compiler = new Maco_Openwire_Model_Template_Compiler();
    $html = '<button @click="increment">+1</button>';

    $counter = new Maco_Openwire_Block_Component_Counter();
    $result = $compiler->compile($html, $counter);

    expect($result)->toContain('data-ow:click="increment"');
    expect($result)->not()->toContain('@click="increment"');
});

it('compiles multiple event directives', function () {
    $compiler = new Maco_Openwire_Model_Template_Compiler();
    $html = '<button @click="increment" @blur="validate">Submit</button>';

    $counter = new Maco_Openwire_Block_Component_Counter();
    $result = $compiler->compile($html, $counter);

    expect($result)->toContain('data-ow:click="increment"');
    expect($result)->toContain('data-ow:blur="validate"');
    expect($result)->not()->toContain('@click');
    expect($result)->not()->toContain('@blur');
});

it('compiles {{ variable }} bindings to actual values', function () {
    $compiler = new Maco_Openwire_Model_Template_Compiler();
    $html = '<span>{{ count }}</span>';

    $counter = new Maco_Openwire_Block_Component_Counter();
    $counter->setData('count', 42);
    $result = $compiler->compile($html, $counter);

    expect($result)->toContain('<span>42</span>');
    expect($result)->not()->toContain('{{ count }}');
});

it('compiles openwire root directive', function () {
    $compiler = new Maco_Openwire_Model_Template_Compiler();
    $html = '<div openwire="counter">Content</div>';

    $counter = new Maco_Openwire_Block_Component_Counter();
    $result = $compiler->compile($html, $counter);

    expect($result)->toContain('data-ow-component="openwire_component/counter"');
    expect($result)->toContain('data-ow-id="');
    expect($result)->toContain('data-ow-config=');
    expect($result)->toContain('x-data="{}"');
    expect($result)->not()->toContain(' openwire="counter"');
});

it('handles HTML without directives unchanged', function () {
    $compiler = new Maco_Openwire_Model_Template_Compiler();
    $html = '<div class="normal">Normal content</div>';

    $counter = new Maco_Openwire_Block_Component_Counter();
    $result = $compiler->compile($html, $counter);

    expect($result)->toBe('<div class="normal">Normal content</div>');
});

it('handles empty variable bindings gracefully', function () {
    $compiler = new Maco_Openwire_Model_Template_Compiler();
    $html = '<span>{{ nonexistent }}</span>';

    $counter = new Maco_Openwire_Block_Component_Counter();
    $result = $compiler->compile($html, $counter);

    expect($result)->toContain('<span></span>');
});
