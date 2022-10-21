<?php

namespace App\Controller;

use App\Entity\Tn;
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

        $tns = $tnRepository->findAll();

        $weeks4 = new \DateInterval('P4W');
        $weeks8 = new \DateInterval('P8W');

        /**
         * -------------------------------------------------------------------------------------
         */


        $index = 0;

        foreach($tns as $tn){
            if($tn->getAusgeschieden()){

                $interval = $tn->getStartterminAsDiff($tn->getAusgeschieden());
                $diffdays = $interval->format('%R%a');
                $diffweeks = number_format((intval($interval->format('%R%a'))/7), 2, ',','');
                $ausgeschieden = $tn->getAusgeschieden();

            }else{

                $diffdays = "--";
                $diffweeks = "--";
                $ausgeschieden = null;
            }

            $starttermin = $tn->getStarttermin()->format('d.m.Y');
            $dateAfter4Weeks = $tn->getStarttermin()->add($weeks4)->format('d.m.Y');
            $dateAfter8Weeks = $tn->getStarttermin()->add($weeks8)->format('d.m.Y');

            $exportData[$index]['nachname'] = $tn->getNachname();
            $exportData[$index]['vorname'] = $tn->getVorname();
            $exportData[$index]['gebdatum'] = $tn->getGebdatum();
            $exportData[$index]['starttermin'] = $starttermin;
            $exportData[$index]['after4weeks'] = $dateAfter4Weeks;
            $exportData[$index]['after8weeks'] = $dateAfter8Weeks;
            $exportData[$index]['ausgeschieden'] = $ausgeschieden;
            $exportData[$index]['grundAusgeschieden'] = $tn->getGrundAusgeschieden();
            $exportData[$index]['diffdays'] = $diffdays;
            $exportData[$index]['diffweeks'] = $diffweeks;
            $exportData[$index]['aktiv'] = $tn->isStatus();

            $index++;
        
        }

        /**
         * -------------------------------------------------------------------------------------
            
            * Termin BO
            * Termin Tätigkeiten
            * Termin Stärken
            * Termin Bewerbungscoaching
            * Termin Foto
            * Termin Perspektivplan

         */
                
        
        return $this->render('app/index.html.twig', [
            'exportData' => $exportData,
        ]);
    }
}
