<?php

namespace App\Repository\Main;

use App\Entity\Main\Achat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Achat>
 *
 * @method Achat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Achat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Achat[]    findAll()
 * @method Achat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AchatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Achat::class);
    }

    public function save(Achat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Achat $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllJoinFournisseur()
    {
        return $this->createQueryBuilder('a')
            ->addSelect('f')
            ->leftJoin('a.fournisseur', 'f')
            ->getQuery()->getResult()
            ;
    }

    public function getAchatNoSync()
    {
        return $this->queryNoSync()->getQuery()->getResult();
    }

    public function getAchatNoSyncNext(int $achatCode)
    {
        return $this->queryNoSync()
            ->andWhere('a.code > :code')
            ->setParameter('code', $achatCode)
            ->getQuery()->getResult()
            ;
    }

    /**
     * @return QueryBuilder
     */
    protected function queryNoSync(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->addSelect('f')
            ->leftJoin('a.fournisseur', 'f')
            ->where('a.sync IS NULL')
            ;
    }


//    /**
//     * @return Achat[] Returns an array of Achat objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Achat
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
