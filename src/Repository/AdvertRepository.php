<?php

namespace App\Repository;

use App\Entity\Advert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    public function findByDate()
    {
      $value = new \DateTime();
        return $this->createQueryBuilder('a')
            ->andWhere('a.appointmentdate >= :val')
            ->setParameter('val', $value)
            ->orderBy('a.appointmentdate', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByCity()
    {
      $value = new \DateTime();
        return $this->createQueryBuilder('a')
            ->andWhere('a.appointmentdate >= :val')
            ->setParameter('val', $value)
            ->select('a.city, COUNT(a.city)')
            ->groupBy('a.city')
            ->getQuery()
            ->getArrayResult();
        ;
    }

    public function findBycityshow($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.city = :val')
            ->setParameter('val', $value)
            ->orderBy('a.appointmentdate', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
//    /**
//     * @return Advert[] Returns an array of Advert objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Advert
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
