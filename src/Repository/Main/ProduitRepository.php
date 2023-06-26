<?php

namespace App\Repository\Main;

use App\Entity\Main\Produit;
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
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function save(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findListAll()
    {
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->leftJoin('p.categorie', 'c')
            ->getQuery()->getResult();
    }

//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findByQuery(float|bool|int|string|null $query)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('c')
            ->leftJoin('p.categorie', 'c')
            ->where('p.libelle LIKE :query')
            ->orWhere('c.libelle LIKE :query')
            ->orderBy('p.libelle', 'ASC')
            ->setParameter('query', "%{$query}%")
            ->getQuery()->getResult();
    }

    public function findByCodeOrReference(float|bool|int|string|null $query)
    {
        return $this->createQueryBuilder('p')
            ->where('p.reference LIKE :query')
            ->orWhere('p.codebarre LIKE :query')
            ->setParameter('query', "{$query}")
            ->getQuery()->getResult()
            ;
    }
}
