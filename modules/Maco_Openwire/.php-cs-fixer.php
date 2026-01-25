<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Maco Studios
 *
 * @see https://github.com/maco-studios/openwire
 */

use Ergebnis\License;
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$license = License\Type\None::text(
    License\Range::since(
        License\Year::fromString('2026'),
        new DateTimeZone('UTC')
    ),
    License\Holder::fromString('Maco Studios'),
    License\Url::fromString('https://github.com/maco-studios/openwire')
);

$finder = Finder::create()
    ->in(__DIR__)
    ->append(array_merge(
        glob(__DIR__ . '/*.php') ?: [],
        glob(__DIR__ . '/.*.php') ?: []
    ))
    ->exclude(['vendor', 'node_modules']);

$config = new Config();
$config->setFinder($finder);
$config->setRules([
    '@PSR12' => true,
    'header_comment' => [
        'comment_type' => 'PHPDoc',
        'header' => trim($license->header()),
        'location' => 'after_declare_strict',
        'separate' => 'both',
    ],
]);

return $config;
