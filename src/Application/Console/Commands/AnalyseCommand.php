<?php

declare(strict_types=1);

namespace NunoMaduro\PhpInsights\Application\Console\Commands;

use NunoMaduro\PhpInsights\Application\ConfigResolver;
use NunoMaduro\PhpInsights\Application\Console\Analyser;
use NunoMaduro\PhpInsights\Application\Console\OutputDecorator;
use NunoMaduro\PhpInsights\Application\Console\Style;
use NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class AnalyseCommand
{
    /**
     * Holds an instance of the Analyser.
     *
     * @var \NunoMaduro\PhpInsights\Application\Console\Analyser
     */
    private $analyser;

    /**
     * Holds an instance of the Files Repository.
     *
     * @var \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository
     */
    private $filesRepository;

    /**
     * Creates a new instance of the Analyse Command.
     *
     * @param  \NunoMaduro\PhpInsights\Application\Console\Analyser  $analyser
     * @param  \NunoMaduro\PhpInsights\Domain\Contracts\Repositories\FilesRepository  $filesRepository
     */
    public function __construct(Analyser $analyser, FilesRepository $filesRepository)
    {
        $this->analyser = $analyser;
        $this->filesRepository = $filesRepository;
    }

    /**
     * Handle the given input.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     *
     * @return void
     */
    public function __invoke(InputInterface $input, OutputInterface $output): void
    {
        $style = new Style($input, OutputDecorator::decorate($output));

        $directory = $this->getDirectory($input);

        $this->analyser->analyse($style, $this->getConfig($input, $directory), $directory);
    }

    /**
     * Gets the config from the given input.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  string  $directory
     *
     * @return array
     */
    private function getConfig(InputInterface $input, string $directory): array
    {
        if ($config = $input->getArgument('config-path')) {
            if (file_exists(getcwd() . DIRECTORY_SEPARATOR . 'phpinsights.php')) {
                $config = getcwd() . DIRECTORY_SEPARATOR . 'phpinsights.php';
            }
        }

        return ConfigResolver::resolve($config ?? [], $directory);
    }

    /**
     * Gets the directory from the given input.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     *
     * @return string
     */
    private function getDirectory(InputInterface $input): string
    {
        $directory = $input->getArgument('directory');

        if (is_string($directory)) {
            if ($directory[0] !== DIRECTORY_SEPARATOR) {
                $directory = getcwd() . DIRECTORY_SEPARATOR . $directory;
            }
        } else {
            $directory = $this->filesRepository->getDefaultDirectory();
        }

        return $directory;
    }
}
