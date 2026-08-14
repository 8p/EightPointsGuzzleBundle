<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR12' => true,
         '@Symfony' => true,
        'phpdoc_annotation_without_dot' => false,
        'phpdoc_summary' => false,
        'phpdoc_align' => false,
        'yoda_style' => false,
        'concat_space' => ['spacing' => 'one'],
        'single_line_throw' => false,
        'global_namespace_import' => ['import_classes' => false, 'import_constants' => false, 'import_functions' => null],
        'fully_qualified_strict_types' => ['import_symbols' => true],
        // @TODO enabled after update to PHP 8.0+
        'trailing_comma_in_multiline' => false,
    ])
    ->setFinder($finder);
