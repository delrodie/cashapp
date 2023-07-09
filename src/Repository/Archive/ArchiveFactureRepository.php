<?php

namespace App\Repository\Archive;

use App\Entity\Archive\Facture;
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
class ArchiveFactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    public function remove(Facture $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAll()
    {
        return $this->createQueryBuilder('f')
            ->addSelect('u')
            ->leftJoin('f.user', 'u')
            ->getQuery()->getResult();
    }

    public function getRecetteJournaliereGlobale()
    {
        return $this->createQueryBuilder('f')
            ->select('f.date, SUM(f.montant) as totalMontant')
//            ->addSelect('f.date')
            ->groupBy('f.date')
            ->orderBy('f.date',"DESC")
            ->getQuery()->getResult();
    }

    public function getRecetteJournaliereByCaisse()
    {
        return $this->createQueryBuilder('f')
            ->select('f.date, SUM(f.montant) as totalMontant, u.username as caisse')
            ->leftJoin('f.user', 'u')
            ->groupBy('f.date, caisse')
            ->orderBy('f.date', "DESC")
            ->getQuery()->getResult();
    }

    public function getBilan(int $an)
    {
        return $this->createQueryBuilder('f')
            ->where('f.date BETWEEN :debut AND :fin')//038058
            ->andWhere('f.reference <> :code')
            ->setParameters([
                'debut' => "{$an}-01-01",
                'fin' => "{$an}-12-31",
                'code' => "038058"
            ])
            ->getQuery()->getResult();
    }

    public function suppression()
    {
        return $this->createQueryBuilder('f')
            ->delete(Facture::class, 'f')
            ->where('f.reference = :code')
            ->setParameter('code', '038058');
    }
}