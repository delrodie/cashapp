<?php

namespace App\Repository\Main;

use App\Entity\Main\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Facture>
 *
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    public function save(Facture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Facture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllList()
    {
        return $this->createQueryBuilder('f')
            ->addSelect('ca')
            ->addSelect('cl')
            ->leftJoin('f.caisse', 'ca')
            ->leftJoin('f.client', 'cl')
            ->getQuery()->getResult()
            ;
    }

    public function getTotalNAP()
    {
        return $this->createQueryBuilder('f')
            ->select('SUM(f.nap)')
            ->getQuery()->getSingleScalarResult()
            ;
    }

    public function findByCaisseAndPeriode(int $id, string $debut=null, string $fin=null)
    {
        $query =  $this->createQueryBuilder('f')
            ->addSelect('c')
            ->leftJoin('f.caisse', 'c')
            ->where('c.id = :id');
            if($debut AND $fin){
                $query->andWhere('f.createdAt BETWEEN :debut AND :fin')
                ->setParameters([
                    'id' => $id,
                    'debut' => "{$debut} 00:00:00",
                    'fin' => "{$fin} 23:59:59"
                ]);
            }else{
                $query->setParameter('id', $id);
            }
           return  $query->getQuery()->getResult();
    }

//    /**
//     * @return Facture[] Returns an array of Facture objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Facture
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
