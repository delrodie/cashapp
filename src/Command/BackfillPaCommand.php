<?php

namespace App\Command;

use App\Repository\Main\FactureRepository;
use App\Repository\Main\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:backfill-pa',
    description: 'Injecte le prixAchat actuel dans les lignes produits des factures existantes qui ne l\'ont pas encore.'
)]
class BackfillPaCommand extends Command
{
    public function __construct(
        private FactureRepository $factureRepository,
        private ProduitRepository $produitRepository,
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 1. Charger tous les prix d'achat actuels en une fois : [reference => prixAchat]
        $tousLesCodes = $this->produitRepository->createQueryBuilder('p')
            ->select('p.reference AS reference', 'p.prixAchat AS prixAchat')
            ->getQuery()
            ->getArrayResult();

        $prixAchatMap = [];
        foreach ($tousLesCodes as $row) {
            $prixAchatMap[$row['reference']] = (int) $row['prixAchat'];
        }

        // 2. Parcourir toutes les factures par lots pour ne pas saturer la mémoire
        $batchSize = 200;
        $offset = 0;
        $totalMisAJour = 0;

        $io->title('Backfill du prixAchat sur les factures existantes');

        while (true) {
            $factures = $this->factureRepository->createQueryBuilder('f')
                ->orderBy('f.id', 'ASC')
                ->setFirstResult($offset)
                ->setMaxResults($batchSize)
                ->getQuery()
                ->getResult();

            if (empty($factures)) {
                break;
            }

            foreach ($factures as $facture) {
                $produits = $facture->getProduits();
                $modifie = false;

                foreach ($produits as $index => $ligne) {
                    // Ne touche pas aux lignes qui ont déjà un prixAchat (ex: déjà migrées, ou nouvelles factures)
                    if (isset($ligne['prixAchat'])) {
                        continue;
                    }

                    $code = $ligne['code'] ?? null;
                    $produits[$index]['prixAchat'] = $prixAchatMap[$code] ?? 0;
                    $modifie = true;
                }

                if ($modifie) {
                    $facture->setProduits($produits);
                    $totalMisAJour++;
                }
            }

            $this->em->flush();
            $this->em->clear(); // libère la mémoire, détache les entités déjà traitées

            $io->writeln(sprintf('Lot traité : %d factures (offset %d)', count($factures), $offset));

            $offset += $batchSize;
        }

        $io->success(sprintf('%d facture(s) mise(s) à jour avec le prixAchat.', $totalMisAJour));

        return Command::SUCCESS;
    }
}