<?php

namespace App\Controller;

use App\Entity\Jobcoach;
use App\Service\SessionService;
use App\Repository\JobcoachRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use \Doctrine\Common\Collections\Criteria;
use \Doctrine\Common\Collections\Expr\Comparison;

use Doctrine\Persistence\ManagerRegistry;


class BenutzerController extends AbstractController
{
    private $user;
    private $isLogged;

    private function checkUser(SessionService $sessionService):bool
    {
        $checker = true;

        if(!$sessionService->getSessionValue('islogged')){
            
            $checker = false;
        }

        if($sessionService->getSessionValue('role')!='admin'){
            
            $checker = false;

        }

        $this->isLogged = true;

        $this->user = [
            'id' => $sessionService->getSessionValue('id'),
            'nachname' => $sessionService->getSessionValue('nachname'),
            'vorname' => $sessionService->getSessionValue('vorname'),
            'email' => $sessionService->getSessionValue('email'),
            'role' => $sessionService->getSessionValue('role')
        ];

        return $checker;

    }


    /**
     * @Route("/panel/benutzer-verwaltung", name="app_benutzer_verwaltung")
     */
    public function index(
        SessionService $sessionService,
        JobcoachRepository $jobcoachRepository
        ): Response
    {
        if(!$this->checkUser($sessionService)){
            return $this->redirectToRoute('app_home');
        }

        $criteria = new Criteria();
        $criteria->orderBy(['nachname'=>Criteria::ASC]);
        $users = $jobcoachRepository->matching($criteria);


        return $this->render('benutzer/index.html.twig', [
            'isLogged' => $this->isLogged,
            'user' => $this->user,
            'users' => $users
        ]);
    }
    /**
     * @Route("/panel/benutzer-verwaltung/edit/{userid}", name="app_benutzer_verwaltung_edit")
     */
    public function benutzerVerwaltungEdit(
        int $userid,
        JobcoachRepository $jobcoachRepository,
        Request $request,
        ManagerRegistry $doctrine
        ):Response {

        $jobcoach = $jobcoachRepository->findOneBy(['id'=>$userid]);

        $entityManager = $doctrine->getManager();

        $editForm = $this->createFormBuilder()
        ->add('jobcoachid', HiddenType::class, [
            'data' => $userid
        ])
        ->add('Nachname', TextType::class,[
            'required' => True,
            'attr' => ['class'=>'form-control form-control-sm'],
            'data' => $jobcoach->getNachname()
        ])
        ->add('Vorname',TextType::class,[
            'required' => True,
            'attr' => ['class'=>'form-control form-control-sm'],
            'data' => $jobcoach->getVorname()
        ])
        ->add('Benutzer',ChoiceType::class,[
            'attr' => ['class'=>'form-control form-control-sm'],
            'choices' => [
                'admin' => 'admin',
                'user' => 'user'],
            'data' => $jobcoach->getRole()
        ])
        ->add('Rufnummer',TextType::class,[
            'required' => True,
            'attr' => ['class'=>'form-control form-control-sm'],
            'data' => $jobcoach->getTelefonnummer()
        ])
        ->add('Email',EmailType::class,[
            'required' => True,
            'attr' => ['class'=>'form-control form-control-sm'],
            'data' => $jobcoach->getEmail()
        ])
        ->add('Status', ChoiceType::class,[
            'required' => True,
            'choices' => [
                'Aktive' => 1,
                'Inaktive' => 0,
            ],
            'data' => $jobcoach->isIsActive(),
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Kennwort',PasswordType::class,[
            'required' => False,
            'label' => 'Kennwort (restart)',
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Aktualisieren',SubmitType::class,[
            'attr' => ['class' => 'btn btn-outline-warning form-control']
        ])
        ->getForm();

        $editForm->handleRequest($request);

        if($editForm->isSubmitted()){

            $currentJobcoach = $jobcoachRepository->findOneBy(['id' => $editForm->get('jobcoachid')->getData()]);


            if($editForm->get('Kennwort')->getData()!==Null){

                $hashedPassword = hash('sha3-512', $editForm->get('Kennwort')->getData());

                $currentJobcoach->setKennwort($hashedPassword);

            }
            
            $currentJobcoach->setVorname($editForm->get('Vorname')->getData());
            $currentJobcoach->setNachname($editForm->get('Nachname')->getData());
            $currentJobcoach->setRole($editForm->get('Benutzer')->getData());
            $currentJobcoach->setEmail($editForm->get('Email')->getData());
            $currentJobcoach->setTelefonnummer($editForm->get('Rufnummer')->getData());
            $currentJobcoach->setIsActive($editForm->get('Status')->getData());

            $entityManager->flush();

            return $this->redirectToRoute('app_home');

        }

        return $this->render('benutzer/edit.html.twig',[
            'isLogged' => $this->isLogged,
            'user' => $this->user,
            'editForm' => $editForm->createView()
        ]);
    }
}
