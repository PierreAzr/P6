<?php

namespace App\Repository;

use App\Entity\Advert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

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

    public function findByDate($page, $nbPerPage)
    {
      $value = new \DateTime();
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.appointmentdate >= :val')
            ->setParameter('val', $value)
            ->orderBy('a.appointmentdate', 'ASC')
            ;
            $qb
            // On définit l'annonce à partir de laquelle commencer la liste
            ->setFirstResult(($page-1) * $nbPerPage)
            // Ainsi que le nombre d'annonce à afficher sur une page
            ->setMaxResults($nbPerPage)
            ;

            // On retourne l'objet Paginator correspondant à la requête construite
            return new Paginator($qb, true);
    }

    public function AllCity()
    {
      $value = new \DateTime();
        return $this->createQueryBuilder('a')
            ->andWhere('a.appointmentdate >= :val')
            ->setParameter('val', $value)
            ->select('a.city, COUNT(a.city)')
            ->groupBy('a.city')
            ->orderBy('a.city', 'ASC')
            //->orderBy('COUNT(a.city)', 'DESC')
            ->getQuery()
            ->getArrayResult();
        ;
    }

    public function findByCity($city, $page, $nbPerPage)
    {
      $value = new \DateTime();
        $qb = $this->createQueryBuilder('a')
            ->andWhere('a.city = :city')
            ->setParameter('city', $city)
            ->andWhere('a.appointmentdate >= :val')
            ->setParameter('val', $value)
            ->orderBy('a.appointmentdate', 'ASC')
            ;

            $qb
            ->setFirstResult(($page-1) * $nbPerPage)
            ->setMaxResults($nbPerPage)
            ;

            // On retourne l'objet Paginator correspondant à la requête construite
            return new Paginator($qb, true);
    }

    // public function findCity($value)
    // {
    //     return $this->createQueryBuilder('a')
    //         ->andWhere('a.city = :val')
    //         ->setParameter('val', $value)
    //         ->orderBy('a.city', 'ASC')
    //         ->setMaxResults(10)
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

    public function findCity($city)
    {
      $qb = $this->createQueryBuilder('e');
      $qb->select('DISTINCT  e.city')
         ->where('e.city LIKE :city')
         ->setParameter('city', $city.'%');

      $arrayQb = $qb->getQuery()->getArrayResult();
      $array = array();
      foreach ($arrayQb as $value) {
        $array[] = $value;
      }

      return $array;
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
