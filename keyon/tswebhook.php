<?php 


 /*Turn on All error reporting*/
 error_reporting(E_ALL);

 /*Setting Header Parameters*/
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
//header('Content-Type: application/x-www-formdata-urlencoded');

/*Require ProcessClass*/
require_once('processClass.php');

/*
* This Enpoint will catch information from the Fora Json Data Exachagne. This endpoint is expecting paramaters and the their format that originate from the Data API APEX Document TS V 2.3 
*
* Partner Endpoint can expect these parameters to be passed from 
*@ GroupID: String, TS Group ID
•@ PatientID: String, TS Patient ID
•@ RecID: String, TS ID of batch data transmission
•@ MeasureData: [{ Measurement Data }]
•@ MDataID: String, TS ID of measurement data
•@ Remark: String, remark of data
•@ MDateTime: String, measurement date & time
•@ MDateTimeUTC: String, measurement UTC date & time
•@ MType: String, measurement type
•@ MSlot: String, measurement time slot
•@ MValue1: String, measurement value 1
•@ MValue2: String, measurement value 2
•@ MValue3: String, measurement value 3
•@ MRefNote1: String, measurement reference note 1
•@ MRefNote2: String, measurement reference note 2
•@ MRefNote3: String, measurement reference note 3
•@ MRefNote4: String, measurement reference note 4
•@ MNote: String, measurement note
•@ MPDateTime: String, date & time of newly added data
•@ MPDateTimeUTC: String, UTC date & time of newly added data
•@ MDeviceType: String, meter model No.
•@ MDeviceID: String, meter serial No.
•@ MMeterNote: String, meter note
•@ CustomField1: String, customized field 1
•@ CustomField2: String, customized field 2
*
*/
//var_dump("heretest");
//var_dump($_POST);

$mdata = file_get_contents('php://input');
//var_dump($mdata);
//var_dump($mdata);
$mmdata = json_decode($mdata);
//var_dump($mmdata);

	$postdata="";
	//var_dump("Here");

	if(empty($mmdata))
	{
		//var_dump("Here LIke WOW");
		///lets check for POST 
		if(isset($_POST))
		{
			$postdata = $_POST;
			//var_dump($postdata);
			
			//var_dump($postdata["patientID"]);
			//var_dump($postdata["API_Meth"]);
		}
}

$processData = new ProcessData();//instantiate the class

//var_dump($test);

//exit();
if(isset($_POST)|| is_object($mmdata) || !empty($postdata))//if the post variable exist//if the post variable exist
{
	if(isset($mmdata->AddPatientInfo) && $mmdata->AddPatientInfo->API_Meth=="AddPatientInformation")
	{
	   $adpatientInfo = $processData->GetAddPatientInfoData($mmdata->AddPatientInfo);
	   print_r($adpatientInfo);
	}
	/*elseif($mmdata->ImportPatientData->API=="ImportPostGressData")
	{
		$path="misc/patients.csv";
		$handle = fopen($path,"rw");
		$test = $processData->ImportPatients($path,$handle);
		print_r(json_encode($test,JSON_PRETTY_PRINT));
	}*/
	elseif(isset($mmdata->AddPatientInfo) && $mmdata->AddPatientInfo->API_Meth=="AddPatientDevice")
	{
		$addPatientDevice = $processData->GetAddPatientDeviceData($mmdata->AddPatientInfo);
		print_r($addPatientDevice);
	}
	elseif(isset($mmdata->RemovePatientInfo) && $mmdata->RemovePatientInfo->API_Meth=="RemovePatientDevice")
	{
		$removePatientDevice = $processData->GetRemovePatientDeviceData($mmdata->RemovePatientInfo);
		print_r($removePatientDevice);
	}
	elseif(isset($mmdata->GetPatientInfo) && $mmdata->GetPatientInfo->API_Meth=="GetPatientDevice")
	{
		$getPatientDevice = $processData->GetPatientDeviceData($mmdata->GetPatientInfo);
		print_r($getPatientDevice);
	}
	elseif(isset($mmdata->GetPatientInfo) && $mmdata->GetPatientInfo->API_Meth=="GetNebulizerData")
	{
		$getNebData = $processData->GetNebData($mmdata->GetPatientInfo);
		print_r($getNebData);
	}
	elseif(isset($mmdata->Account) && $mmdata->Account->API_Meth=="GetAccountInfoByAccountNumber")
	{
		/*@ Post Account NUmber From Front End

		@ Post Unique 9 Digit User Number
		*/
		$accountNum = $mmdata->Account->accountNunber;
		$userUnique = $mmdata->Account->userUniqueId;
		$getAccountInfo = $processData->GetAccountInformation($accountNum,$userUnique);
		//check to see if its an array 
		if(is_array($getAccountInfo) && !empty($getAccountInfo))
		{
			//should be an array and it should have data before its returned
		    print_r(json_encode($getAccountInfo));
		}
	}
	elseif(isset($mmdata->Account) && $mmdata->Account->API_Meth=="GetBillingInfoByAccountNumber")
	{

	}
	elseif(isset($mmdata->PatientCpt) && $mmdata->PatientCpt->API_Meth=="AddPatientCpt"){
		$patientCpt = $processData->AddPatientCpt($mmdata->PatientCpt);
		print_r($patientCpt);
	}
	elseif(isset($mmdata->PatientCpt) && $mmdata->PatientCpt->API_Meth=="GetPatientCpt"){
		$patientCpt = $processData->GetPatientCpt($mmdata->PatientCpt);
		print_r($patientCpt);
	}
	elseif(isset($mmdata->PatientCpt) && $mmdata->PatientCpt->API_Meth=="GetPatientCpt2"){
		$patientCpt = $processData->GetPatientCpt2($mmdata->PatientCpt);
		print_r($patientCpt);
	}
	elseif(isset($mmdata->PatientCpt) && $mmdata->PatientCpt->API_Meth=="AddMeterCpt"){
		$patientCpt = $processData->AddMeterCpt($mmdata->PatientCpt);
		print_r($patientCpt);
	}
	elseif(isset($mmdata->PatientCpt) && $mmdata->PatientCpt->API_Meth=="GetMeterCpt"){
		$patientCpt = $processData->GetMeterCpt($mmdata->PatientCpt);
		print_r($patientCpt);
	}
	elseif(isset($mmdata->PatientCpt) && $mmdata->PatientCpt->API_Meth=="GetMeasurements"){
		$patientCpt = $processData->GetMeasurements($mmdata->PatientCpt);
		print_r($patientCpt);
	}
	elseif(isset($mmdata->PatientCpt) && $mmdata->PatientCpt->API_Meth=="GetMeasurements2"){
		$patientCpt = $processData->GetMeasurements2($mmdata->PatientCpt);
		print_r($patientCpt);
	}
	elseif(isset($mmdata->PatientCpt) && $mmdata->PatientCpt->API_Meth=="MarkBilled"){
		$patientCpt = $processData->MarkBilled($mmdata->PatientCpt);
		print_r($patientCpt);
	}
	elseif(isset($mmdata->PatientCpt) && $mmdata->PatientCpt->API_Meth=="GetPatientCptBilling"){
		$patientCpt = $processData->GetPatientCptBilling($mmdata->PatientCpt);
		print_r($patientCpt);
	}
	elseif(isset($mmdata->PatientCpt) && $mmdata->PatientCpt->API_Meth=="GetPatientCptBilling2"){
		$patientCpt = $processData->GetPatientCptBilling2($mmdata->PatientCpt);
		print_r($patientCpt);
	}
	elseif(isset($mmdata->NursyInfo) && $mmdata->NursyInfo->API_Meth=="LookupNursesInfo")
	{
		//var_dump("here");
	   $ncsid = $mmdata->NursyInfo->ncsid;
	   $jurisdiction = $mmdata->NursyInfo->jurisdiction;
	   $license = $mmdata->NursyInfo->rnlicense;
	   $ltype = $mmdata->NursyInfo->ltype;
	   $getInfo = $processData->NursysGetNurseInfo($ncsid,$jurisdiction,$license,$ltype);
	   print(json_encode($getInfo,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->NursyInfo) && $mmdata->NursyInfo->API_Meth=="LookupBatchNursesInfo")
	{
		//var_dump("here");exit();
		/* Grab the account number thats being passed over frm the front end
		* Call Process Class to query the DB for all nurses assocaited with the account number 
		* Loop throught and set the varaibles to pass to Nuryses function for each nurse that needs to be verified
		*/
		$accountnumber = $mmdata->NursyInfo->accountnumber;
		$nursysar = $processData->GetNurseList($accountnumber);
		//var_dump($nursysar);
		if(count($nursysar) >0 && is_array($nursysar))
		{
			foreach($nursysar["nurseInfo"] as $r)
			{
				 $getInfo = $processData->NursysBatchGetNurseInfo($r["state"],$r["licensenum"],$r["licensetype"],$r["ncsbnid"]);
				// var_dump($getInfo);exit();
				//var_dump($getInfo);
				 if(is_array($getInfo) && !empty($getInfo))
				 {
					foreach($getInfo as $n)
					{
						$newar[] = array("firstname"=>$n["FirstName"],"lastname"=>$n["LastName"],"licensetype"=>$n["LicenseType"],"licensenum"=>$n["LicenseNumber"],
					"activeStatus"=>$n["Active"],"licensestatus"=>$n["LicenseStatus"],"licensexpirationDt"=>$n["LicenseExpirationDate"]);
					}
					
				 }
			}
			//var_dump($newar);
			$ggetInfo = array_filter($newar);
			//lets convert Expiration Date from Datetime to Date 
			$updtDb = $processData->UpdateLicenseCheckDate($ggetInfo);
			//var_dump($updtDb);
			//neeed to update the Internal DB with the Last Checked Date and Active status
			
		}
	  
	   print(json_encode($ggetInfo,JSON_PRETTY_PRINT));
	}
  elseif(isset($mmdata->PatientInfo) && $mmdata->PatientInfo->API_Meth=="PatientLookupSearch")
  {
     $keyword = $mmdata->PatientInfo->patientkeyword;
     $account = $mmdata->PatientInfo->accountnumber; 
     $subaccnt = $mmdata->PatientInfo->subaccountnumber;
     $filterby = $mmdata->PatientInfo->filter;
     $accounttype = $mmdata->PatientInfo->accounttype;
     $getproflicense = $processData->findProfLicense($account,$subaccnt);
     $lokup = $processData->lookupPatientSearch($account,$subaccnt,$keyword,$filterby,$accounttype,$getproflicense["license"]);
     if(!empty($lokup))
     {
        //have records so lets send payupload back with JSON
        print(json_encode($lokup,JSON_PRETTY_PRINT));
     }
     else{
      //lets send an empty message back to the user
      $msg = array("Message"=>"No Reslts found, Please Try Again");
      print(json_encode($msg,JSON_PRETTY_PRINT));
     }
  }
  /*Keyon add 5/21/25 Methods to Insert Medication log times  */
  elseif(isset($mmdata->MedicationAdmin) && $mmdata->MedicationAdmin->API_Meth=="InsertUpdateMedLogTimes")
  {
	//see if the administer date is already in the medlog table and if so we are going to update. If not we are going to insert 
	$accountnumber = $mmdata->MedicationAdmin->accountId;
	$patientid = $mmdata->MedicationAdmin->pid;
	$adminDate = $mmdata->MedicationAdmin->adminDate; //Use this as a foriegn Key | setting this to todays date but we can changhe it later if we need to with a dynamcic variable 
	//var_dump($adminDate);
	$providerid = $mmdata->MedicationAdmin->providerid;
	$medname = $mmdata->MedicationAdmin->medname;
	$ordnumber = $mmdata->MedicationAdmin->ordernumber;
	$status = $mmdata->MedicationAdmin->status; //Medication Status 
	$time = date('H:i:s'); //current time or time stamp
	$admintimes = $mmdata->MedicationAdmin->slotedtimes;
	//go get the patient information || But we should be able to have the EMR APP pass the patient name and ID over to the endpoint since The Admin App is based on the Clients Charts
	$getpatientInfo = $processData->GetPatientInfobyPatientId($accountnumber,$patientid);
	//var_dump($getpatientInfo);
	$patientname = $getpatientInfo[0]["first_name"]." ".$getpatientInfo[0]["lastname"];
	//now get the provider ID with the provider ID that's passed over | Or the app should be able to pass in the logged in provider 
	$provider = $processData->LookUpInternalProvider($providerid);
	$providername = $provider["provider"][0]["firstname"]." ".$provider["provider"][0]["lastname"];
    $convdt = new DateTime($adminDate);
	$administrated_at = $convdt->format('Y-m-d');
	//var_dump($administrated_at);
	//go get the medication ID for the active medication (parameter - medname)
	$getmedid = $processData->DoesMedExist($accountnumber,$ordnumber,$providerid,$patientid,$medname,$status);
	$medicationid = $getmedid["records"][0]["medentryid"];
	$checkstatus = $processData->checkMedlogtablenfo($accountnumber,$patientid,$administrated_at,$medicationid);
	//var_dump($checkstatus);
	
	$providersignature = $providername;
	$initval = explode(" ",$providersignature);
	$fname = $initval[0];
	$lname = $initval[1];
	$provinitials =substr($fname,0,1) ." ". substr($lname,0,1);
	if($checkstatus["count"] <=0) //emplty
	{
		 
		
		//lets Insert the Medication Information that we need to log
		$loginfo = $processData->InsertMedLog( $accountnumber,$patientid,$patientname,$ordnumber,
		$providername,$providerid,$medicationid,$administrated_at,$time,$status,json_encode($admintimes),$notes,$providersignature,$provinitials);
		//var_dump($loginfo);
		//check to see if it was successfull
		if(!empty($loginfo) && $loginfo["results"]=="Inserted")
		{
			//Let insert the log timesdf but first let json decode the times 
			//var_dump($admintimes);
		
			$insttimes = array();
			foreach($admintimes as $stime)
			{
				if($stime !="")
				{
					
					$insertlog = $processData->insertMedlogtableInfo($accountnumber,$patientid,$medicationid,$administrated_at,$stime->time,$provinitials,$providersignature);
					//var_dump($insertlog);
					if(!empty($insertlog) && $insertlog["results"]=="Insert")
					{
						array_push($insttimes,$insertlog["results"]);
					}
				}
			}
			
			if(!empty(array_filter($insttimes)) )
			{
				$msg =array("code"=>"200-Successdfull","results"=>$insttimes[0]);
				print(json_encode($msg,JSON_PRETTY_PRINT));
			}
		}
		
		
	}
	else{
		//we need to update the Medlogtable and then the times table 
		//var_dump("Okay, it exist now lets figure out how to update the information");
		$insttimes = array();
		$jtime = json_encode($admintimes);
		$updatelog = $processData->UpdateMedLog($accountnumber,$patientid,$medicationid,$administrated_at,$jtime,$status,$time);
		//var_dump($updatelog);
		if(!empty($updatelog) && $updatelog["result"]=="Updated")
		{
			//we need to get the logtimes entryid's to ensure we are updating the correct rows versus having all the rows be updated with the same values
			$getrowinfo = $processData->getmedtimeEntries($administrated_at,$accountnumber,$patientid);
			//var_dump($getrowinfo);
			if($getrowinfo !='' && $getrowinfo["count"] ==count($admintimes))
			{

			    $i=-1;
				foreach($admintimes as $stime)
				{
					$i++;
					//var_dump($stime->time);
					if($stime !="")
					{
						$logentry = $getrowinfo["results"][$i]["logid"];
						//var_dump($logentry);
						$updatetimelog = $processData->updateMedlogtimetableInfo($logentry,$accountnumber,$patientid,$medicationid,$administrated_at,$stime->time,$provinitials,$providersignature);
						//var_dump($updatetimelog);
						if(!empty($updatetimelog) && $updatetimelog["results"]=="Updated")
						{
							array_push($insttimes,$updatetimelog["results"]);
						}
					}
				}
			}

		} 
			
			
			if(!empty(array_filter($insttimes)) )
			{
				$msg =array("code"=>"200-Successfull","results"=>$insttimes[0]);
				print(json_encode($msg,JSON_PRETTY_PRINT));
			}
		
		//we need to update the database 
		//$updatelog = $processData->updateMedlogtasbleInfo($accountnumber,$patientid,$adminData,$admintimes,$provinitials,$provsignature);
		/*if(!empty($updatelog) && $updatelog["results"]=="Updated")
		{
			$msg = array("code"=>"200-Successfull","results"=>$udpatedlog["results"]);
			print(json_encode($msg,JSON_PRETTY_PRINT));
		} */
	}
  }
  /*Keyon 5/16/25 Added Method to Get PatientAssigned Pharmacy */
  elseif(isset($mmdata->MedicationAdmin) && $mmdata->MedicationAdmin->API_Meth=="GetAssignedPatPharmacy")
  {
	 $accountnumber = $mmdata->MedicationAdmin->accountnumber;
	 $patientid = $mmdata->MedicationAdmin->patientid;
	 $findpharm = $processData->lookupPatientPharmacy($accountnumber,$patientid);
	 if(is_array($findpharm) && $findpharm !="")
	 {
		print(json_encode($findpharm,JSON_PRETTY_PRINT));
	 }
  }
  /*--------keyon add Final Signoff for MedicationAdmin------*/
  elseif(isset($mmdata->MedicationAdmin) && $mmdata->MedicationAdmin->API_Meth=="MedSignOff")
  {
	/*-----Array with multiple Medication Objects nested inside with the information that we need to update our logging tables Medicationlog and <logtimes------*/ 
	/* @Medication object 
	     @ medication Table values or update values 
		 @timeobjects - Times, dose, and status
		 @date object : time, status, dosage,early reason , temp status, locked,[signedoff]-nurse,date
		 @tabsavailable 

	 ** Note: I'm not going to process the possible Medication Objects here but will pass them to the processClass file that will then parse and update tables 
	*/
	//var_dump($mmdata->MedicationAdmin->signoffObj);
	$accountnumber = $mmdata->MedicationAdmin->accountnumber;
	$npinumber = $mmdata->MedicationAdmin->npinumber;
    $patientid = $mmdata->MedicationAdmin->patientid;
	$ordernumber = $mmdata->MedicationAdmin->ordernumber;
	$signoffobj = $mmdata->MedicationAdmin->signoffObj;
	$licensenumber = $mmdata->MedicationAdmin->proflicensenumber;
	//$medtimes = json_decode($signoffobj);
	$tabsavail = $signoffobj->medicatabsAvailable;
	//var_dump($medtimes);
	$updatemedinfo = $processData->processMedTimes($accountnumber,$patientid,$signoffobj);
	//var_dump($updatemedinfo);
	if(is_array($updatemedinfo) && !empty($updatemedinfo))
	{
		print(json_encode($updatemedinfo));
	}

  }
  //keyon add on Local Site - Attemp to Grab active medication to pour into the Medication App 
  elseif(isset($mmdata->MedicationAdmin ) && $mmdata->MedicationAdmin->API_Meth=="GetPatientMeds")
  {

    $accountnumber = $mmdata->MedicationAdmin->accountId;
    $npinumber = $mmdata->MedicationAdmin->providerid;
    $patientid = $mmdata->MedicationAdmin->pid;
    //$providerid = $mmdata->MedicationAdmin->providerid;
   
    $findactivemedorders = $processData->findpatientactiveMedOrders($accountnumber,$npinumber,$patientid);
    //var_dump($findactivemedorders);
	if(count($findactivemedorders["records"]) >=1)
    {
      print(json_encode($findactivemedorders,JSON_PRETTY_PRINT));
    }
	else{
		$msgar = array("code"=>"323-Empty","results"=>"No Records found");
		print(json_encode($findactivemedorders,JSON_PRETTY_PRINT));
	}
  }
  elseif(isset($mmdata->MedicationAdmin) && $mmdata->MedicationAdmin->API_Meth=="InsetPrescription")
  {
	
   //Medication Paramaters 
   $accountnumber = $mmdata->MedicationAdmin->accountnumber;
	$ordernumber = $mmdata->MecationAdmin->Ordernumber;
   $medname = $mmdata->MedicationAdmin->medicationName;
   $ndcnumber = $mmdata->MedicationAdmin->ndcNumber;
   $rx = $mmdata->MedicationAdmin->rxNorm;
   $diagnois = $mmdata->MedicationAdmin->diagnosis;
   $dosage = $mmdata->MedicationAdmin->dosage;
   $freq = $mmdata->MedicationAdmin->frequency;
   $route = $mmmdata->MedicationAdmin->route;
   $prn = $mmdata->MedicationAdmin->prn;
   $quantity = $mmdata->MedicationAdmin->quantity; 

   //Prescription Parameters 
   $rxNum = $mmdata->MedicationAdmin->rxNumber;
   $filldate = $mmdata->MedicationAdmin->filledDate;
   $numofRefills = $mmdata->MedicationAdmin->refills;
   $stDate = $mmdata->MedicationAdmin->startDate;
   $endDt = $mmdata->MedicationAdmin->endDate;
   $refillreminder = $mmdata->MedicationAdmin->refillReminderDate;
   $xpireDate = $mmdata->MedicationAdmin->expirationDate;

   /*Order Information - Provider Info to make the Order,
   * if the provider doesn't exist, return a message that an account needs to be made for the provider
   * 
   * */
  $provname = $mmdata->MedicationAdmin->providerName;
  $provDea = $mmdata->MedicationAdmin->providerDea;
  $provNpi = $mmdata->MedicationAdmin->providerNpi;
  $licensenum = $mmdata->MedicationAdmin->licenseNumber;
  $provAddress = $mmdata->MedicationAdmin->providerAddress;
  $provOffice = $mmdata->MedicationAdmin->providerOffice;
  $provCell = $mmdata->MedicationAdmin->providerCell;
  $provEmail = $mmdata->MedicationAdmin->providerEmail;

  /*
  Pharmancy Informaiton || Pharmacy Parmater 
  *Should be the patient Pharmacy and not the main the Pharmacy
  * However, We should check the main Pharmacy and if the patient Pharmacy Doesn't exist, then go ahead and add it to the Overarching Pharmancy Table 
  */
  $pharmName = $mmdata->MedicationAdmin->pharmacyName; 
  $pharmDea = $mmdata->MedicationAdmin->pharmacyDea;
  $pharmNpi = $mmdata->MedicationAdmin->pharmacyNpi;
  $pharmAddr = $mmdata->MedicationAdmin->pharamacyAddress;
  $pharmOffice = $mmdata->MedicationAdmin->pharmacyOffice; 
  $pharmEmail = $mmdata->MedicationAdmin->pharmacyEmail;

    $checkifmedExist = $processData->DoesMedExist($accountnumber,$odernumber,$npinumber,$medname,'Active');
	$insertMedication = $processData->InsertAdminAppMedication($accountnumber,$ordernumber,$medname,$ndcnumber,$rx,$diagnois,$dosage,$freq,$route,$prn,$quantity);
	/*$insertpharmacyInfo = $processData->InsertAdminAppPharmacy($accountnumber,$odernumber,$pharmName,$pharmDea,$pharmNpi,$pharmAddr,$pharmOffice,$pharmEmail);
	*$insertproviderInfo = $processData->InsertAdminAppProviderInfo($accountnumber,$ordernunber,$provname,$provDea,$provNpi,$licensenum,$provAddress,$provOffice,$provCell,$provEmail); */
	$insertPrescription = $processData->InsertPrescriptionInfo($accountnumber,$ordernumber,$npinumber,$rxNum,$filldate,$numofRefills,$stDate,$endDate,$refillreminder,$xpireDate);
	if($insertPrescription !="" && is_array($insertPrescription))
	{
		print(json_encode($insertPrescription,JSON_PRETTY_PRINT));
	}

  }
  elseif(isset($mmdata->MedicationAdmin) && $mmdata->MedicationAdmin->API_Meth=="InsertAdminProviderInfo")
  {
	  //ProviderInfo Paramaters 
	$provname = $mmdata->MedicationAdmin->providerName;
	$provDea = $mmdata->MedicationAdmin->providerDea;
	$provNpi = $mmdata->MedicationAdmin->providerNpi;
	$licensenum = $mmdata->MedicationAdmin->licenseNumber;
	$provAddress = $mmdata->MedicationAdmin->providerAddress;
	$provOffice = $mmdata->MedicationAdmin->providerOffice;
	$provCell = $mmdata->MedicationAdmin->providerCell;
	$provEmail = $mmdata->MedicationAdmin->providerEmail;


	  $insertproviderInfo = $processData->InsertAdminAppProviderInfo($accountnumber,$ordernunber,$provname,$provDea,$provNpi,$licensenum,$provAddress,$provOffice,$provCell,$provEmail);
	 if( $insertproviderInfo !="" && is_array( $insertproviderInfo))
	{
		print(json_encode( $insertproviderInfo,JSON_PRETTY_PRINT));
	}
	
  }
  elseif(isset($mmdata->MedicationAdmin) && $mmdata->MedicationAdmin->API_Meth=="InsertAdminPharmacyInfo")
  {
	  //Pharmacy Paramaters 
	  $pharmName = $mmdata->MedicationAdmin->pharmacyName; 
	  $pharmDea = $mmdata->MedicationAdmin->pharmacyDea;
	  $pharmNpi = $mmdata->MedicationAdmin->pharmacyNpi;
	  $pharmAddr = $mmdata->MedicationAdmin->pharamacyAddress;
	  $pharmOffice = $mmdata->MedicationAdmin->pharmacyOffice; 
	  $pharmEmail = $mmdata->MedicationAdmin->pharmacyEmail;

   $insertpharmacyInfo = $processData->InsertAdminAppPharmacy($accountnumber,$odernumber,$pharmName,$pharmDea,$pharmNpi,$pharmAddr,$pharmOffice,$pharmEmail);
	 if($insertpharmacyInfo !="" && is_array($insertpharmacyInfo))
	{
		print(json_encode($insertpharmacyInfo,JSON_PRETTY_PRINT));
	}
	
  }
  elseif(isset($mmdata->MedicationAdmin) && $mmdata->MedicationAdmin->API_Meth=="InsertAdminPrescriptionInfo")
  {
	  //PRescription Paramaters 
	$accountnumber = $mmdata->MedicationAdmin->accountnumber;
	$ordernumber = $mmdata->MecationAdmin->Ordernumber;
	$rxNum = $mmdata->MedicationAdmin->rxNumber;
   $filldate = $mmdata->MedicationAdmin->filledDate;
   $numofRefills = $mmdata->MedicationAdmin->refills;
   $stDate = $mmdata->MedicationAdmin->startDate;
   $endDt = $mmdata->MedicationAdmin->endDate;
   $refillreminder = $mmdata->MedicationAdmin->refillReminderDate;
   $xpireDate = $mmdata->MedicationAdmin->expirationDate;
   $insertPrescription = $processData->InsertPrescriptionInfo($accountnumber,$ordernumber,$npinumber,$rxNum,$filldate,$numofRefills,$stDate,$endDate,$refillreminder,$xpireDate);
	 if($insertPrescription !="" && is_array($insertPrescription))
	{
		print(json_encode($insertPrescription,JSON_PRETTY_PRINT));
	}
	
  }
  elseif(isset($mmdata->MedicationAdmin) && $mmdata->MedicationAdmin->API_Meth=="InsertAdminMecationInfo")
  {
	  //Medication Paramaters 
	  $accountnumber = $mmdata->MedicationAdmin->accountnumber;
	  $ordernumber = $mmdata->MedicationAdmin->ordernumber;
	  $patientid = $mmdata->MedicationAdmin->patientid;
	 $medname = $mmdata->MedicationAdmin->name;
	 $npinumber = $mmdata->MedicationAdmin->npinumber;
	 $ndcnumber = $mmdata->MedicationAdmin->ndcnumber;
	 $rx = $mmdata->MedicationAdmin->rxNumber;
	 $diagnois = $mmdata->MedicationAdmin->diagnosis;
	 $dosage = $mmdata->MedicationAdmin->dosage;
	 $freq = $mmdata->MedicationAdmin->frequency;
	 $route = $mmdata->MedicationAdmin->route;
	 $prn = $mmdata->MedicationAdmin->prn;
	 $quantity = $mmdata->MedicationAdmin->quantity; 
	 $medsettings = $mmdata->MedicationAdmin->medsetting;
	 $totalTabs = $mmdata->MedicationAdmin->totalTabs;
	 $proflicense=$mmdata->MedicationAdmin->proflicensenumber;
	 $newmedsettings="";
	 $medchangetype="";
	 $instruction="Take 2 and call me in the morning";

	 //perscription Information
	 $rxnumber = $mmdata->MedicationAdmin->rxNumber;
	 $dtfilled = $mmdata->MedicationAdmin->datescriptFilled;
	 $refills = $mmdata->MedicationAdmin->refills;
	 $startdate = $mmdata->MedicationAdmin->startDate;
	 $enddate = $mmdata->MedicationAdmin->endDate;
	 $refilreminderdt= $mmdata->MedicationAdmin->refillReminderDate;
	 $refillexpirationdt = $mmdata->MedicationAdmin->expirationDate;

	 //provider information 
	 $providername = $physican;
	 $deanumber="k55534343";
	 $npinumber=$npinumber;
	 $licensenumber=$proffisionalicense;
	 $provaddress="";
	 $officenumber="";
	 $providcell="";
	 $provemail="";

	 //Pharmacy Information 
	 $pharmacyname= $mmdata->MedicationAdmin->pharmacy;
	 $pharmnpi = $mmdata->MedicationAdmin->pharmacyNpi;
	 $pharmaddr = $mmdata->MedicationAdmin->pharmacyAddress;
	 $pharmacyOffice = $mmdata->MedicationAdmin->pharmacyPhone;
	 $pharmacyCell = $mmdata->MedicationAdmin->pharmacyCell;
	 $pharmacyEmail = $mmdata->MedicationAdmin->pharmacyEmail;
	 $pharmdeanumber = $mmdata->MedicationAdmin->pharmacyDea;
	
	 //lets insert the Prescription information 
	 $checkifmedExist = $processData->DoesMedExist($accountnumber,$ordernumber,$npinumber,$patientid,$medname,'Active');
	 if(isset($checkifmedExist) && is_array($checkifmedExist) && $checkifmedExist["count"]=="1" && $medsettings=="")
	 {
		print(json_encode($checkifmedExist,JSON_PRETTY_PRINT));
	 }
	 elseif(is_array($checkifmedExist) && $medsettings=="update")
	 {
		
		//var_dump("Made it inside the update block, lets do it"); debug
		$newmedsettings="Administered";
		$updateMedinformation = $processData->UpdateMedicationInfo($accountnumber,$ordernumber,$patientid,$ndcnumber,$rx,$prn,$newmedsettings,$totalTabs);

		print(json_encode($updateMedinformation,JSON_PRETTY_PRINT));
	 }
	 elseif(is_array($checkifmedExist) && $checkifmedExist["count"] <="0" && $medsettings=="")
	 {
		/*Mecication Does not exist and we should be ready to update 
		*We are going to update each seaction in seperate functions 
		*/
		//var_dump("Ready to run the insert code"); debug
		 $orderdate = date('Y-m-d');  //current date 
		 $ordertime= date('Y-m-d h:i:s'); //current time
		 $ordertype="Nurse Order";
		 $abndelivered=0;
		 $ordphysicianreadback ="";
		 $physician="";
		 $secphysician="";
		 $physicanNpi="";
		 $provemail="";
		 $ordphone="";
		 $ordfax="";
		 $ordaddress="";
		 $ordsendtophyscians="";
		 $ordwoundcare="";
		 $verbalorder="";
		 $verbalorderdate= date('Y-m-d');
		 $nurseSignature=""; //call the nurses table and get the nurses name
		 $orderDescrip="Adding a new medication from the Medication Administration Application - Order created in the automated workflow";
		 $orderstatus="Pending"; //has tp be until provider signs off on the order
		 $provsigdate="0000-00-00";


		/* Create An Order before we Insert Medication Infformation */ 
		$getNum = $processData->GetGlobalOrderNumber(); //should brinb back the incremented order number for direct use. Its an array being returned so you have grab the array prop name
		
		/*Now we weed to look up the provider information with the $ordernumber that was sent over. Pull the Provider information and NPI from the previous order */
		$getorderbyPatid = $processData->getMedAdminOrdersbyPatientID($patientid);
		//var_dump($getorderbyPatid);
		foreach($getorderbyPatid["orders"] as $ordinfo)
		{
			if($ordinfo["ordernumber"]==$ordernumber && $ordinfo["npinumber"]==$npinumber)
			{
				//grb that informaiton or assign variables 
				$physician=$ordinfo["primary_physician"];
				$secphysician=$ordinfo["sec_physician"];
				$physicanNpi = $ordinfo["npinumber"];
				$provemail=$ordinfo["email"];
				$ordphone = $ordinfo["phone"];
				$ordfax = $ordinfo["fax"];
				$ordaddress = $ordinfo["address"];
				//check address and if its blank go get the patients address from the patients table with the (patientid)
				if(empty($ordaddres))
				{
					$grabpatientaddr = $processData->grabPatientAddressbyID($patientid);
					$ordaddress = $grabpatientaddr["address"];
					/*if the address is still empty - then generate an system message for the address but then alert Nurse and Provider that the patient information needs to be update while 
					* the order is still pending
					*/
					if($ordaddress =="" || $ordaddress==null)
					{
						$ordaddress="Address Info Needs Updating - System Generated";
					}
				} 
				$ordsendtophyscians="1";
				$verbalorder="1";
				
				$getnursesig = $processData->GetIndividualNurse($accountnumber,$proflicense);
				if(!empty($getnursesig))
				{
					$nurseSignature = $getnursesig["records"]["firstname"]." ".$getnursesig["records"]["lastname"];
				}
				else{
					$nurseSignature="Signature Needed";
				}
				


			}
		}
		//check to see if I have enough information to create an order now 
		if($orderdate !="" && $ordertime !="" && $physician !="" && $physicanNpi !="")
		{
			//Okay should have enough to create an automated order and then send automated emails for notification 
			//Checked to see if there are enough Order variable filled in value to create an automated order
		/*----*/
		
		$ordar = array("accountnumber"=>$accountnumber,"ordDate"=>$orderdate,"ordTime"=>$ordertime,"ordtype"=>$ordertype,"abndeliv"=>$abndelivered,"readback"=>$ordphysicianreadback,
		"primephysician"=>$physician,"secphysician"=>$secphysician,"email"=>$provemail,"npi"=>$physicanNpi,
		"address"=>$ordaddress,"phone"=>$ordphone,"fax"=>$ordfax,"sendtophysician"=>$ordsendtophyscians,"woundcare"=>$ordwoundcare,
		"verbaloffer"=>$verbalorder,"verbalOrderDt"=>$verbalorderdate,"verbalOrderTime"=>$ordertime,"hasmed"=>'',
		"hasdiag"=>$diagnois,"hassupplies"=>'',"hasValueSign"=>'',"description"=>$orderDescrip,"status"=>$orderstatus,"ordernumber"=>$getNum["ordernumber"],"writer"=>'system',
 		 "nursesigname"=>$nurseSignature,"nursesigdate"=>$orderdate,"providersignature"=>'',"provsigdate"=>$provsigdate);
			$createOrder =  $processData->InsertOrderTemplate($patientid,$ordar);
			//var_dump($createOrder); debugh
			//now send out notification via Mandrill 
			$jdata = json_decode($createOrder);
			
			if($jdata->result == "Inserted")
			{
				//send Email later 

				//now Insert Medecation Info
				$newmedsettings="Administered";
				$medchangetype="New";
				$insertmed = $processData->InsertAdminMecationInfo($accountnumber,$ordernumber,$patientid,$ndcnumber,$rx,$prn,$newmedsettings,$totalTabs,$route,$diagnois,$freq,$dosage,$medname,$instruction,$medchangetype);
				
				//var_dump($insertmed);
				if($insertmed["result"]=="Inserted")
				{
					//Now Insert Prescreption Information 
					$insertPrescription = $processData->InsertPerscription($accountnumber,$patientid,$medname,$rxnumber,$dtfilled,$refills,$startdate,$enddate,$refilreminderdt,$refillexpirationdt);
					//var_dump($insertPrescription); debug
					if($insertPrescription["results"]=="Inserted")
					{
						//double cross check to ensure the provider exist and attached to the change order | then we need to add the provider through Register function 
						$checkprovider = $processData->LookUpInternalProvider($npinumber);
						//var_dump($checkprovider);debug
						if(!empty($checkprovider) && is_array($checkprovider))
						{
							if($npinumber ==$checkprovider["provider"][0]["npinumber"])
							{
								//Provider alread exist so lets log that informaiton into the paprovidre table  - If not register Provier 
								$explodename = explode(" ", $physician);
								$firstname =$explodename[0];
								$lastname = $explodename[1];
								$subaccountnumber="5052001";
								$taxonomy="00000x000";
								$addr1 = $checkprovider["provider"][0]["addr1"];
								$phone = $checkprovider["provider"][0]["tel"];
								$fax = $checkprovider["provider"][0]["fax"];
								$licensenumber="";
								$logProviderInfo = $processData->logpaProvider($accountnumber,$subaccountnumber,$firstname,$lastname,$npinumber,$provemail,$deanumber,$taxonomy,
								$ordernumber,$patientid,$addr1,$phone,$fax,$licensenumber);
								
								//check to see if the successfull result is there 
								if($logProviderInfo["results"]=="Inserted")
								{
									//var_dump("Now Insert the Pharmacy Information");
									/*$pharmacyname
									$pharmnpi 
									$pharmaddr
									$pharmacyOffice 
									$pharmacyCell 
									$pharmacyEmail 
									$pharmdeanumber */
									$assigndt=date("Y-m-d");
									$notes="Notes go here";
									//lets check to see if Pharmacy exist and if not lets add it to our overarching Pharmacy table 
									$doesphrmexist = $processData->findPharmacy($accountnumber,$pharmacyname,$npinumber);
									//var_dump($doesphrmexist); debug
									if($doesphrmexist["count"] < 1) //pharmacy doesn't exist, lets put it into the table 
									{
										$insertpharm = $processData->InsertPharmacy($accountnumber,$pharmacyname,$pharmnpi,$pharmaddr,$pharmacyOffice,$pharmacyCell,$pharmdeanumber,$pharmacyEmail);
										//var_dump($insertpharm); debug
										if($insertpharm["results"]=="Inserted")
										{
											$insertpharm  = $processData->logpaPharmacy($accountnumber,$subaccountnumber,$patientid, $npinumber, $pharmacyname,$pharmdeanumber,$pharmnpi,$assigndt,$notes);
											if($insertpharm["results"]=="Inserted")
											{
												//now send successf payload back.
												$success= array("status"=>"200 Successfull","message"=>"Medication Added Successfully");
												print(json_encode($success,JSON_PRETTY_PRINT));
											}
										}

									}
									else{
										//Pharmacy Alreay exist and now we just need to log that information 
										$insertpharm  = $processData->logpaPharmacy($accountnumber,$subaccountnumber,$patientid, $npinumber, $pharmacyname,$pharmdeanumber,$pharmnpi,$assigndt,$notes);
										//var_dump($insertpharm);debug
										if($insertpharm["results"]=="Inserted")
										{
											//now send successf payload back.
											$success= array("status"=>"200 Successfull","message"=>"Medication Added Successfully");
											print(json_encode($success,JSON_PRETTY_PRINT));
										}
									}
									
								}
								else{
									var_dump($logProviderInfo);//should show the errors 
								}
								//var_dump($logProviderInfo);exit();
							}
						}
						else{
							//You willneed to register the provider
							var_dump("You need to register the provider");
						}
						
					}
					else{
						//Roll back transaction and then send payload error 
						var_dump($insertPrescription);
						var_dump("Insertion of the Prescription didn't work successfully, Roll back transaction");
					}
				}
				else{
					//return error and possibly need to roll back the entire transaction . So roll back the transaction and then send payload on Error
					var_dump("medicsation wasn't inserted successfully");
				}
			}
			else{
				//We need to send the user and the system a return message letting them know that the entire mediation didn't get added successfully
			}
		}
		//set the order variables needed for the orders template 
		
	

	 }
	 else{
		//var_dump($checkifmedExist);
		var_dump("Okaaay");
	 }
	 //var_dump("It skipped the if condition");
	 exit();
	 var_dump($checkifmedExist);
	 $insertMedication = $processData->InsertAdminAppMedication($accountnumber,$ordernumber,$medname,$ndcnumber,$rx,$diagnois,$dosage,$freq,$route,$prn,$quantity);
	 if($insertMedication !="" && is_array($insertMedication))
	{
		print(json_encode($insertMedication,JSON_PRETTY_PRINT));
	}
	
  }
  elseif(isset($mmdata->NursyInfo) && $mmdata->NursyInfo->API_Meth=="DischargePatientFromCard")
  {
    $accountnumber = $mmdata->NursyInfo->accountnumber;
    $subaccntnumber = $mmdata->NursyInfo->subaccount;
    $accounttype = $mmdata->NursyInfo->accounttype;
    $patientid = $mmdata->NursyInfo->patientid;
    $providerid = $mmdata->NursyInfo->providerid;
    $patientInfo = $mmdata->NursyInfo->nurseInfo[0];
   // var_dump($pateintInfo);
    $xpload = explode("-",$patientInfo);
   // var_dump($xpload);
    $patientnumber = $xpload[0];
    $patientfname = $xpload[1];
    $patientlname = $xpload[2];
   
    $pid = abs($patientid);//final patientID
		$provid = $mmdata->NursyInfo->providerid; //provider could be nurse or Provider
    $getproflicense = $processData->findProfLicense($accountnumber,$subaccntnumber);
  
     if($accounttype=="Registered Nurse")
     {
      //lets discharge from the nruse assignment table 
      $dischargenurse = $processData->dischargePatient($getproflicense["license"],$accountnumber,$subaccntnumber,$patientid,$patientfname,$patientlname,$accounttype);
       if(isset($dischargenurse) && !empty($dischargenurse))
       {
        $msg = array("status"=>"200 Successfull","message"=>$dischargenurse);
        print(json_encode($dischargenurse,JSON_PRETTY_PRINT));
       }
     }
     if($accounttype=="Provider")
     {
      $dischargenurse = $processData->dischargePatient($getproflicense["license"],$accountnumber,$subaccntnumber,$patientid,$patientfname,$patientlname,$accounttype);
      if(isset($dischargenurse) && !empty($dischargenurse))
      {
       $msg = array("status"=>"200 Successfull","message"=>$dischargenurse);
       print(json_encode($dischargenurse,JSON_PRETTY_PRINT));
      }
     }
    // $dischargenurse = $processData->DischargeNurseByNurseID($pid,$accountnumber,$nurseid);
		//var_dump($dischargenurse);
		
  }
	elseif(isset($mmdata->NursyInfo) && $mmdata->NursyInfo->API_Meth=="DischargeNurseFromPatient")
	{
		$patientid = $mmdata->NursyInfo->patientid;
		$accountnumber = $mmdata->NursyInfo->accountnumber;
		$pid = abs($patientid);
		$nurseid = $mmdata->NursyInfo->nurseid;
    
		//var_dump($patientid);
		//var_dump($accountnumber);
		//var_dump($nurseid);
		$dischargenurse = $processData->DischargeNurseByNurseID($pid,$accountnumber,$nurseid);
		//var_dump($dischargenurse);
		if($dischargenurse !="" && is_array($dischargenurse))
		{
			print(json_encode($dischargenurse,JSON_PRETTY_PRINT));
		}
	}
  elseif(isset($mmdata->ProviderInfo) && $mmdata->ProviderInfo->API_Meth=="AssignPatientToProvider")
	{
		$providerid = $mmdata->ProviderInfo->providerid;
		//var_dump($patientid);
		$accountnumber = $mmdata->ProviderInfo->accountnumber;
		//$accountnumber ="904575107";
		$pid =$providerid;//don't need the abs here because some LicenseNumber actually have letters
		$patientar = $mmdata->ProviderInfo->patientInfo;
		//var_dump($patientar);exit();
		$assignPatient = $processData->AssignPatientToProvider($pid,$accountnumber,$patientar);
		if($assignPatient !="" && is_array($assignPatient))
		{
			print_r(json_encode($assignPatient,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->NursyInfo) && $mmdata->NursyInfo->API_Meth=="AssignPatientToNurse")
	{
		$nurseid = $mmdata->NursyInfo->nurseid;
		//var_dump($patientid);
		$accountnumber = $mmdata->NursyInfo->accountnumber;
		//$accountnumber ="904575107";
		$sid =$nurseid;//don't need the abs here because some LicenseNumber actually have letters
		$patientar = $mmdata->NursyInfo->patientInfo;
		//var_dump($patientar);exit();
		$assignPatient = $processData->AssignPatientToNurse($sid,$accountnumber,$patientar);
		if($assignPatient !="" && is_array($assignPatient))
		{
			print_r(json_encode($assignPatient,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->NursyInfo) && $mmdata->NursyInfo->API_Meth=="AssigNurseToPatient")
	{
		$patientid = $mmdata->NursyInfo->patientid;
		//var_dump($patientid);
		$accountnumber = $mmdata->NursyInfo->accountnumber;
		//$accountnumber ="904575107";
		$pid = abs($patientid);
		$nursar = $mmdata->NursyInfo->nurseInfo;
		//var_dump($nursar);exit();
		$assignNurse = $processData->AssignNurseToPatient($pid,$accountnumber,$nursar);
		if($assignNurse !="" && is_array($assignNurse))
		{
			print_r(json_encode($assignNurse,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->NursyInfo) && $mmdata->NursyInfo->API_Meth=="GetPatientNurse")
	{
		$patientid = $mmdata->NursyInfo->patientnumber;
		$accountnumber = $mmdata->NursyInfo->accountnumber;
		$pid = abs($patientid);
		$getnurse = $processData->GetPatientNurseList($accountnumber,$pid);
		print_r(json_encode($getnurse,JSON_PRETTY_PRINT));
	}
  elseif(isset($mmdata->NursyInfo) && $mmdata->NursyInfo->API_Meth=="GetIndividualAccountAid")
	{
		$accountnumber = $mmdata->NursyInfo->accountnumber;
		$certnumber = $mmdata->NursyInfo->certificatenumber;
    //var_dump($certnumber);
		$getnurse = $processData->GetIndividualAid($accountnumber,$certnumber);
    //var_dump($getnurse);
		if($getnurse !="" && is_array($getnurse))
		{
			print_r(json_encode($getnurse,JSON_PRETTY_PRINT));
		}

	}
	elseif(isset($mmdata->NursyInfo) && $mmdata->NursyInfo->API_Meth=="GetIndividualAccountNurse")
	{
		$accountnumber = $mmdata->NursyInfo->accountnumber;
		$licensenumber = $mmdata->NursyInfo->licensenum;
		$getnurse = $processData->GetIndividualNurse($accountnumber,$licensenumber);
		if($getnurse !="" && is_array($getnurse))
		{
			print_r(json_encode($getnurse,JSON_PRETTY_PRINT));
		}

	}
	elseif(isset($mmdata->NursyInfo) && $mmdata->NursyInfo->API_Meth=="GetAccountPatients")
	{
		$accountnumber = $mmdata->NursyInfo->accountnumber;
		$nurseid = $mmdata->NursyInfo->nurseid;
    
		$getPatient = $processData->GetAccountPatients($accountnumber);
    
		//var_dump($getPatient);

		if($getPatient !="" && is_array($getPatient))
		{
			//now get all patient assignments 
			$getnrsassignments = $processData->GetPatientNursAssignments($accountnumber,$nurseid);
			//var_dump($getnrsassignments);
			if( is_array($getnrsassignments) || empty($getnrsassignments))
			{
				//need to merge array and send them pack for the picklist 
				//var_dump("here");
				$msg= array("status"=>"200","patientInfo"=>$getPatient,"nurseAssignment"=>$getnrsassignments);
				print_r(json_encode($msg,JSON_PRETTY_PRINT));
			}
			
		}
	}
  /*
  Get Phone number associated with the account
  */
  elseif(isset($mmdata->Sms) && $mmdata->Sms->API_Meth=="GetAccountSMSPHone")
  {
    //var_dump("Breakcache");
    $accnt = $mmdata->Sms->accountnumber;
    $subaccnt = $mmdata->Sms->subaccountnumber;
    $getphone = $processData->GetAccountPhoneNumber($accnt,$subaccnt);
  }
  /*
  Get Six Digit and Veifiy Code section */
  elseif(isset($mmdata->Sms) && $mmdata->Sms->API_Meth=="Sms6DigitText")
	{
		
    $accnt = $mmdata->Sms->accountnumber;
    $subaccnt = $mmdata->Sms->subaccountnumber;
		$phone = $mmdata->Sms->smsPhone;
    
		if(!empty($accnt) && !empty($subaccnt))
		{
			//session exist and phone isn't empty 
			$getcode = $processData->GetSMSCode($accnt,$subaccnt,$phone);
     // var_dump($getcode);exit();
			if($getcode !="" && $getcode["TwilioStatus"]=="successful")
			{
				$msg = array("message"=>"SMS Code Sent");
				print(json_encode($msg,JSON_PRETTY_PRINT));
			}
      elseif($getcode["status"]=="No Match Found")
      {
        $msg = array("message"=>"No Matching Phone Number Found");
        print(json_encode($msg,JSON_PRETTY_PRINT));
      }
      elseif($getcode["error_code"])
      {
        print(json_encode($getcode,JSON_PRETTY_PRINT));
      }
		}
		else{
			$msg =array("message"=>"Can't Locate Account Information. Please Update your profile Information.","error"=>"Phone Number not located");
			print(json_encode($msg,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->Sms) && $mmdata->Sms->API_Meth=="VerifyLoginCode")
	{
		$accountnumber = $mmdata->Sms->accountnumber;
    $subaccountnumber = $mmdata->Sms->subaccountnumber;
		$logincode = $mmdata->Sms->verification_code;
		//veriffy it against the users info 
		$verify = $processData->VerifyLoginCode($accountnumber,$subaccountnumber,$logincode);
		if(is_array($verify) && !empty($verify))
		{
			print(json_encode($verify,JSON_PRETTY_PRINT));
		}
		else{
			//send error array 
      $mssg = array("message"=>"No token allowed","error"=>$verify);
			print(json_encode($mssg,JSON_PRETTY_PRINT));
		}

	}
  /*End six digit section*/
  elseif (isset($mmdata->Account) && $mmdata->Account->API_Meth=="GetAccountType")
  {
   
    $accntnumber = $mmdata->Account->accountnumber;
    $subaccntnumber = $mmdata->Account->subaccountnumber;
    //var_dump($accntnumber);
    //var_dump($subaccntnumber);
    /*02/6/2025 
    *@Going to take the account and subaccount number and find the professional license of th AccountType
    *The native Alldata variable isn't pulling the NPI through from the postgress db. This is the attempt to solve it
    */
    $getproflicense = $processData->findProfLicense($accntnumber,$subaccntnumber);
   //var_dump($getproflicense["license"]);
   // $npinumber ="1164514139";//"328389345";//DR Brown npi number isn't pulling through on the allData object | Hard coded for now
    //var_dump($accntnumber, $subaccntnumber);
    $getaccnttype = $processData->getAccountType($accntnumber,$subaccntnumber,$getproflicense["license"]);// was the original hardcoded parameter $npinumber
    //var_dump($getaccnttype);exit();
    if( is_array($getaccnttype) || !empty($getaccnttype))
			{
				//need to merge array and send them pack for the picklist 
				//var_dump("here");
				$msg= array("status"=>"200","patientInfo"=>"","providerAssignment"=>$getaccnttype,"professionalLicense"=>$getproflicense);
				print_r(json_encode($msg,JSON_PRETTY_PRINT));
			}
  }
  elseif(isset($mmdata->ProviderInfo) && $mmdata->ProviderInfo->API_Meth=="GetProviderAccountPatients")
	{
		$accountnumber = $mmdata->ProviderInfo->accountnumber;
		$providerid = $mmdata->ProviderInfo->providerid;
		$getPatient = $processData->GetAccountPatients($accountnumber);
		//var_dump($getPatient);
    //var_dump($providerid);

		if($getPatient !="" && is_array($getPatient))
		{
			//now get all patient assignments 
			$getnrsassignments = $processData->GetPatientProviderAssignments($accountnumber,$providerid);
			//var_dump($getnrsassignments);
			if( is_array($getnrsassignments) || empty($getnrsassignments))
			{
				//need to merge array and send them pack for the picklist 
				//var_dump("here");
				$msg= array("status"=>"200","patientInfo"=>$getPatient,"providerAssignment"=>$getnrsassignments);
				print_r(json_encode($msg,JSON_PRETTY_PRINT));
			}
			
		}
	}
	elseif(isset($mmdata->Account) && $mmdata->Account->API_Meth=="GetIndividualPermissions")
	{
		$accountnumber = $mmdata->Account->useraccntnumber;
		$subaccntnumber = $mmdata->Account->usersubaccntnumber;
		$username = $mmdata->Account->username;
		$email = $mmdata->Account->userEmail;
		//var_dump($accountnumber);
		//var_dump($username);
		//var_dump($email);
		//var_dump($subaccntnumber);
		//var_dump("Perm");//exit();
		//Check the processclass to pass the parameters that's needed
		$checkperm = $processData->GetIndividualUserPremissions($username,$accountnumber,$subaccntnumber,$email);
		
		//var_dump($checkperm);
		if($checkperm !="")
		{
			//send back the json 
		   print_r(json_encode($checkperm,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->Account) && $mmdata->Account->API_Meth=="GetUserAccountPermission")
	{
		$accountnumber = $mmdata->Account->accountnumber;
		$subaccountnumber = $mmdata->Account->subaccountnumber;
		$getPerm = $processData->IndividualUserPremissions($accountnumber,$subaccountnumber);
		
		if(is_array($getPerm) && !empty($getPerm))
		{
			
			print_r(json_encode($getPerm,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->Account) && $mmdata->Account->API_Meth=="UpdateIndividualPermissions")
	{
		$accountnumber = $mmdata->Account->useraccntnumber;
		$subaccntnumber = $mmdata->Account->usersubaccntnumber;
		$username = $mmdata->Account->username;
		$email = $mmdata->Account->userEmail;
		$cando = $mmdata->Account->candoperm;
        $cantdo =$mmdata->Account->cantdoperm;
		$accounttype=$mmdata->Account->accounttype;
		$checkperm = $processData->UpdateIndividualUserPremissions($username,$accountnumber,$subaccntnumber,$email,$accounttype,$cando,$cantdo);
		//var_dump($checkperm);
		if(is_array($checkperm) && !empty($checkperm) && $checkperm["message"]=="Updated")
		{
			
			print_r(json_encode($checkperm,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->Account) && $mmdata->Account->API_Meth=="GetDefaultPermissions")
	{
		$accountnumber = $mmdata->Account->useraccntnumber;
		$subaccntnumber = $mmdata->Account->usersubaccntnumber;
		$username = $mmdata->Account->username;
		$email = $mmdata->Account->userEmail;
		$accounttype=$mmdata->Account->accounttype;
		$getperm = $processData->GetUserDefaultPremissions($accounttype);
		
		if(!empty($getperm) && is_array($getperm))
		{
			print_r(json_encode($getperm,JSON_PRETTY_PRINT));
		}

	}
  elseif(isset($mmdata->NursyInfo) && $mmdata->NursyInfo->API_Meth=="GetAccountAids")
  {
    $action = $mmdata->NursyInfo->action;
		$patientid = $mmdata->NursyInfo->patientid;
		$accountnumber = $mmdata->NursyInfo->accountnumber;
		$pid = abs($patientid);

      //var_dump($action);
		/*
		Paramaters that need to be passed over
		@account number
		@premissions
		@unique Nine
		*/
		$getaid = $processData->GetNurseAidList($accountnumber);
		//var_dump($getaid);
		if(is_array($getaid) && !empty($getaid) && $action !="GetPatientsAssigned")
		{
			
			print_r(json_encode($getaid,JSON_PRETTY_PRINT));
		}
    elseif(is_array($getaid) && $action=="GetPatientsAssigned")
		{
			//now get List of Nurse and Assigned Nurse for patient
			//get assigned nurses by passing the patientid
			
			$nwnurse = $processData->GetAidAssignmentList($accountnumber);
		  var_dump($nwnurse);
			$gtassignednurse = $processData->GetNurseAssignmentInfo($accountnumber,$pid);
			$mgar = array("status"=>"200", "message"=>"Success","nurseInfo"=>$nwnurse,"nurseassignmentInfo"=>$gtassignednurse);
			

			//var_dump($merge);
			print_r(json_encode($mgar,JSON_PRETTY_PRINT));
		}
		else{
			$msg = array("status"=>"Error","message"=>$getnurse);
			print_r(json_encode($msg,JSON_PRETTY_PRINT));
		}
  }
	elseif(isset($mmdata->NursyInfo) && $mmdata->NursyInfo->API_Meth=="GetAccountNurse")
	{
		
		$action = $mmdata->NursyInfo->action;
		$patientid = $mmdata->NursyInfo->patientid;
		$accountnumber = $mmdata->NursyInfo->accountnumber;
		$pid = abs($patientid);

      
		/*
		Paramaters that need to be passed over
		@account number
		@premissions
		@unique Nine
		*/
		$getnurse = $processData->GetNurseList($accountnumber);
		//var_dump($getnurse);exit();
		if (is_array($getnurse) && $action == "GetPatientsAssigned"){
			$new_result = $processData->GetPatientsAssigned($getnurse);
			print_r(json_encode($new_result, JSON_PRETTY_PRINT));
		}
		elseif(is_array($getnurse) && !empty($getnurse) && $action !="GetNursesAssigned")
		{
			
			print_r(json_encode($getnurse,JSON_PRETTY_PRINT));
		}
		elseif(is_array($getnurse) && $action=="GetNursesAssigned")
		{
			//now get List of Nurse and Assigned Nurse for patient
			//get assigned nurses by passing the patientid
			
			$nwnurse = $processData->GetNurseAssignmentList($accountnumber);
		
			$gtassignednurse = $processData->GetNurseAssignmentInfo($accountnumber,$pid);
			$mgar = array("status"=>"200", "message"=>"Success","nurseInfo"=>$nwnurse,"nurseassignmentInfo"=>$gtassignednurse);
			

			//var_dump($merge);
			print_r(json_encode($mgar,JSON_PRETTY_PRINT));
		}
		else{
			$msg = array("status"=>"Error","message"=>$getnurse);
			print_r(json_encode($msg,JSON_PRETTY_PRINT));
		}
		//var_dump($getnurse);
	}
	elseif(isset($mmdata->Patients) && $mmdata->Patients->GetAccountPatients)
	{
		
	}
	elseif(isset($mmdata->ManageNurseList) && $mmdata->ManageNurseList->API_Meth=="Add_Nurse")
	{
		//var_dump("here");
	   /*$ncsid = $mmdata->ManageNurseList->ncsid;
	   $jurisdiction = $mmdata->ManageNurseList->jurisdiction;
	   $license = $mmdata->ManageNurseList->rnlicense;
	   $ltype = $mmdata->ManageNurseList->ltype;
	   */
	 // var_dump("inside Add a Nurse Method right Now");exit();
	 //var_dump($mmdata);exit();
	  $ncsbnid = $mmdata->ManageNurseList->ncsid;
	 // var_dump($ncsbnid);
        $jurisdiction = $mmdata->ManageNurseList->jurisdiction;
       $subactioncode = $mmdata->ManageNurseList->subactioncode;//required
         $licensnum = $mmdata->ManageNurseList->licensnum;
          $licensetype = $mmdata->ManageNurseList->ltype;
         $email = $mmdata->ManageNurseList->email;
         $addr1 = $mmdata->ManageNurseList->address1;//req
         $addr2 = $mmdata->ManageNurseList->address2;
         $ccity = $mmdata->ManageNurseList->city;//req
		// var_dump($ccity);exit();
         $sstate = $mmdata->ManageNurseList->state;//req
         $zzip = $mmdata->ManageNurseList->zip;//req
         $lastfourssn = $mmdata->ManageNurseList->lastfourssn;//req
        $bdayear = $mmdata->ManageNurseList->bdayear;//req
		$lastfourbday = explode("-",$bdayear);
		   $bylstfour = $lastfourbday[0];
         $hospitalpracsetting = $mmdata->ManageNurseList->hospitalpracsetting;//req
         $hostpracsettingother = $mmdata->ManageNurseList->hostpracsettingother;
         $notficationenabled = $mmdata->ManageNurseList->notficationenabled;//req
         $reminderenabled = $mmdata->ManageNurseList->reminderenabled;//req
         $locationlist = $mmdata->ManageNurseList->locationlist;
         $recid = $mmdata->ManageNurseList->recid;
		 
		 $getInfo = $processData->ManageNursysList($subactioncode,$jurisdiction,$licensnum,$licensetype,$ncsbnid,$email,$addr1,$addr2,$ccity,$sstate,$zzip,$lastfourssn,$bylstfour,$hospitalpracsetting,$hostpracsettingother,$notficationenabled,$reminderenabled,$locationlist,$recid);
		 print_r(json_encode($getInfo,JSON_PRETTY_PRINT));
	   //$getInfo = $processData->ManageNursysList($ncsid,$jurisdiction,$license,$ltype);
	   //print_r($getInfo);
	}
	elseif(isset($mmdata->Account) && $mmdata->Account->API_Meth=="GetAccountUsers")
	{
		//lets get the paramaters 
		$accountnum = $mmdata->Account->accountnumber;
		//$uniquenine = $mmdata->Account->uniquenine;
		//var_dump($accountnum);
		$getusers = $processData->GetAccountUsers($accountnum);
		if(is_array($getusers) && !empty($getusers))
		{
			print(json_encode($getusers,JSON_PRETTY_PRINT));
		}
	}
	elseif($mmdata->Account->API_Meth=="GetPendingAccountInfo")
	{
		$accounttype = $mmdata->Account->accountype;
		$uniqueID = $mmdata->Account->uniqueNine;
		$getInfo = $processData->GetPendingAccountInfo($accounttype,$uniqueID);
		if(is_array($getInfo) && !empty($getInfo))
		{
			//return information to the front end
			print(json_encode($getInfo,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->Account) && $mmdata->Account->API_Meth==" UpdatePendingAccountInfo")
	{
		$accountNumber = $mmdata->Account->accountnumber;
		$unine = $mmdata->Account->uniqueNine;
		$accntStat = $mmdata->Account->Accountstatus;
		$updatePendingAccountInfo($accountNumber,$unine,$accntStat);
		if(is_array($updatePendingAccountInfo) && !empty($updatePendingAccountInfo))
		{
			print(json_encode($updatePendingAccountInfo,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->Account) && $mmdata->Account->API_Meth=="SendNewSubAccountEmail")
	{
		//lets get the posted parameters
		//var_dump("made it");
		$accntnum = $mmdata->Account->accountnumber;
		$unine = $mmdata->Account->uniquenine;
		$accounttype = $mmdata->Account->accounttype;
		$accounthlder = $mmdata->Account->accountholder;
		$useremail = $mmdata->Account->usertoemail;
		$userfirstname = $mmdata->Account->firstname;
		$userlastname = $mmdata->Account->lastname;
		$getAccntEmail = $processData->GetEMRAccountInfo($accntnum,$unine);
		//var_dump($getAccntEmail);
		//I need to check the getAccntEmail variable and conduct some conditional ogic
		if($getAccntEmail["status"]=="Successful")
		{
			//lets get the ifnormation that we need
			$adminEmail = $getAccntEmail["data"][0]["email"];
			$userEmail = $getAccntEmail["data"][0]["user_email"];
			$name = $userfirstname." ".$userlastname;
			$sendEmail = $processData->QueSubEmailTemplate($adminEmail,$userEmail,$name, $accounttype, $unine, $accntnum);
			if($sendEmail=="Sent")
			{
				$msgar = array("status"=>"Successfull");
				print(json_encode($msgar,JSON_PRETTY_PRINT));
			}

		}
		else{
			var_dump("didn't work");
			var_dump($getAccntEmail);
		}
	}
	elseif(isset($mmdata->Account) && $mmdata->Account->API_Meth=="UpdateParentAccount")
	{
		$accountnumber = $mmdata->Account->accountnumber;
		$status = $mmdata->Account->status;
		$updateaccount = $processData->UpdateAccountStatusByAccountNumber($accountnumber,$status);
		if($updatedaccount["message"]=="Account Updated")
		{
			$msg = array("message"=>"Account Updated Successfully","status"=>"200");
			print(json_encode($msg,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->Messages) && $mmdata->Messages->API_Meth=="FetchMessages")
	{
		//var_dump("Jump it off");
		$accountnumber = $mmdata->Messages->accountnumber;
		$subaccountnumber = $mmdata->Messages->subaccountnumber;
		//var_dump($accountnumber);
		//var_dump($subaccountnumber);
		$status="NEW";
		$fetch = $processData->FetchMessages($subaccountnumber,$accountnumber,$status);
		//var_dump($fetch);
		if(!empty($fetch) && is_array($fetch))
		{
			print(json_encode($fetch,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->Insurance) && $mmdata->Insurance->API_Meth=="SaveNewInsurance"){
		$subid = $mmdata->Insurance->accountnumber;
		$pid = $mmdata->Insurance->pid;
		$name = $mmdata->Insurance->newName;
		$stDate = $mmdata->Insurance->newStart;
		$endDate = $mmdata->Insurance->newEnd;
		$incLim = $mmdata->Insurance->newIncLim;
		$annLim = $mmdata->Insurance->newAnnLim;
		$result = $processData->SaveNewInsurance($subid, $pid, $name, $stDate, $endDate, $incLim, $annLim);
		print_r(json_encode($result));
	}
	elseif($mmdata->Messages->API_Meth=="SearchAllMessagesByIDS")
	{
		$accountnumber = $mmdata->Messages->accountnumber;
		$subaccountnumber = $mmdata->Messages->subaccount;
		$messages = $mmdata->Messages->message;
		$fetch = $processData->SearchAllMessagesByIDS($subaccountnumber,$accountnumber,$messages);
		if(!empty($fetch) && is_array($fetch))
		{
			print(json_encode($fetch,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->Messages) && $mmdata->Messages->API_Meth=="FetchAllMessagesByIDS")
	{
		$accountnumber = $mmdata->Messages->accountnumber;
		$subaccountnumber = $mmdata->Messages->subaccountnumber;
		$fetch = $processData->FetchAllMessagesByIDS($subaccountnumber,$accountnumber);
		//var_dump($fetch);
		if(!empty($fetch) && is_array($fetch))
		{
			print(json_encode($fetch,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->Messages) && $mmdata->Messages->API_Meth=="FetchMessagesByThreadID")
	{
		$accountnumber = $mmdata->Messages->accountnumber;
		$subaccountnumber = $mmdata->Messages->subaccountnumber;
		$threadid  = $mmdata->Messages->threadid;
		$fetch = $processData->FetchMessagesByThreadID($subaccountnumber,$accountnumber,$threadid);
		if(!empty($fetch) && is_array($fetch))
		{
			print(json_encode($fetch,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($postdata["API_Meth"]) && $postdata["API_Meth"]=="SendInternalMessages")
	{
		//var_dump("Jump it off");
		$filedata="";
		$currentdir="";
		$fileurl="";
		$filename="";
		$reciversubaccnt = $postdata["recieversubaccountnumber"];
		$sendersubaccnt = $postdata["sendersubaccountnumber"];
		$senderaccountnum = $postdata["senderaccountnunber"];
        $message = $postdata["message"];
		$subject = $postdata["subject"];
        $status = $postdata["status"];
		$date = date("Y-m-d H:i:s");
        $sender = $postdata["senderName"];
		$additionalcontacts = $postdata["additionalcontacts"];
		$internalcontacts = explode(",",$postdata["internalcontacts"]);
		//var_dump($internalcontacts);//exit();
		$sendmsg="";
		if(isset($_FILES))
		{
			$filedata = $_FILES["files"];
			$currentdir = getcwd();
			$movefile = $processData->MoveUploadedFile($filedata,$currentdir);
			$fileurl="";
			if($movefile["status"]=="Successfull")
			{
				//File Upload was successdfull and now lets get the fileurl so we can store it in the db
				$fileurl = $movefile["fileurl"];
				$filename = $movefile["filename"];
			}
		}
		$action="Insert";
		if($additionalcontacts && is_array($additionalcontacts))
		{
			//run it through the isValidEmail funciton 
			foreach($additionalcontacts as $key=>$contacts)
			{
				//var_dump($contacts);
				$isvalid = $processData->isEmailValid($contacts);
				
				if($isvalid==1)
				{
					//send email notification 
					
					$emailnotification = $processData->SendInternalMessagingNotication($contacts,$sender);
					//var_dump($emailnotification);
					
					
					
				}
				else{
					var_dump("Not valid");
				}
			}
			
		}
		//exit();
		$status="NEW";
		//var_dump("mde it");
		//var_dump(array_filter($internalcontacts));
		if(count(array_filter($internalcontacts)) >1)
		{
			$i=0;
			foreach(array_filter($internalcontacts) as $cn)
			{
				$i++;
				//var_dump($i);
				$reciversubaccnt = $cn["subaccountnumber"];
				$sendmsg = $processData->SendInternalhMessages($reciversubaccnt,$sendersubaccnt,$senderaccountnum,$message,$status,$date,$sender,$action,$fileurl,$filename,$subject);
			}
		}
		else{
			$sendmsg = $processData->SendInternalhMessages($reciversubaccnt,$sendersubaccnt,$senderaccountnum,$message,$status,$date,$sender,$action,$fileurl,$filename,$subject);
		}
		//$sendmsg = $processData->SendInternalhMessages($reciversubaccnt,$sendersubaccnt,$senderaccountnum,$message,$status,$date,$sender,$action,$fileurl,$filename,$subject);
		//var_dump($sendmsg);
		if(!empty($sendmsg) && is_array($sendmsg) && $sendmsg["message"]=="Inserted")
		{
			print(json_encode($sendmsg,JSON_PRETTY_PRINT));
		}
	}
	elseif($mmdata->Contacts->API_Meth =="InsertContact")
	{
		
		$firstname = $mmdata->Contacts->firstname;
        $lastname = $mmdata->Contacts->lastname;
        $email = $mmdata->Contacts->email;
        $phone = $mmdata->Contacts->phone;
        $address = $mmdata->Contacts->address;
        $addr2 = $mmdata->Contacts->address2;
        $city =  $mmdata->Contacts->city;
        $state = $mmdata->Contacts->state;
        $zip = $mmdata->Contacts->zip;
        $country = $mmdata->Contacts->country;
        $dob = $mmdata->Contacts->dob;
        $notes = $mmdata->Contacts->notes;
        $accountnumber =$mmdata->Contacts->accountid;
        $subaccnt = $mmdata->Contacts->subaccount;
		//Now lets send that information to the datbase to be Inserted 
		$addContact = $processData->AddContact($firstname,$lastname,$email,$phone,$address,$addr2,$city,
		$state,$zip,$country,$dob,$notes,$accountnumber,$subaccnt);
		var_dump($addContact);
		if($addContact["status"]=="Successfull")
		{
			//everything worked out lets return payload to the front-end to inform the user 
		}
	}
	elseif(isset($postdata) && $postdata !="" && $postdata["API_Meth"]=="UploadNurseFile")
	{
		if(isset($_FILES))
		{
			$filedata = $_FILES["files"];
			
		}
		
	//var_dump($filedata);
	//$accountid = $postdata["accountid"];
	$accountid="95017";
	$currentdir = getcwd();
	$desireddir = getcwd()."/keyon/insurance";
	//define("File_Path",$accountid."/files/insurance/");
	define("File_Path",$currentdir);
	//define("Web_Path",$_SERVER["HTTP_SERVER"]); //Not working right on this VNC show we need to try something else
	$fileurl="";
	// $docurl = Web_Path;
	//get the real server Name 
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$serverName = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
	$scriptPath = $_SERVER['REQUEST_URI'];
	//$fullUrl = $protocol . $serverName . $scriptPath;
	$fullUrl = $protocol . $serverName."/";
	//$fullUrl = $protocol . $serverName ."/insurancefiles";
	//var_dump($fullUrl);

	
	//var_dump($docurl);
	if(!file_exists($currentdir))
	{
		 //Should never run right now because its the ......./ and we need perm updated so we can create and write to directories
		$crtdir = mkdir($currentdir."/img",0777,true);
		
	}
	else{
		if($filedata["error"] !=0)
		{
			$errorAr = array("Upload_Message"=>"Files didn't load correctly");
			
			print(json_encode($errorAr,JSON_PRETTY_PRINT));
		}
		else{
				$name = preg_replace("/[^A-Z0-9._-]/i","_",$filedata["name"]);
				$i=0;
				$pathinfo = pathinfo($name);
				while(file_exists($name))
				{
					$i++;
					$name = $pathinfo["filename"]."-".$i.".".$pathinfo["extension"];
				}
				$success = move_uploaded_file($filedata["tmp_name"],"/var/www/html/new_pdfs/100000000/insurance/123/test123.pdf");
				
				$fileurl = $fullUrl.$name;
				 
				
				if(!$success)
				{
					
					$result = array("error"=>"File could not upload correctly","message"=>$success);
					print_r(json_encode($result,JSON_PRETTY_PRINT));
				}
				else{
					
					$result = array("fileurl"=>$fileurl,"status"=>"Successfull","error"=>"");
					print_r(json_encode($result,JSON_PRETTY_PRINT));
					
					
					
				}
			}
		}
	}
	elseif($mmdata->Account->API_Meth=="CreateTwilioSubAccount")
	{
		$accountnumber = $mmdata->Account->accountnumber;
		$accountname ="TestComplany";
		require_once("Twilio/autoload.php");
		$accountsid ="AC47708c582759b32e2a8b4febd0b225d8";
		$authtoken ="ced08978e386ec64315975b60d40a129";
		$twilnumber ="+18556730319";
		$Twilendpoint="https://api.twilio.com/2010-04-01/Accounts.json";
		$client = new Twilio\Rest\Client($accountsid,$authtoken);
		$crsubacnt = $client->accounts->create($accountname);
		
	}
	elseif($mmdata->Messages->API_Meth=="SearchContacts")
	{
		//var_dump("here");
		$accountnumber = $mmdata->Messages->accountnumber;
		$subaccountnumber = $mmdata->Messages->sbaccnt;
		$srchterm = $mmdata->Messages->searchterm;
		$exname = explode(" ",$srchterm);
		$firstname = $exname[0];
		$lastname = $exname[1];
		$getterm = $processData->GetContactBySearch($accountnumber,$subaccountnumber,$firstname,$lastname);
		//var_dump($getterm);
		if(is_array($getterm) && isset($getterm))
		{
			print(json_encode($getterm,JSON_PRETTY_PRINT));
		}
		
	}
	elseif($mmdata->Messages->API_Meth=="SearchGroups")
	{
		$accountnumber = $mmdata->Messages->accountnumber;
		$subaccountnumber = $mmdata->Messages->sbaccnt;
		$srchterm = $mmdata->Messages->searchterm;
		//$exname = explode(" ",$srchterm);
		//$firstname = $exname[0];
		//$lastname = $exname[1];
		//var_dump("Clear Cache");
		$getterm = $processData->GetContactByGroup($accountnumber,$subaccountnumber,$srchterm);
		//var_dump($getterm);
		if(is_array($getterm) && isset($getterm))
		{
			print(json_encode($getterm,JSON_PRETTY_PRINT));
		}
		
	}
	elseif($mmdata->Messages->API_Meth=="SearchAllContactsNurseProviders")
	{
		$searchterm = $mmdata->Messages->searchterm;
		$explodeterm = explode(" ",$searchterm);
		$firstname = $explodeterm[0];
		$lastname = $explodeterm[1];
		$account =$mmdata->Messages->accountnumber;
		$subaccount = $mmdata->Messages->subaccountnumber;
		$getresults = $processData->SearchAllContactsNurseProviders($account,$firstname,$lastname);
		if(is_array($getresults) && isset($getresults))
		{
			print(json_encode($getresults,JSON_PRETTY_PRINT));
		}
		//var_dump($getresults);
	}
	elseif($mmdata->PdfList->API_Meth=="GetAllNursePdfs")
	{
		$subid = $mmdata->PdfList->subid;
		$pid = $mmdata->PdfList->pid;
		$result = $processData->GetAllNursePdfs($subid, $pid);
		print_r(json_encode($result));
	}
	elseif(isset($postdata) && $postdata !="" && $postdata["API_Meth"] == "AddNewPdf"){
		$subid = $postdata["subid"];
		$externid = $postdata["externid"];
		$pdfType = $postdata["pdfType"];
		$file = $_FILES["file"];
		$latest = $postdata["latest"];
		$result = $processData->AddNewPdf($subid, $externid, $pdfType, $file, $latest);
		print_r(json_encode($result));
	}
	elseif($mmdata->Account->API_Meth=="AddContact")
	{
		$accountid = $mmdata->Account->accountid;
		$subaccnt = $mmdata->Account->subaccntid;
		$fname = $mmdata->Account->FirstName;
		$lname = $mmdata->Account->Lastname;
		$email = $mmdata->Account->email;
		$phone = $mmdata->Account->phone;
		$address = $mmdata->Account->address;
		$address2 = $mmdata->Account->address2;
		$city = $mmdata->Account->city;
		$state = $mmdata->Account->state;
		$zip = $mmdata->Account->zipcode;
		$country = $mmdata->Account->country;
		$dob = $mmdata->Account->dob;
		$gender = $mmdata->Account->gender;
		$contacttype = $mmdata->Account->accounttype;
		$notes = $mmdata->Account->notes;
		$date = new DateTime(); 
		$creationdt = $date->format('Y-m-d H:i:s');
		$addcontact = $processData->InsertContactInfo($accountid,$subaccnt,$fname,$laname,$email,$phone,$address,$address2,
		$city,$state,$zip,$country,$dob,$gender,$contacttype,$notes,$creationdt);
		if($addcontact["messages"]=="Inserted")
		{
			//send back json with amessage and code status 
			$msg = array("code"=>"200","status"=>"Inserted","msg"=>"Contact Successfully Added");
			print(json_encode($msg,JSON_PRETTY_PRINT));
		}
	}
	// elseif($postdata["API_Meth"] == "UpdateAccount")
	// {
	// 	$accountnumber = $postdata["accountnumber"];
	// 	$uniqueID = $postdata["uniqueid"];
	// 	$firstname = $postdata["Firstname"];
	// 	$lastname = $postdata["Lastname"];
	// 	$address = $postdata["Address"];
	// 	$city = $postdata["prCity"];
	// 	$state = $postdata["prState"];
	// 	$zip = $postdata["prZip"];
	// 	$password = $postdata["password"];
	// 	$phone = $postdata["cell"];
	// 	$accntStatus = "Active";
	// 	$compPreference = $postdata["commpref"];
	// 	$oldInsFile = $postdata["oldInsFile"];
	// 	$oldLicenseFile = $postdata["oldLicenseFile"];
	// 	$insFile = $_FILES["insuranceFile"];
	// 	$licenseFile = $_FILES["licenseFile"];

	// 	$result = $processData->UpdateAccount($accountnumber, $uniqueID, $firstname, $lastname, $address, $city, $state, $zip, $password, $phone, $accntStatus, $compPreference, $oldInsFile, $oldLicenseFile, $insFile, $licenseFile);
	// }
	elseif(isset($postdata) && $postdata !="" && $postdata["API_Meth"]=="CreateSubAccount"/*$mmdata->Account->API_Meth=="CreateSubAccount"*/)
	{
		
		//Call to Get Account Number or GRap Account number from post The Fron End system should have this number
		$accountnumber = $postdata["accountnumber"];
		
		//then we need to call and get the EMR license to be associated with the subaccount | Need a license generation function

		$accountrole= $postdata["accountrole"];//"Administrator";//$mmdata->Account->Formdata->accountrole;
		//var_dump($accountrole);exit();
		$username = $postdata["username"];
		$firstname=$postdata["Firstname"]; //User First Name
		$lastname=$postdata["Lastname"];
		$address = $postdata["Address"]; //Personal Address
		$city = $postdata["prCity"]; //Personal City Data
		$state = $postdata["prState"]; //Personal State 
		$zip = $postdata["prZip"]; //Personal Zipcode
		$username=$postdata["username"];
		$password= $postdata["password"];
		$email=$postdata["email"];
		// $uploadfileurl =$mmdata->Account->Formdata->fileuploadurl;
		//var_dump($uploadfileurl);exit();
		//$premission =$mmdata->Account->Formdata->permission;
		$phone=$postdata["cell"];
		$npi="";//Should nw get this from the post method for sub-accounts 
		$license="";//nurses license that could be validated later but for now we're just going to store this information
		$uniqueID=(string)$mmdata->Account->Formdata->uniqueNineID;//Nine Diget that goes into Front End System
		$accnType=$mmdata->Account->Formdata->accountType;// not needed ; //Manager or Provider
    //lets check to see if Account Type is Home Health Aid
    $ishomeHeathAid=""; 
    $hh_hasCertificate="";
    $aidcertificateNumber="";
    if($mmdata->Account->Formdata->accountType=="Home Health Aid")
    {
      $ishomeHeathAid="Yes";
      if($mmdata->Account->Formdata->aidHasCertificate=="Yes")
      {
        //set the cert number 
        $hh_hasCertificate=$mmdata->Account->Formdata->aidHasCertificate;
      }
      if($mmdata->Account->Formdata->aidCerticateValue !="")
      {
        $aidcertificateNumber = $mmdata->Account->Formdata->aidCerticateValue;
      }
    }
		$dob = $mmdata->Account->Formdata->dob;
		$gender = $mmdata->Account->Formdata->gender;
		//$phone=$mmdata->Account->Formdata->userphone; //Need to Add to the new Form
		$useracntemail=$email;
		$proffisionalicense=$postdata["license"]; //Assigned for Provider, LPN oR RN | everyone can have their own;
		$accntStatus="Pending";
		$upremission=$accountrole;
		$emrlicense="46801843";
		$ucreationDt=date("Y-m-d");
		$compPreference=$postdata["commpref"];
		//2/14/2024 - Adding Patient Variables to Add Patient Sub accounts 
		$emgcontact = $postdata["emergencycontact"];
		$emgcontactphone = $postdata["emergencycontactphone"];
		$maritalstatus = $postdata["martialstatus"];
		$insuranceprovider = $postdata["Insuranceprovider"];
		$policynumber = $postdata["policynumber"];
		$bloodtype = $postdata["bloodtype"];
		$allergies = $postdata["allergies"];
		$insFile = $_FILES["insuranceFile"];
		$licenseFile = $_FILES["licenseFile"];
		$createAccountUser="";//instantiate variable 

       //lets generate the EMRL license number and after the CreateAccountUser function is complete, store information in license table 
	   //generate licensenumber first
	   $genlicense = $processData->GenerateUserLicense();
	   //var_dump($genlicense);
	    //lets check the EMR License table to see if the license has already been created or assigned to avoid duplicate keys
		$checklicensenum = $processData->CheckGeneratedLicense($genlicense);
		//var_dump($checklicensenum);
		if($checklicensenum["message"]=="License Does Not Exist")
		{
			//assign license number to this variable to be passed into the create Accountuser function 
			$emrlicense = $genlicense;
			$status="active";
			//now insert the license into the table 
			$insertLicense = $processData->InsertLicense($genlicense,$accountnumber,$uniqueID,$status);
			//var_dump($insertLicense);
			if($insertLicense["message"]=="Inserted")
			{
				$createAccountUser = $processData->CreateAccountUser($accountnumber,$uniqueID,$username,$accnType,$firstname,$lastname,$phone,$email,$address,$city,$state,$zip,$proffisionalicense,$accntStatus,$upremission,$emrlicense,$ucreationDt,$compPreference,$insFile,$licenseFile);
			}
			else{
				$msg =array("message"=>"Problem, With Inserting License, Please Contact Your Administrator");
				return $msg;
			}
			
		}
		else{
			$msg = array("message"=>"License Already Assigned","error"=>"Please update users Account Information");
			return $msg;
		}
		
		//var_dump($createAccountUser);
		if($accnType=="Patient")
		{
			//Add to the Patient Table as well
			$addpatient = $processData->AddPatientAccount($accountnumber,$uniqueID,$username,$accnTyp,$firstname,$lastname,$dob,$gender,$phone,$email,$address,$city,$state,$zip,$emgcontact,$emgcontactphone,$maritalstatus,$insuranceprovider,$policynumber,$bloodtype,$allergies);
			

		}
    
    if($accnType=="Home Health Aid")
    {
      
      $caregiver="Home Health Aid";
  
      $addnurse = $processData->AddHomeAidAccount($accountnumber,$uniqueID,$username,$caregiver,$firstname,$lastname,$phone,$email,$address,$city,$state,$zip,
      $ishomeHeathAid,$hh_hasCertificate, $aidcertificateNumber,$accntStatus); 
     
      if($addnurse["message"]=="Record Exist")
      {
        return $addnurse["message"];
      }
      if($addnurse["message"]=="Inserted")
      {
        //contine on with the next line of code
      }
    }
		
		if($createAccountUser["status"] =="Successfull")
		{ 
			//lwts sets permissions here 
					//get the default permissions 
			$adminrole=$accountrole;
			$adm = $processData->GetUserDefaultPremissions($adminrole);
			if(is_array($adm) && !empty($adminrole))
			{
				//insert the permissions into the user premissions table 
				$insert = $processData->SaveUserPremissions($adminrole,$username,$accountnumber,$uniqueID,$adm);
				if($insert["message"]=="Inserted")
				{
					$msgar = array("message"=>"Account Created Successfully","status"=>"Successfull");
					print(json_encode($msgar,JSON_PRETTY_PRINT));
				}
				else{
					$msgar = array("message"=>"Account Not Created Successfully","status"=>"Unsuccessfull");
					print(json_encode($msgar,JSON_PRETTY_PRINT));
				}
			}
				
			
			/*$msgar = array("message"=>"Account Created Successfully","status"=>"Successfull");
			print(json_encode($msgar,JSON_PRETTY_PRINT));*/
		}

	}
	elseif($mmdata->Account->API_Meth=="CreateAccount")
	{
		
		$accountnumber=$mmdata->Account->Formdata->accountnumber;
		//var_dump($accountnumber);
		$typofaccount = $mmdata->Account->Formdata->accounttype;//PErsonal or Business should be the two values that being sent over
		//if account type is personal set business info to empty
		if($typofaccount=="Personal")
		{
			$orgname="";
			$orgaddress="";
			$city="";
			$orgstate="";
			$orgzip="";
			
		}
		else{
			$orgname=$mmdata->Account->Formdata->orgname;
			$orgaddress=$mmdata->Account->Formdata->companyaddress;
			$city=$mmdata->Account->Formdata->companycity;
			$orgstate=$mmdata->Account->Formdata->companystate;
			$orgzip=$mmdata->Account->Formdata->companyzip;
		}
		
		$phone=$mmdata->Account->Formdata->companyphone;
		$prAddr = $mmdata->Account->Formdata->prAddress; //Personal Address
		$prCity = $mmdata->Account->Formdata->prCity; //Personal City Data
		$prState = $mmdata->Account->Formdata->prState; //Personal State 
		$prZip = $mmdata->Account->Formdata->prZip; //Personal Zipcode
		$accountrole= "Administrator";//$mmdata->Account->Formdata->accountrole;
		$acntContact=$mmdata->Account->Formdata->accountcontact;
		$firstname=$mmdata->Account->Formdata->Firstname; //User First Name
		$lastname=$mmdata->Account->Formdata->Lastname;
		$username=$mmdata->Account->Formdata->username;
		$password= substr($username,0,4).substr($acntContact,0,4).substr($accountnumber,0,3);
		$email=$mmdata->Account->Formdata->email;
		$packageLevel=$mmdata->Account->Formdata->packageLevel;
		$npi="";//do't think we need this actually and can be stransfered to acount_user table 
		$license="";// don't need yet $mmdata->Account->Formdata->license; //might not actually ned this as well
		//billing Address associated with the credit Card 
		$billaddr = $mmdata->Account->Formdata->billAddr;
		$billcity = $mmdata->Account->Formdata->billCity;
		$billstate = $mmdata->Account->Formdata->billState;
		$billzip = $mmdata->Account->Formdata->billZip;
		$userLicense=$mmdata->Account->Formdata->emrUserLicense;
		$licenseState=$mmdata->Account->Formdata->licenseState;
		$licenseStartDate=$mmdata->Account->Formdata->licenseStrtDate;
		$licenseEndDt=$mmdata->Account->Formdata->licenseEndDt;
		$licenseCost=$mmdata->Account->Formdata->licenseCost;
		
		$accounttype="Administrator";// $mmdata->Account->Formdata->accounttype;
		$secreteQuestion="";// dont need yet or didnt' add //$mmdata->Account->Formdata->$secreteQuestion;
		$terms=$mmdata->Account->Formdata->terms;
		//license code goes here 
		$genlicense = $processData->GenerateUserLicense();
		$createAccount="";
		//var_dump($genlicense);
		 //lets check the EMR License table to see if the license has already been created or assigned to avoid duplicate keys
		 $checklicensenum = $processData->CheckGeneratedLicense($genlicense);
		 if($checklicensenum["message"]=="License Does Not Exist")
		{
			//assign license number to this variable to be passed into the create Accountuser function 
			$emrlicense = $genlicense;
			$status="active";
			//now insert the license into the table 
			$insertLicense = $processData->InsertLicense($genlicense,$accountnumber,$uniqueID,$status);
			//var_dump($insertLicense);
			if($insertLicense["message"]=="Inserted")
			{
				$createAccount = $processData->CreateAccount($accountnumber,$orgname,$accountrole,$acntContact,$firstname,$lastname,$username,$password,$email,$prAddr,$prCity,$prState,$prZip,$orgaddress,$city,$orgstate,$orgzip,$phone,$packageLevel,$npi,$license,$userLicense,$licenseState,$licenseStartDate,$licenseEndDt,$licenseCost,$accounttype,$secreteQuestion,$terms);
			}
		 	else{
				$msg =array("message"=>"Problem, With Inserting License, Please Contact Your Administrator");
				return $msg;
			 }
		}
		else{
				$msg = array("message"=>"License Already Assigned","error"=>"Please update users Account Information");
				return $msg;
			}
		 
		// orginal line of code $createAccount = $processData->CreateAccount($accountnumber,$orgname,$accountrole,$acntContact,$firstname,$lastname,$username,$password,$email,$prAddr,$prCity,$prState,$prZip,$orgaddress,$city,$orgstate,$orgzip,$phone,$packageLevel,$npi,$license,$userLicense,$licenseState,$licenseStartDate,$licenseEndDt,$licenseCost,$accounttype,$secreteQuestion,$terms);

		// var_dump($createAccount);
		if($createAccount["status"]=="Inserted")
		{
			//now Create Account 
			//$accountnumber="45000";
			$uniqueID=$mmdata->Account->Formdata->uniqueNineID;//Nine Diget that goes into Front End System
			$accnType="";// not needed $mmdata->Account->accntType; //Manager or Provider
			//$firstname=$mmdata->Account->Formdata->Firstname; //User First Name
			//$lastname=$mmdata->Account->Formdata->Lastname; // User Last Name
			$phone=$mmdata->Account->Formdata->userphone; //Need to Add to the new Form
			$useracntemail=$email;
			$address=$mmdata->Account->Formdata->prAddress; //do we want the user Personal Address ? Or Company Address | Both for Account Setup 
			$city=$mmdata->Account->Formdata->prCity;
			$state=$mmdata->Account->Formdata->prState;
			$zip=$mmdata->Account->Formdata->prZip;
			$proffisionalicense=$mmdata->Account->proflicense; //Assigned for Provider, LPN oR RN | everyone can have their own;
			$accntStatus=$mmdata->Account->Formdata->accountstatus;
			$upremission=$mmdata->Account->Formdata->premLevel;
			$emrlicense=$mmdata->Account->Formdata->emrLicense;
			$ucreationDt=date("Y-m-d");
			$compPreference=$mmdata->Account->Formdata->communicationPreference;
			$createAccountUser = $processData->CreateAccountUser($accountnumber,$uniqueID,$username,$accnType,$firstname,$lastname,$phone,$email,$address,$city,$state,$zip,$proffisionalicense,$accntStatus,$upremission,$emrlicense,$ucreationDt,$compPreference,null,null);
			//var_dump($createAccountUser);
			if($createAccountUser["status"] =="Successfull")
			{
				//Now Lets Setup Billing Code
				//$accountnumber="45000";// should already have this 
				//if the account type is personal then set the company address to empty
				if($typofaccount=="Personal")
				{
				$compname= ""; // Set to empty because its a personal account
				$billaddr=$mmdata->Account->Formdata->prAddress; //
				//var_dump($billaddr);
				$billcity=$mmdata->Account->Formdata->prCity;
				$billstate=$mmdata->Account->Formdata->prState;
				$billzip=$mmdata->Account->Formdata->prZip;
				}
				elseif($typofaccount=="Business")
				{
					$compname= $orgname; // should already have this as well
				$billaddr=$mmdata->Account->Formdata->companyaddress; //
				//var_dump($billaddr);
				$billcity=$mmdata->Account->Formdata->companycity;
				$billstate=$mmdata->Account->Formdata->companystate;
				$billzip=$mmdata->Account->Formdata->companyzip;
				}
				else{
					//Do nothing
				}
				$email="";// don't need I don't believe $mmdata->Account->Formdata->billemail; //or this could be
				$cardholdername=$mmdata->Account->Formdata->cardholdername;
				$ccnum=$mmdata->Account->Formdata->cardnumber;
				$cv3=$mmdata->Account->Formdata->cardcvv3;
				$expDt=$mmdata->Account->Formdata->cardexpDt;
				$pcklevel=$mmdata->Account->Formdata->packageLevel;
				$billfreq =$mmdata->Account->Formdata->billfreq;
				$pckcost ="30000.00";// Needs to be dynamic (function that I can call to get package pricing) $mmdata->Account->Formdata->packageCost;
				$isCurrent=$mmdata->Account->Formdata->isCurrent;
				$createDt=date("Y-m-d");
				$insertBilling = $processData->InsertBillingInfo($accountnumber,$compname,$billaddr,$billcity,$billstate,$billzip,$email,$cardholdername,$ccnum,$cv3,$expDt,$pcklevel,$billfreq,$pckcost,$isCurrent,$createDt);
				//var_dump($insertBilling);
				if($insertBilling["status"]=="Inserted")
				{
					$msg= array("status"=>"200","message"=>"Account Created Successfully");
					print(json_encode($msg,JSON_PRETTY_PRINT));
					return $msg;
				}
				else{
					$msg = array("status"=>"Problem with Billing","message"=>"Problem With Billing","error"=>$insertBilling);
					print(json_encode($msg,JSON_PRETTY_PRINT));
					return $msg;
				}
			}
			else{
				//let the user know that the account user was createed successfully. Mysql Error should be returned
				$msg = array("status"=>"Not Successfull","message"=>"Account User not created Successfully");
				return $msg;
			}
		}
		else{
			//send the note back as to why the Account wasn't created - Hopefully it already exist 
			$msg = array("status"=>"Not Success","message"=>$createAccount);
			return $msg;
		}
	}
	/*elseif($mmdata->Account->API_Meth=="CreateSubAccount")
	{
		//Accounts should fall underneath the Account Now 
		//We need a way to Add SubAccounts for Adminis or Managers to Make 
		$accountnumber="45000";
		$uniqueID="888998890";//Nine Diget that goes into Front End System
		$accnType=$mmdata->Account->accntType; //Manager or Provider
		$firstname=$mmdata->Account->firstname; //User First Name
		$lastname=$mmdata->Account->lastname; // User Last Name
		$phone=$mmdata->Account->userphone; //Need to Add to the new Form
		$useracntemail=$email;
		$address=$mmdata->Account->useraddress; //do we want the user Personal Address ? Or Company Address | Both for Account Setup 
		$city=$mmdata->Account->usercity;
		$state=$mmdata->Account->userstate;
		$zip=$mmdata->Account->zip;
		$proffisionalicense=$mmdata->Account->proflicense; //Assigned for Provider, LPN oR RN | everyone can have their own;
		$accntStatus=$mmdata->Account->accountstatus;
		$upremission=$mmdata->Account->premLevel;
		$emrlicense=$mmdata->Account->emrLicense;
		$ucreationDt=date("Y-m-d");
		$compPreference=$mmdata->Account->communicationPreference;
		$createAccountUser = $processData->CreateAccountUser($accountnumber,$uniqueID,$accnType,$firstname,$lastname,$phone,$email,$address,$city,$state,$zip,$proffisionalicense,$accntStatus,$upremission,$emrlicense,$ucreationDt,$compPreference);
	}*/
	// elseif($postdata["API_Meth"]=="SavePatientNote")
	// {
	// 	$notify = $postdata["notify"];
	// 	$result = $processData->InsertPatientNotes($postdata["patientID"],$postdata["PatientNote"],date('Y-m-d'),$notify,$postdata["email"],$postdata["name"]);
	// 	//var_dump($result);
	// 	if($result=="Inserted")
	// 	{
	// 		$success = array('msg'=>'Inserted','test'=>'test');
	// 		print_r(json_encode($success,JSON_PRETTY_PRINT));
	// 	}
	// 	else{
	// 		$success= array("msg"=>"Error" );
	// 		print($success);
	// 	}
	// }
	elseif(isset($mmdata->ManageNurseList) && $mmdata->ManageNurseList->API_Meth=="InternalAddNurse")
	{
		$ncsbnid = $mmdata->ManageNurseList->ncsid;
		// var_dump($ncsbnid);
		//    $jurisdiction = $mmdata->ManageNurseList->jurisdiction;
		//   $subactioncode = $mmdata->ManageNurseList->subactioncode;//required
			$extern = $mmdata->ManageNurseList->extern;
			$licensnum = $mmdata->ManageNurseList->licensnum;
			 $licensetype = $mmdata->ManageNurseList->ltype;
			$email = $mmdata->ManageNurseList->email;
			$primephone = $mmdata->ManageNurseList->primephone;
			$secphone = $mmdata->ManageNurseList->secondaryphone;
			$addr1 = $mmdata->ManageNurseList->address1;//req
			$addr2 = $mmdata->ManageNurseList->address2;
			$ccity = $mmdata->ManageNurseList->city;//req
			$sstate = $mmdata->ManageNurseList->state;//req
			$zzip = $mmdata->ManageNurseList->zip;//req
			$lastfourssn = $mmdata->ManageNurseList->lastfourssn;//req
		   $bdayear = $mmdata->ManageNurseList->bdayear;//req
			$hospitalpracsetting = $mmdata->ManageNurseList->hospitalpracsetting;//req
			// $hostpracsettingother = $mmdata->ManageNurseList->hostpracsettingother;
			$notficationenabled = $mmdata->ManageNurseList->notficationenabled;//req
			$reminderenabled = $mmdata->ManageNurseList->reminderenabled;//req
			$locationlist = $mmdata->ManageNurseList->locationlist;
			// $recid = $mmdata->ManageNurseList->recid;
			$account = $mmdata->ManageNurseList->accountnumber;
			$firstname = $mmdata->ManageNurseList->firstname;
			$lastname = $mmdata->ManageNurseList->lastname;
			$activeStatus= $mmdata->ManageNurseList->activestatus;
			$username = $mmdata->ManageNurseList->username;
			$checkstatus=date("Y-m-d");
			$licenseExp = $mmdata->ManageNurseList->licenseExp;
			$insuranceCompany = $mmdata->ManageNurseList->insuranceCompany;
			$insuranceStart = $mmdata->ManageNurseList->insuranceStart;
			$insuranceEnd = $mmdata->ManageNurseList->insuranceEnd;
			$perIncidentAmnt = $mmdata->ManageNurseList->perIncidentAmnt;
			$annualAggAmnt = $mmdata->ManageNurseList->annualAggAmnt;
			$getInfo = $processData->InsertNurse($account,$extern,$firstname,$lastname,$username,$licensnum,$licensetype,$ncsbnid,$email,$primephone,$secphone,$addr1,$addr2,$ccity,$sstate,$zzip,$lastfourssn,$bdayear,$hospitalpracsetting,$notficationenabled,$reminderenabled,$locationlist,$activeStatus,$checkstatus,$licenseExp,$insuranceCompany,$insuranceStart,$insuranceEnd,$perIncidentAmnt,$annualAggAmnt);
			print_r(json_encode($getInfo,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->ManageNurseList) && $mmdata->ManageNurseList->API_Meth=="InternalUpdateNurse")
	{
		$ncsbnid = $mmdata->ManageNurseList->ncsid;
		// var_dump($ncsbnid);
		   $jurisdiction = $mmdata->ManageNurseList->jurisdiction;
		  $subactioncode = $mmdata->ManageNurseList->subactioncode;//required
			$licensnum = $mmdata->ManageNurseList->licensnum;
			 $licensetype = $mmdata->ManageNurseList->ltype;
			$email = $mmdata->ManageNurseList->email;
			$primephone = $mmdata->ManageNurseList->primephone;
			$secphone = $mmdata->ManageNurseList->secondaryphone;
			$addr1 = $mmdata->ManageNurseList->address1;//req
			$addr2 = $mmdata->ManageNurseList->address2;
			$ccity = $mmdata->ManageNurseList->city;//req
			$sstate = $mmdata->ManageNurseList->state;//req
			$zzip = $mmdata->ManageNurseList->zip;//req
			$lastfourssn = $mmdata->ManageNurseList->lastfourssn;//req
		   $bdayear = $mmdata->ManageNurseList->bdayear;//req
			$hospitalpracsetting = $mmdata->ManageNurseList->hospitalpracsetting;//req
			$hostpracsettingother = $mmdata->ManageNurseList->hostpracsettingother;
			$notficationenabled = $mmdata->ManageNurseList->notficationenabled;//req
			$reminderenabled = $mmdata->ManageNurseList->reminderenabled;//req
			$locationlist = $mmdata->ManageNurseList->locationlist;
			$recid = $mmdata->ManageNurseList->recid;
			$account = $mmdata->ManageNurseList->accountnumber;
			$firstname = $mmdata->ManageNurseList->firstname;
			$primephone = $mmdata->ManageNurseList->primearyphone;
			$secphone = $mmdata->ManageNurseList->secondaryphone;
			$lastname = $mmdata->ManageNurseList->lastname;
			$activeStatus= $mmdata->ManageNurseList->activestatus;
			$checkstatus=date("Y-m-d");
			$getInfo = $processData->UpdateNurse($account,$firstname,$lastname,$licensnum,$licensetype,$ncsbnid,$email,$primephone,$secphone,$addr1,$addr2,$ccity,$sstate,$zzip,$lastfourssn,$bdayear,$hospitalpracsetting,$notficationenabled,$reminderenabled,$locationlist,$activeStatus,$checkstatus);
			print_r(json_encode($getInfo,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->ManageNurseList) && $mmdata->ManageNurseList->API_Meth=="DeleteNurse")
	{
		//var_dump("here");
		$jurisdiction = $mmdata->ManageNurseList->jurisdiction;
       $subactioncode = $mmdata->ManageNurseList->subactioncode;//required
         $licensnum = $mmdata->ManageNurseList->licensnum;
          $licensetype = $mmdata->ManageNurseList->ltype;
         $email = $mmdata->ManageNurseList->email;
         $addr1 = $mmdata->ManageNurseList->address1;//req
         $addr2 = $mmdata->ManageNurseList->address2;
         $ccity = $mmdata->ManageNurseList->city;//req
		// var_dump($ccity);exit();
         $sstate = $mmdata->ManageNurseList->state;//req
         $zzip = $mmdata->ManageNurseList->zip;//req
         $lastfourssn = $mmdata->ManageNurseList->lastfourssn;//req
        $bdayear = $mmdata->ManageNurseList->bdayear;//req
		$lastfourbday = explode("-",$bdayear);
		   $bylstfour = $lastfourbday[0];
         $hospitalpracsetting = $mmdata->ManageNurseList->hospitalpracsetting;//req
         $hostpracsettingother = $mmdata->ManageNurseList->hostpracsettingother;
         $notficationenabled = $mmdata->ManageNurseList->notficationenabled;//req
         $reminderenabled = $mmdata->ManageNurseList->reminderenabled;//req
         $locationlist = $mmdata->ManageNurseList->locationlist;
         $recid = $mmdata->ManageNurseList->recid;
			$getInfo = $processData->ManageNursysList($subactioncode,$jurisdiction,$licensnum,$licensetype,$ncsbnid,$email,$addr1,$addr2,$ccity,$sstate,$zzip,$lastfourssn,$bylstfour,$hospitalpracsetting,$hostpracsettingother,$notficationenabled,$reminderenabled,$locationlist,$recid);
			print_r(json_encode($getInfo,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->ManageNurseList) && $mmdata->ManageNurseList->API_Meth=="DeleteNurseInternal")
	{
		$licensenum = $mmdata->ManageNurseList->licensnum;
		$accountnum = $mmdata->ManageNurseList->accountnum;
		$removeNurse = $processData->RemoveInternalNurse($accountnum,$licensenum);
		print_r(json_encode($removeNurse,JSON_PRETTY_PRINT));
	}
	elseif($mmdata->ManageNurseList->API_Meth=="SendNurseSettings")
	{
		//lets assign the variaable s
		$checkhowoften = $mmdata->ManageNurseList->checklicenefreq;
        $notifyexpdt = $mmdata->ManageNurseList->notifyexpdt;
        $comtype = $mmdata->ManageNurseList->Communicationtypes;
        $notifytypes = $mmdata->ManageNurseList->notifytypes;
		$accountnumber = $mmdata->ManageNurseList->accountnumber;
		$lastchecked = date('Y-m-d');
		$sendsettings = $processData->SetNurseSettings($accountnumber,$checkhowoften,$notifyexpdt,$comtype,$notifytypes,$lastchecked);
		if($sendsettings=="Inserted")
		{
			$msg = array("success"=>"200","message"=>"Inserted");
			print(json_encode($msg,JSON_PRETTY_PRINT));
		}
		else{
			$msg = array("success"=>"200","message"=>$sendsettings);
			print(json_encode($msg,JSON_PRETTY_PRINT));
		}
	}
  elseif(isset($mmdata->Order) && $mmdata->Order->API_Meth=="GetOrderByAccountOrderID")
	{
		$accountID =  $mmdata->Order->accountnumber;
    $orderid = $mmdata->Order->orderID;
		//var_dump($accountID);
  //  var_dump($orderid);
		$getorderinfo = $processData->GeOrderInfoByAccountNOrderID($accountID,$orderid);
	//	var_dump($getorderinfo);
		//$cardsar = array("html"=>$getorderinfo);
		print(json_encode($getorderinfo,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->API_Meth) && $mmdata->API_Meth=="SavePatientNote")
	{
		$notify = $mmdata->notify;
		$mmdata->startDt = $mmdata->startDt ? $mmdata->startDt : date('Y-m-d');
		$mmdata->endDt = $mmdata->endDt ? $mmdata->endDt : date('Y-m-d');
    $providersignature = $mmdata->provsignature;
    $provsigdt = $mmdata->provsigdt;
		$result = $processData->InsertPatientNotes($mmdata->patientID,$mmdata->PatientNote,$mmdata->pname,date('Y-m-d'),$notify,$mmdata->email,$mmdata->name,$mmdata->startDt,$mmdata->endDt,$mmdata->startTime,$mmdata->endTime,$mmdata->type,$mmdata->timeLength,$providersignature,$provsigdate);
		
		if($result=="Inserted")
		{
			$success = array('msg'=>'Inserted','test'=>'test');
			print_r(json_encode($success,JSON_PRETTY_PRINT));
		}
		else{
			$success= array("msg"=>"Error" );
			print($success);
		}
	}
	elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="UpdateProviderOnTeam")
	{
		$provid = $mmdata->Provider->provid;
		$value = $mmdata->Provider->value;
		$result = $processData->UpdateProviderOnTeam($provid,$value);
		print(json_encode($result, JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="GetProvidersForGroup")
	{
		$accountnum= $mmdata->Provider->provideaccount;
		var_dump($accountnum);
		$getprovidecard = $processData->GetProviderCards($accountnum);
		var_dump($getprovidercard);
		$cardsar = array("html"=>$getprovidecard);
		print(json_encode($cardsar,JSON_PRETTY_PRINT));
	}
	/*elseif($mmdata->Provider->API_Meth=="DeleteProviderGroup")
	{

        $grpname = $mmdata->Provider->groupname; 
        $movegroup = $processData->DeleteGroupbyName($grpname);
        print(json_encode($movegroup,JSON_PRETTY_PRINT));
	}*/
	elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="CreateProviderGroup")
	{
		$npinumbers =$mmdata->Provider->npis;// json_encode($mmdata->Provider->npis,JSON_PRETTY_PRINT);
		$accountnumber =  $mmdata->Provider->accountnumber;
		//var_dump($npinumbers);
		$groupname = $mmdata->Provider->groupname;
		$groupowner=$mmdata->Provider->groupowner; //WHen implemented into the system we should be able to pass this variable versus hard coding it. 
		//$result = explode("/",$npinumbers);
		$emailarray = $mmdata->Provider->emails;
		$grptype = $mmdata->Provider->grouptype;
		$result[] = array();
		foreach($npinumbers as $n)
		{
			$result[] = array("npi"=>$n->npinumber);
		}
		//lets get the contact array information before we pass it to the GetProvideerByNPI Function 
		foreach($emailarray as $er)
		{
			$result2[] = array("email"=>$er->email,"firstname"=>$er->firstName,"lastname"=>$er->lastName,"accountnumber"=>$accountnumber,"phone"=>$er->primaryPhone,
			"groupowner"=>$groupowner,"groupname"=>$groupname);
		}
		 
		$newfilter = array_filter($result);
		$emailfilter = array_filter($result2);
		//var_dump($newfilter);
		$getprovider = $processData->GetProviderbyNPI($newfilter,$groupowner,$groupname,$accountnumber,$emailfilter,$grptype);
		if($getprovider=="Added Successfully")
		{
			//return success payload
			$grpadded = array("status"=>"200","message"=>"Group"." ".$groupname." has been added successfully");
			print(json_encode($grpadded,JSON_PRETTY_PRINT));
		}
		else{

			print(json_encode($getprovider,JSON_PRETTY_PRINT));
		}
		//var_dump($newfilter);
	}
	elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="GetProviderGroup")
	{
		$accountnumber = $mmdata->Provider->accountnumber;
		$groupowner=$mmdata->Provider->groupowner;//"Joseph Mulevhill"; //WHen implemented into the system we should be able to pass this variable versus hard coding it. 
		$grptype = $mmdata->Provider->grouptype;
		$getprovider = $processData->GetGroupByAccountNumber($accountnumber,$groupowner,$grptype);
		//var_dump($getrpovider);
		if($getprovider !="")
		{
			//return success payload
			//$msg = array("success"=>"200","html"=>$getprovider);
			$msg = array("success"=>"200","group"=>$getprovider);
			print(json_encode($msg,JSON_PRETTY_PRINT));
		}
		else{
			$getprovider = array("message"=>"No group found");
			print(json_encode($getprovider,JSON_PRETTY_PRINT));
		}
		//var_dump($newfilter);
	}
	elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="DeleteProviderGroup")
	{
		$accountnum = $mmdata->Provider->accountnumber;
		$groupowner = $mmdata->Provider->groupowner;
		$groupname = $mmdata->Provider->groupname;
		$removegroup  = $processData->RemoveGroupByAcccount($accountnum,$provowner,$groupname);
		if($removegroup !="")
		{
			//return success payload
			//$msg = array("success"=>"200","html"=>$getprovider);
			//$msg = array("success"=>"200","group"=>$getprovider);
			print(json_encode($removegroup,JSON_PRETTY_PRINT));
		}
		else{
			$getprovider = array("message"=>"Group Not Removed Successfully");
			print(json_encode($getprovider,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="SendProviderEmail")
	{
		$toemail = $mmdata->Provider->toemail;
		//var_dump($toemail);exit();
		$subject = $mmdata->Provider->subject;
		$message = $mmdata->Provider->message;
		$provider = $mmdata->Provider->provider;
		$result = $processData->EmailProvider($toemail,$subject,$message,$provider);
		if($result =="EmailSent")
		{
			$msg = array("message"=>$result);
			print(json_encode($msg,JSON_PRETTY_PRINT));
		}
	}
	elseif(isset($mmdata->Twilio) && $mmdata->Twilio->API_Meth=="SendConferenceText")
	{
		//var_dump($mmdata);
		$confnum = $mmdata->Twilio->conferencenumbers;
		$newconfnum =explode(",",$confnum);
		$conferencenums = array_filter($newconfnum);
		$confmsg = $mmdata->Twilio->conferencemessage;
		//We need the Vue app to pass the the provider phone number so it can be dynamic but for now, I'm hard coding it
		$primphysnumber ="317-750-8432";//"917-855-0135";
		//var_dump($conferencenums);
		 $sendconf = $processData->SendConferenceText($conferencenums, $confmsg);
		 //var_dump($sendconf);exit();
		 if($sendconf["TwilioStatus"]=="successfull")
		 {
		 	 $accntmsg ="Pacmny Provider, You're conference Call and Group has been setup, please Join Now by calling 18556730319";
			 $accntprovmsg = $processData->SendProviderSMS($primphysnumber,$accntmsg);
			 print(json_encode($accntprovmsg,JSON_PRETTY_PRINT));
		 }
		 //now that conference text has been sent the provider a text to tell him that the conference has been set and to call in 

	}
	elseif(isset($mmdata->Twilio) && $mmdata->Twilio->API_Meth=="SendPhoneCall")
	{
		
		$tonumber = $mmdata->Twilio->callnumber;
		$provnumber = $mmdata->Twilio->from;
		$sendcall = $processData->SendProviderCall($tonumber,$provnumber);
		print(json_encode($sendcall,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Twilio) && $mmdata->Twilio->API_Meth=="SendText")
	{
		
		$tonum = $mmdata->Twilio->smsnumber;
		$tomsg = $mmdata->Twilio->smsmessage;
		$sendtext = $processData->SendProviderSMS($tonum,$tomsg);
		print(json_encode($sendtext,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="GetProviderInfo")
	{
		$providerid = $mmdata->Provider->providerid;
		$getprovresult = $processData->GetProviderInfo($providerid);
		print(json_encode($getprovresult,JSON_PRETTY_PRINT));
		//var_dump($getprovresult);

	}
  elseif(isset($mmdata->UpdatePatientData) && $mmdata->UpdatePatientData->API_Meth=="Upate_Patients")
  {
    //var_dump($mmdata->UpdatePatientData->accountnumber);
     $accountnumber = $mmdata->UpdatePatientData->accountnumber;
     //($accountnumber);
     $subaccountnumber= $mmdata->UpdatePatientData->personalID;
     $patientid = $mmdata->UpdatePatientData->patientid;
     //var_dump($patientid);exit();
     $firstname = $mmdata->UpdatePatientData->firstname;
     $lastname = $mmdata->UpdatePatientData->lastname;
     $gender = $mmdata->UpdatePatientData->gender;
     $address = $mmdata->UpdatePatientData->address;
     $city = $mmdata->UpdatePatientData->city;
     $state = $mmdata->UpdatePatientData->state;
     $zip = $mmdata->UpdatePatientData->zip;
     $emergcontact = $mmdata->UpdatePatientData->emergencycontact;
     $martialstatus = $mmdata->UpdatePatientData->maritalstatus;
     //var_dump($martialstatus);
     $dob = $mmdata->UpdatePatientData->dob;
     $socnum = $mmdaa->UpdatePatientData->ssnumber;
     $phone = $mmdata->UpdatePatientData->phone;
     //var_dump($phone);
     $email = $mmdata->UpdatePatientData->email;
     //lets update the data in the database and then return a success message 
     $updateDb = $processData->updatePatientData($accountnumber,$patientid,$firstname,$lastname,$gender,$address,
     $city,$state,$zip,$emergcontact,$martialstatus,$dob,$socnum,$phone,$email);
     //var_dump($updateDb);
     if(isset($updateDb) && !empty($updateDb))
     {
      print(json_encode($updateDb,JSON_PRETTY_PRINT));
     }
     else{
       $msg = array("error"=>"Issue with Updating Patient Info","errormsg"=>$updateDb);
       print(json_encode($updateDb,JSON_PRETTY_PRINT));
     }

  }
  elseif(isset($mmdata->Patients) && $mmdata->Patients->API_Meth=="GetProviderAssignedPatient")
  {
     $accountnumber = $mmdata->Patients->accountnumber;
     $subaccountnumber = $mmdata->Patients->provsubaccountnumber;
     $getproflicense = $processData->findProfLicense($accountnumber,$subaccountnumber);
    // var_dump($getproflicense);
     $accounttype = $getproflicense["records"][0]["account_type"];
     $proflicense = $getproflicense["license"];
    // var_dump($proflicense);
     //var_dump($accounttype);
     $patientresults = $processData->GetProvAssignedPatients($accountnumber,$subaccountnumber,$accounttype,$proflicense);
    // var_dump($patientresults);
     print(json_encode($patientresults,JSON_PRETTY_PRINT));
  }
	elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="GetAllProviders")
	{

		$patid = $mmdata->Provider->patientid;
		$provideresults = $processData->GetAllClientProvider($patid);
		print(json_encode($provideresults,JSON_PRETTY_PRINT));
	}
  elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="GetAccountProviders")
	{

		$accntid = $mmdata->Provider->accountnumber;
		$provideresults = $processData->GetAccountProvider($accntid);
		print(json_encode($provideresults,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="GetSingleProvider")
	{

		$patid = $mmdata->Provider->patientid;
		$provid = $mmdata->Provider->providerid;
		$provideresults = $processData->GetSingleClientProvider($patid,$provid);
		print(json_encode($provideresults,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="LookupProvider")
	{

           $emailaddr = $mmdata->Provider->emailaddr;
           //look up providder by email address
           $result = $processData->LookupProviderByEmail($emailaddr);
           print(json_encode($result,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="UpdateProvider")
	{
		$providerid = $mmdata->Provider->providerid;
		$email = $mmdata->Provider->email;
		$pid = $mmdata->Provider->patientid;
		$ordnumber = $mmdata->Provider->ordernumber;
		$firstname = $mmdata->Provider->firstname;
		$lastname = $mmdata->Provider->lastname;
		$address1 = $mmdata->Provider->address1;
		$address2 = $mmdata->Provider->address2;
		$city = $mmdata->Provider->city;
		$state = $mmdata->Provider->state;
		$zip = $mmdata->Provider->zip;
		$tel = $mmdata->Provider->phone;
		$fax = $mmdata->Provider->fax;
		$npinum = $mmdata->Provider->npinumber;
		$status = $mmdata->Provider->status;
		$cell = $mmdata->Provider->cell;
		$updateinfo = $processData->UpdateProviderInfo($providerid,$email,$pid,$ordnumber,$firstname,$lastname,$address1,$address2,$city,$state,$zip,$tel,$fax,$npinum,$status,$cell);
		//var_dump($updateinfo);
		print(json_encode($updateinfo,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="AddProvider")
	{
		//get the rest of the variables 
		$firstname = $mmdata->Provider->provFname;
		$lastname = $mmdata->Provider->provLname;
		$address1 = $mmdata->Provider->provAddr1;
		$address2 = $mmdata->Provider->provAddr2;
		$city     = $mmdata->Provider->provCity;
		$state = $mmdata->Provider->provState;
		$zip = $mmdata->Provider->provZip;
		$tel = $mmdata->Provider->provTel;
		$cell = $mmdata->Provider->provCell;
		$fax = $mmdata->Provider->provFax;
		$email = $mmdata->Provider->provEmail;
		$npinum = $mmdata->Provider->npinumber;
		$pid = $mmdata->Provider->pid;
		$ordnumber = $mmdata->Provider->ordernumber;
		$status= $mmdata->Provider->status;
		$result = $processData->InsertNewProvider($pid,$ordnumber,$firstname,$lastname,$address1,$address2,$city,$state,$zip,$tel,$cell,$fax,$npinum,$status,$email);
		print_r(json_encode($result,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Provider) && $mmdata->Provider->API_Meth=="DeleteProvider")
	{
		$pid = $mmdata->Provider->pid;
		$result = $processData->DeleteProvider($pid);
		print_r(json_encode($result, JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->MedGrid) && $mmdata->MedGrid->API_Meth=="PopulateMedGrid")
	{
		//var_dump($mmdata);
		//var_dump($mmdata->MedGrid->API_Meth);
		$pid = $mmdata->MedGrid->pid;
		$grid = $processData->PopulateMedicationGrid($pid);
		print($grid);
	}
	elseif(isset($mmdata->API_Meth) && $mmdata->API_Meth=="GetPatientMeds"){
		$medArr = array(
			"pid" => $mmdata->pid,
			"accountId" => $mmdata->accountId
		);
		$result = $processData->GetPatientMeds($medArr);
		print_r(json_encode($result));
	}
	elseif(isset($mmdata->API_Meth) && $mmdata->API_Meth=="GetPatientProcs"){
		$procArr = array(
			"pid" => $mmdata->pid,
			"accountId" => $mmdata->accountId
		);
		$result = $processData->GetPatientProcs($procArr);
		print_r(json_encode($result));
	}
	elseif(isset($mmdata->API_Meth) && $mmdata->API_Meth=="UpdatePrepourForActiveMedications"){
		$arr = array(
			"pid" => $mmdata->pid,
			"accountId" => $mmdata->accountId
		);
		$result = $processData->UpdatePrepourForActiveMedications($arr);
		print_r(json_encode($result));
	}
	elseif(isset($mmdata->API_Meth) && $mmdata->API_Meth=="GetPrePourMeds"){
		$arr = array(
			"pid" => $mmdata->pid,
			"accountId" => $mmdata->accountId
		);
		$result = $processData->GetPrePourMeds($arr);
		print_r(json_encode($result));
	}
	elseif(isset($mmdata->MedGrid) && $mmdata->MedGrid->API_Meth=="PopulateMedGridByOrderID")
	{
		//var_dump($mmdata);
		//var_dump($mmdata->MedGrid->API_Meth);
		$pid = $mmdata->MedGrid->pid;
		$ordnumber = $mmdata->MedGrid->ordernumber;
		$grid = $processData->PopulateMedicationGridByOID($pid,$ordnumber);
		print($grid);
	}
	elseif(isset($mmdata->MedicationEdit) && $mmdata->MedicationEdit->API_Meth=="EditMedication")
	{
		$medentryid = $mmdata->MedicationEdit->medentryid;
		$meddata = $processData->GetPatientMedicationByID($medentryid);
		//put into json format for Front-End
		print($meddata);
	}
	elseif(isset($mmdata->GetCurrentMed) && $mmdata->GetCurrentMed->API_Meth=="ViewCurrentMedication")
	{
		$patientID = $mmdata->GetCurrentMed->patientid;
		$griddata = $processData->CurrentMedicationGrid($patientID);
		print($griddata);
	}
	elseif(isset($mmdata->GetCurrentMed) && $mmdata->GetCurrentMed->API_Meth=="ViewAllMedication")
	{
		$patientID = $mmdata->GetCurrentMed->patientid;
		$griddata = $processData->PopulateMedicationGrid($patientID);
		print($griddata);
	}
	elseif(isset($mmdata->Diagnosis) && $mmdata->Diagnosis->API_Meth=="PopulateDiagGrid")
	{
		$patientID = $mmdata->Diagnosis->patientID;
		$griddata = $processData->GetDiagnosisGrid($patientID);
		print($griddata);
	}
	elseif(isset($mmdata->DiagGrid) && $mmdata->DiagGrid->API_Meth=="PopulateDiagGridByOrderID")
	{
		//var_dump($mmdata);
		//var_dump($mmdata->MedGrid->API_Meth);
		$pid = $mmdata->DiagGrid->pid;
		$ordnumber = $mmdata->DiagGrid->ordernumber;
		$grid = $processData->PopulateDiagnosisGridByOID($pid,$ordnumber);
		print($grid);
	}
	elseif(isset($mmdata->Diagnosis) && $mmdata->Diagnosis->API_Meth=="EditDiagnosis")
	{
		$patientID = $mmdata->Diagnosis->patientID;
		$diagID = $mmdata->Diagnosis->diagID;
		$griddata = $processData->EditDiagnosisByPatientAndDiagID($diagID,$patientID);
		print($griddata);
	}
	elseif(isset($mmdata->MedicationEdit) && $mmdata->MedicationEdit->API_Meth=="UpdateMedication")
	{
		
		$patientID = $mmdata->MedicationEdit->patientID;
		$medentryid = $mmdata->MedicationEdit->medentryid;
		
		$medordnumber = $mmdata->MedicationEdit->ordernumber;
		
		$medmedname = $mmdata->MedicationEdit->medname;
		$meddoseamount = $mmdata->MedicationEdit->doseamount;
		$meddoseUOM = $mmdata->MedicationEdit->doseUOM;
		$medfrequency = $mmdata->MedicationEdit->frequency;
		$medPrn = $mmdata->MedicationEdit->prn;
		$medReason = $mmdata->MedicationEdit->diagcode;
		$medRoute = $mmdata->MedicationEdit->medRoute;
		$medAltroute = $mmdata->MedicationEdit->altroute;
		$medMedinstruction = $mmdata->MedicationEdit->instruction;
		$medMedstDate = $mmdata->MedicationEdit->medstrtdt;
		$medMedendDate = $mmdata->MedicationEdit->medenddt;
		$medchange_new = $mmdata->MedicationEdit->changetype;
		$medCategory = $mmdata->MedicationEdit->durgclassification;
		$medStatus = $mmdata->MedicationEdit->medstatus;
		$medAdditionalSetting = $mmdata->MedicationEdit->additionalsettings;
		$meDirectionUse = $mmdata->MedicationEdit->directionuse;
		
		//now lets pass to process class for processing
		$medAr = array("patientID"=>$patientID,"ordernumber"=>$medordnumber,"medname"=>$medmedname,"doseamount"=>$meddoseamount,"doseUOM"=>$meddoseUOM,"frequency"=>$medfrequency,
		"prn"=>$medPrn,"diagnosisCode"=>$medReason,"route"=>$medRoute,"altroute"=>$medAltroute,"instruction"=>$medMedinstruction,
		"medStartDate"=>$medMedstDate,"medEndDate"=>$medMedendDate,"changetype"=>$medchange_new,"drugCategory"=>$medCategory,
		"additionalsettings"=>$medAdditionalSetting,"directionuse"=>$meDirectionUse,"status"=>$medStatus);
		$update = $processData->UpdatePatientMedInfoByIDs($medentryid,$patientID,$medAr);
		print($update);
	}
	elseif(isset($mmdata->API_Meth) && $mmdata->API_Meth=="InsertNewOrder"){
		$orderArr = array(
			"pid" => $mmdata->pid,
			"accountId" => $mmdata->accountId,
			"abn" => $mmdata->abnDelivery,
			"address" => $mmdata->address,
			"creatorId" => $mmdata->creatorId,
			"creatorName" => $mmdata->creatorName,
			"description" => $mmdata->description,
			"email" => $mmdata->email,
			"fax" => $mmdata->fax,
			"npi" => $mmdata->npi,
			"orderDate" => $mmdata->orderDate,
			"orderTime" => $mmdata->orderTime,
			"orderType" => $mmdata->orderType,
			"phone" => $mmdata->phone,
			"primaryPhysician" => $mmdata->primaryPhysician,
			"primaryTaxonomy" => $mmdata->primaryTaxonomy,
			"readOrderBack" => $mmdata->readOrderBack,
			"status" => $mmdata->status,
			"newDiags" => json_decode($mmdata->newDiags, true),
			"newMeds" => json_decode($mmdata->newMeds, true),
			"newProcs" => json_decode($mmdata->newProcs, true),
			"nurseSignature" =>$mmdata->nurseSignature,
			"nurseSigDate" => $mmdata->nurseSigDate,
			"provSignature" => $mmdata->provSignature,
			"provSigDate" => $mmdata->provSigDate
		);
		$result = $processData->InsertNewOrder($orderArr);
		print($result);
	}
	elseif(isset($mmdata->API_Meth) && $mmdata->API_Meth=="AddCurrentMed"){
		$medArr = array(
			"pid" => $mmdata->pid,
			"accountId" => $mmdata->accountId,
			"abn" => $mmdata->abnDelivery,
			"medName" => $mmdata->medName,
			"medShorthand" => $mmdata->medShorthand,
			"quantity" => $mmdata->quantity,
			"frequency" => $mmdata->frequency,
			"duration" => $mmdata->duration,
			"prn" => $mmdata->prn,
			"route" => $mmdata->route,
			"altRoute" => $mmdata->altRoute,
			"instructions" => $mmdata->instructions,
			"startDate" => $mmdata->startDate,
			"status" => $mmdata->status,
			"assignerName" => $mmdata->assignerName,
			"assignerId" => $mmdata->assignerId,
			"diagCode" => $mmdata->diagCode,
			"diagDescription" => $mmdata->diagDescription,
			"prepour" => $mmdata->prepour,
			"administration" => $mmdata->administration,
			"nebulizer" => $mmdata->nebulizer
		);
		$result = $processData->AddCurrentMed($medArr);
		print($result);
	}
	elseif(isset($mmdata->API_Meth) && $mmdata->API_Meth=="GetOrders"){
		$orderArr = array(
			"pid" => $mmdata->pid,
			"accountId" => $mmdata->accountId,
		);
		$result = $processData->GetOrders($orderArr);
		print($result);
	}
	elseif(isset($mmdata->OrderTemp) && $mmdata->OrderTemp->API_Meth=="InsertOrder")
	{
		//lets grad the orderTemp Object
		//var_dump($mmdata->OrderTemp);
    $accntnumber = $mmdata->OrderTemp->accountnumber;
		$patientID = $mmdata->OrderTemp->patientID;
		$ordDate = $mmdata->OrderTemp->orderdate;
  		$ordTime = $mmdata->OrderTemp->ordertime;
  		$ordType = $mmdata->OrderTemp->ordertype;
		$abnDelivery = $mmdata->OrderTemp->abndelivery;
		$ordReadback = $mmdata->OrderTemp->ordReadback;
		$ordPrimaryPhysician = $mmdata->OrderTemp->primphysician;
    $secPhysicians = $mmdata->OrderTemp->secphysician;
  		$email = $mmdata->OrderTemp->email;
		$npinumber = $mmdata->OrderTemp->npinumber;
		$ordAddress = $mmdata->OrderTemp->address;
		$ordPhone = $mmdata->OrderTemp->phone; 
		$ordFax = $mmdata->OrderTemp->fax;
		$ordsendtophyscians = $mmdata->OrderTemp->sendtophysicans;
		$ordwoundcare = $mmdata->OrderTemp->woundcare;
		$ordverboffer = $mmdata->OrderTemp->verbalorder;
		$ordverbOrdstrtDt = $mmdata->OrderTemp->verbalStartdt;
		$ordverbOrdtime = $mmdata->OrderTemp->verbalEnddt;
		$ordhasmed = $mmdata->OrderTemp->hasmed;
		$ordhasdiag = $mmdata->OrderTemp->hasdiag;
		$ordSupplies="false";
		$ordValue = "false";
		$ordDescrip = $mmdata->OrderTemp->description;
		$ordStatus = $mmdata->OrderTemp->status;
		$orderNumber = $mmdata->OrderTemp->ordernumber;
		$writer = $mmdata->OrderTemp->writer;
    $nurseSigname = $mmdata->OrderTemp->nurseSignature;
    $nurseSigdt = $mmdata->OrderTemp->nurseSigDate;
    $provSignature = $mmdata->OrderTemp->provSignature;
    if($mmdata->OrderTemp->provsigDate=="" && $mmdata->OrderTemp->provSignature =="")
    {
      $provsigDate=null;
    }
    elseif($mmdata->OderTemp->provsigDate =="" && $mmdata->OrderTemp->provSignature !="")
    {
      //cant have a signature with a blank Signature date so lets set it to todays date 
      $provsigDate = date("Y-m-d");
    }
    else{
      $provsigDate = $mmdata->OrderTemp->provSigDate;
    }
        
   

		/*----*/
		$ordar = array("accountnumber"=>$accntnumber,"ordDate"=>$ordDate,"ordTime"=>$ordTime,"ordtype"=>$ordType,"abndeliv"=>$abnDelivery,"readback"=>$ordReadback,
		"primephysician"=>$ordPrimaryPhysician,"secphysician"=>$secPhysicians,"email"=>$email,"npi"=>$npinumber,
		"address"=>$ordAddress,"phone"=>$ordPhone,"fax"=>$ordFax,"sendtophysician"=>$ordsendtophyscians,"woundcare"=>$ordwoundcare,
		"verbaloffer"=>$ordverboffer,"verbalOrderDt"=>$ordverbOrdstrtDt,"verbalOrderTime"=>$ordverbOrdtime,"hasmed"=>$ordhasmed,
		"hasdiag"=>$ordhasdiag,"hassupplies"=>$ordSupplies,"hasValueSign"=>$ordValue,"description"=>$ordDescrip,"status"=>$ordStatus,"ordernumber"=>$orderNumber,"writer"=>$writer,
  "nursesigname"=>$nurseSigname,"nursesigdate"=>$nurseSigdt,"providersignature"=>$provSignature,"provsigdate"=>$provsigDate);
		/*--end ordar--*/
		
		/*-----*/
		/*$ordmedname = $mmdata->OrderTemp->medname;
		$doseamount = $mmdata->OrderTemp->doseamount;
		$doseUOM = $mmdata->OrderTemp->doesUOM;
		$ordfrequency = $mmdata->OrderTemp->frequency;
		$ordPrn = $mmdata->OrderTemp->prn;
		$ordReason = $mmdata->OrderTemp->reason;
		$ordRoute = $mmdata->OrderTemp->route;
		$ordAltroute = $mmdata->OrderTemp->alroute;
		$ordMedinstruction = $mmdata->OrderTemp->medinstruction;
		$ordMedstDate = $mmdata->OrderTemp->medStartDate;
		$ordMedendDate = $mmdata->OrderTemp->medEndDate;
		$ordchange_new = $mmdata->OrderTemp->neworchange;
		$ordCategory = $mmdata->OrderTemp->category;
		$ordMedUnderstanding = $mmdata->OrderTemp->medunderstanding;
		$ordAdditionalSetting = $mmdata->OrderTemp->additionalSettings;*/
		/*-----*/
		
		/*$medar = array("ordernumber"=>$ordernumber,"patientid"=>$patientID,"medicationname"=>$ordmedname,"dignosiscode"=>$ordReason,"medamount"=>$doseamount,"doseUOM"=>$doseUOM,
		"frequency"=>$ordfrequency,"prn"=>$ordPrn,"route"=>$ordRoute,"altroute"=>$ordAltroute,"instruction"=>$ordMedinstruction,"medstartdt"=>$ordMedstDate,
		"medenddt"=>$ordMedendDate,"changetype"=>$ordchange_new,"drugclassification"=>$ordCategory,"medunderstanding"=>$ordMedUnderstanding,"additionalsettings"=>$ordAdditionalSetting);*/
		/*--end med settings---*/
		/*$ordDiagnosis  = $mmdata->OrderTemp->diagnosis;
		$ordProcedure = $mmdata->OrderTemp->procedure;
		$ordDiagCode = $mmdata->OrderTemp->diagIC10;
		$ordDiagDate = $mmdata->OrderTemp->diagnosisDate;
		$ordControlsystem = $mmdata->OrderTemp->diagControlRating;
		$ordOnset = $mmdata->OrderTemp->Onsetexacerbation;
		$ordExacerbation = $mmdata->OrderTemp->Exacerbationonset;
		$onsetexacerbate="";
		if($ordDiagnosis =="true" && $ordProcedure=="false")
		{
			$diagtype="Diagnosis";
		}
		else{
			if($ordDiagnosis=="false" && $ordProcedure=="true")
			{
				$diagtype="Procedure";
			}
		}
		if($ordOnset=="true" && $ordExacerbation=="false")
		{
			$onsetexacerbate ="onset";
		}
		else{

			if($ordOnset=="" && $ordExacerbation=="true")
			{
				$onsetexacerbate="exacerbation";
			}
		}*/
		/*------*/
		
		/*$diagar = array("hasdiagnosis"=>$ordhasdiag,"diagtype"=>$diagtype,"icd10"=>$ordDiagCode,"onsetexacerbation"=>$onsetexacerbate,"diagdate"=>$ordDiagDate,"syscontrolRating"=>$ordControlsystem);*/
		/*---end disagarrray---*/
		
		//var_dump($patientID);exit();
		$result = $processData->InsertOrderTemplate($patientID,$ordar);
    //var_dump($result);exit();
		print($result);
		
		
		
	}
  //Update Orders
  elseif(isset($mmdata->OrderTemp) && $mmdata->OrderTemp->API_Meth=="UpdateOrder")
	{
		//lets grad the orderTemp Object
		//var_dump($mmdata->OrderTemp);
    $accntnumber = $mmdata->OrderTemp->accountnumber;
		$patientID = $mmdata->OrderTemp->patientID;
		$ordDate = $mmdata->OrderTemp->orderdate;
  		$ordTime = $mmdata->OrderTemp->ordertime;
  		$ordType = $mmdata->OrderTemp->ordertype;
		$abnDelivery = $mmdata->OrderTemp->abndelivery;
		$ordReadback = $mmdata->OrderTemp->ordReadback;
		$ordPrimaryPhysician = $mmdata->OrderTemp->primphysician;
    $secPhysicians = $mmdata->OrderTemp->secphysician;
  		$email = $mmdata->OrderTemp->email;
		$npinumber = $mmdata->OrderTemp->npinumber;
		$ordAddress = $mmdata->OrderTemp->address;
		$ordPhone = $mmdata->OrderTemp->phone; 
		$ordFax = $mmdata->OrderTemp->fax;
		$ordsendtophyscians = $mmdata->OrderTemp->sendtophysicans;
		$ordwoundcare = $mmdata->OrderTemp->woundcare;
		$ordverboffer = $mmdata->OrderTemp->verbalorder;
		$ordverbOrdstrtDt = $mmdata->OrderTemp->verbalStartdt;
		$ordverbOrdtime = $mmdata->OrderTemp->verbalEnddt;
		$ordhasmed = $mmdata->OrderTemp->hasmed;
		$ordhasdiag = $mmdata->OrderTemp->hasdiag;
		$ordSupplies="false";
		$ordValue = "false";
		$ordDescrip = $mmdata->OrderTemp->description;
		$ordStatus = $mmdata->OrderTemp->status;
		$orderNumber = $mmdata->OrderTemp->ordernumber;
		$writer = $mmdata->OrderTemp->writer;
    $nurseSigname = $mmdata->OrderTemp->nurseSignature;
    $nurseSigdt = $mmdata->OrderTemp->nurseSigDate;
    $provSignature = $mmdata->OrderTemp->provSignature;
    if($mmdata->OrderTemp->provsigDate=="" && $mmdata->OrderTemp->provSignature =="")
    {
      $provsigDate=null;
    }
    elseif($mmdata->OderTemp->provsigDate =="" && $mmdata->OrderTemp->provSignature !="")
    {
      //cant have a signature with a blank Signature date so lets set it to todays date 
      $provsigDate = date("Y-m-d");
    }
    else{
      $provsigDate = $mmdata->OrderTemp->provSigDate;
    }
        
   

		/*----*/
		$ordar = array("accountnumber"=>$accntnumber,"ordDate"=>$ordDate,"ordTime"=>$ordTime,"ordtype"=>$ordType,"abndeliv"=>$abnDelivery,"readback"=>$ordReadback,
		"primephysician"=>$ordPrimaryPhysician,"secphysician"=>$secPhysicians,"email"=>$email,"npi"=>$npinumber,
		"address"=>$ordAddress,"phone"=>$ordPhone,"fax"=>$ordFax,"sendtophysician"=>$ordsendtophyscians,"woundcare"=>$ordwoundcare,
		"verbaloffer"=>$ordverboffer,"verbalOrderDt"=>$ordverbOrdstrtDt,"verbalOrderTime"=>$ordverbOrdtime,"hasmed"=>$ordhasmed,
		"hasdiag"=>$ordhasdiag,"hassupplies"=>$ordSupplies,"hasValueSign"=>$ordValue,"description"=>$ordDescrip,"status"=>$ordStatus,"ordernumber"=>$orderNumber,"writer"=>$writer,
  "nursesigname"=>$nurseSigname,"nursesigdate"=>$nurseSigdt,"providersignature"=>$provSignature,"provsigdate"=>$provsigDate);
		/*--end ordar--*/
		
		/*-----*/
		/*$ordmedname = $mmdata->OrderTemp->medname;
		$doseamount = $mmdata->OrderTemp->doseamount;
		$doseUOM = $mmdata->OrderTemp->doesUOM;
		$ordfrequency = $mmdata->OrderTemp->frequency;
		$ordPrn = $mmdata->OrderTemp->prn;
		$ordReason = $mmdata->OrderTemp->reason;
		$ordRoute = $mmdata->OrderTemp->route;
		$ordAltroute = $mmdata->OrderTemp->alroute;
		$ordMedinstruction = $mmdata->OrderTemp->medinstruction;
		$ordMedstDate = $mmdata->OrderTemp->medStartDate;
		$ordMedendDate = $mmdata->OrderTemp->medEndDate;
		$ordchange_new = $mmdata->OrderTemp->neworchange;
		$ordCategory = $mmdata->OrderTemp->category;
		$ordMedUnderstanding = $mmdata->OrderTemp->medunderstanding;
		$ordAdditionalSetting = $mmdata->OrderTemp->additionalSettings;*/
		/*-----*/
		
		/*$medar = array("ordernumber"=>$ordernumber,"patientid"=>$patientID,"medicationname"=>$ordmedname,"dignosiscode"=>$ordReason,"medamount"=>$doseamount,"doseUOM"=>$doseUOM,
		"frequency"=>$ordfrequency,"prn"=>$ordPrn,"route"=>$ordRoute,"altroute"=>$ordAltroute,"instruction"=>$ordMedinstruction,"medstartdt"=>$ordMedstDate,
		"medenddt"=>$ordMedendDate,"changetype"=>$ordchange_new,"drugclassification"=>$ordCategory,"medunderstanding"=>$ordMedUnderstanding,"additionalsettings"=>$ordAdditionalSetting);*/
		/*--end med settings---*/
		/*$ordDiagnosis  = $mmdata->OrderTemp->diagnosis;
		$ordProcedure = $mmdata->OrderTemp->procedure;
		$ordDiagCode = $mmdata->OrderTemp->diagIC10;
		$ordDiagDate = $mmdata->OrderTemp->diagnosisDate;
		$ordControlsystem = $mmdata->OrderTemp->diagControlRating;
		$ordOnset = $mmdata->OrderTemp->Onsetexacerbation;
		$ordExacerbation = $mmdata->OrderTemp->Exacerbationonset;
		$onsetexacerbate="";
		if($ordDiagnosis =="true" && $ordProcedure=="false")
		{
			$diagtype="Diagnosis";
		}
		else{
			if($ordDiagnosis=="false" && $ordProcedure=="true")
			{
				$diagtype="Procedure";
			}
		}
		if($ordOnset=="true" && $ordExacerbation=="false")
		{
			$onsetexacerbate ="onset";
		}
		else{

			if($ordOnset=="" && $ordExacerbation=="true")
			{
				$onsetexacerbate="exacerbation";
			}
		}*/
		/*------*/
		
		/*$diagar = array("hasdiagnosis"=>$ordhasdiag,"diagtype"=>$diagtype,"icd10"=>$ordDiagCode,"onsetexacerbation"=>$onsetexacerbate,"diagdate"=>$ordDiagDate,"syscontrolRating"=>$ordControlsystem);*/
		/*---end disagarrray---*/
		
		//var_dump($patientID);exit();
		$result = $processData->updateOrderTemplate($patientID,$ordar);
    //var_dump($result);exit();
		print($result);
		
		
		
	}
  //end Update Orders
  elseif(isset($mmdata->Order) && $mmdata->Order->API_Meth=="GetAccountPendingOrders")
  {
    $accnt = $mmdata->Order->accountnumber;
    $ordernum = $processData->GetAccountPendingOrders($accnt);
    if(is_array($ordernum) && !empty($ordernum))
    {
      print(json_encode($ordernum,JSON_PRETTY_PRINT));
    }
  }
  elseif(isset($mmdata->OrderFilter) && $mmdata->OrderFilter->API_Meth=="OrderLookUp")
  {
    
    $ordertype = $mmdata->OrderFilter->ordtype;
    $accountType = $mmdata->OrderFilter->emraccounttype;//$mmdata->OrderFilter->accounttype;
    $dtRange = $mmdata->OrderFilter->srchdtrange;
    $srchpatientname = $mmdata->OrderFilter->patientname;
    $orderstatus = $mmdata->OrderFilter->orderstatus;
    $proflicense = $mmdata->OrderFilter->proflicense;
    $accountnumber = $mmdata->OrderFilter->accountnumber;
    //var_dump($accountnumber);
   /* var_dump($ordertype);
    var_dump($accountType);
    var_dump($orderstatus);
    var_dump($srchpatientname);
    var_dump($dtRange); */
    $getFilter = $processData->OrderFilter($ordertype,$orderstatus,$accountType,$dtRange,$srchpatientname,$proflicense,$accountnumber);
   // var_dump($getFilter);//exit();
    if(is_array($getFilter) && $getFilter !="")
    {
     
      print(json_encode($getFilter,JSON_PRETTY_PRINT));
    }

  }
  elseif(isset($mmdata->Order) && $mmdata->Order->API_Meth=="GetAccountPendingOrdersByID")
  {
    $accnt = $mmdata->Order->accountnumber;
    $ordernum =$mmdata->Order->orderID;
    $patientid = $mmdata->Order->patientid;
    $getdata="";
   // var_dump($patientid);
    //$getdata = $processData->GetAccountPendingOrdersByID($accnt,$ordernum,$patientid);
    if(isset($mmdata->Order->orderID) && $ordernum !='')
    {
       $getdata = $processData->GetAccountPendingOrdersByID($accnt,$ordernum,$patientid);
    }
    if(isset($mmdata->Order->patientid) && !isset($mmdata->Order->orderID))
    {
      $getdata = $processData->GetAllPendingOrdersByPatientID($accnt,$patientid);
    }
    
    if(is_array($getdata) && !empty($getdata))
    {
     // var_dump($getdata);
      print(json_encode($getdata,JSON_PRETTY_PRINT));
    }
  }
	elseif(isset($mmdata->Order) && $mmdata->Order->API_Meth=="ClearPending")
	{
		$ordnum = $mmdata->Order->ordnum;
		$status = $mmdata->Order->status;
		$result = $processData->DeleteBothMedDiagbyORDID($ordnum, $status);
		print(json_encode($result));
	}
	elseif(isset($mmdata->systemData) && $mmdata->systemData->API_Meth=="GetOrderNumber")
	{
		//var_dump("This is where we are");
		//var_dump($mmdata);
		$getNum = $processData->GetGlobalOrderNumber();
		print(json_encode($getNum));
	}
	elseif(isset($mmdata->Order) && $mmdata->Order->API_Meth=="PopulateOrderGrid")
	{
		$patientId = $mmdata->Order->patientId;
		$oresult = $processData->GetOrderGrid($patientId);
		print($oresult);
	}
	elseif(isset($mmdata->MedicationInsert) && $mmdata->MedicationInsert->API_Meth=="InsertMedication")
	{
		
		$orderum = $mmdata->MedicationInsert->ordernumber;
		//var_dump($orderum);
		$patientid =$mmdata->MedicationInsert->patientID;
		$medname = $mmdata->MedicationInsert->medname;
		$shorthand = $mmdata->MedicationInsert->shorthand;
		$diagcode = $mmdata->MedicationInsert->diagcode;		
		$medamount = $mmdata->MedicationInsert->doseamount ;
		$meddoseuom = $mmdata->MedicationInsert->doseUOM ;
		$medfreq = $mmdata->MedicationInsert->frequency ;
		$prn = $mmdata->MedicationInsert->prn ;
		$route = $mmdata->MedicationInsert->medRoute ;
		$altroute = $mmdata->MedicationInsert->altroute;
		$instruction = $mmdata->MedicationInsert->instruction ;
		$medstartdt = $mmdata->MedicationInsert->medstrtdt ;
		$medenddt = $mmdata->MedicationInsert->medenddt; 
		$medchangetype = $mmdata->MedicationInsert->changetype ;
		$drugclass  = $mmdata->MedicationInsert->durgclassification ;
		
		$medunderstanding = $mmdata->MedicationInsert->directionuse;
		$addsettings = $mmdata->MedicationInsert->additionalsettings ;
		$status = $mmdata->MedicationInsert->status;
		$writer = $mmdata->MedicationInsert->writer;
		$mresult = $processData->InsertOrderMedication($orderum,$patientid,$medname,$shorthand,$diagcode,$medamount,$meddoseuom,$medfreq,$prn,$route,$altroute,$instruction,$medstartdt,$medenddt,$medchangetype,$drugclass,$medunderstanding,$addsettings,$status,$writer);
		print(json_encode($mresult));
	}
	elseif(isset($mmdata->MedicationModifer) && $mmdata->MedicationModifer->API_Meth=="Transaction_ChangeMedicationvariables")
	{
		$patientid =$mmdata->MedicationModifer->patientID;
		$ordernum = $mmdata->MedicationModifer->ordernumber;
		$medname = $mmdata->MedicationModifer->medname;
		$shorthand = $mmdata->MedicationModifer->shorthand;
		$diagcode = $mmdata->MedicationModifer->diagcode;		
		$medamount = $mmdata->MedicationModifer->doseamount;
		$meddoseuom = $mmdata->MedicationModifer->doseUOM;
		$medfreq = $mmdata->MedicationModifer->frequency;
		$prn = $mmdata->MedicationModifer->prn;
		$route = $mmdata->MedicationModifer->medRoute ;
		$altroute = $mmdata->MedicationModifer->altroute;
		$instruction = $mmdata->MedicationModifer->instruction;
		$medstartdt = $mmdata->MedicationModifer->medstrtdt;
		$medenddt = $mmdata->MedicationModifer->medenddt; 
		$medchangetype = $mmdata->MedicationModifer->changetype;
		$drugclass  = $mmdata->MedicationModifer->durgclassification;
		$medunderstanding = $mmdata->MedicationModifer->directionuse;
		$addsettings = $mmdata->MedicationModifer->additionalsettings;
		$status = $mmdata->MedicationModifer->status;
		$mresult = $processData->InsertOrderMedication($ordernum,$patientid,$medname,$shorthand,$diagcode,$medamount,$meddoseuom,$medfreq,$prn,$route,$altroute,$instruction,$medstartdt,$medenddt,$medchangetype,$drugclass,$medunderstanding,$addsettings,$status);
		print(json_encode($mresult));
	}
	elseif(isset($mmdata->MedicationModifer) && $mmdata->MedicationModifer->API_Meth=="Transaction_DCMedication")
	{
		$patientid =$mmdata->MedicationModifer->patientID;
		$medid = $mmdata->MedicationModifer->medid;
		$ordnum = $mmdata->MedicationModifer->ordernumber;
		$dcdate = $mmdata->MedicationModifer->dcdate;
		$mresult = $processData->DiscontinueMed($patientid,$medid,$ordnum,$dcdate);
		print(json_encode($mresult,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->MedicationModifer) && $mmdata->MedicationModifer->API_Meth=="UpdatePastMedInfo_PullNew")
	{
		$patientid =$mmdata->MedicationModifer->patientID;
		$ordernum = $mmdata->MedicationModifer->ordernumber;
		$enddate = $mmdata->MedicationModifer->enddate;
		$medentry = $mmdata->MedicationModifer->medentry;
		$transactiontype = "Change Med Dose";
		//var_dump($patientid. " ".$ordernum ." ".$enddate ." ".$medentry." ".$transactiontype);
		$updatEnddate = $processData->UpdateMedDoseEndDate($patientid,$ordernum,$enddate,$medentry,$transactiontype);
		print(json_encode($updatEnddate));
	}
	elseif(isset($mmdata->Diagnosis) && $mmdata->Diagnosis->API_Meth=="UpdateDiagnosis")
	{
		//var_dump($mmdata->Diagnosis);
		$pid = $mmdata->Diagnosis->patientID;
		$diagId = $mmdata->Diagnosis->diagID;
		$diagtype = $mmdata->Diagnosis->diagnosis_type;
		$diagcode = $mmdata->Diagnosis->icd10code; 
		$proccode = $mmdata->Diagnosis->procedurecode;
		$onset = $mmdata->Diagnosis->onset_exacerbation;
		$diagDate = $mmdata->Diagnosis->diagnosisDate;
		$controlrate = $mmdata->Diagnosis->diagControlRating;
		$ordnum = $mmdata->Diagnosis->ordernumber;
		$diagresult = $processData->UpdateDiagnosis($pid,$ordnum,$diagId,$diagtype,$diagcode,$proccode,$onset,$diagDate,$controlrate);
		
		print($diagresult);
	}
	elseif(isset($mmdata->Diagnosis) && $mmdata->Diagnosis->API_Meth=="InsertDiagnosis")
	{
		//var_dump($mmdata);
		$ordernum = $mmdata->Diagnosis->ordernumber;
		$diagcode = $mmdata->Diagnosis->diagcode;
		$controlrating = $mmdata->Diagnosis->diagControlRating;
		$diagtype = $mmdata->Diagnosis->diagnosis_type;
		$onset = $mmdata->Diagnosis->onset_exacerbation;
		$proccode = $mmdata->Diagnosis->proccode;
		$procShorthand = $mmdata->Diagnosis->procShorthand;
		$pid = $mmdata->Diagnosis->pid; //"45064367";
		$diagDate = $mmdata->Diagnosis->diagnosisDate;
		$endDate = $mmdata->Diagnosis->endDate;
		$status = $mmdata->Diagnosis->status;
		$instruction = $mmdata->Diagnosis->instruction;
		$writer = $mmdata->Diagnosis->writer;
		$diagresult = $processData->InsertDiagnosis($pid,$ordernum,$diagtype,$diagcode,$proccode,$procShorthand,$controlrating,$onset,$diagDate,$endDate,$status,$instruction,$writer);
		print($diagresult);

	}
	elseif(isset($mmdata->MedicationUpdate) && $mmdata->MedicationUpdate->API_Meth=="DeleteMedication")
	{
		//var_dump($mmdata);
		$ordernum = $mmdata->MedicationUpdate->ordernumber;
		$medentryid = $mmdata->MedicationUpdate->medentryid;
		$patientid = $mmdata->MedicationUpdate->patientid;
		//lets call the process class function that associated with this API_Method
		$dresult = $processData->DeleteMedication($patientid,$medentryid);
		print(json_encode($dresult,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Diagnosis) && $mmdata->Diagnosis->API_Meth=="DeleteDiagnosis")
	{
		$diagId = $mmdata->Diagnosis->diagID;
		$patientid = $mmdata->Diagnosis->patientID;

		$dresult = $processData->DeleteDiagnosis($diagId,$patientid);
		print(json_encode($dresult,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Diagnosis) && $mmdata->Diagnosis->API_Meth=="SearchProcedure-Code")
	{
		//var_dump($mmdata);
		$code = $mmdata->Diagnosis->searchterms;
		$searchresult = $processData->SearchProcedureCodes_ByCode($code);
		print(json_encode($searchresult));
	}
	elseif(isset($mmdata->Diagnosis) && $mmdata->Diagnosis->API_Meth=="SearchProcedure-Description")
	{
		$description = $mmdata->Diagnosis->description;
		$searchresult = $processData->SearchProcedureCodes_ByDescription($description);
		print(json_encode($searchresult));
	}
	// elseif($mmdata->Diagnosis->API_Meth=="ProcedureInsert")
	// {
	// 	//var_dump($mmdata);
	// 	$proccode = $mmdata->Diagnosis->procedurecode;
	// 	$diagcode="";
	// 	$procdate = $mmdata->Diagnosis->diagnosisDate;
	// 	$description = $mmdata->Diagnosis->description;
	// 	$onset = $mmdata->Diagnosis->onset_exacerbation;
	// 	$diagtype = $mmdata->Diagnosis->diagnosis_type;//remember to do a switch case on this and insert on value or the other in the database 
	// 	$ordernum = $mmdata->Diagnosis->ordernumber;
	// 	$controlrating ="";
	// 	$pid =$mmdata->Diagnosis->patientID;
	// 	$status = $mmdata->Diagnosis->status;
	// 	$searchresult = $processData->InsertDiagnosis($pid,$ordernum,$diagtype,$diagcode,$proccode,$controlrating,$onset,$procdate,$status,"");
	// 	print(json_encode($searchresult));
	// }
	elseif(isset($mmdata->Orders) && $mmdata->Orders->API_Meth=="ChangeOrderStatus")
	{
		$ordnum = $mmdata->Orders->ordernumber;
		$status = $mmdata->Orders->status;
		$ordersar = $processData->ChangeOrderStatus($ordnum, $status);
		print(json_encode($ordersar,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Orders) && $mmdata->Orders->API_Meth=="GetALLPatientOrders")
	{
		//var_dump($mmdata);
		$pid = $mmdata->Orders->patientID;
		$ordersar = $processData->GetAllPatientOrders($pid);
		print(json_encode($ordersar,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Orders) && $mmdata->Orders->API_Meth=="GetOrderByDates")
	{
		$pid =$mmdata->Orders->patientID;
		$stdate =$mmdata->Orders->ordstartdate;
		$enddate =$mmdata->Orders->ordsenddate;
		//var_dump($pid." ".$stdate." ".$enddate);
		$ordersar = $processData->GetOrderByStartAndEndDT($pid,$stdate,$enddate);
		print(json_encode($ordersar,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Orders) && $mmdata->Orders->API_Meth=="GetOrdersByCombination")
	{
		//var_dump("here");exit();
		$pid = $mmdata->Orders->patientID;
		$stdate = $mmdata->Orders->ordstartdate;
		$enddate = $mmdata->Orders->ordsenddate;
		$properties = $mmdata->Orders->ordertype;
		if($mmdata->Orders->orderstatus)
		{
		}
		else{
			
			$orderstatus="";
		}
		$ordersar = $processData->GetOrderByCombo($pid,$stdate,$enddate,$properties,$orderstatus);
		
		print(json_encode($ordersar,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Orders) && $mmdata->Orders->API_Meth=="ViewOrderByID")
	{
		$ordid = $mmdata->Orders->orderid;
		
		$getorder = $processData->GetOrderbyID($ordid);
		print(json_encode($getorder,JSON_PRETTY_PRINT));

	}
	elseif(isset($mmdata->Note) && $mmdata->Note->API_Meth=="FilterNotes")
	{
		$pid =$mmdata->Note->patientID;
		$startdate = $mmdata->Note->poststartdate;
		$enddate = $mmdata->Note->postenddate;
		$getresults = $processData->GetNotes($pid,$startdate,$enddate);
		print(json_encode($getresults,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Note) && $mmdata->Note->API_Meth=="GetAllNotes")
	{
		$patientId =$mmdata->Note->patientID;
		$getresults = $processData->GetAllNotes($patientId);
		print(json_encode($getresults,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Note) && $mmdata->Note->API_Meth=="GetNoteByID")
	{
		$pid =$mmdata->Note->patientID;
		$noteid =$mmdata->Note->noteid;
		$getresults = $processData->GetNotesById($pid,$noteid);
		print(json_encode($getresults,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Allergy) && $mmdata->Allergy->API_Meth=="GetAllergies")
	{
		$pid = $mmdata->Allergy->pid;
		$results = $processData->GetAllergies($pid);
		print(json_encode($results,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Allergy) && $mmdata->Allergy->API_Meth=="AddAllergies")
	{
		$pid = $mmdata->Allergy->pid;
		$name = $mmdata->Allergy->name;
		$results = $processData->AddAllergies($pid,$name);
		print(json_encode($results,JSON_PRETTY_PRINT));
	}
	elseif(isset($mmdata->Administration) && $mmdata->Administration->API_Meth=="ImportUserPermissions")
	{
		//Manually Update Users Peremssions 
		//Grap All User Records 
		$getuser = $processData->GetAllUserAccounts();
		if(!empty($getuser) && is_array($getuser))
		{
			//grab records 
			$rec = $getuser["records"];
			//var_dump($rec);
			//loop through users and check to see if they alread exist in the premissions feature table 
			foreach($rec as $r)
			{
				//var_dump($r["accountnumber"]);
				//var_dump($r["unique_ID"]);
				$checkdefault = $processData->IndividualUserPremissions($r["accountnumber"],$r["unique_ID"]);
				if(!empty($checkdefault))
				{
					//do nothing because permissions already exist
				}
				else{
					//lets check the accounttype 
					if($r["account_type"]=="Provider")
					{
						//var_dump("Made it its an Admnistrator");
							$role="Administrator";
							$getdefault = $processData->GetUserDefaultPremissions($role);
							//var_dump($getdefault);
							$insertperm = $processData->SaveUserPremissions($role,$r["username"],$r["accountnumber"],$r["unique_ID"],$getdefault);
							//exit();
					}
					else{
						var_dump("View role");
						$role="View";
						$getdefault = $processData->GetUserDefaultPremissions($role);
						//var_dump($getdefault);
						$insertperm = $processData->SaveUserPremissions($role,$r["username"],$r["accountnumber"],$r["unique_ID"],$getdefault);
						//var_dump($insertperm);exit();
					}
				}
			}
			var_dump("Import Done");exit();
		}
		else{
			var_dump("Issue");
			var_dump($getuser);
		}
		//End Manull Update Uwers Permissions
	}
	else{
		$d = $mmdata;//$_POST;
		//var_dump($d ."Made It");exit();
		$pdata = $processData->ProcessSystemData($d);
		//var_dump($pdata);
		print_r(json_encode((array) $pdata));
	}
}
else{
	var_dump("nothing Posted");
}