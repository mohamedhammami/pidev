<?php

namespace App\Controller;

use App\Entity\Loan;
use App\Entity\Tranche;
use App\Form\LoanType;
use App\Repository\LoanRepository;
use App\Repository\TrancheRepository;
use App\Repository\UserRepository;
use DateInterval;
use DateTime;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/loan")
 */
class LoanController extends AbstractController
{
    /**
     * @Route("/", name="app_loan_index", methods={"GET"})
     */
    public function index(LoanRepository $loanRepository): Response
    {
        return $this->render('loan/index.html.twig', [
            'loans' => $loanRepository->findBy(['status' => 1]),
        ]);
    }
    /**
     * @Route("/loans", name="loan_client", methods={"GET"})
     */
    public function getLoans(LoanRepository $loanRepository, TrancheRepository $repository):Response
    {
        $loans    = $loanRepository->findBy(['user' => $this->getUser()->getId()]);

        return $this->render('loan/loans.html.twig', [
            'loans' => $loans,
        ]);
    }

    /**
     * @Route("/new", name="app_loan_new", methods={"GET", "POST"})
     */
    public function new(Request $request,UserRepository $userRepository, LoanRepository $loanRepository, MailerInterface $mailer): Response
    {
        $user= $userRepository->findOneBy(['isSuperAdmin' => 1]);

        $loan = new Loan();
        $form = $this->createForm(LoanType::class, $loan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $loan->setUser($this->getUser());
            $loan->setStatus(1);
            $loan->setDateOfSend(new \DateTime('now'));
            $loanRepository->add($loan);
            $emailAssigned = new TemplatedEmail();
            $emailAssigned
                ->from(new Address('Micro_credit@group.com', 'Admin_Micro_credit'))
                ->to($user->getEmail())
                ->subject('Nouvelle demande de crédit')
                ->htmlTemplate('mail/demande.html.twig')
                ->context([
                    'loan' =>  $loan,
                    'user' => $user
                ]);
            $mailer->send($emailAssigned);
            return $this->redirectToRoute('app_loan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('loan/new.html.twig', [
            'loan' => $loan,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_loan_show", methods={"GET"})
     */
    public function show(Loan $loan): Response
    {
        return $this->render('loan/show.html.twig', [
            'loan' => $loan,
        ]);
    }
    function puissance($x,$y)
    {
        $resultat=1;
        for ($i=0;$i<$y;$i++)
            $resultat *=  $x;
        return $resultat;
    }
    /**
     * @Route("/{id}/accept", name="app_loan_accept", methods={"GET", "POST"})
     */
    public function acceptCredit(Request $request, Loan $loan, LoanRepository $loanRepository, TrancheRepository $repository): Response
    {
        $loan->setStatus(2);

        $loanRepository->add($loan);

        //le capital demandé
        $amount = $loan->getAmount();
        //le taux d'interêt
        $t = 0.1;
        //n c'est le nombre des mois
        $n = ($loan->getDuration());

        $value = (1+($t/12));
        $value1 = pow($value, -($n));
        $finalvalue = 1 - $value1;
        $m = ($amount * ($t/12))/$finalvalue;
        for ($i=0; $i<$n; $i++){
            $tranche = new Tranche();
            $tranche->setAmount($m);
            $tranche->setCredit($loan);
            $tranche->setIsVAlid(true);
            $day = new DateTime('now');
            $now = strtotime($day->format('Y-m-d H:i:s'));

            $valuedate= date('d-m-Y', strtotime('+' . $i .' month', $now)) . PHP_EOL;
            $tranche->setStartDate(new DateTime($valuedate));

            $interval = new DateInterval('P1M');
            $day1 = new DateTime('now');
            $day1->add($interval);

            $now1 = strtotime($day1->format('Y-m-d H:i:s'));
            $valuedate1= date('d-m-Y', strtotime('+' . $i .' month', $now1)) . PHP_EOL;
            $tranche->setEndDate(new DateTime($valuedate1));


            //$date = $tranche->getStartDate();
            //$date->add(new DateInterval("P31D"));

            //$tranche->setEndDate($tranche->getStartDate()->add(new DateInterval("P31D")));
            $repository->add($tranche);
        }

        return $this->redirectToRoute('app_loan_index');
    }

    /**
     * @Route("/{id}/refuse", name="app_loan_refuse", methods={"GET", "POST"})
     */
    public function refuseCredit(Request $request, Loan $loan, LoanRepository $loanRepository): Response
    {
        $loan->setStatus(3);
        $loanRepository->add($loan);

        return $this->redirectToRoute('app_loan_index');
    }
    /**
     * @Route("/{id}/show_tranche", name="show_tranche_loan", methods={"GET"})
     */
    public function getTranche(Loan $loan):Response
    {
        $tranches = $loan->getTranches();
        return $this->render('loan/show_tranche.html.twig', [
            'tranches' => $tranches
        ]);
    }

}
