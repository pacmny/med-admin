<?php
session_start();
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");


if(isset($_POST))
{
    require_once 'Response.php';	
    require_once 'ICD.php';
    $response = new Response();

    $mdata = file_get_contents('php://input');
   // var_dump($mdata);
    $mmdata = json_decode($mdata);

    $action=trim($mmdata->action);
    $search = trim($mmdata->searchTerm);
    $code = trim($mmdata->ICDCode);
    $releaseURL = trim($mmdata->ICDReleaseUrl);
    $uri="";
    switch($action)
    {
        case"Search":
        {
            $searchuri="https://clinicaltables.nlm.nih.gov/api/icd10cm/v3/search?sf=code,name&df=code,name&authenticity_token=&maxList=100&terms=".urlencode($search);
            $headers= array(
                
                'Content-Type: application/json',
                
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$searchuri);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // set curl without result echo
            $response = curl_exec($ch);
           // header('Content-Type: application/json');	
           $resultar = json_decode($response);
           $nresultar = [];
           //if resultar is empty and not an array don't loop 
           if(is_array($resultar))
           {
           //var_dump($resultar[3]);
               $i=0;
               $html="";
               $html .="<div class='icd10listcontainer col-lg12'>";
               foreach($resultar[3] as $r)
               {
               
                 $nresultar[] = array("code"=>$r[0],"description"=>$r[1]);
                 //build HTML Here 
                 $html .="<div class='icd10srchlist'>".
                 "<span id='icdcode'>".$r[0]."</span> - <span id='icddesript'>".$r[1]."</span>".
                 "</div>";
                 
               }
               $html .="</div>";
               $resultcount = count($nresultar);
               if($resultcount !=0)
               {
                //have data 
                $payloadAr = array("result"=>$resultcount,"html"=>$html,"actualResults"=>$nresultar);
                print(json_encode($payloadAr,JSON_PRETTY_PRINT));
               }
               else{
                //dont have data 
                 $payloadAr = array("result"=>$resultcount,"msg"=>"No Results Found","actualResults"=>$nresultar);
                 print(json_encode($payloadAr,JSON_PRETTY_PRINT));
               }
             }
             else{
              // not an array so send back empty results 
               $payloadAr = array("result"=>0,"msg"=>"No Results Found","actualResults"=>$nresultar);
               print(json_encode($payloadAr,JSON_PRETTY_PRINT));
             }
          
           //build the HTML for the front append after search element

          // print(json_encode($nresultar,JSON_PRETTY_PRINT));
          
           
           
           
            break;
        }
        case"CodeLookUp":
        {
            /*if($releaseURL !="")
            {
                $uri = $releaseURL;
            }
            else{
                $uri ="https://id.who.int/icd/release/10/".$code;
            }
           
            $icdapi = new ICDCodes($uri);
            $response->set(1,$icdapi->get());
            //let try the new code set for dobule calls here 
            $jdata = json_decode($response->encode());	
            $relearAr = explode(":",$jdata->data->latestRelease);
            $newReleaseURL ="https:".$relearAr[1];
            $icdapi = new ICDCodes($newReleaseURL);
            $response->set(1,$icdapi->get());
             $json =$response->encode();
             $decodejson = json_decode($json);
            $browse = $decodejson->data->browserUrl;
            $browsear = explode(":",$browse);
            $browserURL = "https:".$browsear[1];
            $hasdata = count($decodejson->actualResults);
           // var_dump($hasdata);
           $html="<div class='icd10listcontainer col-lg12'>".
           "<div class='icd10srchlist'>".
           "<span id='icdcode'>".$decodejson->data->code."</span> - <span id='icddesript'>".$decodejson->data->title->{"@value"}."</span><span class='tool-tip' id='tipicon'><a href='#showbook' id='icdbrowserlink'><img src='https://cdn4.iconfinder.com/data/icons/fluent-outline-20px-vol-6/20/ic_fluent_tooltip_quote_20_regular-512.png' height='30' width='30' alt='img'/></a></span>".
           "</div>".
           "</div>";
           $msg = array("founddata"=>$hasdata,"html"=>$html,"actualArray"=>$decodejson->data,"browserURL"=>$browserURL);
           print(json_encode($msg,JSON_PRETTY_PRINT));*/
          // print_r($response->encode());

           /*
           @6/5/2023 | Going to use the first enpoint for all the Code Look Ups 
           */

            $searchuri="https://clinicaltables.nlm.nih.gov/api/icd10cm/v3/search?sf=code,name&df=code,name&authenticity_token=&maxList=15&terms=".urlencode($search);
            $headers= array(
                
                'Content-Type: application/json',
                
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$searchuri);
            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // set curl without result echo
            $response = curl_exec($ch);
           // header('Content-Type: application/json'); 
           $resultar = json_decode($response);
           $nresultar = [];
           //if resultar is empty and not an array don't loop 
           if(is_array($resultar))
           {
           //var_dump($resultar[3]);
               $i=0;
               $html="";
               $html .="<div class='icd10listcontainer col-lg12'>";
               foreach($resultar[3] as $r)
               {
               
                 $nresultar[] = array("code"=>$r[0],"description"=>$r[1]);
                 //build HTML Here 
                 $html .="<div class='icd10srchlist'>".
                 "<span id='icdcode'>".$r[0]."</span> - <span id='icddesript'>".$r[1]."</span>".
                 "</div>";
                 
               }
               $html .="</div>";
               $resultcount = count($nresultar);
               if($resultcount !=0)
               {
                //have data 
                $payloadAr = array("result"=>$resultcount,"html"=>$html,"actualResults"=>$nresultar);
                print(json_encode($payloadAr,JSON_PRETTY_PRINT));
               }
               else{
                //dont have data 
                 $payloadAr = array("result"=>$resultcount,"msg"=>"No Results Found","actualResults"=>$nresultar);
                 print(json_encode($payloadAr,JSON_PRETTY_PRINT));
               }
             }
             else{
              // not an array so send back empty results 
               $payloadAr = array("result"=>0,"msg"=>"No Results Found","actualResults"=>$nresultar);
               print(json_encode($payloadAr,JSON_PRETTY_PRINT));
             }

            break;
        }
        
    }
}
else{
    $msg = array("message"=>"Nothing Posted","posObj"=>$_POST);
    print_r(json_encode($msg,JSON_PRETTY_PRINT));
}





?>