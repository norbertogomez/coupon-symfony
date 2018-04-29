<?php

namespace App\Controller;

use App\Entity\Coupon;
use App\Repository\CouponRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccountController extends Controller
{
    /**
     * @Route("/account", name="account")
     */
    public function index()
    {
      if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
        return $this->redirectToRoute('home');
      }

      $coupons = $this->getDoctrine()
        ->getRepository(Coupon::class)
        ->findByUserId($this->getUser()->getId());

      return $this->render('account/index.html.twig', [
        'coupons' => $coupons
      ]);
    }
}
