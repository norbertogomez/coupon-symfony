<?php

  namespace App\Repository;

  use App\Entity\Coupon;
  use App\Entity\User;
  use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
  use Symfony\Bridge\Doctrine\RegistryInterface;

  class UserRepository extends ServiceEntityRepository
  {
    public function __construct(RegistryInterface $registry)
    {
      parent::__construct($registry, User::class);
    }

    public function isTheCouponOwner($user_id, Coupon $coupon)
    {
      $entityManager = $this->getEntityManager();

      $query = $entityManager->createQuery('
        SELECT c 
        FROM App\Entity\Coupon c
        JOIN c.user u
        WHERE u.id = ?1
        AND c.id = ?2
      ');

      $query->setParameter(1, $user_id);
      $query->setParameter(2, $coupon->getId());

      return !empty($query->getResult()) ? true : false;
    }
  }