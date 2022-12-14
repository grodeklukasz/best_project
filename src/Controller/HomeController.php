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
    public function login(
        Request $request, 
        JobcoachRepository $jobcoachRepository, 
        SessionService $sessionService, 
        RequestStack $requestStack
        ): Response
    {   
        //if user is Logged redirect to app main view

        if($sessionService->getSessionValue('islogged'))
            //return $this->redirect('http://172.20.12.81/index.php/panel');
            return $this->redirectToRoute('app_panel');

        $user = new User();
        $errormsg = '';

        $isLogged = False;

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
            $pass = hash('sha3-512',trim($form->get('Kennwort')->getData()));

            $jobcoach = $jobcoachRepository->findOneBy(['email'=>$email,'kennwort'=>$pass,'isActive'=>1]);

            if(!$jobcoach){
                
                $errormsg = 'Benutzer unbekannt.';
  
            }else{

        
                $sessionService->setSessionValues('islogged',True);
                $sessionService->setSessionValues('id',$jobcoach->getId());
                $sessionService->setSessionValues('nachname',$jobcoach->getNachname());
                $sessionService->setSessionValues('vorname',$jobcoach->getVorname());
                $sessionService->setSessionValues('email',$jobcoach->getEmail());
                $sessionService->setSessionValues('role',$jobcoach->getRole());

                return $this->redirectToRoute('app_panel');
                //return $this->redirect('http://172.20.12.81/index.php/panel');
                
            }
            
        }
                
        return $this->render('home/index.html.twig', [
            'isLogged' => $isLogged,
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
