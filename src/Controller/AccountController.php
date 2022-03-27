<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountType;
use App\Repository\AccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/account")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="app_account_index", methods={"GET"})
     */
    public function index(AccountRepository $accountRepository): Response
    {
        return $this->render('account/index.html.twig', [
            'accounts' => $accountRepository->findAll(),
        ]);
    }

    /**
     * @Route("/epargne", name="epargne_account_index", methods={"GET"})
     */
    public function compteEpargne(AccountRepository $accountRepository): Response
    {
        $acccc= $accountRepository->findEpargneAccount($this->getUser()->getId());
        if ($acccc != null){
            return $this->render('account/epargne_account_index.html.twig', [
                'account' => end($acccc),
            ]);
        }
        else
            return $this->render('account/epargne_account_index.html.twig', [
                'account' => end($acccc),
            ]);


    }

    /**
     * @Route("/courant", name="courant_account_index", methods={"GET"})
     */
    public function compteCourant(AccountRepository $accountRepository): Response
    {
        $acccc= $accountRepository->findCourantAccount($this->getUser()->getId());
        if ($acccc != null){
            return $this->render('account/courant_account_index.html.twig', [
                'account' => end($acccc),
            ]);
        }
        else
            $acccc = [];
            return $this->render('account/courant_account_index.html.twig', [
                'account' => end($acccc),
            ]);

    }

    /**
     * @Route("/new", name="app_account_new", methods={"GET", "POST"})
     */
    public function new(Request $request, AccountRepository $accountRepository): Response
    {
        $account = new Account();
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $account->setUser($this->getUser());
            $account->setIsClosed(1);
            $account->setOpeningDate(new \DateTime('now'));
            $accountRepository->add($account);
            return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/new.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_account_show", methods={"GET"})
     */
    public function show(Account $account): Response
    {
        return $this->render('account/show.html.twig', [
            'account' => $account,
        ]);
    }

    /**
     * @Route("/{id}/validate/transaction", name="app_validate_transaction", methods={"GET", "POST"})
     */
    public function validateTransaction(Account $account, Request $request, AccountRepository $repository): Response
    {
        $accountBenif = $this->getDoctrine()->getRepository(Account::class)->findOneBy(['rib' => $request->get('ribBenif')]);
        $account->setBalance($account->getBalance() - $request->get('amount'));
        $accountBenif->setBalance($accountBenif->getBalance() + $request->get('amount'));

        $repository->add($account);
        $repository->add($accountBenif);

        return $this->redirectToRoute('courant_account_index');
    }

    /**
     * @Route("/{id}/edit", name="app_account_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Account $account, AccountRepository $accountRepository): Response
    {
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accountRepository->add($account);
            return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('account/edit.html.twig', [
            'account' => $account,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_account_delete", methods={"POST"})
     */
    public function delete(Request $request, Account $account, AccountRepository $accountRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$account->getId(), $request->request->get('_token'))) {
            $accountRepository->remove($account);
        }

        return $this->redirectToRoute('app_account_index', [], Response::HTTP_SEE_OTHER);
    }
}
