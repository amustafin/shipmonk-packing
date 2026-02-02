<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Option;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/run.php',
    ])
    ->withRootFiles()
    ->withPreparedSets(
        psr12: true,
        arrays: true,
        comments: true,
        docblocks: true,
        spaces: true,
        namespaces: true,
    )
    ->withPhpCsFixerSets(
        php84Migration: true,
    )
    ->withSpacing(
        indentation: Option::INDENTATION_SPACES,
        lineEnding: "\n",
    )
    ->withConfiguredRule(
        LineLengthFixer::class,
        [
            'line_length' => 130,
            'inline_short_lines' => false,
        ],
    )
    ->withConfiguredRule(
        OrderedClassElementsFixer::class,
        [
            'order' => [
                'use_trait',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'method',
            ],
        ],
    );

