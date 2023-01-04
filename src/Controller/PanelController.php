<?php

namespace App\Controller;

use App\Repository\TnRepository;
use App\Repository\JobcoachRepository;
use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use \Doctrine\Common\Collections\Criteria;
use \Doctrine\Common\Collections\Expr\Comparison;

class PanelController extends AbstractController
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
     * @Route("/panel", name="app_panel")
     */
    public function index(
        SessionService $sessionService, 
        TnRepository $tnRespository, 
        JobcoachRepository $jobcoachRepository): Response
    {
        if($sessionService->getSessionValue('role')=='admin')
            return $this->redirectToRoute('app_adminpanel');
        
        $criteria1 = new Criteria();
        $expr = new Comparison('jobcoach', Comparison::EQ, $jobcoachRepository->find($this->user['id']));
        $criteria1->where($expr);
        $criteria1->andwhere(Criteria::expr()->eq('status',1));
        $criteria1->orderBy(['nachname'=>Criteria::ASC]);
        $allTn = $tnRespository->matching($criteria1);

        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->neq('id',$this->user['id']));
        $criteria->orderBy(['nachname'=>Criteria::ASC]);
        $allJobCoaches =$jobcoachRepository->matching($criteria);

        return $this->render('panel/index.html.twig', [
            'isLogged' => $this->isLogged,
            'user' => $this->user,
            'allJobcoach' => $allJobCoaches,
            'allTeilnehmer' => $allTn
        ]);
    }
    /**
     * @Route("/archiv", name="app_archiv")
     */
    public function appArchiv(
        SessionService $sessionService,
        TnRepository $tnRespository,
        JobCoachRepository $jobcoachRepository
    ):Response
    {
        $criteria = new Criteria();
        $expr = new Comparison('jobcoach', Comparison::EQ, $jobcoachRepository->find($this->user['id']));
        $criteria->where($expr);
        $criteria->andwhere(Criteria::expr()->neq('status',1));
        $criteria->orderBy(['nachname'=>Criteria::ASC]);
        $allTn = $tnRespository->matching($criteria);

        return $this->render('panel/index.html.twig',[
            'isLogged' => $this->isLogged,
            'user' => $this->user,
            'allJobcoach' => [],
            'allTeilnehmer' => $allTn
        ]);
    }
    /**
     * @Route("panel/jobcoach/{jobcoach_id}", name="app_jobcoach")
     */
    public function app_jobcoach(
        int $jobcoach_id,
        SessionService $sessionService, 
        TnRepository $tnRepository,
        JobcoachRepository $jobcoachRepository
        ):Response
    {
        $criteria1 = new Criteria();
        $expr = new Comparison('jobcoach', Comparison::EQ, $jobcoachRepository->find($jobcoach_id));
        $criteria1->where($expr);
        $criteria1->andwhere(Criteria::expr()->eq('status',1));
        $criteria1->orderBy(['nachname'=>Criteria::ASC]);
        $allTn = $tnRepository->matching($criteria1);

        $criteria = new Criteria();
        $criteria->where(Criteria::expr()->neq('id',$this->user['id']));
        $criteria->orderBy(['nachname'=>Criteria::ASC]);
        $allJobCoaches =$jobcoachRepository->matching($criteria);

        $currentUser = $jobcoachRepository->find($jobcoach_id);

        return $this->render('panel/index.html.twig',[
            'isLogged' => $this->isLogged,
            'user' => $this->user,
            'allJobcoach' => $allJobCoaches,
            'allTeilnehmer' => $allTn
        ]);
    }
    /**
     * @Route("/panel/admin", name="app_adminpanel")
     */
    public function adminpanel(SessionService $sessionService, JobcoachRepository $jobcoachRepository){
        
        if($sessionService->getSessionValue('role')!='admin')
            return $this->redirectToRoute('app_panel');

        $allJobCoaches = $jobcoachRepository->findAll();

        $criteria = new Criteria();
        $criteria->orderBy(['nachname'=>Criteria::ASC]);
        $allJobCoaches = $jobcoachRepository->matching($criteria);
        
        return $this->render('panel/adminpanel.html.twig', [
            'isLogged' => $this->isLogged,
            'allJobCoaches' => $allJobCoaches,
            'user' => $this->user,
        ]);

    }
    /**
     * @Route("/panel/admin/coach/{id}", name="app_adminpanel_coach")
     */
    public function adminpanelcoach(
        int $id, 
        SessionService $sessionService, 
        TnRepository $tnRespository, 
        JobcoachRepository $jobcoachRepository
        ){
            
            $allTn = $tnRespository->findBy(
                [
                    'jobcoach'=>$jobcoachRepository->find($id),
                ],
                ['status' => 'DESC']
            );

            $criteria = new Criteria();
            $criteria->where(Criteria::expr()->neq('id',$this->user['id']));
            $criteria->orderBy(['nachname'=>Criteria::ASC]);
            $allJobCoaches =$jobcoachRepository->matching($criteria);

            return $this->render('panel/index.html.twig', [
                'isLogged' => $this->isLogged,
                'user' => $this->user,
                'allTeilnehmer' => $allTn,
                'allJobcoach' => $allJobCoaches,
            ]);
        }
}
