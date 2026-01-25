<?php

declare(strict_types=1);

use Maco_Openwire_Block_Component_Counter;

it('compiles single-quoted event directives', function () {
    $compiler = new Maco_Openwire_Model_Template_Compiler();
    $html = "<button @click='increment'>+1</button>";

    $counter = new Maco_Openwire_Block_Component_Counter();
    $result = $compiler->compile($html, $counter);

    expect($result)->toContain('data-ow:click="increment"');
    expect($result)->not()->toContain("@click='increment'");
});

it('compiles mustache bindings inside attributes', function () {
    $compiler = new Maco_Openwire_Model_Template_Compiler();
    $html = '<button title="{{ count }}">Title</button>';

    $counter = new Maco_Openwire_Block_Component_Counter();
    $counter->setData('count', 5);
    $result = $compiler->compile($html, $counter);

    expect($result)->toContain('title="5"');
});

it('escapes dangerous values in mustache bindings to prevent XSS', function () {
    $compiler = new Maco_Openwire_Model_Template_Compiler();
    $html = '<span>{{ count }}</span>';

    $counter = new Maco_Openwire_Block_Component_Counter();
    $counter->setData('count', '<script>alert(1)</script>');
    $result = $compiler->compile($html, $counter);

    expect($result)->toContain('&lt;script&gt;alert(1)&lt;/script&gt;');
});

it('supports single-quoted openwire root directives', function () {
    $compiler = new Maco_Openwire_Model_Template_Compiler();
    $html = "<div openwire='counter'>Content</div>";

    $counter = new Maco_Openwire_Block_Component_Counter();
    $result = $compiler->compile($html, $counter);

    expect($result)->toContain('data-ow-component="openwire_component/counter"');
    expect($result)->toContain('data-ow-id="');
    expect($result)->toContain('data-ow-config=');
});
