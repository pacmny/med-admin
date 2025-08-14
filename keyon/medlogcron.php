<?php 
 /*Setting Header Parameters*/
 header('Access-Control-Allow-Methods: GET, POST');
 header('Access-Control-Allow-Origin: *');
 header("Access-Control-Allow-Headers: *");
require_once("processClass.php");


//lets check todays's date | Set EST Time setting below
date_default_timezone_set('America/New_York'); 
$today = date("Y-m-d");


//now lets pass today's date to the process and SqlClass functions to get a list of all the patients active medications that are being logged
/*Get Active Orders->Medications->Medlog->Medlogtime | Formulate the needed query needed to pull the relational data back 
@Note: This needs to pull Data for ALL Patients With Active Medications with administrative_settings=True Vs only check for an individual patients
*/
$processData =new ProcessData();
$checkmedlogdts = $processData->checkActiveMedLogDates($today);
//var_dump("Number of Records"." ".$checkmedlogdts["count"]);// debug 
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
     // var_dump($medadmintimear);exit();
      $accountnumber = $d["accountnumber"];
      $patientid = $d["patientid"];
      $medid = $d["medicationid"];
      $administereddt = $today;
      $provinit = $d["provinitials"];
      $provSig = $d["providersignature"];
     
     
      if(is_array($medadmintimear) && !empty($medadmintimear))
      {
        foreach($medadmintimear as $mr)
        {
         
          //loop through each medadmintimear item and insert a record in the medlogtimes for Today's date 
          $insertNewTime = $processData->insertMedlogtableInfo($accountnumber,$patientid,$medid,$administereddt,$mr->time,$provinit,$provSig);
          //Now see if the New Time Entry was added successfully 
         // var_dump($insertNewTime);exit();
          if($insertNewTime["results"]=="Insert")
          {
           
           array_push($successar, $insertNewTime["patientid"]."-".$insertNewTime["medid"]." Logged successfully");
          }
          else{
            //if there is a issue lets make an error array and then log it in a file and then send an email notification of all records that had issues 
            array_push($errorar,$insertNewTime["patientid"]."-".$insertNewTime["medid"]."Error: ".$insertNewTime["error"]);
          }
        }
      }
       /*lets Add New LogTime ----Original Code May need to back and revisit this once the Database is cleaned up - To Inconsistent right now with all
       dummy data */
     /*  $accountnumber = $d["accountnumber"];
       $patientid = $d["patientid"];
       $medid = $d["medid"];
       $administereddt = $today;
       $admintime =  new DateTime('now');
       //convert DateTime object into string format 
       $admintimes = $admintime->format('Y-m-d H:i:s');
       $provinit = $d["provsignoffinitials"];
       $provSig = $d["provsignoffsignature"];
       $insertNewTime = $processData->insertMedlogtableInfo($accountnumber,$patientid,$medid,$administereddt,$admintimes,$provinit,$provSig); */
      // var_dump($insertNewTime);exit();
      /* if($insertNewTime["results"]=="Insert")
       {
         //contineu; could use this BUT lets do nothing for now
        array_push($successar, $insertNewTime["patientid"]."-".$insertNewTime["medid"]." Logged successfully");
       }
       else{
         //if there is a issue lets make an error array and then log it in a file and then send an email notification of all records that had issues 
         array_push($errorar,$insertNewTime["patientid"]."-".$insertNewTime["medid"]."Error: ".$insertNewTime["error"]);
       } */
     }
  }
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

}
?>