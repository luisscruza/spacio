<?php

namespace Spacio\Framework\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewMigrationCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('new:migration')
            ->setDescription('Create a new migration')
            ->addArgument('name', InputArgument::REQUIRED, 'Migration name (e.g. create_users_table)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = trim((string) $input->getArgument('name'));
        if ($name === '') {
            $output->writeln('Migration name is required.');

            return Command::FAILURE;
        }

        $directory = BASE_PATH.'/database/migrations';
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $timestamp = date('Y_m_d_His');
        $filename = $timestamp.'_'.$name.'.php';
        $path = $directory.'/'.$filename;

        if (file_exists($path)) {
            $output->writeln("Migration already exists at {$path}.");

            return Command::FAILURE;
        }

        $stub = $this->buildStub($name);
        file_put_contents($path, $stub);

        $output->writeln("Created: {$path}");

        return Command::SUCCESS;
    }

    protected function buildStub(string $name): string
    {
        $stubPath = BASE_PATH.'/src/Stubs/migration.stub';
        $table = $this->guessTable($name);

        if (is_file($stubPath)) {
            $stub = file_get_contents($stubPath);

            return str_replace(
                ['{{ table }}'],
                [$table],
                $stub
            );
        }

        return <<<PHP
<?php

use Spacio\Framework\Database\Migrations\Migration;
use Spacio\Framework\Database\Contracts\ConnectionInterface;
use Spacio\Framework\Database\Schema\Table;

return new class extends Migration {
    public function up(ConnectionInterface \$connection): void
    {
        Table::create('{$table}', [
            'id' => ['integer', 'pk', 'autoincrement'],
        ], \$connection);
    }
};
PHP;
    }

    protected function guessTable(string $name): string
    {
        if (str_starts_with($name, 'create_') && str_ends_with($name, '_table')) {
            return substr($name, 7, -6);
        }

        return 'table_name';
    }
}
