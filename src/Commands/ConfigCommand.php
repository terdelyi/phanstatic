<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Terdelyi\Phanstatic\Models\Config;
use Terdelyi\Phanstatic\Support\Helpers;
use Terdelyi\Phanstatic\Support\OutputHelper;

class ConfigCommand extends Command
{
    use OutputHelper;

    private Config $config;
    private Helpers $helpers;

    private OutputInterface $output;

    public function __construct(Config $config, Helpers $helpers)
    {
        parent::__construct();

        $this->config = $config;
        $this->helpers = $helpers;
    }

    protected function configure(): void
    {
        $this->setName('config')
            ->setDescription('Show current configuration');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;

        $this->logo();
        $this->lines();
        $this->showGenericConfig();
        $this->showGenerators();
        $this->showCollections();
        $this->showMeta();
        $this->lines();

        return Command::SUCCESS;
    }

    private function showGenericConfig(): void
    {
        $configMessage = $this->config->path
            ? 'This configuration is loaded from '.$this->config->path
            : 'No config file set, this is the default configuration.';

        $this->output->writeln($configMessage);

        $this->lines();

        $title = $this->config->title ?: 'N/A';

        $this->text('→ <options=bold>Title:</>             '.$title);
        $this->text('→ <options=bold>Base url:</>          '.$this->helpers->getBaseUrl());
        $this->text('→ <options=bold>Build directory:</>   '.$this->helpers->getBuildDir());
        $this->text('→ <options=bold>Source directory:</>  '.$this->helpers->getSourceDir());
    }

    private function showGenerators(): void
    {
        $generators = $this->config->generators;
        if (count($generators) > 0) {
            $this->lines();
            $this->text('<options=bold>Loaded content generators (in runtime order):</>');
            foreach ($generators as $generator) {
                $this->item($generator);
            }
        }
    }

    private function showCollections(): void
    {
        $collections = $this->config->collections;
        if (count($collections) > 0) {
            $this->lines();
            $this->text('<options=bold>Collections set:</>');

            foreach ($collections as $collectionDir => $collection) {
                $this->item($collection->title.' ('.$collectionDir.')');
            }
        }
    }

    private function showMeta(): void
    {
        $meta = $this->config->meta;
        if (count($meta) > 0) {
            $this->lines();
            $this->text('<options=bold>Site meta data:</>');

            foreach ($meta as $key => $value) {
                $this->item('- '.$key.': '.$value);
            }
        }
    }
}
