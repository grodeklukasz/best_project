<?php

namespace App\Controller;

use App\Service\SessionService;
use App\Repository\TerminRepository;
use App\Repository\TnRepository;
use App\Repository\JobcoachRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
