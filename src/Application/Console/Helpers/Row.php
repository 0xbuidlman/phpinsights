<?php


namespace NunoMaduro\PhpInsights\Application\Console\Helpers;


use NunoMaduro\PhpInsights\Domain\Contracts\HasAvg;
use NunoMaduro\PhpInsights\Domain\Contracts\HasDetails;
use NunoMaduro\PhpInsights\Domain\Contracts\HasMax;
use NunoMaduro\PhpInsights\Domain\Contracts\HasPercentage;
use NunoMaduro\PhpInsights\Domain\Contracts\HasValue;
use NunoMaduro\PhpInsights\Domain\Contracts\SubCategory;
use NunoMaduro\PhpInsights\Domain\Insights\Feedback;

/**
 * @internal
 */
final class Row
{
    /**
     * @var \NunoMaduro\PhpInsights\Domain\Insights\Feedback
     */
    private $feedback;

    /**
     * @var string
     */
    private $metricClass;

    /**
     * @var string|null
     */
    private static $category;

    /**
     * Row constructor.
     *
     * @param  \NunoMaduro\PhpInsights\Domain\Insights\Feedback  $feedback
     * @param  string  $metricClass
     */
    public function __construct(Feedback $feedback, string $metricClass)
    {
        $this->feedback = $feedback;
        $this->metricClass = $metricClass;
    }

    /**
     * Gets the content of the first cell.
     *
     * @return string
     */
    public function getFirstCell(): string
    {
        if (class_exists($name = $this->metricClass)) {
            /** @var \NunoMaduro\PhpInsights\Domain\Contracts\HasValue $metric */
            $metric = new $name();

            /** @var string $a */
            $name = ucfirst(substr((string) strrchr($name, "\\"), 1));

            $name = trim((string) preg_replace('/(?<!\ )[A-Z]/', ' $0', $name));

            if ($metric instanceof HasPercentage || $metric instanceof SubCategory) {
                $name = '• ' . trim(str_replace((string) self::$category, '', $name));
            } else {
                self::$category = $name;
                $name = "<bold>$name</bold>";
            }

            $name = str_pad(trim($name), 21, ' ');

            if ($metric instanceof HasPercentage && $percentage = $metric->getPercentage($this->feedback->getCollector()) !== 0.00) {
                $name .= sprintf('%.2f%%', $metric->getPercentage($this->feedback->getCollector()));
            }
        }

        return $name;
    }

    /**
     * Gets the content of the second cell.
     *
     * @param  string  $dir
     *
     * @return string
     */
    public function getSecondCell(string $dir): string
    {
        $metric = new $this->metricClass();

        $cell = $metric instanceof HasValue ? $metric->getValue($this->feedback->getCollector()) : '';
        $cell .= $metric instanceof HasAvg ? sprintf(' <fg=magenta>avg %s</>', $metric->getAvg($this->feedback->getCollector())) : '';
        $cell .= $metric instanceof HasMax ? sprintf(' <fg=yellow>max %s</>', $metric->getMax($this->feedback->getCollector())) : '';
        foreach ($this->feedback->allFrom($metric) as $insight) {
            $cell .= $insight->hasIssue() ? "<fg=red> ✘ --> </>" : ' <info>✔</info>';
            if ($insight->hasIssue()) {
                $cell .= "{$insight->getTitle()}";
                if ($insight instanceof HasDetails) {
                    $cell .= ':';
                    $details = $insight->getDetails();
                    $details = array_slice($details, -3, 3, true);

                    foreach ($details as $detail) {
                        $detail = str_replace(realpath($dir) . '/', '', $detail);
                        $cell .= "\n<fg=red>•</> $detail";
                    }
                }
                break;
            }
        }

        return trim($cell);
    }
}
