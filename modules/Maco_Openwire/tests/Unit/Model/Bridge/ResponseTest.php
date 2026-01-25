<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

use Maco_Openwire_Model_Bridge_Response;

it('creates success response correctly', function () {
    $response = Maco_Openwire_Model_Bridge_Response::success(
        'html',
        '<div>content</div>',
        null,
        ['count' => 1],
        ['component_id' => 'ow_123']
    );

    expect($response->isOk())->toBeTrue();
    expect($response->getMode())->toBe('html');
    expect($response->getHtml())->toBe('<div>content</div>');
    expect($response->getData())->toBeNull();
    expect($response->getState())->toBe(['count' => 1]);
    expect($response->getMeta())->toBe(['component_id' => 'ow_123']);
    expect($response->getErrors())->toBe([]);
});

it('creates error response correctly', function () {
    $errors = [['code' => 'INVALID_ACTION', 'message' => 'Action not allowed']];
    $response = Maco_Openwire_Model_Bridge_Response::error($errors, ['trace_id' => 'abc']);

    expect($response->isOk())->toBeFalse();
    expect($response->getMode())->toBe('html');
    expect($response->getHtml())->toBeNull();
    expect($response->getData())->toBeNull();
    expect($response->getState())->toBe([]);
    expect($response->getMeta())->toBe(['trace_id' => 'abc']);
    expect($response->getErrors())->toBe($errors);
});

it('serializes to array consistently', function () {
    $response = new Maco_Openwire_Model_Bridge_Response(
        true,
        'data',
        null,
        ['items' => [1, 2, 3]],
        ['selected' => 1],
        ['timestamp' => 123456],
        []
    );

    $array = $response->toArray();

    expect($array)->toBe([
        'ok' => true,
        'mode' => 'data',
        'html' => null,
        'data' => ['items' => [1, 2, 3]],
        'state' => ['selected' => 1],
        'meta' => ['timestamp' => 123456],
        'errors' => [],
    ]);
});

it('always contains required fields in array', function () {
    $response = Maco_Openwire_Model_Bridge_Response::success('html', 'html', null);

    $array = $response->toArray();

    expect($array)->toHaveKeys(['ok', 'mode', 'html', 'data', 'state', 'meta', 'errors']);
});
