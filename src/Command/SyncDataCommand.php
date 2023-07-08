<?php

namespace App\Command;

use App\Repository\Main\AchatRepository;
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
        private CloudRepository $cloudRepository, private AchatRepository $achatRepository
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
        $achats = $this->achatRepository->getAchatNoSync(); //dd(json_encode($factures));

        if ($factures || $achats) {
            $cloud = $this->cloudRepository->findOneBy([], ['id' => "DESC"]); //dd($cloud);
            if (!$cloud) $url = "https://localhost:8000/api/sync/";
            else $url = "{$cloud->getUrl()}/api/sync/";

            $httpCLient = HttpClient::create();
            try {
                $response = $httpCLient->request('POST', $url, [
                    'json' => [
                        'factures' => $factures,
                        'achats' => $achats
                    ]
                ]);
//
                // Si le statut du resultat est 100 alors la synchronisation est effective
                // Sinon si le statut est 101 ainsi la facture existe dans la base de données distante
                /// Sinon une erreur concernant un des produits de la facture a été rencontrée
                if ($response->getStatusCode() === 201) {
                    if ($response->getContent()) {//dd('ici');
                        // Mise à jour des factures locales
                        foreach ($factures as $facture) {
                            $facture->setSync(true);
                            $this->entityManager->persist($facture);
                        }

                        // Mise à jour des achats locaux
                        foreach ($achats as $achat){
                            $achat->setSync(true);
                            $this->entityManager->persist($achat);
                        }

                        $this->entityManager->flush();
                        $io->success('Synchronisation effectuée avec succès');
                    } else {
                        $io->warning("Aucune donnée à synchroniser!");
                    }
                } elseif ($response->getStatusCode() === 200){ //dd($response->getContent());
                    if ($response->getContent()){
                        $updateFacture = $this->factureRepository->findOneBy(['code' => $response->getContent()]);
                        if ($updateFacture){
                            $updateFacture->setSync(true);
                            $this->factureRepository->save($updateFacture, true);
                        }
                    }
                }
                else {
                    $io->error('Echec: un des produits concernés n\'a pas été trouvé dans la base de données distante ');
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
