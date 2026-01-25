<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

use Maco_Openwire_Model_Bridge_ModeResolver;
use Maco_Openwire_Model_Bridge_Request;
use Maco_Openwire_Block_Component_Abstract;

it('resolves mode by priority: request preference first', function () {
    $resolver = new Maco_Openwire_Model_Bridge_ModeResolver();

    $request = new Maco_Openwire_Model_Bridge_Request([
        'component' => 'test',
        'meta' => ['mode_preference' => 'data']
    ]);

    $component = $this->createMock(Maco_Openwire_Block_Component_Abstract::class);
    $component->method('getDefaultMode')->willReturn('html');

    $mode = $resolver->resolve($request, $component);

    expect($mode)->toBe('data');
});

it('resolves mode by priority: component default second', function () {
    $resolver = new Maco_Openwire_Model_Bridge_ModeResolver();

    $request = new Maco_Openwire_Model_Bridge_Request([
        'component' => 'test',
        'meta' => [] // no preference
    ]);

    $component = $this->createMock(Maco_Openwire_Block_Component_Abstract::class);
    $component->method('getDefaultMode')->willReturn('data');

    $mode = $resolver->resolve($request, $component);

    expect($mode)->toBe('data');
});

it('resolves mode by priority: global default last', function () {
    $resolver = new Maco_Openwire_Model_Bridge_ModeResolver();

    $request = new Maco_Openwire_Model_Bridge_Request([
        'component' => 'test',
        'meta' => [] // no preference
    ]);

    $component = $this->createMock(Maco_Openwire_Block_Component_Abstract::class);
    $component->method('getDefaultMode')->willReturn(null); // no default

    $mode = $resolver->resolve($request, $component);

    expect($mode)->toBe('html'); // global default
});

it('ignores invalid mode preference', function () {
    $resolver = new Maco_Openwire_Model_Bridge_ModeResolver();

    $request = new Maco_Openwire_Model_Bridge_Request([
        'component' => 'test',
        'meta' => ['mode_preference' => 'invalid']
    ]);

    $component = $this->createMock(Maco_Openwire_Block_Component_Abstract::class);
    $component->method('getDefaultMode')->willReturn('data');

    $mode = $resolver->resolve($request, $component);

    expect($mode)->toBe('data'); // falls to component default
});

it('validates mode correctly', function () {
    $resolver = new Maco_Openwire_Model_Bridge_ModeResolver();

    expect($resolver->isValidMode('html'))->toBeTrue();
    expect($resolver->isValidMode('data'))->toBeTrue();
    expect($resolver->isValidMode('invalid'))->toBeFalse();
});

it('returns global default correctly', function () {
    $resolver = new Maco_Openwire_Model_Bridge_ModeResolver();

    expect($resolver->getGlobalDefault())->toBe('html');
});
