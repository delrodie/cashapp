<?php

namespace App\Command;

use App\Repository\Main\CloudRepository;
use App\Repository\Main\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

#[AsCommand(
    name: 'app:sync-data',
    description: 'Synchronisation des données locale à la base de données à distance',
)]
class SyncDataCommand extends Command
{
    public function __construct(
        private FactureRepository $factureRepository, private EntityManagerInterface $entityManager,
        private CloudRepository $cloudRepository
    )
    {
        parent::__construct();
    }
//    protected function configure(): void
//    {
//        $this
//            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
//        ;
//    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Récupération de la liste des factures non synchronisées
        $factures = $this->factureRepository->getFactureNoSync();

        if ($factures) {
            $cloud = $this->cloudRepository->findOneBy([], ['id' => "DESC"]);
            if (!$cloud) $url = "https://localhost:8000/api/sync/";
            else $url = "{$cloud->getUrl()}/api/sync/";


            $httpCLient = HttpClient::create();
            try {
                $response = $httpCLient->request('POST', $url, [
                    'json' => [
                        'factures' => $factures,
//                    'achats' => 'test'
                    ]
                ]);
                if ($response->getStatusCode() === 200) {
                    if ($response->getContent()) {//dd('ici');
                        foreach ($factures as $facture) {
                            $facture->setSync(true);
                            $this->entityManager->persist($facture);
                        }
                        $this->entityManager->flush();
                        $io->success('Synchronisation effectuée avec succès');
                    } else {
                        $io->warning("Aucune donnée à synchroniser!");
                    }
                } else {
                    $io->warning('Aucune donnée à synchroniser. La base de données distante est à jour');
                }
            } catch (ClientExceptionInterface $e) {
                $io->error("Une erreur est subvenue lors de la synchronisation : ", $e->getMessage());
            }
        }else{
            $io->warning("La base de données distante est à jour");
        }

        return Command::SUCCESS;
    }
}
