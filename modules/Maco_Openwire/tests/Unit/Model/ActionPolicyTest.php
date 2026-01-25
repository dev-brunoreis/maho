<?php

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

use Maco_Openwire_Model_Security_ActionPolicy;

it('allows valid method', function () {
    $policy = new Maco_Openwire_Model_Security_ActionPolicy();
    $allowedActions = ['increment', 'decrement'];

    $result = $policy->isAllowed('increment', $allowedActions);

    expect($result)->toBeTrue();
});

it('denies method not in allowed actions', function () {
    $policy = new Maco_Openwire_Model_Security_ActionPolicy();
    $allowedActions = ['increment', 'decrement'];

    $result = $policy->isAllowed('delete', $allowedActions);

    expect($result)->toBeFalse();
});

it('validates method name', function () {
    $policy = new Maco_Openwire_Model_Security_ActionPolicy();

    expect($policy->isValidMethod('increment'))->toBeTrue();
    expect($policy->isValidMethod('_privateMethod'))->toBeFalse();
    expect($policy->isValidMethod('toHtml'))->toBeFalse();
    expect($policy->isValidMethod('setTemplate'))->toBeFalse();
});
