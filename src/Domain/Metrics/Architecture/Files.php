<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Architecture;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\OneObjectStructurePerFileSniff;

/**
 * @internal
 */
final class Files implements HasValue, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return (string) count($collector->getFiles());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            OneObjectStructurePerFileSniff::class,
        ];
    }
}
