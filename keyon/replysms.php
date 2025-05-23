<?php 
 session_start();
//session_destroy();exit();
var_dump("Here");
 require_once("Twilio/autoload.php");
	$accountsid ="AC47708c582759b32e2a8b4febd0b225d8";
	$authtoken ="ced08978e386ec64315975b60d40a129";
	
	$callar = array("7652937359","3176403937");
	//$client = new Twilio\Rest\Client($accountsid,$authtoken);
	$response = new Twilio\TwiML\MessagingResponse();
  var_dump($response);
  var_dump($_POST);
  var_dump($_SESSION);

	$body = $_POST["Body"];
	$from = $_POST["From"];
  var_dump($_POST["Body"]);
	$handel = fopen("log.txt","w+");
	foreach($_POST as $p)
	{
		$logmsg .=$p."\n";
		//fwrite($handel, $logmsg);
	}
	fwrite($handel,$body." ".$from);
	if(isset($_SESSION["Parkave"]))
	{
		fwrite($handel, "Session Exists"." ".$_SESSION["Parkave"]["fromMsg"]);
	}
	fclose($handel);
	if(!isset($_SESSION["Parkave"]) && empty($_SESSION["Parkave"]))
	{
		//set session 
		$_SESSION["Parkave"] = array("convo_type"=>"directory","fromNumber"=>$_POST["From"],"fromMsg"=>$_POST["Body"]);
    var_dump($_SESSION["Parkave"]);
	$message = "Welcome to Park Avenue Concerige Medicne. Tell me, are you looking to:\n";
	$message .="1 - Schedule Appointment\n";
	$message .="2 - Ask Your Provider a Question\n";
	$message .="3 - Find a Provider\n";
	$message .="Text back either Schedule Appointment or Ask Provider a question";
  var_dump($response->message($message));
	$response->message($message);
	print($response);
	}
	else{
		//var_dump("SEssion is alive");
		//var_dump($_SESSION["Parkave"]);
		//var_dump($_POST);
		//session must exist alreay so lets vet the session to see what the convo-type is in order to properly navigate the connections
         //lets update the session 
		$message="";
		unset($_SESSION["Parkave"]["fromMsg"]);
	   $_SESSION["Parkave"]["fromMsg"] =  $_POST["Body"];
	   $sessval = $_SESSION["Parkave"]["fromMsg"];
	   $handel2 = fopen("log.txt","w+");
	   fwrite($handel2,$sessval."Set from Session Keyon");
	   //fclose($handel2);
	   
	   	 if(isset($_SESSION["Parkave"]["fromMsg"]))
	   	 {

	   	 	fwrite($handel2,"made it");
        var_dump($_SESSIOn["Parkave"]["fromMsg"]);
	   	 	switch($_SESSION["Parkave"]["fromMsg"])
	   	 	{
	   	 			case"Schedule Appointment":
	   	 			{
	   	 				$message .="1"." ".date("Y-m-d")."\n";
				   	 	$message .="2" ." ".date("Y-m-d",strtotime("+1 day"))."\n";
						$message .="3"." ".date("Y-m-d",strtotime("+2 day"))."\n";
						$message .="4"." ". date("Y-m-d",strtotime("+3 day"))."\n";
						$message .="5"." ". date("Y-m-d",strtotime("+4 day"))."\n";
					   	$message2 = "okay";
					  $response->message($message);
					  print($response);
	   	 				break;
	   	 			}
	   	 	}
	   	 	/*$message .="1"." ".date("Y-m-d")."\n";
	   	 	$message .="2" ." ".date("Y-m-d",strtotime("+1 day"))."\n";
			$message .="3"." ".date("Y-m-d",strtotime("+2 day"))."\n";
			$message .="4"." ". date("Y-m-d",strtotime("+3 day"))."\n";
			$message .="5"." ". date("Y-m-d",strtotime("+4 day"))."\n";
		   	$message2 = "okay";
		  $response->message($message);
		  print($response);*/
	   	 }
			
	   	 		
	   	 
	   		/*$message .="1"." ".date("Y-m-d")."\n";
			$message .="2" ." ".date("Y-m-d",strtotime("+1 day"))."\n";
			$message .="3"." ".date("Y-m-d",strtotime("+2 day"))."\n";
			$message .="4"." ". date("Y-m-d",strtotime("+3 day"))."\n";
			$message .="5"." ". date("Y-m-d",strtotime("+4 day"))."\n";
		   	$message2 = "okay";
		  $response->message($message);
		  print($response);*/
	   
	  
	  // print($response);
		//var_dump($_SESSION["Parkave"]);
		//var_dump($_SESSION["Parkave"]["formMsg"]);
		//var_dump($_SESSION["Parkave"]["convo_type"]);
		//check the body for the response 
	  
				
			
		
		/*if(isset($_SESSION["Parkave"]))
		{
			$msgresponse = $_POST["Body"];
			switch($sessval)
			{
				case"Schedule Appointment":
				{
					$handel3 =fopen("log.txt","w+");
					fwrite($handel3,"Made it");
					fclose($handel3);
					
					$message .="1"." ".date("Y-m-d");
					$message .="2" ." ".date("Y-m-d",strtotime("+1 day"));
					$message .="3"." ".date("Y-m-d",strtotime("+2 day"));
					$message .="4"." ". date("Y-m-d",strtotime("+3 day"));
					$message .="5"." ". date("Y-m-d",strtotime("+4 day"));

					$response->message($message);
					print($response);
					break;
				}
			}
		}
		else{
			//var_dump("nothing poste");
			//var_dump($_SESSION["Parkave"]);
			$testmsg = $_SESSION["Parkave"]["fromMsg"];
			//$testmsg = $_SESSION["Parkave"]["convo_type"];
			//$message ="Nothing posted or the variable doesnt exist";
			//var_dump($message);
			//$response->message($message);
			//print($response);
			$response->message($testmsg);
			print($response);
		}*/
		
	}
	//var_dump($response);
	//var_dump($_POST);
	//print($response);

    ?>