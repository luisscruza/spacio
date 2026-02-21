<?php

namespace Spacio\Framework\Console\Commands;

use Spacio\Framework\Database\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('migrate')
            ->setDescription('Run pending database migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $migrator = app(Migrator::class);
        $ran = $migrator->run();

        if (count($ran) === 0) {
            $output->writeln('No pending migrations.');

            return Command::SUCCESS;
        }

        foreach ($ran as $name) {
            $output->writeln("Migrated: {$name}");
        }

        return Command::SUCCESS;
    }
}
