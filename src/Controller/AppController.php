<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\TnRepository;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="app_app")
     */
    public function index(TnRepository $tnRepository): Response
    {   
        $tn = $tnRepository->find(1);

        //$interval = $tn->getStartterminAsDiff($tn->getGebdatum());
        $interval = $tn->getStartterminAsDiff($tn->getAusgeschieden());

        $weeks =(intval($interval->format('%a'))/7);

        $weeks4 = $tn->getStarttermin();
        //$weeks8 = 
        
        return $this->render('app/index.html.twig', [
            'tn' => $tn,
            'interval' => $interval->format('%y years, %m month, %d days'),
            'days' => $interval->format('%R%a days'),
            'weeks' => number_format($weeks, 2, ',','')
        ]);
    }
}
