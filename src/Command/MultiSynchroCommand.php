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
    name: 'app:multi-synchro',
    description: 'Multi synchronisation des données locales à la base de données distante',
)]
class MultiSynchroCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FactureRepository $factureRepository,
        private CloudRepository $cloudRepository,
        private AchatRepository $achatRepository,
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

        // Gestion des achats ajoutés
        $achats = $this->achatRepository->getAchatNoSync();
        if ($achats){
            $this->gestionEntities($achats, $input, $output, 'achat', 'sync-achat');
        }

        // Gestion des factures ajoutées
        $factures = $this->factureRepository->getFactureNoSync();
        if ($factures){
            $this->gestionEntities($factures, $input, $output, 'facture', 'sync-facture');
        }

        if (!$factures && !$achats){
            $io->info("La base de données distante est à jour!");
        }


        return Command::SUCCESS;
    }

    private function gestionEntities(mixed $data, $input, $output, string $dataType, string $syncType): void
    {
        $io = new SymfonyStyle($input, $output);

        // Le lien de synchronisation
        $cloud = $this->cloudRepository->findOneBy([],['id'=>"DESC"]);
        $url = "https://localhost:8000/api/{$syncType}/";
        if ($cloud) $url = "{$cloud->getUrl()}/api/{$syncType}/";

        $httpClient = HttpClient::create();

        try{
            while (true){
                $statut = $code = null;

                $response = $httpClient->request('POST', $url,[
                    'json' => [$dataType => $data]
                ]);

                $statusCode = $response->getStatusCode();
                $content = $response->getContent() ? json_decode($response->getContent(), true) : null;

                if ($statusCode === 201){
                    if ($content){
                        // Mise à jour des données locales
                        foreach ($data as $item){
                            $item->setSync(true);
                            $this->entityManager->persist($item);
                        }

                        $this->entityManager->flush();

                        $io->success("Synchronisation de la facture a été effectuée avec succès!");
                    }else{
                        $io->info("Aucune facture à synchroniser.");
                    }
                    break;

                }elseif ($statusCode === 200){
                    $statut = $content['statut'] ?? null; //dd($statut);
                    $code = $content['code'] ?? null;

                    if ( $statut === 101){
                        switch ($dataType){
                            case 'achat':
                                $updateItem = $this->achatRepository->findOneBy(['code' => $code]);
                                break;
                            case 'facture':
                                $updateItem = $this->factureRepository->findOneBy(['code' => $code]);
                        }

                        if ($updateItem){
                            $updateItem->setSync(true);
                            $this->entityManager->persist($updateItem);
                            $this->entityManager->flush();

                            $io->warning("L'erreur concernant la facture {$updateItem->getCode()} a été résolue avec succès! La synchronisation va reprendre dans quelques instant");

                        }

                    }elseif($statut === 102){
                        $io->error("Echec! Un des produits concernés par la facture n'a pas été trouvé dans la base de données distante");

                        // Envoi de mail au developpeur
                        // Continuer la synchronisation

                    }else{
                        // Envoi de mail au developpeur
                        break;
                    }

                    // Recherche suivante
                    switch ($dataType){
                        case 'achat':
                            $data = $this->achatRepository->getAchatNoSyncNext($code);
                            break;
                        case 'facture':
                            $data = $this->factureRepository->getFactureNoSyncNext($code);
                    }
                    if (!$data) {
                        break;
                    }
                }

                sleep(5);

            }
        }catch (ClientExceptionInterface $exception){
            $io->error("Une erreur est survenue lors de la synchronisation : {$exception->getMessage()}");
        }

    }

    private function gestionAchats(mixed $achats, InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        //dd(json_encode(['achats'=>$achats], true));

        // Le lien de synchronisation
        $cloud = $this->cloudRepository->findOneBy([],['id'=>"DESC"]);
        $url = "https://localhost:8000/api/sync-achat/";
        if ($cloud) $url = "{$cloud->getUrl()}/api/sync-achat/";

        $httpClient = HttpClient::create();

        try{
            while (true){
                $statut = $code = null;

                $response = $httpClient->request('POST', $url,[
                    'json' => ['achats' => $achats]
                ]);
                //dd($achats);
                $statusCode = $response->getStatusCode();
                $content = $response->getContent() ? json_decode($response->getContent(), true) : null;

                if ($statusCode === 201){

                    if ($content){
                        // Mise à jour des factures locales
                        foreach ($achats as $achat){
                            $achat->setSync(true);
                            $this->entityManager->persist($achat);
                        }

                        $this->entityManager->flush();

                        $io->success("Synchronisation de l'achat a été effectuée avec succès!");
                    }else{
                        $io->info("Aucun achat à synchroniser.");
                    }
                    break;

                }elseif ($statusCode === 200){

                    $statut = $content['statut'] ?? null; //dd($statut);
                    $code = $content['code'] ?? null;

                    if ( $statut === 103){
                        $updateAchat = $this->achatRepository->findOneBy(['code' => $code]);
                        if ($updateAchat){
                            $updateAchat->setSync(true);
                            $this->achatRepository->save($updateAchat, true);

                            $io->warning("L'erreur concernant l'achat {$updateAchat->getCode()} a été résolue avec succès! La synchronisation va reprendre dans quelques instant");

                        }

                    }elseif($statut === 104){
                        $io->error("Echec! Un des produits concernés par l'achat n'a pas été trouvé dans la base de données distante");

                        // Envoi de mail au developpeur
                        // Continuer la synchronisation

                    }else{
                        // Envoi de mail au developpeur
                        break;
                    }

                    // Recherche suivante
                    $achats = $this->achatRepository->getAchatNoSyncNext($code);
                    if (!$achats) {
                        break;
                    }
                }

                sleep(5);

            }
        } catch (ClientExceptionInterface $exception){
            // Envoie de mail au developpeur
            $io->error("Une erreur est survenue lors de la synchronisation : {$exception->getMessage()}");
        }
    }
}