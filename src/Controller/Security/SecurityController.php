<?php
  namespace App\Controller\Security;

  use App\Entity\User;
  use App\Form\UserType;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Request;
  use Symfony\Component\Routing\Annotation\Route;
  use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
  use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

  class SecurityController extends Controller
  {
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
      if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
        return $this->redirectToRoute('account');
      }

      $user = new User();
      $form = $this->createForm(UserType::class, $user);

      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('home');
      }

      return $this->render(
        'security/register.html.twig',
        ['form' => $form->createView()]
      );
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
      if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
        return $this->redirectToRoute('account');
      }

      $error = $authenticationUtils->getLastAuthenticationError();
      $lastUsername = $authenticationUtils->getLastUsername();

      return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error'         => $error,
      ]);
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheck(Request $request, AuthenticationUtils $authUtils)
    {
      //Route should exist handled with providers
    }
  }