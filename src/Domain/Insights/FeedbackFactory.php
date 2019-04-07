<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain\Insights;

use InvalidArgumentException;
use NunoMaduro\PhpInsights\Domain\Analyser;
use NunoMaduro\PhpInsights\Domain\Contracts\HasInsights;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use NunoMaduro\PhpInsights\Domain\Exceptions\DirectoryNotFoundException;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @internal
 */
final class FeedbackFactory
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository
     */
    private $filesRepository;

    /**
     * @var \NunoMaduro\PhpInsights\Domain\Analyser
     */
    private $analyser;

    /**
     * Creates a new instance of Feedback Factory.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository  $filesRepository
     * @param  \NunoMaduro\PhpInsights\Domain\Analyser  $analyser
     */
    public function __construct(FilesRepository $filesRepository, Analyser $analyser)
    {
        $this->filesRepository = $filesRepository;
        $this->analyser = $analyser;
    }

    /**
     * @param  array  $metrics
     * @param  string  $dir
     *
     * @return \NunoMaduro\PhpInsights\Domain\Insights\Feedback
     *
     * @throws \ReflectionException
     */
    public function get(array $metrics, string $dir): Feedback
    {
        try {
            $files = array_map(function (SplFileInfo $file) {
                return $file->getRealPath();
            }, iterator_to_array($this->filesRepository->in($dir)->getFiles()));
        } catch (InvalidArgumentException $e) {
            throw new DirectoryNotFoundException($e->getMessage());
        }

        $collector = $this->analyser->analyse($files);

        $metrics = array_filter($metrics, function ($metricClass) {
            return class_exists($metricClass) && array_key_exists(HasInsights::class, class_implements($metricClass));
        });

        $insights = [];
        foreach ($metrics as $metricClass) {
            $metric = new $metricClass();

            $insights = array_merge($insights, array_map(function ($insightClass) use ($collector) {
                return new $insightClass($collector);
            }, $metric->getInsights($collector)));
        }

        return new Feedback($collector, $insights);
    }
}
