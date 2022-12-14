<?php

namespace App\Controller;

use App\Entity\Tn;
use App\Service\SessionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\TnRepository;
use App\Repository\TerminRepository;
use App\Repository\TerminTypeRepository;

class AppController extends AbstractController
{
    /**
     * @Route("/app", name="app_app")
     */
    public function index(TnRepository $tnRepository, SessionService $sessionService): Response
    {   
        
        if(!$sessionService->getSessionValue('islogged')){
            return $this->redirectToRoute('app_home');
        }

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

            $exportData[$index]['id'] = $tn->getId();
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
            * Termin T??tigkeiten
            * Termin St??rken
            * Termin Bewerbungscoaching
            * Termin Foto
            * Termin Perspektivplan

         */
                
        
        return $this->render('app/index.html.twig', [
            'exportData' => $exportData,
        ]);
    }
    /**
     * @Route("/tn/{id}", name="app_tn")
     */
    public function tnDetail(int $id, TnRepository $tnRepository, TerminRepository $terminRepository, TerminTypeRepository $terminTypeRepository): Response 
    {

        $tn = $tnRepository->find($id);

        $terminTypes = $terminTypeRepository->findAll();
        $counter = 0;

        foreach($terminTypes as $terminType)
        {
            $allTermins[$terminType->getId()] = $terminRepository->findBy(['tn'=>$id, 'termintype'=>$terminType->getId()]);
            
            $resultArray = $terminRepository->countTypesOfTermin($id, $terminType->getId());

            $terminName = $terminType->getTerminName();

            $terminArrayStats[$counter] = [
                'terminname' => $terminName,
                'count' => $resultArray[0]['counter'],
            ];

        
            $counter++;

        }

        if($tn->getAusgeschieden() == Null){

            $diffdays = "";

            $diffweeks = "";

        }else{

            $interval = $tn->getStartterminAsDiff($tn->getAusgeschieden());

            $diffdays = $interval->format('%R%a');

            $diffweeks = number_format((intval($interval->format('%R%a'))/7), 2, ',','');

        }


        return $this->render('app/detail.html.twig',[
            'id' => $id,
            'tn' => $tn,
            'diffdays' => $diffdays,
            'diffweeks' => $diffweeks,
            'termine' => $allTermins,
            'terminArrayStats' => $terminArrayStats,
        ]);
    }

    
}
