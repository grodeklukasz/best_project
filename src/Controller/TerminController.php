<?php

namespace App\Controller;

use App\Entity\TerminType;
use App\Entity\Termin;

use App\Service\SessionService;
use App\Repository\TerminRepository;
use App\Repository\TerminTypeRepository;
use App\Repository\TnRepository;
use App\Repository\JobcoachRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

use Doctrine\Persistence\ManagerRegistry;

class TerminController extends AbstractController
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
     * @Route("/termin", name="app_termin")
     */
    public function index(
        SessionService $sessionService, 
        TerminRepository $terminRepository, 
        TnRepository $tnRespository,
        JobcoachRepository $jobcoachRepository
        ): Response
    {

        //today date
        $date = new \DateTimeImmutable();

        //if user has admin permissions
        //get all Teilnehmer
        if($jobcoachRepository->find($this->user['id'])->getRole()=='admin'){

            $allTn = $tnRespository->findAll();

        }else{

            $allTn = $tnRespository->findBy(
                [
                    'jobcoach'=>$jobcoachRepository->find($this->user['id']),
                ],
                ['nachname'=>'ASC']
            );
        
        }

        // for each TN find Termins

        foreach($allTn as $tn){
            $alleTermine[] = $terminRepository->findBy(
                [
                    'tn'=>$tn->getId(),
                    'termindatum'=>$date
                ],
                []);
        }
        
      
        
        return $this->render('termin/index.html.twig', [
            'isLogged' => $this->isLogged,
            'user' => $this->user,
            'alleTermine' => $alleTermine
        ]);
    }

    /**
     * @Route("/termin/addTermin/{tnId}", name="app_addTermin")
     */
    public function addTermin(
        int $tnId,
        Request $request,
        TnRepository $tnRepository,
        TerminRepository $terminRepository,
        TerminTypeRepository $terminType,
        ManagerRegistry $doctrine
        ):Response 
    {
        $tn = $tnRepository->find($tnId);

        $terminTypes = $terminType->findAll();

        $verschoben = [
            'Nein' => 0,
            'Ja' => 1
        ];

        $entityManager = $doctrine->getManager();

        $addTerminForm = $this->createFormBuilder()
        ->add('tnId',HiddenType::class,[
            'data' => $tn->getId()
        ])
        ->add('Nachname',TextType::class,[
            'required' => True,
            'data' => $tn->getNachname(),
            'disabled' => True,
            'attr' => ['class' => 'form-control form-control-sm']
        ])
        ->add('Vorname', TextType::class,[
            'required' => True,
            'data' => $tn->getVorname(),
            'disabled' => True,
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Termindatum',DateType::class,[
            'required' => True,
            'widget' => 'single_text',
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Termin',ChoiceType::class,[
            'required' => True,
            'choices' => $terminTypes,
            'choice_value' => 'id',
            'choice_label' => function(?TerminType $terminType){
                return $terminType ? $terminType->getTerminname() : '';
            },
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Verschoben', ChoiceType::class,[
            'required' => True,
            'choices' => $verschoben,
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Bemerkung',TextareaType::class,[
            'required' => False,
            'attr' => ['class'=>'form-control']
        ])
        ->add('Speichern',SubmitType::class,[
            'attr'=>['class'=>'btn form-control btn-sm btn-outline-success']
        ])
        ->getForm();

        $addTerminForm->handleRequest($request);

        if($addTerminForm->isSubmitted()){

            $tnId = $addTerminForm->get('tnId')->getData();
        
            $termin = new Termin();

            $tn = $tnRepository->find($tnId);

            $termin->setTn($tn);
            $termin->setTermindatum($addTerminForm->get('Termindatum')->getData());
            $termin->setTerminType($addTerminForm->get('Termin')->getData());
            $termin->setVerschoben($addTerminForm->get('Verschoben')->getData());
            $termin->setBemerkung($addTerminForm->get('Bemerkung')->getData());
            
            $entityManager->persist($termin);

            $entityManager->flush();

            return $this->redirectToRoute('app_teilnehmer',['id'=>$tnId]);
        }

        return $this->render('termin/addTermin.html.twig',[
            'isLogged' => $this->isLogged,
            'user' => $this->user,
            'tnId' => $tnId,
            'addTerminForm' => $addTerminForm->createView()
        ]);
    }

    /**
     * @Route("/termin/editTermin/{terminId}", name="app_editTermin")
     */
    public function edittermin(
        int $terminId,
        Request $request,
        TerminRepository $terminRepository,
        TnRepository $tnRepository,
        TerminTypeRepository $terminType,
        ManagerRegistry $doctrine
        ):Response
    {
        $entityManager = $doctrine->getManager();

        $currentTermin = $terminRepository->find($terminId);

        $isVerschoben = $currentTermin->isVerschoben();
        
        $currentTerminType = $currentTermin->getTermintype();

        $terminTypes = $terminType->findAll();

        $verschoben = [
            'Ja' => 1,
            'Nein' => 0
        ];

        $editForm = $this->createFormBuilder()
        ->add('tnId', HiddenType::class,[
            'data'=>$currentTermin->getTn()->getId()
        ])
        ->add('terminId', HiddenType::class,[
            'data' => $terminId
        ])
        ->add('Datum', DateType::class,[
            'required' => True,
            'data'=>$currentTermin->getTermindatum(),
            'widget'=>'single_text',
            'attr'=> ['class'=>'form-control form-control-sm']
        ])
        ->add('Termin',ChoiceType::class,[
            'required' => True,
            'choices' => $terminTypes,
            'choice_label'=> function(?TerminType $terminType){
                return $terminType ? $terminType->getTerminname() : '';
            },
            'data' => $currentTerminType,
            'attr'=>['class'=>'form-control form-control-sm']
        ])
        ->add('Verschoben', ChoiceType::class,[
            'required' => True,
            'choices' => $verschoben,
            'data' => $isVerschoben,
            'attr' => ['class'=>'form-control form-control-sm']
        ])
        ->add('Bemerkung', TextareaType::class,[
            'required' => False,
            'data'=>$currentTermin->getBemerkung(),
            'attr' => ['class'=>'form-control']
        ])
        ->add('Aktualisieren', SubmitType::class,[
            'attr'=>['class'=>'btn form-control btn-sm btn-outline-warning']
        ])
        ->getForm();

        $editForm->handleRequest($request);

        if($editForm->isSubmitted()){

            $terminId = $editForm->get('terminId')->getData();
            $tnId = $editForm->get('tnId')->getData();
            $datum = $editForm->get('Datum')->getData();
            $termintype = $editForm->get('Termin')->getData();
            $verschoben = $editForm->get('Verschoben')->getData();
            $bemerkung = $editForm->get('Bemerkung')->getData();

            $termin = $terminRepository->find($terminId);
            $tn = $tnRepository->find($tnId);

            $termin->setTermindatum($datum);
            $termin->setTermintype($termintype);
            $termin->setTn($tn);
            $termin->setBemerkung($bemerkung);
            $termin->setVerschoben($verschoben);

            $entityManager->flush();

            return $this->redirectToRoute('app_teilnehmer',['id'=>$tnId]);
        }

        return $this->render('termin/editTermin.html.twig',[
            'isLogged' => $this->isLogged,
            'user' => $this->user,
            'terminId' => $terminId,
            'termin' => $currentTermin,
            'editform' => $editForm->createView()
        ]);
    }

    /**
     * @Route("/termin/delete/{terminId}", name="app_termin_delete")
     */
    public function app_termin_delete(
        int $terminId,
        TerminRepository $terminRepository,
        ManagerRegistry $doctrine,
        Request $request
    ):Response    
    {
        $textArray = [
            'Sind Sie sicher?',
            'es wird gel??scht...'
        ];
        $action = 'makeaction';

        $form = $this->createFormBuilder()
        ->add('terminId', HiddenType::class,[
            'data' => $terminId
        ])
        ->add('submit', SubmitType::class,[
            'label' => 'Ja',
            'attr' =>[
                'class'=>'btn btn-outline-danger btn-sm form-control'
                ]
        ])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted()){

            $entityManager = $doctrine->getManager();
            
            $terminId = $form->get('terminId')->getData();

            $termin = $terminRepository->find($terminId);

            $entityManager->remove($termin);

            $entityManager->flush();

            return $this->redirectToRoute('app_panel');


        }


        return $this->render('UI/modal.html.twig',[
            'isLogged' => $this->isLogged,
            'user' => $this->user,
            'textArray' => $textArray,
            'actionname' => 'Zur??ck',
            'url_path' => '/termin/editTermin/'.$terminId,
            'buttonClasses' => 'btn btn-sm btn-outline-success form-control',
            'form' => $form->createView()
        ]);
    }
}
