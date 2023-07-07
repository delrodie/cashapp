<?php

namespace App\Repository\Archive;

use App\Entity\Archive\Inventaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inventaire>
 *
 * @method Inventaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inventaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inventaire[]    findAll()
 * @method Inventaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArchiveInventaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inventaire::class);
    }

    public function getBilan(int $an)
    {
        return $this->createQueryBuilder('i')
            ->where('i.publieLe BETWEEN :debut AND :fin')
            ->setParameters([
                'debut' => "{$an}-01-01 00:00:00",
                'fin' => "{$an}-12-31 23:59:59"
            ])
            ->getQuery()->getResult()
            ;
    }
}