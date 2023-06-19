<?php

namespace App\Command;

use App\Entity\Main\Produit;
use App\Repository\Archive\ArchiveProduitRepository;
use App\Repository\Main\CategorieRepository;
use App\Repository\Main\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:copy-produit',
    description: 'Copie des données de la table archive Produit dans la nouvelle table Produit',
)]
class CopyProduitCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager, private ArchiveProduitRepository $archiveProduitRepository,
        private ProduitRepository $produitRepository, private CategorieRepository $categorieRepository
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
        set_time_limit(300);
        $io = new SymfonyStyle($input, $output);

        // Création de l'objet ProgressBar
        $progressBar = new ProgressBar($output);
        $progressBar->start();

        $archiveProduits = $this->archiveProduitRepository->getListProduitMoreThanZero();

        $nb=0;
        foreach ($archiveProduits as $produit){
            $verif = $this->produitRepository->findOneBy(['slug' => $produit->getSlug()]);
            $nvCategorie = $this->categorieRepository->findOneBy(['slug' => $produit->getCategorie()->getSlug()]);

            if (!$verif AND $nvCategorie){
                $nvProduit = new Produit();
                $reference = $nvCategorie->getCode()."". 1001 + $nb;
                $nvProduit->setReference($reference);
                $nvProduit->setCodebarre($produit->getCodebarre());
                $nvProduit->setLibelle($produit->getLibelle());
                $nvProduit->setPrixAchat($produit->getPrixAchat());
                $nvProduit->setPrixVente($produit->getPrixVente());
                $nvProduit->setStock($produit->getStock());
                $nvProduit->setSlug($produit->getSlug());
                $nvProduit->setCategorie($nvCategorie);

                $this->entityManager->persist($nvProduit);
                $nb++;

                $progressBar->advance();
            }
        }

        $this->entityManager->flush();
        $progressBar->finish();

        $io->success("Les {$nb} lignes de la table archive Produit ont été copiées avec succès!");

        return Command::SUCCESS;
    }

}
