<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:import-db',
    description: 'Importation de la base de données archives',
)]
class ImportDatabaseCommand extends Command
{
    private $startTime;
    private $endTime;

    public function __construct(private ParameterBagInterface $params)
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->addOption(
                'import-env',
                null,
                InputOption::VALUE_OPTIONAL,
                'L\'environnement dans lequel la commande doit s\'exécuter.',
                'dev'
            )
            ->addOption(
                'skip-existing',
                null,
                InputOption::VALUE_NONE,
                'Ignorer les tables existantes lors de l\'importation'
            )
            ->setDescription("Importation de la base de donnée")
            ->setHelp("command")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // Creation de porgressBar
        $progressBar = new ProgressBar($output);
        $progressBar->start();

        // On utilise le service de paramètres pour acceder aux variables d'environnement
        $env = $input->getOption('import-env');
        $skipExistingTables = $input->getOption('skip-existing');

        $archiveDatabaseUrl = $this->params->get('ARCHIVE_DATABASE_URL');

        // Extraction du nom de la base de données via ARCHIVE_DATABASE_URL
        preg_match('/\/([^\/?]+)\?/', $archiveDatabaseUrl, $matches);
        $databaseName = $matches[1];

        // Importation de la base de données
        $importCommand = sprintf(
            'mysql -h %s -u %s -p%s %s < public/.sql/cashapp_archive.sql',
            $this->parseDatabaseHost($archiveDatabaseUrl),
            $this->parseDatabaseUser($archiveDatabaseUrl),
            $this->parseDatabasePassword($archiveDatabaseUrl),
            $databaseName
        );

        if ($skipExistingTables) $importCommand.= ' --force';

        // Executer l'importation
        $process = Process::fromShellCommandline($importCommand);
        $process->setTimeout(1200);

        $this->startTime  = microtime(true);
        $lastOutputLength = 0;
        $process->run(function ($type, $buffer) use ($progressBar, $io, &$lastOutputLength){
            $progressBar->advance();

            // Calculé le temps écoulé
            $currentTime = microtime(true);
            $elapsedTime = $currentTime - $this->startTime;

            // Calculer le temps estimé restant
            $process = $progressBar->getProgress();
            $estimatedTimeRemaining = $elapsedTime * (100 / $process) - $elapsedTime;

            // Afficher le temps écoulé et le temps estimé restant
            $output = sprintf(
                "Temps écoulé: %.2f secondes, Temps restant estimé: %.2f secondes",
                $elapsedTime,
                $estimatedTimeRemaining
            );

            // Effacer la ligne précédente
            if ($lastOutputLength > 0){
                $io->write(str_repeat("\x08", $lastOutputLength));
            }

            // Afficher la nouvelle ligne
            $io->write($output);
            $lastOutputLength = strlen($output);
        });

        $this->endTime = microtime(true);
        $totalTime = $this->endTime - $this->startTime;
        $io->writeln(sprintf("Total temps d'exécution: %.2f seconds", $totalTime));

        if (!$process->isSuccessful())
            throw new ProcessFailedException($process);

        $progressBar->finish();
        $io->success('Base de données importées avec succès!');

        return Command::SUCCESS;
    }

    private function parseDatabaseUser(\UnitEnum|float|array|bool|int|string|null $archiveDatabaseUrl): string
    {
        preg_match('/\/\/([^:]+):/', $archiveDatabaseUrl, $matches);
        return $matches[1];
    }

    private function parseDatabasePassword(\UnitEnum|float|array|bool|int|string|null $archiveDatabaseUrl): string
    {
        preg_match('/mysql:\/\/[^:]+:([^@]+)/', $archiveDatabaseUrl, $matches);
        return $matches[1];
    }

    private function parseDatabaseHost(\UnitEnum|float|array|bool|int|string|null $archiveDatabaseUrl): string
    {
//        preg_match('/\/\/([^:]+)/', $archiveDatabaseUrl, $matches);
//         dd($matches[1]);
        $parseUrl = parse_url($archiveDatabaseUrl);
        return $parseUrl['host'];
    }

}
