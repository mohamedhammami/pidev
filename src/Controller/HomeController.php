<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Loan;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(): Response
    {
        $totalCreditAccepted = $this->getDoctrine()->getRepository(Loan::class)->findBy(['status' => 2]);
        $totalCreditRefused  = $this->getDoctrine()->getRepository(Loan::class)->findBy(['status' => 3]);
        $totalUser           = $this->getDoctrine()->getRepository(User::class)->findAll();
        $accuser             = $this->getDoctrine()->getRepository(Loan::class)->findBy(['status' => 2, 'user'=> $this->getUser()->getId()]);
        $acc                 = $this->getDoctrine()->getRepository(Account::class)->findCourantAccount($this->getUser()->getId() );
        $tranche             = $this->getDoctrine()->getRepository(Loan::class)->findBy(['user' => $this->getUser()]);

        return $this->render('home/index.html.twig', [
            'totalaccepted' => $totalCreditAccepted,
            'totalrefused'  => $totalCreditRefused,
            'totalUser'     => $totalUser,
            'accuser'       => $accuser,
            'amount'        => end($acc),
            'tranche'       => $tranche
        ]);
    }

    /**
     * @Route("/aceeuil", name="acceuil_page")
     */
    public function home1():Response
    {
        return $this->render('home1.html.twig');
    }
}
