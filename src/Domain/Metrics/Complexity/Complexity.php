<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Complexity;

use NunoMaduro\PhpInsights\Domain\Collector;
use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Insights\CyclomaticComplexityIsHigh;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Metrics\CyclomaticComplexitySniff;

/**
 * @internal
 */
final class Complexity implements HasAvg, HasInsights
{
    /**
     * {@inheritdoc}
     */
    public function getAvg(Collector $collector): string
    {
        return sprintf('%.2f', $collector->getAverageComplexityPerMethod());
    }

    /**
     * {@inheritdoc}
     */
    public function getInsights(): array
    {
        return [
            CyclomaticComplexityIsHigh::class,
            CyclomaticComplexitySniff::class,
        ];
    }
}
