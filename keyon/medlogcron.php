<?php 
 /*Setting Header Parameters*/
 header('Access-Control-Allow-Methods: GET, POST');
 header('Access-Control-Allow-Origin: *');
 header("Access-Control-Allow-Headers: *");
require_once("processClass.php");
/* Testing Medcronlog */
      //lets check todays's date | Set EST Time setting below
      date_default_timezone_set('America/New_York'); 
      $today = date("Y-m-d", strtotime('+1 day'));
      $actdate = date("Y-m-d"); //I need this to actually look up the actual log entries for today and not tomorrow
      
      //now lets pass today's date to the process and SqlClass functions to get a list of all the patients active medications that are being logged
      /*Get Active Orders->Medications->Medlog->Medlogtime | Formulate the needed query needed to pull the relational data back 
      @Note: This needs to pull Data for ALL Patients With Active Medications with administrative_settings=True Vs only check for an individual patients
      */
      $processData =new ProcessData();
      $checkmedlogdts = $processData->checkActiveMedLogDates($today); //$today param not really being used as of right now - this is returning active medlog
      //var_dump("Number of Records"." ".$checkmedlogdts["count"]);// debug 
      //var_dump($checkmedlogdts);
      $errorar = [];
      $successar = [];
      if(is_array($checkmedlogdts) && !empty($checkmedlogdts) && isset($checkmedlogdts["records"]))
      {
        /*
        Loop Through and check today's date against the Medlogtimes Date 
        If last log date isn't today's, then Insert a new Medlogtime date & time in the Database 
        @checkmedlogdts datarows shouldhave the seet time logs that needed in order to create new time and taken status row
        @NumbofLogTimes - parameter that holds the number of timelog that needs to be created each night at midnight
        */
        $numoflogtimes = count($checkmedlogdts["records"]);
        foreach($checkmedlogdts["records"] as $d)
        {
           if( $d["status"] =='Active')
           {
            $medadmintimear = json_decode($d["yearmedtime"]);
            $accountnumber = $d["accountnumber"];
            $patientid = $d["patientid"];
            $medid = $d["medicationid"];
            $administereddt = $today;
            $provinit = $d["provinitials"];
            $provSig = $d["providersignature"];
            $activemedids = array();
            $fstat="hold-medtime";   //check against variable 
            $sstat ="discontinue-medtime"; // chek against variable
            $holdstartdt=NULL;
            $holdenddt =NULL;
            if(is_array($medadmintimear) && !empty($medadmintimear))
            {
              //move if else up to the parent level 
              if(!in_array($medid,$activemedids))
              {
                 $islogid = array();
                 $checkslot = $processData->checkPreviousTimeSolots($accountnumber,$medid,$actdate);
                //var_dump($checkslot);exit();
                foreach($medadmintimear as $mr)
                {
                
                  //loop through each medadmintimear item and insert a record in the medlogtimes for Today's date 
                  /* 9/4/25 We need to add code that checks each time entry with the med id to see if a previosus entry exist for Hold or DC*/
                
                  
                    //$checkslot = $processData->checkPreviousTimeSolots($accountnumber,$medid,$actdate);
                    // loop through the checkslots and see if the mr->time matches the checkslot["time"] and if so clone the data with 
                    if(!empty($checkslot) && count($checkslot) >=1)
                    {
                      // now loop 
                    
                      foreach(array_filter($checkslot) as $ck)
                      {
                       
                       
                       // var_dump($ck["time"]);
                       // var_dump($mr->time);
                        $mrtime = $mr->time;
                        //lets check the time format of $mr->time and add seconds if they are not present 
                        if (strpos($mrtime, ':') !== false && substr_count($mrtime, ':') == 1) {   
                          $mrtime .= ':00'; // Add seconds if not present
                        }
                        //check the time matches 
                       
                        if($ck["status"]=="hold-medtime")
                        {
                          $finalstat = $fstat;
                          $holdstartdt = $ck["holdstartdate"];
                          $holdenddt = $ck["holdenddate"];
                        }
                        if($ck["status"]=="discontinue-medtime")
                        {
                          $finalstat = $sstat;
                        }
                       
                        if($ck["time"]==$mrtime && $ck["status"]==$finalstat )
                        {
                      
                          if(!in_array($ck["logid"],array_filter($islogid)))
                          {
                            
                             $insertNewTime = $processData->insertMedlogtableInfo2($accountnumber,$patientid,$medid,$administereddt,$mr->time,$finalstat,$provinit,$provSig,
                            $holdstartdt,$holdenddt);
                            if($insertNewTime["results"]=="Insert")
                            {
                            
                            array_push($successar, $patientid."-".$medid." Logged successfully");
                            array_push($islogid,$ck["logid"]);
                            break;
                            //array_push($activemedids,$medid);//should push in the medlogtimes.logid. This should allow plural entries with same medid to be carried over if needed
                            }
                            else{
                              //if there is a issue lets make an error array and then log it in a file and then send an email notification of all records that had issues 
                              array_push($errorar,$patientid."-".$medid."Error: ".$insertNewTime);
                            }
                          }
                         
                          
                        }
                        else{
                          //do nothing or insert new/clean timeslot
                        } 
                      }
                    }
                    else{
                       //should be an empty time slot with no hold or discontinue status 
                       $finalstat="";
                           $insertNewTime = $processData->insertMedlogtableInfo($accountnumber,$patientid,$medid,$administereddt,$mr->time,$finalstat,$provinit,$provSig);
                          if($insertNewTime["results"]=="Insert")
                          {
                          
                          array_push($successar, $patientid."-".$medid." Logged successfully");
                          array_push($islogid,$ck["logid"]);;//should push in the medlogtimes.logid. This should allow plural entries with same medid to be carried over if needed
                          }
                          else{
                            //if there is a issue lets make an error array and then log it in a file and then send an email notification of all records that had issues 
                            array_push($errorar,$patientid."-".$medid."Error: ".$insertNewTime);
                          }
                    }
                   
                }//end foreach
                array_push($activemedids,$medid);//whaterver happend inside loop should have happen and I should be safe in pushing the medid into the array
              }
              else{
                  //do nothing 
                }
            }
          }
        }
      }
    
          /* End MedCronLog Testing */


  //Now loop throw both Arrays and Send out Email Notification 
  $successnotificationmsg ="The Following Records were logged Successfully:/nr";
  $successnotificationmsg .="<h2>Records Information</h2>";
  $successnotificationmsg .="<table><tbody>";
  if(!empty(array_filter($successar)) && is_array($successar))
  {
    //var_dump("Inside the successar conditional logic - Formating body of success message"); debug
    foreach(array_filter($successar) as $r)
    {
      $successnotificationmsg .="<td>".$r."</td>";
    }
    $successnotificationmsg .="</tbody></table>";
    //now send Notification 
    $sendnotication = $processData->SendInternalMedLogTimesNotication($successnotificationmsg);
    //
    if($sendnotication=="EmailSent")
    {
     // var_dump("Email Sent");debug
      //successfull. Now lets log to a file
      $file = "logfile.txt";
      $handle = fopen("logcron.txt","a");
      if(file_exists($file))
      {
        //prepare time entry with a timestamp 
        $logentry = date("Y-m-d H:i:s")."- MedlogTimes added/n";
        $logentry .=$successnotificationmsg;
        fwrite($handle,$logentry);
        fclose($handle);
      }
      else{
        //let make the file and then log information to the file 
       $msg = date("Y-m-d H:i:s")."-Med Log Error Msg/r"."File Didn't open successfully";
       fwrite($handle,$msg);
       fclose($handle);

      }

    }
     
  }
  //now lets check to see if there were some items that failed
  $errnotificationmsg ="The Following Records were Not logged Successfully:/nr";
  $errnotificationmsg .="<h2>Failed Records Information</h2>";
  $errnotificationmsg .="<table><tbody>";
  if(!empty(array_filter($errorar)) && is_array($errorar))
  {
    //var_dump("Unfortunatley I'm inside the Failed records error array section"); debug
    foreach(array_filter($errorar) as $r)
    {
      $errnotificationmsg .="<td>".$r."</td>";
    }
    $errnotificationmsg .="</tbody></table>";
    //now send Notification 
    $sendnotication = $processData->SendInternalMedLogTimesNotication($errnotificationmsg);
    //
    if($sendnotication=="EmailSent")
    {
      //successfull. Now lets log to a file
      $file = "logfile.txt";
      $handle = fopen("logcron.txt","a");
      if(file_exists($file))
      {
        //prepare time entry with a timestamp 
        $logentry = date("Y-m-d H:i:s")."- Failed Medlog Times/n";
        $logentry .=$errnotificationmsg;
        fwrite($handle,$logentry);
        fclose($handle);
      }
      else{
        //let make the file and then log information to the file 
       $msg = date("Y-m-d H:i:s")."-Med Log Error Msg/r"."File Didn't open successfully | Error Conditional ";
       fwrite($handle,$msg);
       fclose($handle);

      }

    }
     
  }


?>