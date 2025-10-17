<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in(['app', 'routes', 'database', 'packages'])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'single_quote' => false,
        'no_unused_imports' => true,
        'braces' => array(
            'position_after_anonymous_constructs' => 'next',
            'position_after_control_structures' => 'next',
        )
    ])
    ->setFinder($finder);
