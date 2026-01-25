<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

use Maco_Openwire_Model_Bridge_Request;

it('normalizes valid input correctly', function () {
    $data = [
        'component' => 'catalog/product_list',
        'component_id' => 'ow_123',
        'action' => 'increment',
        'payload' => ['count' => 1],
        'state' => ['count' => 5],
        'props' => ['initial' => 0],
        'meta' => ['mode_preference' => 'html', 'trace_id' => 'abc123'],
        'security' => ['form_key' => 'xyz'],
        'context' => ['store_id' => 1],
    ];

    $request = new Maco_Openwire_Model_Bridge_Request($data);

    expect($request->getComponent())->toBe('catalog/product_list');
    expect($request->getComponentId())->toBe('ow_123');
    expect($request->getAction())->toBe('increment');
    expect($request->getPayload())->toBe(['count' => 1]);
    expect($request->getState())->toBe(['count' => 5]);
    expect($request->getProps())->toBe(['initial' => 0]);
    expect($request->getMeta())->toBe(['mode_preference' => 'html', 'trace_id' => 'abc123']);
    expect($request->getSecurity())->toBe(['form_key' => 'xyz']);
    expect($request->getContext())->toBe(['store_id' => 1]);
    expect($request->getModePreference())->toBe('html');
    expect($request->getTraceId())->toBe('abc123');
});

it('provides defaults for optional fields', function () {
    $data = ['component' => 'test/component'];

    $request = new Maco_Openwire_Model_Bridge_Request($data);

    expect($request->getComponentId())->toBeNull();
    expect($request->getAction())->toBeNull();
    expect($request->getPayload())->toBe([]);
    expect($request->getState())->toBe([]);
    expect($request->getProps())->toBe([]);
    expect($request->getMeta())->toBe([]);
    expect($request->getSecurity())->toBe([]);
    expect($request->getContext())->toBe([]);
});

it('validates required component field', function () {
    $data = []; // missing component

    expect(fn () => new Maco_Openwire_Model_Bridge_Request($data))
        ->toThrow(InvalidArgumentException::class, 'Missing or invalid component');
});

it('validates component type', function () {
    $data = ['component' => 123]; // not string

    expect(fn () => new Maco_Openwire_Model_Bridge_Request($data))
        ->toThrow(InvalidArgumentException::class, 'Missing or invalid component');
});

it('validates payload array', function () {
    $data = ['component' => 'test', 'payload' => 'not array'];

    expect(fn () => new Maco_Openwire_Model_Bridge_Request($data))
        ->toThrow(InvalidArgumentException::class, 'Invalid payload');
});

it('validates state array', function () {
    $data = ['component' => 'test', 'state' => 'not array'];

    expect(fn () => new Maco_Openwire_Model_Bridge_Request($data))
        ->toThrow(InvalidArgumentException::class, 'Invalid state');
});

it('enforces payload size limit', function () {
    $largePayload = str_repeat('a', 1024 * 1024 + 1); // >1MB
    $data = ['component' => 'test', 'payload' => ['data' => $largePayload]];

    expect(fn () => new Maco_Openwire_Model_Bridge_Request($data))
        ->toThrow(InvalidArgumentException::class, 'Payload too large');
});

it('enforces payload depth limit', function () {
    $deepArray = [];
    $current = &$deepArray;
    for ($i = 0; $i < 12; $i++) { // >10 depth
        $current['nested'] = [];
        $current = &$current['nested'];
    }
    $data = ['component' => 'test', 'payload' => $deepArray];

    expect(fn () => new Maco_Openwire_Model_Bridge_Request($data))
        ->toThrow(InvalidArgumentException::class, 'Payload too deep');
});
