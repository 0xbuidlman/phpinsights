<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\LinesOfCode;

use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class Total implements HasValue
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getLines());
    }
}
