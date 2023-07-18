<?php

namespace App\Command;

use App\Repository\Main\AchatRepository;
use App\Repository\Main\CloudRepository;
use App\Repository\Main\DestockageRepository;
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
        private CloudRepository $cloudRepository, private AchatRepository $achatRepository,
        private DestockageRepository $destockageRepository
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
        $achats = $this->achatRepository->getAchatNoSync(); //dd(json_encode($achats));
        $destockages = $this->destockageRepository->getDestockageNoSync(); //dd(json_encode($destockages));

        // Pour test
        $data = [
            'factures' => $factures,
            'achats' => $achats,
            'destockages' => $destockages
        ];
        //dd(json_encode($data));

        if ($factures || $achats || $destockages) {
            $cloud = $this->cloudRepository->findOneBy([], ['id' => "DESC"]); //dd($cloud);
            if (!$cloud) $url = "https://localhost:8000/api/sync/";
            else $url = "{$cloud->getUrl()}/api/sync/";

            $httpCLient = HttpClient::create();
            try {
                do{

                    $currentStatut = null;
                    $response = $httpCLient->request('POST', $url, [
                        'json' => [
                            'factures' => $factures,
                            'achats' => $achats,
                            'destockages' => $destockages
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

                            // Mise à jour des destockages locaux
                            foreach ($destockages as $destockage){
                                $destockage->setSync(true);
                                $this->entityManager->persist($destockage);
                            }

                            $this->entityManager->flush();
                            $io->success('Synchronisation effectuée avec succès');
                            break;
                        } else {
                            $io->warning("Aucune donnée à synchroniser!");
                            break;
                        }
                    } elseif ($response->getStatusCode() === 200){
                        $message = json_decode($response->getContent(), true);

                        $statut = $message['statut'];

                        switch ($statut){
                            case 101:
                                $updateFacture = $this->factureRepository->findOneBy(['code' => $message['code']]);
                                if ($updateFacture){
                                    $updateFacture->setSync(true);
                                    $this->factureRepository->save($updateFacture, true);

                                    $io->warning("L'erreur concernant la facture a été resolue avec succès! Veuillez reprendre la synchronisation.");
                                }
                                break;

                            case 102:
                                $io->error('Echec: un des produits concernés n\'a pas été trouvé dans la base de données distante ');
                                break;

                            case 103:
                                $updateAchat = $this->achatRepository->findOneBy(['code' => $message['code']]);
                                if ($updateAchat){
                                    $updateAchat->setSync(true);
                                    $this->achatRepository->save($updateAchat, true);

                                    $io->warning("L'erreur concernant l'achat a été resolue avec succès! Veuillez reprendre la synchronisation.");
                                }
                                break;

                            case 104:
                                $io->error("Echèc: la catégorie d'un des produits concernés n'a pas été trouvé dans la base de données distance");
                                break;

                            case 105:
                                $updateDestockage = $this->destockageRepository->findOneBy(['code' => $message['code']]);
                                if ($updateDestockage){
                                    $updateDestockage->setSync(true);
                                    $this->destockageRepository->save($updateDestockage, true);

                                    $io->warning("L'erreur concernant le destockage a été résolue avec succès! Veuillez reprendre la synchronisation.");
                                }
                                break;

                            case 106:
                                $io->error("Echec: l'un des produits concernés n'a pas été trouvé dans la base de données distante");
                                break;

                        }

                        // Obtenir le nouveau statut de la réponse et vérifier si c'est 101, 103 ou 105
                        $message = $response->toArray();
                        $newStatut = $message['statut'];

                        if ($newStatut === $currentStatut) {
                            // Si le nouveau statut est identique à l'ancien, il est préférable de sortir de la boucle pour éviter une boucle infinie
                            $io->error("La synchronisation a échoué avec le même statut de réponse. Arrêt de la synchronisation pour éviter une boucle infinie.");
                            break;
                        }

                        $currentStatut = $newStatut; // Mettre à jour le statut actuel avec le nouveau statut

                        if (in_array($newStatut, [101, 103, 105], true)) {
                            // Si le nouveau statut est 101, 103 ou 105, passer à la valeur suivante du repository et continuer la boucle
                            switch ($newStatut) {
                                case 101:
                                    $factures = $this->factureRepository->getFactureNoSync();
                                    break;
                                case 103:
                                    $achats = $this->achatRepository->getAchatNoSync();
                                    break;
                                case 105:
                                    $destockages = $this->destockageRepository->getDestockageNoSync();
                                    break;
                            }
                        } else {
                            // Si le nouveau statut n'est pas 101, 103 ou 105, sortir de la boucle, car la synchronisation a échoué
                            $io->error("La synchronisation a échoué avec le statut de réponse : $newStatut");
                            break;
                        }
                    }

                    sleep(5);
                }while(true);
            } catch (ClientExceptionInterface $e) {
                $io->error("Une erreur est subvenue lors de la synchronisation : ", $e->getMessage());
            }
        }else{
            $io->info("La base de données distante est à jour");
        }

        return Command::SUCCESS;
    }
}
