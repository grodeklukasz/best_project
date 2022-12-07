<?php

namespace App\Controller;

use App\Service\SessionService;

use App\Repository\TnRepository;
use App\Repository\JobcoachRepository;
use App\Repository\TerminRepository;
use App\Repository\FmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        TerminRepository $terminRepository,
        FmRepository $fmRepository
        ): Response
        {
        
        if($this->user['role']=='admin'){
            $tnDetails = $tnRepository->findOneBy(['id'=>$id]);
        }else{
            $tnDetails = $tnRepository->findOneBy(['id'=>$id, 'jobcoach'=>$jobcoachRepository->findBy(['id'=>$this->user['id']])]);
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
    
}
