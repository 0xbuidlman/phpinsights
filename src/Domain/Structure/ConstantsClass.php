<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Structure;

use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Publisher;

/**
 * @internal
 */
final class ConstantsClass implements HasValue, HasPercentage
{
    /**
     * {@inheritdoc}
     */
    public function getValue(Publisher $publisher): string
    {
        return sprintf('%d', $publisher->getClassConstants());
    }

    /**
     * {@inheritdoc}
     */
    public function getPercentage(Publisher $publisher): float
    {
        return $publisher->getConstants() > 0 ? ($publisher->getClassConstants() / $publisher->getConstants()) * 100 : 0;
    }
}
