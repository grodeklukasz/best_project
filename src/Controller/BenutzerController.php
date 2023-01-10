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
     * @Route("/panel/benutzer-verwaltung/add", name="app_benutzer_verwaltung_add")
     */
    public function benutzerVerwaltungAdd(
        SessionService $sessionService,
        Request $request,
        JobcoachRepository $jobcoachRepository,
        ManagerRegistry $doctrine
        ):Response {

        if(!$this->checkUser($sessionService)){
            return $this->redirectToRoute('app_home');
        }

        $entityManager = $doctrine->getManager();

        $msg = '';

        $addForm = $this->createFormBuilder()
        ->add('Nachname', TextType::class, [
            'label' => 'Nachname*',
            'required' => True,
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Vorname', TextType::class, [
            'label'=>'Vorname*',
            'required' => True,
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Rufnummer', TextType::class, [
            'required' => False,
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Email', EmailType::class, [
            'label' => 'Email*',
            'required' => True,
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Kennwort', PasswordType::class,[
            'label'=>'Kennwort*',
            'required' => True,
            'attr' => ['class'=>'form-control form-control-sm']
            ]
        )
        ->add('Benutzer', ChoiceType::class,[
            'choices' => [
                'User' => 'user',
                'Admin' => 'admin'
            ],
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Status', ChoiceType::class,[
            'choices' => [
                'Aktive' => 1,
                'Inaktive' => 0
            ],
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Speichern', SubmitType::class, [
            'attr' => [
                'class'=>'btn btn-outline-success btn-sm form-control',
                'style' => 'margin-top: 10px;'
                ]
        ])
        ->getForm();

        $addForm->handleRequest($request);

        if($addForm->isSubmitted()){

            $findJobCoach = $jobcoachRepository->findBy(['email'=>$addForm->get('Email')->getData()]);

            if($findJobCoach){
                $msg = "E-Mail Adresse existiert. Benutzerkonto kann nich erstellt werden.";
            }else{
                
                $jobcoach = new JobCoach();

                $jobcoach->setVorname($addForm->get('Vorname')->getData());
                $jobcoach->setNachname($addForm->get('Nachname')->getData());
                $jobcoach->setTelefonnummer($addForm->get('Rufnummer')->getData());
                $jobcoach->setEmail($addForm->get('Email')->getData());
                $jobcoach->setKennwort(hash('sha3-512',$addForm->get('Kennwort')->getData()));
                $jobcoach->setRole($addForm->get('Benutzer')->getData());
                $jobcoach->setIsActive($addForm->get('Status')->getData());

                $entityManager->persist($jobcoach);

                $entityManager->flush();

                return $this->redirectToRoute('app_home');

            }

        }

        return $this->render('benutzer/add.html.twig',[
            'isLogged' => $this->isLogged,
            'user' => $this->user,
            'addForm' => $addForm->createView(),
            'msg' => $msg
        ]);
    }
    /**
     * @Route("/panel/benutzer-verwaltung/edit/{userid}", name="app_benutzer_verwaltung_edit")
     */
    public function benutzerVerwaltungEdit(
        int $userid,
        SessionService $sessionService,
        JobcoachRepository $jobcoachRepository,
        Request $request,
        ManagerRegistry $doctrine
        ):Response {

        if(!$this->checkUser($sessionService)){
            return $this->redirectToRoute('app_home');
        }

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
