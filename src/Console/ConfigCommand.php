<?php

declare(strict_types=1);

namespace Terdelyi\Phanstatic\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Terdelyi\Phanstatic\Config\CollectionConfig;
use Terdelyi\Phanstatic\Config\Config;

class ConfigCommand extends Command
{
    private Config $config;

    private OutputInterface $output;

    public function __construct(Config $config)
    {
        parent::__construct();

        $this->config = $config;
    }

    protected function configure(): void
    {
        $this->setName('config')
            ->setDescription('Show current configuration');
    }

    /**
     * @param OutputInterface&\Terdelyi\Phanstatic\Support\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->output = $output;

        $this->setStyles();

        $this->showGenericConfig();
        $this->showBuilders();
        $this->showCollections();
        $this->showMeta();

        return Command::SUCCESS;
    }

    private function showGenericConfig(): void
    {
        $title = $this->config->getTitle() ?: '-';

        $this->output->writeln('<bold_yellow>Page title:</bold_yellow> '.$title);
        $this->output->writeln('<bold_yellow>Base URL:</bold_yellow> '.$this->config->getBaseUrl());
        $this->output->writeln('<bold_yellow>Build directory:</bold_yellow> '.$this->config->getBuildDir());
        $this->output->writeln('<bold_yellow>Source directory:</bold_yellow> '.$this->config->getSourceDir());
    }

    private function showBuilders(): void
    {
        $this->output->writeln('');
        $this->output->writeln('<bold_yellow>Content builders (in order):</bold_yellow>');
        foreach ($this->config->getBuilders() as $builder) {
            $this->output->writeln('- '.$builder);
        }
    }

    private function showCollections(): void
    {
        /** @var CollectionConfig[] $collections */
        $collections = $this->config->getCollections();
        if (count($collections) > 0) {
            $this->output->writeln('');
            $this->output->writeln('<bold_yellow>Collections:</bold_yellow>');

            foreach ($collections as $collection) {
                $this->output->writeln('- '.$collection->title);
            }
        }
    }

    private function showMeta(): void
    {
        $meta = $this->config->getMeta();
        if (count($meta) > 0) {
            $this->output->writeln('');
            $this->output->writeln('<bold_yellow>Collections:</bold_yellow>');

            foreach ($meta as $key => $value) {
                $this->output->writeln('- '.$key.': '.$value);
            }
        }
    }

    private function setStyles(): void
    {
        $style = new OutputFormatterStyle(null, null, ['bold']);
        $this->output->getFormatter()->setStyle('bold_yellow', $style);
    }
}
