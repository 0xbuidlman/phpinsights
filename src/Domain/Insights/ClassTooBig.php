<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;

/**
 * @internal
 */
final class ClassTooBig extends Insight implements HasDetails
{
    /**
     * {@inheritdoc}
     */
    public function hasIssue(): bool
    {
        return $this->collector->getMaximumClassLength() > 100;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return 'Having `classes` with more than 100 lines is prohibited - Consider refactoring';
    }

    /**
     * {@inheritdoc}
     */
    public function getDetails(): array
    {
        $classLines = array_filter($this->collector->getPerClassLines(), function ($lines) {
            return $lines > 100;
        });

        uasort($classLines, function ($a, $b) {
            return $b - $a;
        });

        $classLines = array_reverse($classLines);

        return array_map(function ($class, $lines) {
            return "$class: $lines lines";
        }, array_keys($classLines), $classLines);
    }
}
