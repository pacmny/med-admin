#!/usr/bin/php
<?php
//echo getcwd();
require("consts.php");
require_once("processClass.php");
$processData = new ProcessData();
$webhookURL="http://10.10.3.7/pacmny-be/web/index.php/fora/query";
//$webhookURL="http://20.84.40.214/pacmny-be/web/index.php/fora/query";
$con = $processData->dbConnect2();
$sql="Select * FROM {$DB}.foradata WHERE status='Pending' ";
$stmnt = $con->prepare($sql);
try{
   if($stmnt->execute())
   {
     	$results = $stmnt->fetchAll();
        // var_dump($results);
     	//count number of records in returned array
         if(empty($results) )
         {
             $noupdatemsg=date('Y-m-d')."No records are pending";
             $nofile="retransmitNoPending.txt";
             $shandler = fopen($nofile,"w+");
             fwrite($shandler,$noupdatemsg);
             fclose($shandler);
         }
         else
         {
                $recstobeupdated = count($results);
               // var_dump($recstobeupdated);
                //Offset Counter to see if number of results items actuall got updated 
                $uprecs =0;
                //now lets get ready to call the front end web hook
                $jsonData = json_encode($results,JSON_PRETTY_PRINT);
                $ch = curl_init($webhookURL);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // use HTTP POST to send form data
                $rresponse = curl_exec($ch);
               // var_dump($rresponse);
                $ofile="inc/database.xml";
                $fs = fopen($ofile,"+w");
                fwrite($fs,$rresponse);
                fclose($fs);
                $duplicate =$rresponse->data;
               // var_dump($duplicate);
                $forareturnStatus="";
                if($rresponse->data=="")
                {
                   // var_dump("Null");
                    //$forareturnStatus="S";
                    //now lets update the database and the current record Pending Status
                    $newStatus="Updated";
                    //now lets update jsondata array
                        foreach($results as $n)
                        {
                            $uprecs ++;
                            $update = $processData->UpdateForaDataByID($n["patientId"],$n["recordId"],$newStatus);
                             //var_dump($update);
                            if($update =="Updated")
                            {
                                //lets log successful transmits 
                                $msglog = date('Y-m-d')."/n"."PatientID"." ".$n["patientID"].", and RecordID of"." ".$n["recordId"]." was updated succesfully";
                                $ffile = fopen("Successtransmits.txt","w+");
                                    fwrite($ffile,$msglog);
                                    fclose($ffile);
                                   // var_dump("Updated"." ".$n["recordId"]);
                                    //now check to see if we reached the max number of records taht needed updated
                                    if($uprecs ==$recstobeupdated)
                                    {
                                        $ffile = fopen("Successtransmits.txt","w+");
                                        fwrite($ffile,$recstobeupdated."records have all been updated");
                                        fclose($ffile);
                                        continue;
                                    }
                            }
                            else{
                                //lets count to see if the number of items updated match the offset number ... If not lets exit and log issue 
                                if($uprecs < $recstobeupdated )
                                {
                                    $msglog = date('Y-m-d')."/n"."The number of Retransmits to be updated were"." ".$recstobeupdated." "."and only"." ".$uprecs." "."of those records were updated."."/n"."Please check sql syntax or mysql debug file to ensure all Mysql Syntax is correct";
                                    $ffile = fopen("transmitIssues.txt","w+");
                                    fwrite($ffile,$msglog);
                                    fclose($ffile);
                                    continue;
                                }

                            }
                        }
                }
                else{
                        //there was an issue on the front-end somewhere lets log the response and then we can figure out how to fix in code 
                        $msglog = date('Y-m-d')."/n"."The response back from the Front-end Webhook is"."/n "." ".$rresponse->message;
                        $ffile = fopen("transmitIssues.txt","w+");
                                fwrite($ffile,$msglog);
                                fclose($ffile);
                                
                    }
                
            }
        
    }
}
catch(PDOException $e){
        $mfile ="sqlissue.txt";
        $fhandler = fopen($mfile,"w+");
        fwrite($fhandler,$e->__toString());
        fclose($fhandler);
    }
?>
