<?php

namespace App\Repository;

use App\Entity\Termin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Termin>
 *
 * @method Termin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Termin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Termin[]    findAll()
 * @method Termin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TerminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Termin::class);
    }

    public function add(Termin $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Termin $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function countTypesOfTermin(int $tnId, int $terminType): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT Count(*) as counter FROM termin where termintype_id = :terminType and tn_id = :tnId';

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery(['terminType' => $terminType, 'tnId' => $tnId]);

        return $resultSet->fetchAllAssociative();       
    }

    public function countTerminsByType(int $terminTypeId): array 
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql ='SELECT Count(*) as counter FROM termin where termintype_id = :terminTypeId';

        $stmt = $conn->prepare($sql);

        $resultSet = $stmt->executeQuery(['terminTypeId' => $terminTypeId]);

        return $resultSet->fetchAllAssociative();

    }

//    /**
//     * @return Termin[] Returns an array of Termin objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Termin
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
