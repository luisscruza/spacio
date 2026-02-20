<?php

namespace Spacio\Framework\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('new:command')
            ->setDescription('Create a new console command');
        $this->addArgument('name', InputArgument::REQUIRED, 'Command name (e.g. app:hello)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandName = trim((string) $input->getArgument('name'));
        $className = $this->commandToClassName($commandName);

        $directory = BASE_PATH.'/app/Console/Commands';
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $path = $directory.'/'.$className.'.php';
        if (file_exists($path)) {
            $output->writeln("Command already exists at {$path}.");

            return Command::FAILURE;
        }

        $stub = $this->buildStub($className, $commandName);
        file_put_contents($path, $stub);

        $output->writeln("Created: {$path}");

        return Command::SUCCESS;
    }

    protected function commandToClassName(string $commandName): string
    {
        $base = preg_replace('/[^a-zA-Z0-9]+/', ' ', $commandName);
        $base = ucwords(strtolower(trim((string) $base)));
        $base = str_replace(' ', '', $base);

        if ($base === '') {
            $base = 'Command';
        }

        if (! str_ends_with($base, 'Command')) {
            $base .= 'Command';
        }

        return $base;
    }

    protected function buildStub(string $className, string $commandName): string
    {
        $stubPath = BASE_PATH.'/src/Stubs/command.stub';

        if (is_file($stubPath)) {
            $stub = file_get_contents($stubPath);

            return str_replace(
                ['{{ class }}', '{{ command }}'],
                [$className, $commandName],
                $stub
            );
        }

        return <<<PHP
<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class {$className} extends Command
{
    protected function configure(): void
    {
        \$this
            ->setName('{$commandName}')
            ->setDescription('Describe your command');
    }

    protected function execute(InputInterface \$input, OutputInterface \$output): int
    {
        \$output->writeln('Command executed.');

        return Command::SUCCESS;
    }
}
PHP;
    }
}
