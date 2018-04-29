<?php

namespace App\Controller;

use App\Entity\Coupon;
use App\Entity\User;
use App\Exceptions\User\UserNotFoundException;
use App\Service\Coupon\CouponExchange;
use App\Service\Coupon\CouponGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CouponController extends Controller
{
  /**
   * @Route("/coupon/create", name="coupon_create", methods={"POST"})
   */
  public function createCoupon(CouponGenerator $couponGenerator)
  {
    $user = $this->verifyUser();

    $coupon = $couponGenerator->generateCoupon($user->getId());

    return new JsonResponse([
      'coupon' => $coupon->getCode(),
      'status' => $coupon->getStatus(),
      'expiration' => $coupon->getExpirationDate()->format('d-m-Y')
    ]);
  }


  /**
   * @route("/coupon/exchange/{id}", name="coupon_exchange", methods={"POST"})
   */
  public function exchangeCoupon($id, CouponExchange $couponExchange)
  {
    $user = $this->verifyUser();
    $coupon = $this->getDoctrine()
      ->getRepository(Coupon::class)
      ->findOneBy(['id' => $id]);

    $message = $couponExchange->exchangeCoupon($user->getId(), $coupon);

    return new JsonResponse($message);
  }

  /**
   * @return mixed
   * @throws UserNotFoundException
   */
  private function verifyUser(): User
  {
    $user = $this->getUser();

    if (empty($user)) {
      throw new UserNotFoundException();
    }
    return $user;
  }
}
