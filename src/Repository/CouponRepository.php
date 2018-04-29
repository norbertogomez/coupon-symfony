<?php

namespace App\Repository;

use App\Entity\Coupon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Coupon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coupon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coupon[]    findAll()
 * @method Coupon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CouponRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

    public function findByUserId($user_id)
    {
      $entityManager = $this->getEntityManager();

      $query = $entityManager->createQuery('
        SELECT c 
        FROM App\Entity\Coupon c
        JOIN c.user u
        WHERE u.id = ?1
      ');
      $query->setParameter(1, $user_id);

      return $query->getResult();
    }
}
