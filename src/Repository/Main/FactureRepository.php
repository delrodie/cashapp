<?php

namespace App\Repository\Main;

use App\Entity\Main\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\QueryBuilder;
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

    public function getVenteByCaisse(int $caisse, string $debut = null, string $fin = null)
    {
        $query = $this->createQueryBuilder('f')
            ->select('SUM(f.nap)')
            ->where('f.caisse = :id')
            ->setParameter('id', $caisse)
            ->groupBy('f.caisse');

        if ($debut && $fin) {
            $query->andWhere('f.createdAt BETWEEN :debut AND :fin')
                ->setParameter('debut', "{$debut} 00:00:00")
                ->setParameter('fin', "{$fin} 23:59:59");
        }

        return $query->getQuery()->getResult();
    }

    public function getListDesc(int $limit=null)
    {
        $query =  $this->createQueryBuilder('f')
            ->addSelect('ca')
            ->addSelect('cl')
            ->leftJoin('f.caisse', 'ca')
            ->leftJoin('f.client', 'cl')
            ->orderBy('f.id', "DESC");
            if ($limit) {
                $query->setMaxResults($limit);
            }
            return $query->getQuery()->getResult();
    }

    public function getTopClient($client, array $periode=null)
    {
        $query = $this->createQueryBuilder('f')
            ->addSelect('c')
            ->leftJoin('f.client', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $client);
        if ($periode){
            $query->andWhere('f.createdAt BETWEEN :debut AND :fin')
                ->setParameters([
                    'debut' => $periode['debut'],
                    'fin' => $periode['fin']
                ]);
        }

        return $query->getQuery()->getResult();
    }

    public function getVenteByPeriode(string $debut, string $fin)
    {
        return $this->createQueryBuilder('f')
//            ->select('SUM(f.nap)')
            ->where('f.createdAt BETWEEN :debut AND :fin')
            ->setParameters([
                'debut' => $debut,
                'fin' => $fin
            ])
            ->getQuery()->getResult();
    }

    public function getFactureNoSync()
    {
            return $this->queryNoSync()->getQuery()->getResult();
    }

    public function getFactureNoSyncNext(int $factureCode)
    {
        return $this->queryNoSync()
            ->andWhere('f.code > :code')
            ->setParameter('code', $factureCode)
            ->getQuery()->getResult();
    }

    /**
     * @return QueryBuilder
     */
    protected function queryNoSync(): QueryBuilder
    {
        return $this->createQueryBuilder('f')
            ->addSelect('cl')
            ->addSelect('ca')
            ->leftJoin('f.client', 'cl')
            ->leftJoin('f.caisse', 'ca')
            ->where('f.sync IS NULL');
    }

    public function getRecetteGlobalParJour()
    {
        return $this->createQueryBuilder('f')
            ->select('f.createdAt, SUM(f.nap) as napTotal')
            ->groupBy('f.createdAt')
            ->getQuery()->getResult();
    }

    public function getListByClient($client)
    {
        return $this->createQueryBuilder('f')
            ->addSelect('c')
            ->leftJoin('f.client', 'c')
            ->getQuery()->getResult();
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
