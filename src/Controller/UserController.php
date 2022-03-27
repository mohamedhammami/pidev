<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordHasherEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $passwordEncod;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->passwordEncod= $encoder;
    }

    /**
     * @Route("/", name="app_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordEncod->hashPassword($user, $form->get('password')->getData()));
            $type = $request->get('type');
            if ($type === "2"){
                $user->setRoles(['ROLE_USER']);
                $user->setIsActive(false);
                /*$email = (new TemplatedEmail());
                $email
                    ->from(new Address('Altrahammami@gmail.com', 'Admin_micro_credit'))
                    ->to($this->getUser()->getEmail())
                    ->subject('Compte crée avec succès')
                    ->htmlTemplate('mail/email.html.twig')
                    ->context([
                        'password' =>  $form->get('password')->getData(),
                        'mail'    => $this->getUser()->getEmail(),
                        'civilité' => $form->get('civility')->getData(),
                        'user' => $user
                    ]);
                $mailer->send($email); */
                $emailAssigned = new TemplatedEmail();
                $emailAssigned
                    ->from(new Address('Micro_credit@group.com', 'Admin_Micro_credit'))
                    ->to($user->getEmail())
                    ->subject('Compte crée avec succès')
                    ->htmlTemplate('mail/email.html.twig')
                    ->context([
                        'password' =>  $form->get('password')->getData(),
                        'civilité' => $form->get('civility')->getData(),
                        'user' => $user
                    ]);
                $mailer->send($emailAssigned);
            }
            else{
                $user->setIsActive(true);
                $user->setRoles(['ROLE_SUPER_ADMIN']);
            }
            $userRepository->add($user);
            if ($type === "2"){
                $this->addFlash('comptec', 'Votre compte sera activé dans 24 heures au près de super Admin');
            }
            else{
                $this->addFlash('comptec', 'Votre compte est crée avec succès');
            }
            return $this->redirectToRoute('app_login', []);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/desactive", name="app_user_show", methods={"POST", "GET"})
     */
    public function show(User $user, UserRepository $repository): Response
    {
        $user->setIsActive(false);
        $repository->add($user);
        return $this->redirectToRoute('app_user_index');
    }

    /**
     * @Route("/{id}/edit_user", name="app_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user);
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/active", name="app_user_active", methods={"POST", "GET"})
     */
    public function active(User $user, UserRepository $repository): Response
    {
        $user->setIsActive(true);
        $repository->add($user);

        return $this->redirectToRoute('app_user_index');
    }
}
