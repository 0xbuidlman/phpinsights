<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Metrics\Structure;

use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Collector;

/**
 * @internal
 */
final class ClassesNormal implements HasValue, HasPercentage
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Collector $collector): string
    {
        return sprintf('%d', count($collector->getConcreteNonFinalClasses()));
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Collector $collector): float
    {
        return $collector->getClasses() > 0 ? (count($collector->getConcreteNonFinalClasses()) / $collector->getClasses()) * 100 : 0;
    }
}
