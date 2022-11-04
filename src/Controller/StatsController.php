<?php

namespace App\Controller;

use App\Repository\TerminTypeRepository;
use App\Repository\TerminRepository;
use App\Repository\TnRepository;
use App\Entity\TerminType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    /**
     * @Route("/stats", name="app_stats")
     */
    public function index(TerminTypeRepository $terminTypeRepository, TerminRepository $terminRepository): Response
    {
        $allTerminTypes = $terminTypeRepository->findAll();
        $counter = 0;

        foreach($allTerminTypes as $terminType)
        {   
            $resultArray = $terminRepository->countTerminsByType($terminType->getId());
            $allTerminsArray[$counter] = [
                'terminname' => $terminType->getTerminname(),
                'count' => $resultArray[0]['counter'],
            ];
            $counter++;
        }

        $months = [
            1 => "Januar",
            2 => "Februar",
            3 => "März",
            4 => "April",
            5 => "Mai",
            6 => "Juni",
            7 => "Juli",
            8 => "August",
            9 => "September",
            10 => "Oktober",
            11 => "November",
            12 => "Dezember"
        ];


        return $this->render('app\stats.html.twig',[
            'allTerminsArray' => $allTerminsArray,
            'allMonths' => $months
        ]);
     }

     /**
      * @Route("/statistik2", name="app_stats2")
      */
      public function test1(TnRepository $tnRepository):Response 
      {

        $jahr = 2021;
        $monat = 10;

       
        $tns = $tnRepository->findAllByDetails(['termin_name'=>'Einladung']);
        

        $wieGeplantGestartetJa = 0;
        $wieGeplantGestartetNein = 0;

        $eingeladeneTN = 0;
        $startterminImMonat = 0;

        //NA Grund
        $nichtAngetreteneTN = 0;

        // VAB Grund
        $vabTN = 0;

        //EB TN
        $ebTN = 0;


        foreach($tns as $tn){

            $startterminAsDate = new \DateTime($tn['starttermin']); 

            $einladungDatumAsDate = new \DateTime($tn['termindatum']);  

            if(($einladungDatumAsDate->format('Y')==$jahr)&&($einladungDatumAsDate->format('m')==$monat)){
                
                $eingeladeneTN++;

            }

            if(($startterminAsDate->format('Y')==$jahr)&&($startterminAsDate->format('m')==$monat)){

                $startterminImMonat++;

                if($tn['grund_ausgeschieden']=='NA'){
                    $nichtAngetreteneTN++;
                }

                if($tn['grund_ausgeschieden']=='VAB'){
                    $vabTN++;
                }

                if($tn['grund_ausgeschieden']=='EB'){
                    $ebTN++;
                }

                if($tn['starttermin']==$tn['termindatum']){

                    $wieGeplantGestartetJa++;
                    
                }else{

                    $wieGeplantGestartetNein++;

                }
            }

        }


        return $this->render('stats\test1.html.twig',[
            'jahr' => $jahr,
            'monat' => $monat,
            'wieGeplantGestartetJa' => $wieGeplantGestartetJa,
            'wieGeplantGestartetNein' => $wieGeplantGestartetNein,
            'startterminImMonat' => $startterminImMonat,
            'eingeladeneTN' => $eingeladeneTN,
            'vabTN' => $vabTN,
            'ebTN' => $ebTN,
            'nichtAngetreteneTN' => $nichtAngetreteneTN,
            'tns' => $tns
        ]);
      }
      /**
       * @Route("/statistik3", name="app_stats3")
       */
      public function stats3(TnRepository $tnRepository, TerminRepository $terminRepository): Response 
      {
        $jahr = 2021;
        $monat = 9;

        $starttermineImMonat = 0;
        $eingeladeneTNimMonat = 0;

        $nichtAngetreteneTN = 0;
        $vabTN = 0;
        $ebTN = 0;

        
        //find all Termins "Einladung"

        $allStartTermine = $terminRepository->findBy(['termintype'=>25]);

        // count all Einladung Termine for Year and Month
        foreach($allStartTermine as $allStartTermin){
            if(($allStartTermin->getTermindatum()->format('Y')==$jahr)&&($allStartTermin->getTermindatum()->format('m')==$monat)){
                $eingeladeneTNimMonat++;
            }
        }

        //find all Tn with Termins

        $allTnWithTermine = $tnRepository->findAllByDetails(['termin_name'=>'Einladung']);

        $wieGeplantGestartetJa = 0;
        $wieGeplantGestartetNein = 0;

        foreach($allTnWithTermine as $tnwithtermin){

            $startterminAsDate = new \DateTime($tnwithtermin['starttermin']); 
            $einladungDatumAsDate = new \DateTime($tnwithtermin['termindatum']);

            if(($startterminAsDate->format('Y')==$jahr)&&($startterminAsDate->format('m')==$monat)){
                if($tnwithtermin['starttermin']==$tnwithtermin['termindatum']){

                    $wieGeplantGestartetJa++;
                    
                }else{

                    $wieGeplantGestartetNein++;

                }
            }
             

        }


        //find all Tn as Array

        $allTns = $tnRepository->findAllAsArray([]);

        //counter all TN's Termins with EB
        $allTNTerminsWithEB = 0;

        foreach($allTns as $tn){
            
            $startterminAsDate = new \DateTime($tn['starttermin']); 

            //check startterminDate if match to query

            if(($startterminAsDate->format('Y')==$jahr)&&($startterminAsDate->format('m')==$monat)){
               
                $starttermineImMonat++;
                $selectedTn[] = $tn; 

                if($tn['grund_ausgeschieden']=='NA'){
                    $nichtAngetreteneTN++;
                }

                if($tn['grund_ausgeschieden']=='VAB'){
                    $vabTN++;
                }

                if($tn['grund_ausgeschieden']=='EB'){
                    
                    //find all tn's termins which has EB
                    $counter = $terminRepository->countTerminsByTn($tn['id']);
                    
                    //add to counter allTNTerminsWithEB
                    $allTNTerminsWithEB = $allTNTerminsWithEB + $counter[0]['counter'];

                    $ebTN++;
                }

            }
        }

        //Durchnchnittstermine der erfolgreich beendeten TN
        $durchnchnittstermineTNwithEB = $allTNTerminsWithEB / $ebTN;

        //Termine [ingesamt] im Monat vergeben [betrifft auch Folgemonat]
        //get All termins as Array
        $allTermins = $terminRepository->getAllAsArray();
        
        // Termin counter

        $allTerminCounter = 0;

        foreach($allTermins as $termin){

            $terminAsDate = new \DateTime($termin['termindatum']);

            if(($terminAsDate->format('Y')==$jahr)&&($terminAsDate->format('m')==$monat)){
                $allTerminCounter++;
            }

        }


        return $this->render('stats\stats3.html.twig',[
            'searchDate' => $monat . "." . $jahr,
            'monat' => $monat,
            'starttermineImMonat' => $starttermineImMonat,
            'eingeladeneTNimMonat' => $eingeladeneTNimMonat,
            'wieGeplantGestartetJa' => $wieGeplantGestartetJa,
            'wieGeplantGestartetNein' => $wieGeplantGestartetNein,
            'ebTN' => $ebTN,
            'durchnchnittstermineTNwithEB' => $durchnchnittstermineTNwithEB,
            'allTerminCounter' => $allTerminCounter,
            'tns' => $selectedTn,
            'nichtAngetreteneTN' => $nichtAngetreteneTN,
            'vabTN' => $vabTN,
            'ebTN' => $ebTN
        ]);
      }
}

