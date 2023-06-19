<?php

namespace App\Repository\Archive;

use App\Entity\Archive\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArchiveProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function getListProduitMoreThanZero()
    {
        return $this->createQueryBuilder('p')
//            ->addSelect('c')
//            ->leftJoin('p.categorie', 'c')
            ->where('p.stock > 0')
            ->getQuery()->getResult()
            ;
    }
}