<?php

namespace Spacio\Framework\Console\Commands;

use Composer\InstalledVersions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AboutCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('about')
            ->setDescription('Display basic framework information');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $version = InstalledVersions::getRootPackage()['pretty_version'] ?? 'dev';

        $output->writeln([
            '███████╗██████╗  █████╗  ██████╗██╗ ██████╗',
            '██╔════╝██╔══██╗██╔══██╗██╔════╝██║██╔═══██╗',
            '███████╗██████╔╝███████║██║     ██║██║   ██║',
            '╚════██║██╔═══╝ ██╔══██║██║     ██║██║   ██║',
            '███████║██║     ██║  ██║╚██████╗██║╚██████╔╝',
            '╚══════╝╚═╝     ╚═╝  ╚═╝ ╚═════╝╚═╝ ╚═════╝',
            '',
            'Spacio - A slim PHP framework',
            "Version: {$version}",
        ]);

        return Command::SUCCESS;
    }
}
