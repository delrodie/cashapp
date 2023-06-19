<?php

namespace App\Command;

use App\Entity\Main\Categorie;
use App\Repository\Archive\ArchiveCategorieRepository;
use App\Repository\Main\CategorieRepository;
use App\Repository\Main\DomaineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:copy-categorie',
    description: 'Copie des données de la table archive Categorie dans la nouvelle table categorie',
)]
class CopyCategorieCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager, private DomaineRepository $domaineRepository,
        private ArchiveCategorieRepository $archiveCategorieRepository, private CategorieRepository $categorieRepository
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
        $progressBar = new ProgressBar($output);
        $progressBar->start();

        $archiveCategories = $this->archiveCategorieRepository->findAll();

        $nb=0;
        foreach ($archiveCategories as $category){
            $verif = $this->categorieRepository->findOneBy(['slug' => $category->getSlug()]);
            $nvDomaine = $this->domaineRepository->findOneBy(['slug' =>$category->getDomaine()->getSlug()]);

            if (!$verif AND $nvDomaine){
                $nvCategorie = new Categorie();
                $code = $nvDomaine->getCode().''. 10+$nb;
                $nvCategorie->setCode($code);
                $nvCategorie->setLibelle($category->getLibelle());
                $nvCategorie->setDomaine($nvDomaine);
                $nvCategorie->setSlug($category->getSlug());

                $this->entityManager->persist($nvCategorie);
                $nb++;
                $progressBar->advance();
            }
        }

        $this->entityManager->flush();
        $progressBar->finish();

        $io->success("Les {$nb} lignes de catégories ont été copiées avec succès.");

        return Command::SUCCESS;
    }

}
