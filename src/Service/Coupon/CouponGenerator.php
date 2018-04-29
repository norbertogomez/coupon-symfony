<?php

  namespace App\Service\Coupon;


  use App\Entity\Coupon;
  use App\Entity\User;
  use App\Exceptions\User\UserNotFoundException;
  use App\Repository\CouponRepository;
  use App\Repository\UserRepository;
  use Doctrine\ORM\EntityManager;
  use Doctrine\ORM\EntityManagerInterface;
  use Doctrine\ORM\OptimisticLockException;
  use Doctrine\ORM\ORMException;

  class CouponGenerator
  {
    private $userRepository;
    private $couponRepository;
    private $entityManager;

    public function __construct(UserRepository $userRepository, CouponRepository $couponRepository, EntityManagerInterface $em)
    {
      $this->userRepository = $userRepository;
      $this->couponRepository = $couponRepository;
      $this->entityManager = $em;
    }

    public function generateCoupon(int $user_id): Coupon
    {
      try {
        $user = $this->findUserForCouponAction($user_id);
      } catch (UserNotFoundException $exception) {
        //Display error messages to frontend
      }
      $couponCode = $this->generateCode($user_id);

      $coupon = new Coupon();

      $coupon
        ->setCode($couponCode)
        ->setStatus(Coupon::STATUS_ACTIVE)
        ->setExpirationDate(new \DateTime('+1 day'));

      try {
        $this->entityManager->persist($coupon);
        $user->addCoupon($coupon);
        $this->entityManager->flush();
      } catch (OptimisticLockException|ORMException $e) {
        //Log the exceptions
      }

      return $coupon;
    }

    /**
     * Returns a coupon valid code
     * @param $user_id
     * @return string
     */
    private function generateCode($user_id)
    {
      return $user_id . '-' . time();
    }

    /**
     * @param int $user_id
     * @throws UserNotFoundException
     */
    private function findUserForCouponAction(int $user_id): User
    {
      $user = $this->userRepository->findOneBy(['id' => $user_id]);

      if (empty($user)) {
        throw new UserNotFoundException("No se ha encontrado el usuario: $user_id");
      }

      return $user;
    }
  }