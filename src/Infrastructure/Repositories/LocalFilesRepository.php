<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Infrastructure\Repositories;

use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;

/**
 * @internal
 */
final class LocalFilesRepository implements FilesRepository, CustomSourceProviderInterface
{
    /**
     * @var \Symfony\Component\Finder\Finder
     */
    private $finder;

    /**
     * LocalFilesRepository constructor.
     *
     * @param  \Symfony\Component\Finder\Finder  $finder
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultDirectory(): string
    {
        return (string) getcwd();
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles(): iterable
    {
        return $this->finder->exclude(['vendor'])->name(['*.php', '*.json'])->getIterator();
    }

    /**
     * {@inheritdoc}
     */
    public function in(string $directory, array $exclude): FilesRepository
    {
        $this->finder->files()->in([$directory])->exclude($exclude);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function find(array $source)
    {
        return $this->getFiles();
    }
}
