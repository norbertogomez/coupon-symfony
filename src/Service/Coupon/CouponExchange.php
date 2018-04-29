<?php

  namespace App\Service\Coupon;


  use App\Entity\Coupon;
  use App\Entity\User;
  use App\Exceptions\Coupon\CouponAlreadyExchanged;
  use App\Exceptions\Coupon\CouponIsNotFromUser;
  use App\Exceptions\User\UserNotFoundException;
  use App\Repository\CouponRepository;
  use App\Repository\UserRepository;
  use Doctrine\ORM\EntityManager;
  use Doctrine\ORM\EntityManagerInterface;
  use Doctrine\ORM\OptimisticLockException;
  use Doctrine\ORM\ORMException;

  class CouponExchange
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

    public function exchangeCoupon(int $user_id, Coupon $coupon): array
    {
      try {
        $isOwner = $this->userRepository->isTheCouponOwner($user_id, $coupon);

        if (!$isOwner) {
          throw new CouponIsNotFromUser('El cupon no te pertenece');
        }

        if ($coupon->getStatus() === Coupon::STATUS_USED) {
          throw new CouponAlreadyExchanged("El cupón ya ha sido canjeado");
        }

        $coupon
          ->setStatus(Coupon::STATUS_USED);

        $this->entityManager->flush();
      } catch (CouponIsNotFromUser|CouponAlreadyExchanged $exception) {
        return $this->retrieveMessage('error', $exception->getMessage());
      }

      return $this->retrieveMessage('success', 'Tu cupón se ha canjeado satisfactoriamente');
    }

    /**
     * @param $exception
     * @return array
     */
    private function retrieveMessage(string $status, string $message): array
    {
      return [
        'status' => $status,
        'message' => $message,
      ];
    }

  }