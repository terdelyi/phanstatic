<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = (new Finder())
    ->in([
        'bin',
        'src',
        'tests',
    ])
    ->exclude([
        'data',
    ]);

return (new Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS2.0' => true,
        '@PhpCsFixer' => true,
        '@PHP83Migration' => true,
        'declare_strict_types' => true,
        'yoda_style' => false,
        'multiline_whitespace_before_semicolons' => false,
        'php_unit_test_class_requires_covers' => false,
        'unary_operator_spaces' => false,
        'not_operator_with_space' => true,
    ])
    ->setFinder($finder);
