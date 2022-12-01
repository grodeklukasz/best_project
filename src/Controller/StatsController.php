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
       * @Route("/statistik3/{jahr}", name="app_stats3")
       */
      public function stats3(int $jahr, TnRepository $tnRepository, TerminRepository $terminRepository, TerminTypeRepository $terminTypeRepo): Response 
      {
        

        $bigExportArray = array();

            //for all months of the year

            for($monat=1;$monat<13;$monat++){

                //find all Termins "Einladung"
                $allStartTermine = $terminRepository->findBy(['termintype'=>$this->getTerminId($terminTypeRepo, 'Einladung')]);

                // count all Einladung Termine for Year and Month

                $eingeladeneTNimMonat = $this->enFunction1($jahr, $monat, $allStartTermine);

                //get an Array with two elements: wie Geplant Gestartet Ja / wie Geplant Gestartet Nein

                $wieGeplantGestartet = $this->enFunction2($jahr,$monat,$tnRepository);

                //Array with starttermineImMonat, nichtAngetreteneTN, vabTN, ebTN
                $statsArray1= $this->enFunction3($jahr,$monat,$tnRepository, $terminRepository);

                //Durchnchnittstermine der erfolgreich beendeten TN
            
                $durchnchnittstermineTNwithEB = $this->enFunction4($statsArray1['allTNTerminsWithEB'],$statsArray1['ebTN']);

                //Termine [ingesamt] im Monat vergeben [betrifft auch Folgemonat]        

                $allTerminCounter = $this->enFunction5($jahr, $monat, $terminRepository);

                $durchschnittlArray = $this->enFunction8($jahr,$monat,$tnRepository, $terminRepository);
            

                $exportArray = [
                    'searchDate' => $monat . "." . $jahr,
                    'monat' => $monat,
                    'starttermineImMonat' => $statsArray1['starttermineImMonat'],
                    'eingeladeneTNimMonat' => $eingeladeneTNimMonat,
                    'wieGeplantGestartetJa' => $wieGeplantGestartet['wieGeplantGestartetJa'],
                    'wieGeplantGestartetNein' => $wieGeplantGestartet['wieGeplantGestartetNein'],
                    'ebTN' => $statsArray1['ebTN'],
                    'durchnchnittstermineTNwithEB' => $durchnchnittstermineTNwithEB,
                    'allTerminCounter' => $allTerminCounter,
                    'tns' => $statsArray1['selectedTn'],
                    'nichtAngetreteneTN' => $statsArray1['nichtAngetreteneTN'],
                    'vabTN' => $statsArray1['vabTN'],
                    'ebTN' => $statsArray1['ebTN'],
                    'verschoben' => $this->enFunction6($jahr,$monat,$terminRepository),
                    'ausgeschieden' => $this->enFunction7($jahr,$monat,$tnRepository),
                    'durchschnittl_insgesamt'=>$durchschnittlArray['durchschnittl_insgesamt'],
                    'durchschnittl_nurEB'=>$durchschnittlArray['durchschnittl_nurEB'],
                ];

                
                $bigExportArray[] = $exportArray;

            }

        
        return $this->render('stats\bigExportArray.html.twig',[
            'export' => $bigExportArray,
        ]);
      }

      /**
       * Functions for Stats
       */

      public function enFunction1(int $jahr, int $monat, array $allStartTermine): int
      {
        $eingeladeneTNimMonat = 0;

        // count all Einladung Termine for Year and Month
        foreach($allStartTermine as $allStartTermin){
            if(($allStartTermin->getTermindatum()->format('Y')==$jahr)&&($allStartTermin->getTermindatum()->format('m')==$monat)){
                $eingeladeneTNimMonat++;
            }
        }

        return $eingeladeneTNimMonat;
      }
      
      public function enFunction2(int $jahr, int $monat, TnRepository $tnRepository): array
      {
        
        //find all Tn with Termins Enladung

        $allTnWithTermine = $tnRepository->findAllByDetails(['termin_name'=>'Einladung','verschoben'=>0]);

        $wieGeplantGestartetJa = 0;
        $wieGeplantGestartetNein = 0;

        foreach($allTnWithTermine as $tnwithtermin){

            $startterminAsDate = new \DateTime($tnwithtermin['starttermin']); 
            $einladungDatumAsDate = new \DateTime($tnwithtermin['termindatum']);
        
        //!!!!! Jahr !!!!!

            if(($startterminAsDate->format('Y')==$jahr)&&($startterminAsDate->format('m')==$monat)){
            
                if($tnwithtermin['starttermin']==$tnwithtermin['termindatum']){

                    $wieGeplantGestartetJa++;
                    
                }else{

                    $wieGeplantGestartetNein++;

                }
            }
            
        }

        return [
            'wieGeplantGestartetJa'=>$wieGeplantGestartetJa, 
            'wieGeplantGestartetNein'=>$wieGeplantGestartetNein
        ];

      }

      public function enFunction3(int $jahr, int $monat, TnRepository $tnRepository, TerminRepository $terminRepository):array
      {
        //find all Tn as Array

        $starttermineImMonat = 0;

        $nichtAngetreteneTN = 0;
        $vabTN = 0;
        $ebTN = 0;

        $allTns = $tnRepository->findAllAsArray([]);

        //counter all TN's Termins with EB
        $allTNTerminsWithEB = 0;

        //
        $selectedTn = array();

        foreach($allTns as $tn){
            
            $startterminAsDate = new \DateTime($tn['starttermin']); 

            //check startterminDate if match to query
        
            // !!!!! Jahr !!!!!

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

        return [
            'starttermineImMonat' => $starttermineImMonat,
            'nichtAngetreteneTN' => $nichtAngetreteneTN,
            'vabTN' => $vabTN,
            'ebTN' => $ebTN,
            'allTNTerminsWithEB' => $allTNTerminsWithEB,
            'selectedTn' => $selectedTn
        ];
      }

      //Durchnchnittstermine der erfolgreich beendeten TN
      public function enFunction4(int $allTNTerminsWithEB, int $ebTN):float
      {
        $durchnchnittstermineTNwithEB = 0;

        if($ebTN!=0)
            $durchnchnittstermineTNwithEB = $allTNTerminsWithEB / $ebTN;
        
       return $durchnchnittstermineTNwithEB;

      }

      public function enFunction5(int $jahr, int $monat, TerminRepository $terminRepository):int
      {
        //Termine [ingesamt] im Monat vergeben [betrifft auch Folgemonat]
        //get All termins as Array
            $allTermins = $terminRepository->getAllAsArray();
            
            // Termin counter

            $allTerminCounter = 0;

            foreach($allTermins as $termin){

                $terminAsDate = new \DateTime($termin['termindatum']);
            
                // !!!!! Jahr !!!!!

                if(($terminAsDate->format('Y')==$jahr)&&($terminAsDate->format('m')==$monat)){
                    $allTerminCounter++;
                }

            }
        return $allTerminCounter;
      }

      // get All verschoben Termine
      public function enFunction6(int $jahr, int $monat, TerminRepository $terminRepository):int 
      {
        
        $verschobenTermins = 0;

        $termins = $terminRepository->getAllAsArrayWithParams(['verschoben'=>1]);

        foreach($termins as $termin){

            $terminAsDate = new \DateTime($termin['termindatum']);

            if(($terminAsDate->format('Y')==$jahr)&&($terminAsDate->format('m')==$monat)){
                $verschobenTermins++;
            }
            
        }

        return $verschobenTermins;

      }

      //get Ausgeschiedene TN [insgesamt]  im Monat

      public function enFunction7(int $jahr, int $monat, TnRepository $tnRepository):int 
      {
        $ausgeschiedeneTn = 0;
        
        $tns = $tnRepository->findAllAsArray();

        foreach($tns as $tn){

            $terminAsDate = new \DateTime($tn['ausgeschieden']);

            if(($terminAsDate->format('Y')==$jahr)&&($terminAsDate->format('m')==$monat)){
                $ausgeschiedeneTn++;
            }

        }

        return $ausgeschiedeneTn;

      }

      //Durchschnittl. Verweildauer in Wochen / TN [ingesamt] 

      public function enFunction8(int $jahr, int $monat, TnRepository $tnRepository, TerminRepository $terminRepository):array 
      {
        $allVAB = 0;
        $allEB = 0;

        $tncounter = 0;
        $tncounterEB = 0;

        $bigWeeksSrednia = 0;
        $bigWeeksNurEB = 0;


        $tnVAB = $tnRepository->findAllAsArray(['grund_ausgeschieden'=>'VAB']);
        
        $tnEB = $tnRepository->findAllAsArray(['grund_ausgeschieden'=>'EB']);

        foreach($tnEB as $tn){
            
            $terminCount = $terminRepository->countTerminsByTn($tn['id']);

            $terminAsDate = new \DateTime($tn['ausgeschieden']);

            if(($terminAsDate->format('Y')==$jahr)&&($terminAsDate->format('m')==$monat)){

                $tncounter++;
                $tncounterEB++;

                //set Termins as DateTime
                $startTerminAsDate = new \DateTime($tn['starttermin']);
                $endTerminAsDate = new \DateTime($tn['ausgeschieden']);
                
                //make interval between starttermin and ausgeschieden termins
                $interval = $startTerminAsDate->diff($endTerminAsDate);

                //make difference as days
                $diffdays = $interval->format('%R%a');

                //make difference as weeks
                $diffweeks = number_format((intval($interval->format('%R%a'))/7), 2, '.','');

                /*
                echo "<div class='container-fluid'>";
                echo "----- EB: ";
                echo $terminAsDate->format('Y').".".$terminAsDate->format('m').": ";
                echo $tn['nachname'];
                echo " - ";
                echo $terminCount[0]['counter'];
                echo " Days: " . $diffdays .", ";
                echo "Weeks: " . $diffweeks ."<br>";
                echo "</div>";
                */
                $bigWeeksSrednia = (float)$bigWeeksSrednia+$diffweeks;
                $bigWeeksNurEB = (float)$bigWeeksNurEB+$diffweeks;
            }

        }
        
        foreach($tnVAB as $tn){

            $terminCount = $terminRepository->countTerminsByTn($tn['id']);

            $terminAsDate = new \DateTime($tn['ausgeschieden']);

            if(($terminAsDate->format('Y')==$jahr)&&($terminAsDate->format('m')==$monat)){

                $tncounter++;

                //set Termins as DateTime
                $startTerminAsDate = new \DateTime($tn['starttermin']);
                $endTerminAsDate = new \DateTime($tn['ausgeschieden']);

                //make interval between starttermin and ausgeschieden termins
                $interval = $startTerminAsDate->diff($endTerminAsDate);
                
                //make difference as days
                $diffdays = $interval->format('%R%a');
                
                //make difference as weeks
                $diffweeks = number_format((intval($interval->format('%R%a'))/7), 2, '.','');

                /*
                echo "<div class='container-fluid'>";
                echo "----- VAB: ";
                echo $terminAsDate->format('Y').".".$terminAsDate->format('m').": ";
                echo $tn['nachname'];
                echo " - ";
                echo $terminCount[0]['counter'];
                echo " Days: " . $diffdays .", ";
                echo "Weeks: " . $diffweeks ."<br>";
                echo "</div>";
                */
                $bigWeeksSrednia = (float)$bigWeeksSrednia+$diffweeks;

            }

        }

        $durchschnittl_insgesamt = $tncounter>0 ? $bigWeeksSrednia/$tncounter : 0;
        $durchschnittl_nurEB = $tncounterEB>0 ? $bigWeeksNurEB/$tncounterEB : 0;

        /*
        echo "<br><strong>".$tncounter." <br>";
        echo "Verweildauer in Wochen / TN [ingesamt]: ".($tncounter>0 ? $bigWeeksSrednia/$tncounter : 0)."<br>";
        echo "Durchschnittl. Verweildauer in Wochen / TN [nur EB]: ".($tncounter>0 ? $bigWeeksNurEB/$tncounterEB : 0)."</strong><br><br>";
        echo "--------------------------------------------------------------------------------<br>";
        */

        $exportarray = [
            'durchschnittl_insgesamt' => $durchschnittl_insgesamt,
            'durchschnittl_nurEB' => $durchschnittl_nurEB
        ];

        return $exportarray;
       
      }

      //Durchschnittl. Wartezeit Zuweisung bis Einladung [Arbeitstage]

      public function enFunction9(int $jahr, int $monat, TnRepository $tnRepository, TerminRepository $terminRepository, TerminTypeRepository $terminTypeRepo):int
      {

        $terminTypeId = $this->getTerminId($terminTypeRepo, 'Zuweisung');

        $jahr = 2021;
        $monat = 9;

        $testdata = $tnRepository->findAllDatesByMonthAndYear($jahr, $monat);
       
        return $terminTypeId;

      }

      //get termin id by name

      private function getTerminId(TerminTypeRepository $terminTypeRepo, string $terminName):int 
      {
        $terminType = $terminTypeRepo->findBy(['terminName'=>$terminName]);

        if(count($terminType)==null){
            die();
        }
            
        return $terminType[0]->getId();
      }


}

