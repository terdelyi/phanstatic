<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\New\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Terdelyi\Phanstatic\New\Models\Config;
use Terdelyi\Phanstatic\New\Support\Helpers;

class ConfigCommand extends Command
{
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

        $this->setStyles();
        $this->showGenericConfig();
        $this->showGenerators();
        $this->showCollections();
        $this->showMeta();

        return Command::SUCCESS;
    }

    private function setStyles(): void
    {
        $style = new OutputFormatterStyle(null, null, ['bold']);
        $this->output->getFormatter()->setStyle('bold_yellow', $style);
    }

    private function showGenericConfig(): void
    {
        $title = $this->config->title;

        $this->output->writeln('');
        $this->output->writeln('<bold_yellow>Page title:</bold_yellow> '.$title);
        $this->output->writeln('<bold_yellow>Base URL:</bold_yellow> '.$this->helpers->getBaseUrl());
        $this->output->writeln('<bold_yellow>Build directory:</bold_yellow> '.$this->helpers->getBaseUrl());
        $this->output->writeln('<bold_yellow>Source directory:</bold_yellow> '.$this->helpers->getBaseUrl());
        $this->output->writeln('');
    }

    private function showGenerators(): void
    {
        $generators = $this->config->generators;
        if (count($generators) > 0) {
            $this->output->writeln('');
            $this->output->writeln('<bold_yellow>Content generators in runtime order:</bold_yellow>');
            foreach ($generators as $generator) {
                $this->output->writeln('- '.$generator);
            }
        }
    }

    private function showCollections(): void
    {
        $collections = $this->config->collections;
        if (count($collections) > 0) {
            $this->output->writeln('');
            $this->output->writeln('<bold_yellow>Collections configuration:</bold_yellow>');

            foreach ($collections as $collection) {
                $this->output->writeln('- '.$collection->title);
            }
        }
    }

    private function showMeta(): void
    {
        $meta = $this->config->meta;
        if (count($meta) > 0) {
            $this->output->writeln('');
            $this->output->writeln('<bold_yellow>Site meta data:</bold_yellow>');

            foreach ($meta as $key => $value) {
                $this->output->writeln('- '.$key.': '.$value);
            }
        }
    }
}
