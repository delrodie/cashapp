<?php

namespace App\Repository\Archive;

use App\Entity\Archive\Domaine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Domaine>
 *
 * @method Domaine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Domaine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Domaine[]    findAll()
 * @method Domaine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArchiveDomaineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Domaine::class);
    }
}