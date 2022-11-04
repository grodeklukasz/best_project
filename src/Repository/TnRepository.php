<?php

namespace App\Repository;

use App\Entity\Tn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tn>
 *
 * @method Tn|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tn|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tn[]    findAll()
 * @method Tn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TnRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tn::class);
    }

    public function add(Tn $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Tn $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllByDetails(array $criteria=null)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT *
            FROM tn
            LEFT JOIN termin
            ON tn.id = termin.tn_id
            LEFT JOIN termin_type
            ON termin_type.id = termin.termintype_id
        ';

        if($criteria!=Null){
            $sql = $sql . " WHERE ";
            foreach($criteria as $key=>$value){
                $sql = $sql . $key . "= :" . $key . " AND ";
            }
        }
            $sql = rtrim($sql, " AND ");
            
            $stmt = $conn->prepare($sql);

            $resultSet = $stmt->executeQuery($criteria);

            return $resultSet->fetchAllAssociative();
    }

    public function findAllAsArray(array $criteria=null)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT *
            FROM tn
        ';

        if($criteria!=Null){
            $sql = $sql . " WHERE ";
            foreach($criteria as $key=>$value){
                $sql = $sql . $key . "= :" . $key . " AND ";
            }
        }
            $sql = rtrim($sql, " AND ");
            
            $stmt = $conn->prepare($sql);

            $resultSet = $stmt->executeQuery($criteria);

            return $resultSet->fetchAllAssociative();
    }

//    /**
//     * @return Tn[] Returns an array of Tn objects
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

//    public function findOneBySomeField($value): ?Tn
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
