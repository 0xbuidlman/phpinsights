<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Domain;

use Closure;
use ReflectionClass;
use SebastianBergmann\PHPLOC\Collector as BaseCollector;

/**
 * @internal
 */
final class Collector extends BaseCollector
{
    /**
     * @var string[]
     */
    private $constants = [];

    /**
     * @var string[]
     */
    private $traits = [];

    /**
     * @var array<string>
     */
    private $classLines = [];

    /**
     * Holds the current filename.
     *
     * @var string|null
     */
    private $currentFilename;

    /**
     * Holds the current class.
     *
     * @var string|null
     */
    private $currentClass;

    /**
     * {@inheritDoc}
     */
    public function addConstant($name): void
    {
        parent::addConstant($name);

        if ($this->currentFilename !== null) {
            if (! array_key_exists($this->currentFilename, $this->constants)) {
                $this->constants[$this->currentFilename] = '';
            }

            $this->constants[$this->currentFilename] .= "$name ";
        }
    }

    /**
     * {@inheritDoc}
     */
    public function addFile($filename): void
    {
        parent::addFile($filename);

        $this->currentFilename = $filename;
    }

    /**
     * {@inheritDoc}
     */
    public function currentClassReset(): void
    {
        if ($this->currentFilename !== null) {
            $this->classLines[$this->currentFilename] = $this->getParent('currentClassLines');
        }

        parent::currentClassReset();
    }

    /**
     * {@inheritDoc}
     */
    public function incrementTraits(): void
    {
        parent::incrementTraits();

        if ($this->currentFilename !== null) {
            $this->traits[] = $this->currentFilename;
        }
    }

    /**
     * Returns the class lines.
     *
     * @return string[]
     */
    public function getClassLines(): array
    {
        return $this->classLines;
    }

    /**
     * Returns the declared constants.
     *
     * @return string[]
     */
    public function getConstants(): array
    {
        return $this->constants;
    }


    /**
     * Returns the traits.
     *
     * @return string[]
     */
    public function getTraits(): array
    {
        return $this->traits;
    }

    /**
     * {@inheritDoc}
     */
    public function getPublisher(): Publisher
    {
        return new Publisher($this->getParent('counts'));
    }

    /**
     * Gets the given property from the parent class.
     *
     * @param  string  $property
     *
     * @return mixed
     */
    public function getParent(string $property)
    {
        $reflectionClass = new ReflectionClass(\SebastianBergmann\PHPLOC\Collector::class);

        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);
        return $reflectionProperty->getValue($this);
    }
}
