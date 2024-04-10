<?php

namespace App\Repository;

use App\Entity\Timelog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Timelog>
 *
 * @method Timelog|null find($id, $lockMode = null, $lockVersion = null)
 * @method Timelog|null findOneBy(array $criteria, array $orderBy = null)
 * @method Timelog[]    findAll()
 * @method Timelog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimelogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Timelog::class);
    }

    public function findGroupByWorkedPerDay()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT user.email as User, worked as Month, time(sum(TIMEDIFF(endtime,starttime))) as Total FROM `timelog` LEFT JOIN user ON timelog.user_id = user.id GROUP BY `worked`, `user_id`';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
       
    }

    public function findGroupByWorkedPerMonth()
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT user.email as User, DATE_FORMAT(worked, "%M-%Y") AS Month, time(sum(TIMEDIFF(endtime,starttime))) as Total, COUNT(*) as Entries
  FROM `timelog` LEFT JOIN user ON timelog.user_id = user.id GROUP BY DATE_FORMAT(worked, "%m-%Y"), `user_id`';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
       
    }

//    /**
//     * @return Timelog[] Returns an array of Timelog objects
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

//    public function findOneBySomeField($value): ?Timelog
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
