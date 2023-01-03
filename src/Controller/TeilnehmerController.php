<?php

namespace App\Controller;

use App\Service\SessionService;

use App\Entity\Tn;
use App\Entity\Jobcoach;
use App\Entity\Fm;
use App\Entity\Termin;
use App\Repository\TnRepository;
use App\Repository\JobcoachRepository;
use App\Repository\TerminRepository;
use App\Repository\TerminTypeRepository;
use App\Repository\FmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


use Doctrine\Persistence\ManagerRegistry;

class TeilnehmerController extends AbstractController
{
    private $user;
    private $isLogged;

    public function __construct(SessionService $sessionService){
        
        if(!$sessionService->getSessionValue('islogged'))
            return $this->redirectToRoute('app_home');
        
        $this->isLogged = True;

        $this->user = [
            'id' => $sessionService->getSessionValue('id'),
            'nachname' => $sessionService->getSessionValue('nachname'),
            'vorname' => $sessionService->getSessionValue('vorname'),
            'email' => $sessionService->getSessionValue('email'),
            'role' => $sessionService->getSessionValue('role')
         ];

    }

    /**
     * @Route("/panel/teilnehmer/{id}", name="app_teilnehmer") 
     */
    public function app_teilnehmer(
        int $id, 
        SessionService $sessionService, 
        TnRepository $tnRepository, 
        JobcoachRepository $jobcoachRepository,
        FmRepository $fmRepository,
        TerminRepository $terminRepository
        ): Response
        {
        
        if($this->user['role']=='admin'){
            $tnDetails = $tnRepository->findOneBy(['id'=>$id]);
        }else{
            $tnDetails = $tnRepository->findOneBy(
                [
                    'id'=>$id, 
                    'jobcoach'=>$jobcoachRepository->findBy(['id'=>$this->user['id']]),
                    'status' => True
                ]);
        }
        
        if($tnDetails==null){
            return $this->redirectToRoute('app_panel');
        }

        $allTermine = $terminRepository->findBy(['tn'=>$tnDetails->getId()]);

        $fm = $fmRepository->findOneBy(['id'=>$tnDetails->getFm()]);
        $jobcoach = $jobcoachRepository->findOneBy(['id'=>$tnDetails->getJobcoach()]);

      return $this->render('/teilnehmer/index.html.twig',[
        'isLogged' => $this->isLogged,
        'user' => $this->user,
        'tndetails' => $tnDetails,
        'fm' => $fm,
        'jobcoach' => $jobcoach,
        'allTermine' => $allTermine
      ]);
    }
    /**
     * @Route("/panel/addTeilnehmer", name="addTeilnehmer")
     */
    public function app_teilnehmer_add(
        JobcoachRepository $jobcoachRepository,
        FmRepository $fmRepository,
        TnRepository $tnRepository,
        TerminRepository $terminRepository,
        TerminTypeRepository $terminTypeRepository,
        Request $request,
        ManagerRegistry $doctrine
    ):Response
    {
        $entityManager = $doctrine->getManager();

        $fms = $fmRepository->findAll();

        $addForm = $this->createFormBuilder()
        ->add('Nachname', TextType::class,[
            'required' => True,
            'attr' => ['class' => 'form-control form-control-sm']
        ])
        ->add('Vorname', TextType::class,[
            'required' => True,
            'attr' => ['class' => 'form-control form-control-sm']
        ])
        ->add('Telefonnummer', TextType::class,[
            'required' => True,
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Email', TextType::class,[
            'required' => True,
            'attr' => ['class' => 'form-control form-control-sm']
        ])
        ->add('Gebdatum', DateType::class, [
            'required' => True,
            'widget' => 'single_text',
            'attr' => ['class'=>'form-control form-control-sm']
        ]) 
        ->add('Pseudonym', TextType::class,[
            'required' => True,
            'attr' =>['class'=>'form-control form-control-sm']
        ])
        ->add('Zuweisen', DateType::class, [
            'required' => True,
            'widget' => 'single_text',
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Starttermin', DateType::class, [
            'required' => True,
            'widget' => 'single_text',
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Fm', ChoiceType::class,[
            'required' => True,
            'choices' => $fms,
            'choice_value' => 'id',
            'choice_label' => function(?Fm $fm){
                return $fm ? $fm->getNachname() . ', ' . $fm->getVorname() : '';
            },
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Bemerkung',TextAreaType::class,[
            'required' => False,
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Speichern',SubmitType::class,[
            'attr' => ['class' => 'btn form-control btn-outline-success']
        ])
        ->getForm();

        $addForm->handleRequest($request);

        if($addForm->isSubmitted()){

            $nachname = $addForm->get('Nachname')->getData();
            $vorname = $addForm->get('Vorname')->getData();
            $telefonnummer = $addForm->get('Telefonnummer')->getData();
            $email = $addForm->get('Email')->getData();
            $gebdatum = $addForm->get('Gebdatum')->getData();
            $pseudonym = $addForm->get('Pseudonym')->getData();
            $zuweisen = $addForm->get('Zuweisen')->getData();
            $starttermin = $addForm->get('Starttermin')->getData();
            $bemerkung = $addForm->get('Bemerkung')->getData();
            $fm = $addForm->get('Fm')->getData();

            $tn = new Tn();
                        
            $jobcoach = $jobcoachRepository->find($this->user['id']);

            $tn->setNachname($nachname);
            $tn->setVorname($vorname);
            $tn->setTelefonnummer($telefonnummer);
            $tn->setEmail($email);
            $tn->setGebDatum($gebdatum);
            $tn->setPseudonym($pseudonym);
            $tn->setStarttermin($starttermin);
            $tn->setBemerkung($bemerkung);
            $tn->setJobcoach($jobcoach);
            $tn->setFm($fm);
            $tn->setStatus(True);

            $entityManager->persist($tn);

            $entityManager->flush();

            //get id new added TN
            $tn_id = $tn->getId();

            //get new added TN
            $newTn = $tnRepository->find($tn_id);

            $termin = new Termin();

            $termin->setTn($newTn);
            //zuweisung type id 26
            $terminType = $terminTypeRepository->findOneBy(['id'=>26]);
            //set TerminType
            $termin->setTermintype($terminType);
            //set Date of Termin
            $termin->setTerminDatum($zuweisen);
            //set if verschoben ist auf 0 (is not)
            $termin->setVerschoben(0);

            $entityManager->persist($termin);

            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('/teilnehmer/add.html.twig',[
            'isLogged' => $this->isLogged,
            'user' => $this->user,
            'addForm' => $addForm->createView()
        ]);
    }

    /**
     * @Route("/panel/teilnehmer/delete/{id}", name="app_teilnehmer_delete")
     */
    public function app_teilnehmer_delete(
        int $id,
        Request $request,
        ManagerRegistry $doctrine,
        TnRepository $tnRepository,
        SessionService $sessionService
    ):Response
    {
        if($sessionService->checkConditions()){
            return $this->redirectToRoute('app_home');
        }
        $entityManager = $doctrine->getManager();

        $deleteForm=$this->createFormBuilder()
        ->add('nummer', HiddenType::class,[
            'data' => $id
        ])
        ->add('Ja', SubmitType::class,[
            'attr' => ['class'=>'btn form-control btn-danger']
        ])
        ->getForm();

        $deleteForm->handleRequest($request);

        if($deleteForm->isSubmitted()){
            
            $id = $deleteForm->get('nummer')->getData();

            $tn = $tnRepository->find($id);

            $tn->setStatus(False);

            $entityManager->flush();

            return $this->redirectToRoute('app_home');

        }

        return $this->render('teilnehmer/delete.html.twig',[
            'id' => $id,
            'isLogged' => $this->isLogged,
            'user' => $this->user,
            'questionText' => 'Sind Sie sicher?',
            'deleteForm' => $deleteForm->createView()
        ]);
    }

    /**
     * @Route("/panel/teilnehmer/active/{id}", name="app_teilnehmer_active")
     */
    public function app_teilnehmer_active(
        int $id,
        ManagerRegistry $doctrine,
        TnRepository $tnRepository
        ){

        $entityManager = $doctrine->getManager();

        $tn = $tnRepository->find($id);

        $tn->setStatus(True);

        $entityManager->flush();


        return $this->redirectToRoute('app_teilnehmer', ['id' => $id]);
    }

    /**
     * @Route("/panel/teilnehmer/edit/{id}", name="app_teilnehmer_edit")
     */
    public function app_teilnehmer_edit(
        int $id, 
        Request $request,
        TnRepository $tnRepository,
        FmRepository $fmRepository, 
        JobcoachRepository $jobcoachRepository,
        ManagerRegistry $doctrine
        ): Response 
    {

        $entityManager = $doctrine->getManager();

        $tn = $tnRepository->find($id);

        $jobcoaches = $jobcoachRepository->findAll();

        $fms = $fmRepository->findAll();

        $editForm = $this->createFormBuilder()
        ->add('Nachname', TextType::class,[
            'required' => True,
            'data' => $tn->getNachname(),
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Vorname', TextType::class,[
            'required' => True,
            'data' => $tn->getVorname(),
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Gebdatum', DateType::class,[
            'widget' => 'single_text',
            'data' => $tn->getGebdatum(),
            'required' => True,
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Email', TextType::class,[
            'data' => $tn->getemail(),
            'required' => True,
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Telefonnummer', TextType::class,[
            'required' => True,
            'data' => $tn->getTelefonnummer(),
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Pseudonym', TextType::class,[
            'required' => True,
            'data'=> $tn->getPseudonym(),
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Starttermin', DateType::class,[
            'widget' => 'single_text',
            'required' => False,
            'data' => $tn->getStarttermin(),
            'attr'=>['class'=>'form-control form-control-sm']
        ])
        ->add('Jobcoach', ChoiceType::class,[
            'required' => True,
            'choices' => $jobcoaches,
            'choice_value' => 'id',
            'choice_label' => function(?Jobcoach $jobcoach){
                return $jobcoach ? $jobcoach->getNachname() . ', ' . $jobcoach->getVorname() : '';
            },
            'data' => $tn->getJobcoach(),
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Fm', ChoiceType::class,[
            'required' => True,
            'choices' => $fms,
            'choice_value' => 'id',
            'choice_label' => function(?Fm $fm){
                return $fm ? $fm->getNachname() . ', ' . $fm->getVorname() : '';
            },
            'data' => $tn->getFm(),
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('tn_id', HiddenType::class,[
            'data'=>$tn->getId(),
        ])
        ->add('Aktualisieren', SubmitType::class,[
            'attr'=>['class'=>'btn btn-sm btn-outline-warning']
        ])
        ->getForm();

        $editForm->handleRequest($request);

        //edit form is submitted
        if($editForm->isSubmitted()){

                       
            //find element in DB

            $tn = $tnRepository->find($editForm->get('tn_id')->getData());

            $tn->setNachname($editForm->get('Nachname')->getData());

            $tn->setVorname($editForm->get('Vorname')->getData());

            $tn->setGebdatum($editForm->get('Gebdatum')->getData());

            $tn->setTelefonnummer($editForm->get('Telefonnummer')->getData());

            $tn->setEmail($editForm->get('Email')->getData());

            $tn->setPseudonym($editForm->get('Pseudonym')->getData());

            $tn->setStarttermin($editForm->get('Starttermin')->getData());

            $tn->setJobcoach($editForm->get('Jobcoach')->getData());

            $entityManager->flush();
           
            $backurl = "/panel/teilnehmer/".$editForm->get('tn_id')->getData();

            return $this->render('/messages/msg.html.twig',[
                'backurl' => $backurl, 
                'isLogged' => $this->isLogged,
                'user' => $this->user,
                'msg_text' => 'Teilnehmer wurde aktualisiert!'
            ]);

        }

        return $this->render('/teilnehmer/edit.html.twig',[
            'isLogged' => $this->isLogged,
            'user' => $this->user,
            'id' => $tn->getNachname(),
            'editForm' => $editForm->createView()
        ]);
    }
    
}
