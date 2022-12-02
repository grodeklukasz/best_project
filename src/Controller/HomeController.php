<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\JobcoachRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\RequestStack;

use App\Service\SessionService;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home") 
     */
    public function login(Request $request, JobcoachRepository $jobcoachRepository, SessionService $sessionService, RequestStack $requestStack): Response
    {   
        //if user is Logged redirect to app main view

        if($sessionService->getSessionValue('islogged'))
            return $this->redirectToRoute('app_app');

        $user = new User();
        $errormsg = '';

        $form = $this->createFormBuilder()
        ->add('Email',EmailType::class,[
            'required' => True,
            'attr'=>['class'=>'form-control form-control-sm']
        ])
        ->add('Kennwort',PasswordType::class,[
            'required' => True,
            'attr'=>['class'=>'form-control form-control-sm']
        ])
        ->add('Anmelden',SubmitType::class, [
            'attr'=>['class'=>'btn btn-outline-secondary form-control']
        ])
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted()){
            
            $email = $form->get('Email')->getData();
            $pass = $form->get('Kennwort')->getData();

            $jobcoach = $jobcoachRepository->findOneBy(['email'=>$email,'kennwort'=>$pass]);

            if(!$jobcoach){
                
                $errormsg = 'Benutzer unbekannt.';
  
            }else{

        
                $sessionService->setSessionValues('islogged',True);
                $sessionService->setSessionValues('email',$jobcoach->getEmail());
                $sessionService->setSessionValues('role',$jobcoach->getRole());

                return $this->redirectToRoute('app_app');
                
            }
            
        }
                
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'login_form' => $form->createView(),
            'errormsg' => $errormsg,
        ]);
    }

    /**
    * @Route("/logout", name="app_logout") 
    */

    public function logout(SessionService $sessionService){
        
        $sessionService->clear();

        return $this->redirectToRoute('app_home');
    }
}
