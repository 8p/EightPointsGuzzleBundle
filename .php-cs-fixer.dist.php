<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR12' => true,
//         '@Symfony' => true,
//        'phpdoc_annotation_without_dot' => false,
    ])
    ->setFinder($finder);
