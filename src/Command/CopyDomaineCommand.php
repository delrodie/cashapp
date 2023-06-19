<?php

namespace App\Command;

use App\Entity\Main\Domaine;
use App\Repository\Archive\ArchiveDomaineRepository;
use App\Repository\Main\DomaineRepository;
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
    name: 'app:copy-domaine',
    description: 'Copie des données de la table archive domaine dans la nouvelle table domaine.',
)]
class CopyDomaineCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager, private ArchiveDomaineRepository $archiveDomaineRepository
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

        //$test = $this->entityManager->getRepository()
//        $archiveRepo = $this->domaineRepository->
        $nouveauRepo = $this->entityManager->getRepository(Domaine::class);
        $archiveDomaines = $this->archiveDomaineRepository->findAll();

        $nb=0;
        foreach ($archiveDomaines as $domaine){
            // Verification de la non existence de l'information dans la table
            $verif = $nouveauRepo->findOneBy(['slug' => $domaine->getSlug()]);
            if (!$verif) {
                $nvDomaine = new Domaine();
                $nvDomaine->setCode($domaine->getCode());
                $nvDomaine->setLibelle($domaine->getLibelle());
                $nvDomaine->setSlug($domaine->getSlug());

                $this->entityManager->persist($nvDomaine);
                $progressBar->advance();
                $nb++;
            }
        }

        $this->entityManager->flush();
        $progressBar->finish();

        $io->success("Les {$nb} lignes de domaines ont été copiées avec succès!");

        return Command::SUCCESS;
    }
}
