<?php 
class ProcessData{
    private  $webhookURL="http://10.10.3.7/pacmny-be/web/index.php/fora/query";
	//private $sqlightPath = "eventsdb.db";
	
    
public function init()
{
	//Initialize Class so that its operational 
	
	

} 
private function dbConnect()
{
	//lets set the mysql credentials here
	$con = new PDO("mysql:host=pacmnymysql1.mysql.database.azure.com","phpmyadmin","phpmyadmin",[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=>false,PDO::MYSQL_ATTR_SSL_CAPATH=>'/etc/mysql/ssl']);
	//var_dump($con);
	return $con;
}
public function dbConnect2()
{
	//lets set the mysql credentials here
	$con = new PDO("mysql:host=pacmnymysql1.mysql.database.azure.com","phpmyadmin","phpmyadmin",[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=>false,PDO::MYSQL_ATTR_SSL_CAPATH=>'/etc/mysql/ssl']);
	//var_dump($con);
	return $con;
}
public function isEmailValid($email)
{
	$pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $match = preg_match($pattern, $email);
	
	return $match;
}
public function ImportPatients($filepath,$fhandle)
{
	/*
	@take csv file which is the file path and loop through it too Import patients as subaccounts
	@Tables affected: Account_User & Patients
	@Legend for Field Mapping below: 
	*1 = ZV_user_admin
	*161 = externid
	*25 firstname
	*33 lastname
	*14 email //not correct
	*64 I:Guest
	*0 = primkey
	*37 = address
	*41 = city
	*5 = watchlist
	*13 = user login name
	*17 = passwors
	*37 = address
	*41 = city
	*45 = state
	*49 = zip
	*53 = cell
	*57 = cell2
	*61= work
	65 = home
	*69=email
	*73= social security
	*77= dob
	*153 = uniqueID
	*161= accountiD
	*165=npi
	*109=Contacts
	113=contact phone1
	117 = contact phone2
	*/
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$csvpath = $filepath;
	$csvhandle = $fhandle;
	$dbdata[]= array();
	$successdata[] = array();
	//var_dump(fgetcsv($csvhandle));exit();
	$i=0;
	while( ($data = fgetcsv($csvhandle) ) !==FALSE)
	{
	$i++;
	//var_dump($data);
	
	
	//lets match this list up the columns we can do a databae i



	if($i >177 )//if($i >177 && $i <=277) If greate than the columns and Not a provider due to having no npi
	{
		$dbdata[] = array("primkey"=>$data[0],"firstname"=>$data[25],"lastname"=>$data[33],"email"=>$data[69],"address"=>$data[37],"city"=>$data[41],"uniqueID"=>$data[153],"accountnumber"=>$data[161],"social"=>$data[73],"dob"=>$data[77],"address"=>$data[37],"city"=>$data[41],"state"=>$data[45],"zip"=>$data[49],"watchlist"=>$data[17],"dob"=>$data[77],"emgContact"=>$data[109],"emgcontactPhone"=>$data[113],"emgContactPhone2"=>$data[117],"watchList"=>$data[17]);
		
		//Insert Into the database by Providers and Patients 
		if($data[1]=="True" && $data[1]!="False" && $data[1] !=null)
		{
			//Only insert into the Account User Table 
			//var_dump("Administrator");
			//var_dump($data[1]);
			//var_dump("Adminvalue"." ".$data[1]);
			$ucreationDt = date("Y-m-d");	
			//var_dump($ucreationDt);
  
			$import =  $sclass->ImportPatients($data[0],$data[161],$data[153],$data[13],"Provider",$data[25],$data[33],$data[53],$data[69],$data[37],$data[41],$data[45],$data[49],"lm3333","Active","Administrator","232245353",$ucreationDt,"Email",$data[77],$data[73],$data[109],$data[113],$data[117],$data[5]);
												
			if($import["status"]=="Successfull")
			{
				$successdata[] = array("status"=>"Inserted");
			}
		}
		else{
			//Insert both Account User and Patient Table 
			$ucreationDt=date("Y-m-d");	
			//var_dump("Patients");
			//var_dump($ucreationDt);
			$import =  $sclass->ImportPatients($data[0],$data[161],$data[153],$data[13],"Patients",$data[25],$data[33],$data[53],$data[69],$data[37],$data[41],$data[45],$data[49],"lm3333","Active","Viewer","232245353",$ucreationDt,"Email",$data[77],$data[73],$data[109],$data[113],$data[117],$data[5]);
			//var_dump($import);
			if($import["status"]=="Successfull")
			{
				$successdata[] = array("status"=>"Inserted");
			}
		}
      
	}
	else{
		
	/*if($i==277)
	{
		break;
	}*/
	//Continue Importing until its done
	
	}



	}
	if(!empty(array_filter($successdata)))
	{
		$msgar = array("status"=>"Import Successfull");
		return $msgar;
	}
	//var_dump($dbdata);
}
public function InsertMedLog( $accountnumber,$patientid,$patientname,$ordernumber,
$providername,$providerid,$medicationid,$administrated_at,$time,$status,$yearmedtime,$notes,$providersignature,$provinitials)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$insertMeds = $sclass->InsertMedLog( $accountnumber,$patientid,$patientname,$ordernumber,$providername,$providerid,$medicationid,$administrated_at,
	$time,$status,$yearmedtime,$notes,$providersignature,$provinitials);
	return $insertMeds;
}
public function UpdateMedLog($accountnumber,$patientid,$medicationid,$administeredat,$medadmintimes,$status,$ctime)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$findpharm = $sclass->UpdateMedLog($accountnumber,$patientid,$medicationid,$administeredat,$medadmintimes,$status,$ctime);
	return $findpharm;
}
public function checkMedlogtablenfo($accountnumber,$patientid,$adminDate)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$check = $sclass->checkMedlogtablenfo($accountnumber,$patientid,$adminDate);
	return $check;
}
public function SearchMedLog($accountnumber,$patientid,$medicationid,$ordernumber,$administeredat)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$findpharm = $sclass->SearchMedLog($accountnumber,$patientid,$medicationid,$ordernumber,$administeredat);
	return $findpharm;
}
public function getMedlogtbleInfo($accountnumber,$patientid,$medid,$adminDate,$admintimes,$provinitials,$provsignature)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$findpharm = $sclass->getMedlogtbleInfo($accountnumber,$patientid,$medid,$adminDate,$admintimes,$provinitials,$provsignature);
	return $findpharm;
}
public function insertMedlogtableInfo($accountnumber,$patientid,$adminDate,$admintimes,$provinitials,$provsignature)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$findpharm = $sclass->insertMedlogtableInfo($accountnumber,$patientid,$adminDate,$admintimes,$provinitials,$provsignature);
	return $findpharm;
}
public function findPharmacy($accountnumber,$pharmacyname,$npinumber)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$findpharm = $sclass->findPharmacy($accountnumber,$pharmacyname,$npinumber);
	return $findpharm;
}
public function InsertPharmacy($accountnumber,$pharmacyname,$pharmnpi,$pharmaddr,$pharmacyOffice,$pharmacyCell,$pharmdeanumber,$pharmacyEmail)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$insertpharm = $sclass->InsertPharmacy($accountnumber,$pharmacyname,$pharmnpi,$pharmaddr,$pharmacyOffice,$pharmacyCell,$pharmdeanumber,$pharmacyEmail);
	return $insertpharm;
}
public function logpaPharmacy($accountnumber,$subaccountnumber,$patientid, $provnpi, $pharmacyname,$pharmdeanumber,$pharmnpi,$assigndt,$notes)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$logprov = $sclass->logpaPharmacy($accountnumber,$subaccountnumber,$patientid, $provnpi, $pharmacyname,$pharmdeanumber,$pharmnpi,$assigndt,$notes);
	return $logprov;
}
public function lookupPatientPharmacy($accountnumber,$patientid)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getPapharm = $sclass->lookupPatientPharmacy($accountnumber,$patientid);
	return $getPapharm;
}
public function logpaProvider($accountnumber,$subaccountnumber,$firstname,$lastname,$npinumber,$provemail,$deanumber,$taxonomy,
$ordernumber,$patientid,$addr1,$phone,$fax,$proffisionalicense)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$logprov = $sclass->logpaProvider($accountnumber,$subaccountnumber,$firstname,$lastname,$npinumber,$provemail,$deanumber,$taxonomy,
	$ordernumber,$patientid,$addr1,$phone,$fax,$proffisionalicense);
	return $logprov;
}
public function LookUpInternalProvider($npinumber)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$lookup = $sclass->GetProviderbyNPI($npinumber);
	return $lookup;
}
public function InsertPerscription($accountnumber,$patientid,$medname, $rxnumber, $dtfilled,$refills,$startdate,$enddate,$refilreminderdt,$refillexpirationdt)
{
	require_once("SqlClass.php");
	$sclass= new SQLData();
	$getdata = $sclass->InsertPerscription($accountnumber,$patientid,$medname, $rxnumber, $dtfilled,$refills,$startdate,$enddate,$refilreminderdt,$refillexpirationdt);
	return $getdata;
}
public function InsertAdminMecationInfo($accountnumber,$ordernumber,$patientid,$ndcnumber,$rx,$prn,$newmedsettings,$totalTabs,$route,$diagnois,$freq,$dosage,$medname,$instruction,$medchangetype)
{
	require_once("SqlClass.php");
	$sclass= new SQLData();
	$getdata = $sclass->InsertAdminMecationInfo($accountnumber,$ordernumber,$patientid,$ndcnumber,$rx,$prn,$newmedsettings,$totalTabs,$route,$diagnois,$freq,$dosage,$medname,$instruction,$medchangetype);
	return $getdata;
}
public function grabPatientAddressbyID($patientid)
{
	require_once("SqlClass.php");
	$sclass= new SQLData();
	$getdata = $sclass->grabPatientAddressbyID($patientid);
	return $getdata;
}
public function GetMedAdminOrdersbyPatientID($patientid)
{
	require_once("SqlClass.php");
	$sclass= new SQLData();
	$sdata = $sclass->GrabOrderbyPatientID($patientid);
	return $sdata;
}
public function UpdateMedicationInfo($accountnumber,$ordernumber,$patientid,$ndcnumber,$rx,$prn,$medsettings,$totalTabs)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$updateMed = $sclass->UpdateMedicationInfo($accountnumber,$ordernumber,$patientid,$ndcnumber,$rx,$prn,$medsettings,$totalTabs);
	return $updateMed;
}
public function DoesMedExist($accountnumber,$ordernumber,$npinumber,$patientid,$medname,$status="Active")
{
	require_once("SqlClass.php");
  $sclass = new SQLData();
  $activemed = $sclass->DoesMedExist($accountnumber,$ordernumber,$npinumber,$patientid,$medname,$status);
  return $activemed;
}
public function findpatientactiveMedOrders($accountnumber,$npinumber,$patientid)
{
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $activemed = $sclass->findpatientactiveMedOrders($accountnumber,$npinumber,$patientid);
  return $activemed;
}
public function InsertPatientNotes($patientId,$note,$pname,$notedate,$notify,$email,$name,$startDt,$endDt,$startTime,$endTime,$type,$timeLength,$providersignature,$provsigdate)
{
	require("consts.php");
	$host="pacmnymysql1.mysql.database.azure.com";
	$user="phpmyadmin";
	$password="phpmyadmin";

	$dbname=$DB;
	$patID = $patientId;
	$pnotes = $note;
	$pdate =$notedate;
  $provsig = $providersignature;
  $provsigdt = $provsigdate;
	$con = new PDO("mysql:host=$host;dbname=$dbname",$user,$password, [PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=>false,PDO::MYSQL_ATTR_SSL_CAPATH=>'/etc/mysql/ssl']);
	//$con = new PDO("mysql:host=localhost","willown9_admin","Wi!!0s!23",[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=>false,PDO::MYSQL_ATTR_SSL_CAPATH=>'/etc/mysql/ssl']);
        //var_dump($con);
      
	$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$sql="Insert Into {$DB}.patientNotes (patientId,pnotes,postDate,name,startDt,endDt,startTime,endTime,type,timeLength,provsignature,provsigdt) VALUES(:pid,:notes,:pdate,:name,:startDt,:endDt,:startTime,:endTime,:type,:timeLength,:provsig,:provsigdt)";
	$stmnt = $con->prepare($sql);
	$stmnt->bindParam(":pid",$patID);
	$stmnt->bindParam(":notes",$pnotes);
	$stmnt->bindParam(":pdate",$pdate);
	$stmnt->bindParam(":name",$name);
	$stmnt->bindParam(":startDt",$startDt);
	$stmnt->bindParam(":endDt",$endDt);
	$stmnt->bindParam(":startTime",$startTime);
	$stmnt->bindParam(":endTime",$endTime);
	$stmnt->bindParam(":type",$type);
	$stmnt->bindParam(":timeLength",$timeLength);
  $stmnt->bindParam(":provsig",$provsig);
  $stmnt->bindParam(":provsigdt",$provsigdt);
  try{
	if($stmnt->execute())
	{

		//now send email 
		if($notify=="true")
		{
			$sndEmail =  $this->QueEmailTemplate($email,$pname,$type,$name);
			if($sndEmail!="Sent")
			{
				// handle failed email
			}
		}
		$result = "Inserted";
		require_once("SqlClass.php");
		$sclass = new SQLData();
		$sclass->CheckNewTimeCpt(array("pid"=>$patID, "type"=>$type, "time"=>$timeLength));
		return $result;
		//var_dump($result);
		
	}
	else{
		$error = $stmnt->error;
		return $error;
	}
}
catch(PDOException $e)
{
  var_dump($e->__toString());
  $msg = array("status"=>"700-Sql","message"=>$e->__toString());
  return $msg;
}
}
public function RemoveGroupByAcccount($account,$provowner,$group)
{
	$accountnumber = stripslashes($account);
	$owner =stripslashes($provowner);
	$groupname = stripslashes($group);
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$removegroup = $sclass->DeleteGroupByAcccount($accountnumber,$owner,$groupname);
	if(is_array($removegroup) && !empty($removegroup))
	{
		return $removegroup;
	}
}
public function GetProviderGroups($accntnumber, $provowner,$groupname,$grptype)
{
	$accountnumber = $accntnumber;
	$owner = $provowner;
	$grpname = $groupname;
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getlist = $sclass->ViewGroupMembers($accountnumber,$owner,$grpname,$grptype);
}
public function GetNebData($obj)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$arr = array("pid"=>$obj->pid);
	if (is_array($arr) && !empty($arr))
	{
		$apiresult = $sclass->GetNeb($arr["pid"]);
		return $apiresult;
	}
	else {
		$msgar = array("message"=>"PatientInformation Object is null or empty");
		return $msgar;
	}
}
public function GetMeasurements($obj){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$arr = (array("pid"=>$obj->pid, "meterid"=>$obj->meterid));
	if (is_array($arr) && !empty($arr))
	{
		$api_result = $sclass->GetMeasurements($arr);
		return $api_result;
	}
	else {
		$msgar = array("message"=>"DeviceInformation Object is null or empty");
		return $msgar;
	}
}
public function GetMeasurements2($obj){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$arr = (array("pid"=>$obj->pid));
	if (is_array($arr) && !empty($arr))
	{
		$api_result = $sclass->GetMeasurements2($arr);
		return $api_result;
	}
	else {
		$msgar = array("message"=>"DeviceInformation Object is null or empty");
		return $msgar;
	}
}
public function MarkBilled($obj){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$arr = array("fid"=>$obj->fid, "current"=>$obj->current, "icd"=>$obj->icd);
	if (is_array($arr) && !empty($arr))
	{
		$api_result = $sclass->MarkBilled($arr);
		return $api_result;
	}
	else {
		$msgar = array("message"=>"Object is null or empty");
		return $msgar;
	}
}
public function AddPatientCpt($obj){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$arr = array("pid"=>$obj->pid, "meterid"=>$obj->meterid, "cpt"=>$obj->cpt, "cost"=>$obj->cost, "status"=>$obj->status, "cycleCount"=>$obj->cycleCount);
	if (is_array($arr) && !empty($arr))
	{
		$api_result = $sclass->AddPatCpt($arr);
		return $api_result;
	}
	else {
		$msgar = array("message"=>"DeviceInformation Object is null or empty");
		return $msgar;
	}
}
public function AddMeterCpt($obj){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$arr = array("pid"=>$obj->pid, "meter"=>$obj->meter, "cpt"=>$obj->cpt, "meterName"=>$obj->meterName, "icd"=>$obj->icd);
	if (is_array($arr) && !empty($arr))
	{
		$api_result = $sclass->AddMeterCpt($arr);
		return $api_result;
	}
	else {
		$msgar = array("status"=>"Fail", "message"=>"PatientCpt Object is null or empty");
		return $msgar;
	}
}
public function GetMeterCpt($obj){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$arr = array("pid"=>$obj->pid);
	if (is_array($arr) && !empty($arr))
	{
		$api_result = $sclass->GetMeterCpt($arr);
		return $api_result;
	}
	else {
		$msgar = array("message"=>"PatientCpt Object is null or empty");
		return $msgar;
	}
}
public function GetPatientCpt2($obj){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$arr = array("pid"=>$obj->pid);
	if (is_array($arr) && !empty($arr))
	{
		$api_result = $sclass->GetPatCpt2($arr);
		return json_encode($api_result);
	}
	else {
		$msgar = array("message"=>"DeviceInformation Object is null or empty");
		return $msgar;
	}
}
public function GetPatientCpt($obj){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$arr = array("pid"=>$obj->pid);
	if (is_array($arr) && !empty($arr))
	{
		$api_result = $sclass->GetPatCpt($arr);
		return json_encode($api_result);
	}
	else {
		$msgar = array("message"=>"DeviceInformation Object is null or empty");
		return $msgar;
	}
}
public function GetPatientCptBilling($obj){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$arr = $obj->pids;
	$result = [];
	if (is_array($arr) && !empty($arr))
	{
		foreach ($arr as $pid){
			$subarr = array("pid"=>$pid);
			$api_result = $sclass->GetPatCpt($subarr, true);
			$result[$pid] = $api_result;
		}
		return json_encode($result);
	}
	else {
		$msgar = array("message"=>"DeviceInformation Object is null or empty");
		return $msgar;
	}
}
public function GetPatientCptBilling2($obj){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$arr = $obj->pids;
	$result = [];
	if (is_array($arr) && !empty($arr))
	{
		foreach ($arr as $pid){
			$subarr = array("pid"=>$pid);
			$api_result = $sclass->GetPatCpt2($subarr);
			if ($api_result['current'] || $api_result['historical']){
				$result[$pid] = $api_result;
				$icds = $sclass->GetPatICD($pid);
				$result[$pid]["icdList"] = $icds;
			}
		}
		return json_encode($result);
	}
	else {
		$msgar = array("message"=>"Object is null or empty");
		return $msgar;
	}
}
public function UpdateProviderOnTeam($provid, $value)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$api_result = $sclass->UpdateProvOnTeam($provid, $value);
	return $api_result;
}
public function GetProviderbyNPI($getprovider,$grpowner,$grpname,$accountnum,$emails,$grptype)
{

	//var_dump($getprovider);
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$ar = $getprovider;
	$groupname = $grpname;
	$groupowner = $grpowner;
	$accntnumber = $accountnum;
	$er = $emails;
	//var_dump($er);
	$newar = array();
	if(!empty($ar) && $ar !=null)
	{

	
		foreach($ar as $a)
		{
			//var_dump($a["npi"]);
			$result = $sclass->GetProviderbyNPI($a["npi"]);
			//now go and save that data in the Database 
			
			//$result["provider"][0]["accountnumber"]
			$savdata = $sclass->SaveGroupMembers($accntnumber,$groupowner,$groupname,$result["provider"][0]["firstname"],$result["provider"][0]["lastname"],$result["provider"][0]["npinumber"],$result["provider"][0]["email"],$result["provider"][0]["tel"],$grptype);

			if($savdata["status"] =="Inserted")
			{
				//do nothing becuase it inserted correctly
				var_dump($savdata);
				$successar = array("message"=>"Added Successfully");
			}
			else{
				$msg= array("status"=>"700-sqlError","error"=>$savdata);
				return $msg;
			}

		}
	}
	//now lets add Eamil array
	if(!empty($er) && $er !=null)
	{

	
		foreach($er as $e)
		{
			//$result = $sclass->GetProviderbyEmail($e["email"]);
			$savdata = $sclass->SaveGroupMembersWithEmail($accntnumber,$groupowner,$groupname,$e["firstname"],$e["lastname"],$e["email"],$e["phone"],$grptype);

			if($savdata["status"] =="Inserted")
			{
				//do nothing becuase it inserted correctly
				$esuccessar = array("message"=>"Added Successfully");
			}
			else{
				$msg= array("status"=>"700-sqlError","error"=>$savdata);
				return $msg;
			}
		}
	}
	if(is_array($successar) && is_array($esuccessar) && !empty($successar) || !empty($esuccessar))
	{
		$addgroupstatus ="Added Successfully";
		return $addgroupstatus;
	}
	
}
public function DeleteGroupbyName($grpname)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$removegroup = $sclass->DeleteGroupbyName($grpname);
	if(is_array($removegroup) && !empty($removegroup))
	{
		return $removegroup;
	}
	else{
		$deleteroup = array("status"=>"777-sql","message"=>$removegroup);
		return $deletegroup;
	}
}
public function GetGroupByAccountNumber($accountnumber,$groupowner,$grptype)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$grpowner = $groupowner;
	$accntnumber = $accountnumber;
	 $groupinfo = $sclass->GetGroupByAccount($accountnumber,$grpowner,$grptype);

	// var_dump($groupinfo);
	 if(is_array($groupinfo))
	 {
	 	 //$buildmarkup = $this->BuildGroupMarkUp($groupinfo);
		 return $groupinfo;
	 	// return $buildmarkup;
	 }
	 else{
	 	$msg=array("message"=>"No Groups Found");
	 	return $msg;
	 }


}
private function BuildGroupMarkUp($groupinfo)
{
  if(is_array($groupinfo))
  {
	 $html .="<style type='text/css'>
	 .listGroupSec .card .accordhead{
        display: flex;
    flex-direction: row-reverse;
    border-style: solid;
    border-width: 1px;
    border-color: #0444;
}
 .listGroupSec .card .caret{
    text-align: right;
    width: 100px;
}
.listGroupSec .card .grp-name{
    width: 100%;
    margin: 0 auto;
}
.listGroupSec .card .grp-name p{
    text-align: center;
}
.listGroupSec .accordionbottom{
    display: flex;
    flex-direction: column;
    width: 100%;
    height: auto;
    border-style: solid;
    border-width: 1px;
    border-color: #0c131345;
    display: flex;
}
.listGroupSec .providminicard{
    display: flex;
    flex-direction: row-reverse;
    border-bottom-style: solid;
    border-bottom-width: 1px;
    flex-direction: row-reverse;
    padding: 11px;
    margin-bottom: 3px;

}
.listGroupSec #accord-caret{
    cursor:pointer;
}
.listGroupSec #accord-caret:hover{
    color:#4553;
}
.listGroupSec .groupcanvas .card{
    display:flex;
    flex-direction:column;
}
.listGroupSec .subnavcontainer{
    display: none;
    flex-direction: column;
    width: 248px;
    margin-left: -153px;
    border-style: ridge;
    padding: 11px;

}
.listGroupSec #elipse{
    cursor:pointer;
}
.listGroupSec .closecanvas{
    display:none;
}
.listGroupSec .creategrpcanvas{
    display:none;
}
.listGroupSec .groupcanvas{
    display: none;
}
.listGroupSec .accordionbottom{
    display:none;
}
.smsconferencecanvas{
    display: none;
}
.smsconferencecanvas .container #smsform input[type=phone] {
    width: 320px;
    border-top-style: none;
    border-right-style: none;
    border-left-style: none;
    margin-bottom: 30px;
}
#smsconferencebox{
    width: 100%;
    height: 155px;
    margin-bottom: 30px;
}
.smsconferencecanvas .container{
    flex-direction: column;
    display: flex;
    background-color: #ffffff;
    position: absolute;
    width: 812px;
    padding: 20px;
    box-shadow: 1px 1px 9px #5f5852;
}
	 </style>";
  	 $ar = array();
  	 foreach($groupinfo as $info)
  	 {
  	 	$currentgroupname = $info["groupname"];
  	 	if($info["groupname"] !="" && !in_array($info["groupname"], $ar))
  	 	{

			$html .="<div class='card'>".
			"<div class='accordhead'>".
		  	 "<div class='caret'>".
		  	 "<span id='accord-caret'>+</span>".
		  	 "<div class='elipsecontainer'>".
		  	 "<div id='elipse'>...</div>".
		  	 "<div class='subnavcontainer'>".
		  	 "<div class='sub-nav'><a href='#email' id='mergeemail'>Merge Email Groupl</a></div>".
		  	 "<div class='sub-nav'><a href='#textsms' id='mergetext'>Merge SMS Group</a></div>".
		  	  "<div class='sub-nav'><a href='#conftextsms' id='mergeconftext'>Conference Call SMS Group</a></div>".
		  	 "<div class='sub-nav'><a href='#deletegrp' id='deletegrp'>Delete Group</a></div>".
		  	 "</div>".
		  	 "</div>".
		  	 "</div>".
		  	 "<div class='grp-name'><p id='gpname'>".$info["groupname"]."</p></div>".
		  	 "</div>".
		  	 "<div class='accordionbottom'>";

			array_push($ar,$info["groupname"]);
			//var_dump($ar);

  	 	}
  	 	if(in_array($info["groupname"], $ar) && in_array($info["groupname"],$ar)==$currentgroupname)
  	 	{
  	 		$html .="<div class='providminicard col-sm-12 col-md-12 col-lg-12'>".
			  	 	      "<div class='col-sm-12 col-md-3 col-lg-3'>".
			  	 	      "<p id='grprovemail'>".$info["provnpinumber"]."</p>".
			  	 	      "</div>".
			  	 	     "<div class='col-sm-12 col-md-3 col-lg-3'>".
			  	 	     "<p>".$info["grp_provider_fname"]." ".$info["grp_provider_fname"]."</p>".
			  	 	     "</div>".
			  	 	     "<div class='col-sm-12 col-md-3 col-lg-3'>".
			  	 	     "<p id='grpnpi'>".$info["grp_provideremail"]."</p>".
			  	 	     "</div>".
			  	 	     "<div class='col-sm-12 col-md-3 col-lg-3'>".
			  	 	     "<p id='grpphn'>".$info["phone"]."</p>".
			  	 	     "</div>".
			  	 	     "</div>";
  	 	}
  	 	else{
  	 		$html .="</div>";
  	 		$html .="</div>";
  	 	}


  	 }

  	 //$html .="</div>";
  	 return $html;
  }
  else{
  	$msg = array("message"=>"No Markup Can Be Producted");
  	return $msg;
  }
}
public function GetProviderCards($providerid)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$pid = stripslashes($providerid);
	$getcards = $sclass->GetProvidersByAccountNumber($pid);
	//var_dump($getcards);
	$buildcards = $this->BuildSelectProviderCard($getcards);

	if($buildcards !="")
	{
		$cardsar = array("success"=>"200","html"=>$buildcards);
	}
	return $buildcards;
}
private function BuildSelectProviderCard($cardinfo)
{

	if(is_array($cardinfo["provider"]) && !empty($cardinfo["provider"]))
	{
		$i=0;
		foreach($cardinfo["provider"] as $info)
		{

			$html .="<div class=\"contentcanavas\">
                            <div class=\"col1 col-sm-12 col-md-2 col-lg-2\">
                               <label id='npilbl'> <input type=\"checkbox\" class=\"cbox\" id=\"npinumber\" npi=".$info["npinumber"]."/>".$info["npinumber"]."</label>
                            </div>
                            <div class=\"col2 col-sm-12 col-md-10 col-lg-10 col-xl-10\">
                                <div class=\"pvimage\"><img src=\"".$info["providimage"]."\" width=\"90\" height=\"90\"/></div>
                                <div class=\"provname\">
                                    <h4 id=\"provfullname\">".$info["firstname"]." ".$info["lastname"]."</h4>
                                    <p id=\"provemailaddr\">".$info["email"]."</p>
                                </div>
                            </div>
                        </div>";
		}
		//now build the save button 
		$html .="<div class='savegrpbtns'>
		        <div class='svgrpbtn'><a href='#savegrp' id='savgrpbton' title='save group btn'>Save Group</a></div>
		        </div>";
		return $html;
	}

}
public function SendInternalMessagingNotication($contact,$provider)
{
	require_once('Mandrill.php');
	require_once("EmailTemplate.php");
	require("consts.php");
	$emailtemp = new EmailTemplates();
	//var_dump($emailtemp);
	$mandrill = new Mandrill('md-s1z0SU7cJO2IqKUV10TPQw');
	//$e = new EmailTemplates();
	$pretext="You Have A Message From Park Avenue Concierge Medicine | Sent From ".$provider." ";
	$headtext="<h1>You have a message from ".$provider."</h1>";
	$subject="You Have a new notification FROM PACM EMR — ".$provider;
	$utm="";
	$email = $emailtemp->CustomerEmail($pretext, $headtext, $message, $utm);
    	//var_dump($email);
    	//$email2 = $emailtemp->DonarEmail($pretext2, $headfile,$dnremailmessage);
    	//var_dump($email2);
    	//I should have the HTML EMAIL to attached to the Mandrill body section 

   
    $successar = array();
   // var_dump($emailar);//exit();
    //var_dump($emailar);
	$message2 = new stdClass();
	$message2->html =$email;
	//$message->text = "text body";
	$message2->subject =$subject;
	$message2->from_email = "developers@willowmarketing.com";
	$message2->from_name  = "PACM Patient Portal";
		$i=0;
		 //var_dump($e);
		 $message2->to = array(
			array(
			'email' =>$contact,
			'name' =>'',
			'type' => 'to',
			),
			 /*array(
				'email' => 'emailvip52@gmail.com',
				'name' => 'Keyon Whiteside',
				'type' => 'cc'
			),*/
			array(
				'email' =>'keyon5052@gmail.com',
				'name' =>'Keyon Whiteside',
				'type' =>'cc'
			)
		);	

		$message2->track_opens = true;

		if($mandrill->messages->send($message2))
			{
				//var_dump("Sent");
				array_push($successar, "Success");
				/*$returnmsg="EmailSent";
				return $returnmsg;*/
			}else{
				$returnmsg="Mail Did't Send";
				return $returnmsg;
			}
			if(in_array("Success", $successar))
			{
				$returnmsg="EmailSent";
				return $returnmsg;
			}
}
public function EmailProvider($toemail,$subject,$message,$fromprovider)
{
	require_once('Mandrill.php');
	require_once("EmailTemplate.php");
	require_once("consts.php");
	$emailtemp = new EmailTemplates();
	$mandrill = new Mandrill($MandrillPW);
	$e = new EmailTemplates();
	$pretext="You Have A Message From Park Avenue Concierge Medicine | Login To Your Account To Review";
	$headtext="<h1>You have a message from ".$fromprovider."</h1>";
	$utm=$CURR_ADDR . "/#/login";
	$email = $emailtemp->CustomerEmail($pretext, $headtext, $message, $utm);
    	//var_dump($email);
    	//$email2 = $emailtemp->DonarEmail($pretext2, $headfile,$dnremailmessage);
    	//var_dump($email2);
    	//I should have the HTML EMAIL to attached to the Mandrill body section 

    $emailar = explode(",",$toemail);
    $successar = array();
   // var_dump($emailar);//exit();
    //var_dump($emailar);
			 $message2 = new stdClass();
		$message2->html =$email;
		//$message->text = "text body";
		$message2->subject =$subject;
		$message2->from_email = "developers@willowmarketing.com";
		$message2->from_name  = "PACM Patient Portal";
		$i=0;
		foreach($emailar as $e)
		{
			$i++;

			if($e !="")
			{

				 //var_dump($e);
			    $message2->to = array(
			    	array(
					'email' =>$e,
					'name' =>$fromprovider,
					'type' => 'to',
					),
				     /*array(
		                'email' => 'emailvip52@gmail.com',
		                'name' => 'Keyon Whiteside',
		                'type' => 'cc'
	            	),
		            array(
		            	'email' =>'keyon5052@gmail.com',
		            	'name' =>'Keyon Whiteside',
		            	'type' =>'cc'
		            )*/
	            );	

				$message2->track_opens = true;

				if($mandrill->messages->send($message2))
					{
						//var_dump("Sent");
						array_push($successar, "Success");
						/*$returnmsg="EmailSent";
						return $returnmsg;*/
					}else{
						$returnmsg="Mail Did't Send";
						return $returnmsg;
					}
				}


			}
			if(in_array("Success", $successar))
			{
				$returnmsg="EmailSent";
				return $returnmsg;
			}
		/*}*/
		/*$message2->to = array(
			array(
                'email' =>$toemail,
                'name' => $fromprovider,
				'type' => 'to',
                
            ),
            $ar
            array(
                'email' => 'emailvip52@gmail.com',
                'name' => 'Keyon Whiteside',
                'type' => 'cc'
            ),
            array(
            	'email' =>'keyon5052@gmail.com',
            	'name' =>'Keyon Whiteside',
            	'type' =>'cc'
            )
            
		);*/
		//$message->headers = $headers;
		/*$message2->track_opens = true;
		if($mandrill->messages->send($message2))
			{
				//var_dump("Sent");
				$returnmsg="EmailSent";
				return $returnmsg;
			}else{
				$returnmsg="Mail Did't Send";
				return $returnmsg;
			}
		}*/
}
public function  GetPendingAccountInfo($accountype,$unine)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getpendingInfo = $sclass->GetPendingAccountInfo($accountype,$unine);
	return $getpendingInfo;
}
public function UpdatePendingAccountInfo($accountNumber,$unine,$accntStat)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$updateAccount =  $sclass->UpdatePendingAccountInfo($accountNumber,$unine,$accntStat);
	return $updateAccount;
}
public function QueSubEmailTemplate($adminemail,$useremail,$name,$accounttype, $unine, $accntnum)
{
	//var_dump("here");
	require_once('Mandrill.php');
	require_once("EmailTemplate.php");
	require("consts.php");
$emailtemp = new EmailTemplates();
$mandrill = new Mandrill($MandrillPW);

	$pretext="You Have A Message From Park Avenue Concierge Medicine | Finalize Your".$accounttype." Account.";
	$subjectline="Account Pending, Please Finalize Your New EMR Account";
	$headtext="<h2 style='font-size: 1.5em;'>".$name; //add ', RN'
	$accounttype == "Registered Nurse" ? $headtext .= ", RN</h2>" : $headtext .= "</h2>"; 
	$utm= $CURR_ADDR . "/#/verifyNurse?accountType=".$accounttype."&unique=".$unine."&accntnum=".$accntnum;
	$html="<h2>To access your EMR account, please complete and submit your <br><a href=\"".$utm."\">Nurse Verification Form </a></h2>";
	$emailmessage = $html;/*$recipMsg;*/
    	
    	//var_dump($emailmessage);
    	$email = $emailtemp->CustomerEmail($pretext, $headtext, $emailmessage, $utm);
    	//var_dump($email);
    	//$email2 = $emailtemp->DonarEmail($pretext2, $headfile,$dnremailmessage);
    	//var_dump($email2);
    	//I should have the HTML EMAIL to attached to the Mandrill body section 

    	 
			 $message2 = new stdClass();
		$message2->html =$email;
		
		//$message->text = "text body";
		$message2->subject =$subjectline;
		$message2->from_email = "developers@willowmarketing.com";
		$message2->from_name  = "PACM EMR Portal";
		$message2->to = array(
			array(
                'email' => 'trishant@pacmny.com',
                'name' => 'Trishant',
                'type' => 'cc'
            ),
            array(
            	'email' =>'trishantchhetry123@gmail.com',
            	'name' =>'Trishant',
            	'type' =>'cc'
			),
			// array(
            //     'email' => 'jmulvehill@pacmny.com',
            //     'name' => 'Dr. Mulvehill',
            //     'type' => 'cc'
            // ),
            array(
            	'email' =>$useremail,
            	'name' =>$name,
            	'type' =>'cc'
            )
		);
		//$message->headers = $headers;
		$message2->track_opens = true;

		if($mandrill->messages->send($message2))
			{
				//var_dump("Sent");
				$returnmsg="Sent";
				return $returnmsg;
			}else{
				$returnmsg="Fail";
				return $returnmsg;
			}
		//return $response2;

}

public function QueEmailTemplate($emailAddr,$name,$type,$writer)
{
	//var_dump("here");
	require_once('Mandrill.php');
	include("EmailTemplate.php");
	require("consts.php");
	$emailtemp = new EmailTemplates();
	$mandrill = new Mandrill($MandrillPW);
	$pretext="You Have A Message From Park Avenue Concierge Medicine";
	$subjectline=" Note - " . date("m/d/Y");
	if ($type == "Nursing" || $type == "Providers"){
		$subjectline = $type . $subjectline;
	}
	else {
		$subjectline = "Vitals" . $subjectline;
	}
	$headtext="<h1>Hello ".$name."</h1>";
	$utm=$CURR_ADDR . "/#/login?dest=notes";
	$html="<h2>Please <a href=\"" . $utm . "\">login</a> and review</h2> your note written by " . $writer;
	$emailmessage = $html;/*$recipMsg;*/
    	
    	//var_dump($emailmessage);
    	$email = $emailtemp->CustomerEmail($pretext, $headtext, $emailmessage, $utm);
    	//var_dump($email);
    	//$email2 = $emailtemp->DonarEmail($pretext2, $headfile,$dnremailmessage);
    	//var_dump($email2);
    	//I should have the HTML EMAIL to attached to the Mandrill body section 

    	 
			 $message2 = new stdClass();
		$message2->html =$email;
		
		//$message->text = "text body";
		$message2->subject =$subjectline;
		$message2->from_email = "developers@willowmarketing.com";
		$message2->from_name  = "PACM Patient Portal";
		$message2->to = array(
			array(
                'email' => $emailAddr,
                'name' => $name,
				'type' => 'to',
                
            )
		);
		//$message->headers = $headers;
		$message2->track_opens = true;

		if($mandrill->messages->send($message2))
			{
				//var_dump("Sent");
				$returnmsg="Sent";
				return $returnmsg;
			}else{
				$returnmsg="Fail";
				return $returnmsg;
			}
		//return $response2;

}
public function SendPhysicianEmailTemplate($ordernumber,$primephysician)
{
	//var_dump("here");
	require_once('Mandrill.php');
	include("EmailTemplate.php");
	require("consts.php");
$emailtemp = new EmailTemplates();
$mandrill = new Mandrill('md-s1z0SU7cJO2IqKUV10TPQw');
	$e = new EmailTemplates();
	$pretext="Pacmny Notifiction - Dr. ".$primephysician." "." You Have A New Order That Needs to Be Signed. Priority Level - Urgent";
	$subjectline="Verbal Order Needs Approval/Signed";
	$html="<h2>Hi".$primephysician."</h2><p>You have a Verbal Order from Park Avenue Concerige Medecine that needs your attention. Please login and review</p>";
	$headtext="<h1>Verbal Order Needs Signature";
	$utm=$CURR_ADDR . "/#/login";
	$emailmessage = $html;/*$recipMsg;*/
    	
    	//var_dump($emailmessage);
    	$email = $emailtemp->CustomerEmail($pretext, $headtext, $emailmessage, $utm);
    	//var_dump($email);
    	//$email2 = $emailtemp->DonarEmail($pretext2, $headfile,$dnremailmessage);
    	//var_dump($email2);
    	//I should have the HTML EMAIL to attached to the Mandrill body section 

    	 
			 $message2 = new stdClass();
		$message2->html =$email;
		
		//$message->text = "text body";
		$message2->subject =$subjectline;
		$message2->from_email = "developers@willowmarketing.com";
		$message2->from_name  = "PACM Patient Portal";
		$message2->to = array(
			// array(
            //     'email' =>"jmulvehill@pacmny.com",
            //     'name' => "Dr J",
			// 	'type' => 'to',
                
            // ),
            array(
                'email' => 'trishant@pacmny.com',
                'name' => 'Trishant',
                'type' => 'cc'
            ),
            array(
            	'email' =>'keyon5052@gmail.com',
            	'name' =>'Keyon Whiteside',
            	'type' =>'cc'
            )
            
		);
		//$message->headers = $headers;
		$message2->track_opens = true;

		if($mandrill->messages->send($message2))
			{
				//var_dump("Sent");
				$returnmsg="EmailSent";
				return $returnmsg;
			}else{
				$returnmsg="Mail Did't Send";
				return $returnmsg;
			}
		//return $response2;

}
public function ProcessSystemData($pdata)
{
	$postdata = $pdata;

	//lets send postdata to the ForData table
	$response = $this->SendTSResponse($postdata);

	//lets put this data into the database
	$m = $response["measureid"];
	$grpid =  $response["groupdid"];

	$pid = $response["patientID"];
	$recid = $response["recordid"];
	 $mdata = $m->MeasureData["MDataID"];
	 $remark = $m->MeasureData["Remark"];
	$mdtTime =  $m->MeasureData["MDateTime"];  
	$mdtTimeUtc = $m->MeasureData["MDateTimeUTC"]; 
	 $MType = $m->MeasureData["MType"];
	 $MSlot = $m->MeasureData["MSlot"];
	$MValue1 =  $m->MeasureData["MValue1"];
	$MValue2 =$m->MeasureData["MValue2"];
	 $MValue3 = $m->MeasureData["MValue3"];
	 $MRefNote1 =  $m->MeasureData["MRefNote1"];
	 $MRefNote2  =  $m->MeasureData["MRefNote2"];
	 $MRefNote3 = $m->MeasureData["MRefNote3"];
	 $MRefNote4 = $m->MeasureData["MRefNote4"];
	 $MNote = $m->MeasureData["MNote"];
	 $MPDateTime = $m->MeasureData["MPDateTime"];
	 $MPDateTimeUTC = $m->MeasureData["MPDateTimeUTC"];
	 $MDeviceType = $m->MeasureData["MDeviceType"];
	 $MDeviceID = $m->MeasureData["MDeviceID"];
	 $MMeterNote = $m->MeasureData["MMeterNote"];
	$custfield1 ="";
	$custfield2 = "";
	$status ='Pending';
	$forareturnStatus="";//For Return Status variable
	 if($pid ==null || $pid==' ' || $recid==null || $recid==' ')
	 {
		 $forareturnStatus="F";
		 $foraresponse = $this->ReturnResponse($response,$forareturnStatus);
		 if($foraresponse !="")
		 {
			 return $foraresponse;
		 }
	 }
	 else{
			 $insertapidata = $this->InsetForAPIData($grpid,$pid,$recid,$mdata,$remark,$mdtTime,$mdtTimeUtc,$MType,$MSlot,$MValue1,$MValue2,$MValue3,$MRefNote1,$MRefNote2,$MRefNote3,$MRefNote4,$MNote,$MPDateTime,$MPDateTimeUTC, $MDeviceType,$MDeviceID, $MMeterNote, $custfield1,$custfield2,$status);
				 //logging response from being inserted
				 $dg = fopen("inc/database.xml","w+");
				 fwrite($dg,$insertapidata);
				 fclose($dg);
			 if($insertapidata =="Inserted")
				 {
				 // var_dump($insertapidata);exit();
					 // now add notification alert information to the database 
					 $notificationAlert = "New Patient Data for"." ".$pid;

					 $alrtstatus = $this->InsertNotification($pid,$notificationAlert);
					 //if the alert notification is inserted successfully then we need to send a response
					 //back to Fora and then wrie a chrone job to probe the database incase the connection isn't
					 //consistant. We also then need to call the Pacmny Webhook URL to notify
					 //calling webhook
				 /* if($alrtstatus =="Inserted")
					 {*/
						 //get notifcation data
						 $notification = $this->GetNotifications();
					 // var_dump($notification);exit();
						 $jsonData = json_encode($notification,JSON_PRETTY_PRINT);


						 //var_dump($fl);

					 // var_dump(array("msg"=>$jsonData));
						 $ch = curl_init($this->webhookURL);
						 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // Returns response data
						 curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // use HTTP POST to send form data
						 $rresponse = curl_exec($ch);
						 $forareturnStatus="";
						 //if($rresponse->data !=null || $rresponse->data !="")
						 if($rresponse->data=="")
						 {
							 //$forareturnStatus="S";
							 //now lets update the database and the current record Pending Status
							 $newStatus="Updated";
							 //now lets update jsondata array
							 foreach($notification as $n)
							 {
								 $update = $this->UpdateForaDataByID($pid,$n["recordId"],$newStatus);
							 // var_dump($update);exit();
								 if($update =="Updated")
								 {
									 $forareturnStatus="S";
								 }
							 }

						 }
						 else{
							 $forareturnStatus="F";
						 }
						 $info = curl_getinfo($ch);
						 $fp = fopen('inc/database.xml','w+');
						 fwrite($fp,$rresponse);
						 //fwrite($fp,$jsonData);
						 fclose($fp);
				 /* }*/
				 // $forareturnStatus="S";
					 $foraresponse = $this->ReturnResponse($response,$forareturnStatus);
					 if($foraresponse !="")
					 {
						 //var_dump($foraresponse);
						 return $foraresponse;
					 }
					 //now send notification to Webhook
				 }
				 else{
					 //there was an error somwhere and the data didn't get inserted. Send back failed status
					 $forareturnStatus="F";
					 $foraresponse = $this->ReturnResponse($response,$forareturnStatus);
					 if($foraresponse !="")
					 {
						 return $foraresponse;
					 }
				 }
			 }
}
private function SaveDataToFile($postdata)
    {
        /*Where to file find*/
        //var_dump($postdata);
        $dirToScan="inc";
        $filename="database.xml";
        $filepath=  $dirToScan."/".$filename;
        $findfile = scandir($dirToScan);
        $status="na";
       // $mystring = (string) $postdata;
        //$xml="";
        if(is_array($findfile))
        {

            foreach($findfile as $fl)
            {

                if($fl=="database.xml")//if you find the file 
                {
                    //lets open it 
                    //var_dump($fl);
                    $fp = fopen('inc/database.xml','w+');
                    fwrite($fp,json_encode($postdata,JSON_PRETTY_PRINT));
                    fclose($fp);
                    /*$xml = simplexml_load_file($filepath);
                    
                    //navigate down to the node I want
                    $filedata = $xml->filedata;
                    $filedata->addChild("date",date('m-d-Y'));
                    $filedata->addChild("fileinfo",$postdata);//need to replace with actual post data information 
                    $xml->asXML($filepath);*/
                    $status="successful";
                    break;
                }
            }
            return $status;
        }
    }
private function SendTSResponse($postdata)
    {
        //parse data
        if(is_array($postdata))
        {
            $pdata = json_encode($postdata);
        }
        if(is_object($postdata))
        {
            $pdata = $postdata;
        }
        //now decode the data 
       // var_dump($pdata);
        $getData = $this->GetJsonData($pdata);
        //var_dump($getData);echo"Here";exit();
        return $getData;

    }
	private function ReturnResponse($rdata,$status)
    {

       $foraStatus = $status;//S,F
        if(is_array($rdata))
        {
            //var_dump("isobject");
            //now build the response object
            $measureData = $rdata["measureid"];
           // var_dump($measureData);
            $jsondata = new stdClass();
            $jsondata->ResultData= new stdClass();
            $jsondata->ResultData->GroupID= $rdata["groupdid"];
            $jsondata->ResultData->PatientID=$rdata["patientID"];
            $jsondata->ResultData->RecID= $rdata["recordid"];
            $jsondata->ResultData->MeasureData =  json_encode((object)array (

                "MDataID"=>$measureData->MeasureData["MDataID"],
                "Remark"=>$measureData->MeasureData["Remark"],
                "Oobservationid"=>rand(0,100000),//$measureData[0]->MDateTime,
                "Owarnings"=>array(),//$measureData[0]->MDateTimeUTC,
                "OStatus"=>$foraStatus,//$measureData[0]->MType,
                "OResultCode"=>"200",//$measureData[0]->MSlot,
                "OResultMsg" =>"",// $measureData[0]->MValue1,*/



             ),JSON_PRETTY_PRINT);

            $jsondata->ResultData->OStatus="S";
            $jsondata->ResultData->OResultCode="W00";
            $jsondata->ResultData->OResultMsg="";
           // var_dump($jsondata);
            return $jsondata;
        }

    }
	private function GetJsonData($parseData)
    {

       $jdecoded = $parseData;//json_decode($parseData);

        if(is_object($jdecoded))
        {

            //var_dump($jdecoded->GroupdID." "."Keyon");
            $measureData = $jdecoded->UploadData->MeasureData;// json_decode($jdecoded->UploadData->MeasureData);
            //var_dump($measureData->MDataID." "."MID-keyon");
           // var_dump($measuerData[0]->MDataID."MID-TEST");
            //get data from ojbect 
            $groupID = $jdecoded->UploadData->GroupID;
            $patientID = $jdecoded->UploadData->PatientID;
            $recordID = $jdecoded->UploadData->RecID;
            $measuredD = new stdClass();
            $measuredD->MeasureData = array(
                "MDataID"=>$measureData[0]->MDataID,
                "Remark"=>$measureData[0]->Remark,
                "MDateTime"=>$measureData[0]->MDateTime,
                "MDateTimeUTC"=>$measureData[0]->MDateTimeUTC,
                "MType"=>$measureData[0]->MType,
                "MSlot"=>$measureData[0]->MSlot,
                "MValue1" => $measureData[0]->MValue1,
                "MValue2"=>$measureData[0]->MValue2,
                 "MValue3" => $measureData[0]->MValue3,
                 "MRefNote1" => $measureData[0]->MRefNote1,
                 "MRefNote2" => $measureData[0]->MRefNote2,
                 "MRefNote3" => $measureData[0]->MRefNote3,
                 "MRefNote4" => $measureData[0]->MRefNote4,
                 "MNote" => $measureData[0]->MNote,
                 "MPDateTime" => $measureData[0]->MPDateTime,
                 "MPDateTimeUTC" => $measureData[0]->MPDateTimeUTC,
                 "MDeviceType" => $measureData[0]->MDeviceType,
                 "MDeviceID" => $measureData[0]->MDeviceID,
                 "MMeterNote" => $measureData[0]->MMeterNote
            );

            //put data into an array and return
            $responseAr = array("groupdid"=>$groupID,"patientID"=>$patientID,"recordid"=>$recordID,"measureid"=>$measuredD);
             //var_dump($responseAr);exit();
            return $responseAr;

        }
    }

public function GetAddPatientInfoData($obj)
{
	$result = $this->ParsePatientInfo($obj);
	if(is_array($result) && !empty($result))
	{
		//lets see if we can run the poublic function here 
		
		$apiresult = $this->AddPatientInformation($result["pid"],$result["paccount"],$result["ppassword"],$result["psaid"],$result["ptz"],$result["pname"],$result["pbday"],$result["psex"],$result["pheight"],$result["pweight"],$result["ptel1"],$result["ptel2"],$result["pemail"],$result["pTgetup"],$result["ptbreakfast"],$result["ptlunch"],$result["ptdinner"],$result["ptsleep"],$result["paddr"],$result["prace"],$result["metertype"],$result["meterexid"],$result["meterid"],$result["gatewayid"],$obj->icd);
		return $apiresult;
	}
	else{
		//return array with message 
		$msgar = array("message"=>"PatientInformation Object is null or empty");
		return $msgar;
	}
}
public function GetAddPatientDeviceData($obj)
{
	$arr = array("pid"=>$obj->pid, "metertype"=>$obj->metertype, "meterexid"=>$obj->meterexid, "meterid"=>$obj->meterid, "gatewayid"=>$obj->gatewayid, "icd"=>$obj->icd);
	if(is_array($arr) && !empty($arr))
	{
		$apiresult = $this->AddPatientDevice($arr["pid"],$arr["metertype"],$arr["meterexid"],$arr["meterid"],$arr["gatewayid"],$arr["icd"]);
		return $apiresult;
	}
	else {
		$msgar = array("message"=>"DeviceInformation Object is null or empty");
		return $msgar;
	}
}

public function GetRemovePatientDeviceData($obj)
{
	$arr = array("pid"=>$obj->pid, "metertype"=>$obj->metertype, "meterexid"=>$obj->meterexid, "meterid"=>$obj->meterid);
	if(is_array($arr) && !empty($arr))
	{
		$apiresult = $this->RemovePatientDevice($arr["pid"],$arr["metertype"],$arr["meterexid"],$arr["meterid"]);
		return $apiresult;
	}
	else {
		$msgar = array("message"=>"DeviceInformation Object is null or empty");
		return $msgar;
	}
}

public function GetPatientDeviceData($obj)
{
	$arr = array("pid"=>$obj->pid);
	if(is_array($arr) && !empty($arr))
	{
		$apiresult = $this->GetPatientDevice($arr["pid"]);
		return $apiresult;
	}
	else {
		$msgar = array("message"=>"PatientInformation Object is null or empty");
		return $msgar;
	}
}

private function ParsePatientInfo($obj)
{
	
	$patientdata = $obj;
	$pid =$patientdata->pid;
	$paccount = $patientdata->paccount;
	$ppassword =$patientdata->ppassword;
	$psaid =$patientdata->psaid;
	$ptz =$patientdata->ptz; 
	$pname=$patientdata->pname;
	$pbday= $patientdata->pbday;
	$psex = $patientdata->psex;
	$pheight=$patientdata->pheight;
	$pweight =$patientdata->pweight;
	$ptel1 =$patientdata->ptel1;
	$ptel2 = $patientdata->ptel2;
	$pemail =$patientdata->pemail;
	$pTgetup =$patientdata->pTgetup;
	$ptbreakfast =$patientdata->ptbreakfast;
	$ptlunch =$patientdata->ptlunch;
	$ptdinner =$patientdata->ptdinner;
	$ptsleep =$patientdata->ptsleep;
	$paddr =$patientdata->paddr;
	$prace = $patientdata->prace;
	$metertype =$patientdata->metertype; 
	$meterexid =$patientdata->meterexid;
	$meterid =$patientdata->meterid; 
	$gatewayid =$patientdata->gatewayid;
	$patientar = array("pid"=>$pid,"paccount"=>$paccount,"ppassword"=>$ppassword,"psaid"=>$psaid,"ptz"=>$ptz,"pname"=>$pname,"pbday"=>$pbday,"psex"=>$psex,"pheight"=>$pheight,"pweight"=>$pweight,"ptel1"=>$ptel1,"ptel2"=>$ptel2,"pemail"=>$pemail,"pTgetup"=>$pTgetup,"ptbreakfast"=>$ptbreakfast,"ptlunch"=>$ptlunch,"ptdinner"=>$ptdinner,"ptsleep"=>$ptsleep,"paddr"=>$paddr,"prace"=>$prace,"metertype"=>$metertype,"meterexid"=>$meterexid,"meterid"=>$meterid,"gatewayid"=>$gatewayid);
	return $patientar;
}

public function GetPatientDevice($pid)
{
	$webURL="https://telehealth.foracare.com/WebService/WS_DataInterchangeService.asmx?WSDL";
	$paramar = array("Username"=>"pacmapi812","Password"=>"ckT3fw7xacw913");

	$xmlformat3="<soap:Envelope xmlns:soap='http://www.w3.org/2003/05/soap-envelope' xmlns:tdc='http://www.tdcare.com/'>
		<soap:Header>
			<tdc:sValidationSoapHeader>
				<tdc:Username>pacmapi812</tdc:Username>
				<tdc:Password>cE8cwLg25zU</tdc:Password>
			</tdc:sValidationSoapHeader>
		</soap:Header>
		<soap:Body>
			<tdc:DataInterchange>
				<tdc:iCMDType>Q0004</tdc:iCMDType>
				<tdc:iData><![CDATA[<?xml version='1.0' encoding='utf-8' ?>
	<QueryData> 
		<Account>pacm4soaponly</Account> 
		<Password>Cdow7Sewkc12</Password> 
		<QCase>";
		$xmlwrite = "<PID>".$pid."</PID>
		</QCase>
	</QueryData> ]]>
	</tdc:iData>
			</tdc:DataInterchange>
		</soap:Body>
	</soap:Envelope>";

	$xtry = (string) $xmlwrite;

	$xmlformat4 = $xmlformat3.$xtry;
	//header information for the call 
	$headers = array(
	"POST /WebService/WS_DataInterchangeService.asmx?WSDL HTTP/1.1",
	"Host: telehealth.foracare.com",
	"SOAPAction: http://www.tdcare.com/DataInterchange ",
	"Accept: gzip,deflate",
	"Cache-Control: no-cache",
	"Content-Type: text/xml; charset=UTF-8",
	"Content-Length: ".strlen($xmlformat4),
	);

	$ch = curl_init($webURL);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERPWD, $paramar["Username"].":".$paramar["Password"]);
	curl_setopt($ch,CURLOPT_POST, 1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$xmlformat4);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$execute = curl_exec($ch);
	$xx = substr($execute, strpos($execute, '&lt;QueryResult'), -78);
	// $execute = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $execute);

	// $xml = simplexml_load_string($execute);
	$parsed = htmlspecialchars_decode($xx, ENT_HTML5);
	// // $xml2 = substr($xml, 440, 489);
	// $json = json_encode($xml);
	// $json = htmlspecialchars_decode($execute);
	// $json = json_encode($json);
	// $array = json_decode($json, true);
	//print_r($test);
	$msg = array("ReMsg"=>$parsed);
	$mssg = json_encode($msg);
		return $mssg;
	//return $execute;
}

public function RemovePatientDevice($pid, $metertype, $meterexid, $meterid)
{
	$webURL="https://telehealth.foracare.com/WebService/WS_DataInterchangeService.asmx?WSDL";
	$paramar = array("Username"=>"pacmapi812","Password"=>"ckT3fw7xacw913");

	$xmlformat3="<soap:Envelope xmlns:soap='http://www.w3.org/2003/05/soap-envelope' xmlns:tdc='http://www.tdcare.com/'>
		<soap:Header>
			<tdc:sValidationSoapHeader>
				<tdc:Username>pacmapi812</tdc:Username>
				<tdc:Password>cE8cwLg25zU</tdc:Password>
			</tdc:sValidationSoapHeader>
		</soap:Header>
		<soap:Body>
			<tdc:DataInterchange>
				<tdc:iCMDType>D0001</tdc:iCMDType>
				<tdc:iData><![CDATA[<?xml version='1.0' encoding='utf-8' ?>
	<DelData> 
		<Account>pacm4soaponly</Account> 
		<Password>Cdow7Sewkc12</Password> 
		<DCase>";
		$xmlwrite = "<CaseMeterData PID=\"".$pid."\">
		<Meter>
		<MeterType>".$metertype."</MeterType>
		<MeterExID>".$meterexid."</MeterExID> 
		<MeterID>".$meterid."</MeterID> 
		</Meter>
		</CaseMeterData>
		</DCase>
	</DelData> ]]>
	</tdc:iData>
			</tdc:DataInterchange>
		</soap:Body>
	</soap:Envelope>";

	$xtry = (string) $xmlwrite;

	$xmlformat4 = $xmlformat3.$xtry;
	//header information for the call 
	$headers = array(
	"POST /WebService/WS_DataInterchangeService.asmx?WSDL HTTP/1.1",
	"Host: telehealth.foracare.com",
	"SOAPAction: http://www.tdcare.com/DataInterchange ",
	"Accept: gzip,deflate",
	"Cache-Control: no-cache",
	"Content-Type: text/xml; charset=UTF-8",
	"Content-Length: ".strlen($xmlformat4),
	);

	$ch = curl_init($webURL);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERPWD, $paramar["Username"].":".$paramar["Password"]);
	curl_setopt($ch,CURLOPT_POST, 1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$xmlformat4);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$execute = curl_exec($ch);
	$ex = substr($execute,507,1000);
	$nx = substr($execute,558,80);
	$result = substr($ex,20,31);
	$rcode =htmlspecialchars_decode($result);
	$sxml = simplexml_load_string($rcode);
	$ex = explode("!",$execute);
	$test = substr($ex[1],7,35);
	//print_r($test);
	$msg = array("ReCode"=>$sxml,"ReMsg"=>$test);
	$mssg = json_encode($msg);
		return $mssg;
	//return $execute;
}

public function AddPatientDevice($pid, $metertype, $meterexid, $meterid, $gatewayid, $icd)
{
	$webURL="https://telehealth.foracare.com/WebService/WS_DataInterchangeService.asmx?WSDL";
	$paramar = array("Username"=>"pacmapi812","Password"=>"ckT3fw7xacw913");
	// $post = json_encode($paramar);

	$xmlformat3="<soap:Envelope xmlns:soap='http://www.w3.org/2003/05/soap-envelope' xmlns:tdc='http://www.tdcare.com/'>
		<soap:Header>
			<tdc:sValidationSoapHeader>
				<tdc:Username>pacmapi812</tdc:Username>
				<tdc:Password>cE8cwLg25zU</tdc:Password>
			</tdc:sValidationSoapHeader>
		</soap:Header>
		<soap:Body>
			<tdc:DataInterchange>
				<tdc:iCMDType>A0002</tdc:iCMDType>
				<tdc:iData><![CDATA[<?xml version='1.0' encoding='utf-8' ?>
	<AddNewData> 
		<Account>pacm4soaponly</Account> 
		<Password>Cdow7Sewkc12</Password> 
		<ACase>";
		$xmlwrite = "<CaseMeterData PID=\"".$pid."\">
		<Meter>
		<MeterType>".$metertype."</MeterType>
		<MeterExID>".$meterexid."</MeterExID> 
		<MeterID>".$meterid."</MeterID> 
		<GatewayID>".$gatewayid."</GatewayID> 
		</Meter>
		</CaseMeterData>
		</ACase>
	</AddNewData> ]]>
	</tdc:iData>
			</tdc:DataInterchange>
		</soap:Body>
	</soap:Envelope>";

	$xtry = (string) $xmlwrite;

	$xmlformat4 = $xmlformat3.$xtry;
	//header information for the call 
	$headers = array(
	"POST /WebService/WS_DataInterchangeService.asmx?WSDL HTTP/1.1",
	"Host: telehealth.foracare.com",
	"SOAPAction: http://www.tdcare.com/DataInterchange ",
	"Accept: gzip,deflate",
	"Cache-Control: no-cache",
	"Content-Type: text/xml; charset=UTF-8",
	"Content-Length: ".strlen($xmlformat4),
	);

	$ch = curl_init($webURL);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERPWD, $paramar["Username"].":".$paramar["Password"]);
	curl_setopt($ch,CURLOPT_POST, 1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$xmlformat4);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$execute = curl_exec($ch);
	$ex = substr($execute,507,1000);
	$nx = substr($execute,558,80);
	$result = substr($ex,20,31);
	$rcode =htmlspecialchars_decode($result);
	$sxml = simplexml_load_string($rcode);
	$ex = explode("!",$execute);
	$test = substr($ex[1],7,35);
	//print_r($test);
	$msg = array("ReCode"=>$sxml,"ReMsg"=>$test);
	if ($sxml[0] == "00"){
		require_once("SqlClass.php");
		$sclass = new SQLData();
		
		$therapeutic_meters = array("TD7015");
		$physiological_meters = array("TD3261", "TD1261", "TD1242", "TD3140", "TD4283", "TD8255", "TD2555");
		$therapeutic_cpts = array("98976", "98980", "98981");
		$physiological_cpts = array("99454", "99457", "99458");

		if (in_array($metertype, $therapeutic_meters)){
			foreach($therapeutic_cpts as $tcpt){
				$sclass->AddMeterCpt(array("pid"=>$pid, "meter"=>$meterid, "meterName"=>$metertype, "cpt"=>$tcpt, "icd"=>$icd));
			}
		}
		elseif (in_array($metertype, $physiological_meters)){
			foreach($physiological_cpts as $pcpt){
				$sclass->AddMeterCpt(array("pid"=>$pid, "meter"=>$meterid, "meterName"=>$metertype, "cpt"=>$pcpt, "icd"=>$icd));
			}
		}
	}
	$mssg = json_encode($msg);
		return $mssg;
	//return $execute;
}

public function AddPatientInformation($pid,$paccount,$ppassword,$psaid,$ptz,$pname,$pbday,$psex,$pheight,$pweight,$ptel1,$ptel2,$pemail,$pTgetup,$ptbreakfast,$ptlunch,$ptdinner,$ptsleep,$paddr,$prace,$metertype,$meterexid,$meterid,$gatewayid,$icd)
{
	$pTgetup="06:00";
	$ptbreakfast="08:00";
	$ptlunch="12:00";
	$ptdinner ="18:00";
	$ptsleep="22:00";
	$rnd = rand(100,900);
	$nwmeterid ="325020938".$rnd."FFFF";
	/*Logic for MeterID - If meterid has a value then do nothing
	* If it doesn't have a value then increment the default value
	*/
	if(!empty($meterid) && $meterid !="")
	{
		//do nothing because the value was passed over from the front-end application
	}
	else{
		$meterid =$nwmeterid;

	}
	$webURL="https://telehealth.foracare.com/WebService/WS_DataInterchangeService.asmx?WSDL";
	$paramar = array("Username"=>"pacmapi812","Password"=>"ckT3fw7xacw913");
	$post = json_encode($paramar);


	$xmlformat3="<soap:Envelope xmlns:soap='http://www.w3.org/2003/05/soap-envelope' xmlns:tdc='http://www.tdcare.com/'>
			<soap:Header>
				<tdc:sValidationSoapHeader>
					<tdc:Username>pacmapi812</tdc:Username>
					<tdc:Password>cE8cwLg25zU</tdc:Password>
				</tdc:sValidationSoapHeader>
			</soap:Header>
			<soap:Body>
				<tdc:DataInterchange>
					<tdc:iCMDType>A0001</tdc:iCMDType>
					<tdc:iData><![CDATA[<?xml version='1.0' encoding='utf-8' ?>
		<AddNewData> 
			<Account>pacm4soaponly</Account> 
			<Password>Cdow7Sewkc12</Password> 
			<ACase> 
			<Case>";
			$xmlwrite =" 
			<PID>".$pid."</PID> 
			<PAcct>".$paccount."</PAcct> 
			<PPwd>".$ppassword."</PPwd> 
			<PSAID>".$psaid."</PSAID> 
			<PTZ>".$ptz."</PTZ> 
			<PName>".$pname."</PName> 
			<PBirthday>".$pbday."</PBirthday> 
			<PSex>".$psex."</PSex> 
			<PHeight>".$pheight."</PHeight> 
			<PWeight>".$pweight."</PWeight> 
			<PTel1>".$ptel1."</PTel1> 
			<PTel2>".$ptel2."</PTel2> 
			<PEMail>".$pemail."</PEMail> 
			<PTGetUp>".$pTgetup."</PTGetUp> 
			<PTBreakfast>".$ptbreakfast."</PTBreakfast> 
			<PTLunch>".$ptlunch."</PTLunch> 
			<PTDinner>".$ptdinner."</PTDinner> 
			<PTSleep>".$ptsleep."</PTSleep> 
			<PAddr>".$paddr."</PAddr> 
			<PRaceNo>".$prace."</PRaceNo>  
			<MeterData> 
			<Meter> 
			<MeterType>".$metertype."</MeterType> 
			<MeterExID>".$meterexid."</MeterExID> 
			<MeterID>".$meterid."</MeterID> 
			<GatewayID>".$gatewayid."</GatewayID> 
			</Meter> 
			</MeterData> 
			</Case> 
			</ACase> 
		</AddNewData> ]]>
		</tdc:iData>
				</tdc:DataInterchange>
			</soap:Body>
		</soap:Envelope>";

		$xtry = (string) $xmlwrite;

		$xmlformat4 = $xmlformat3.$xtry;
		//header information for the call 
		$headers = array(
		"POST /WebService/WS_DataInterchangeService.asmx?WSDL HTTP/1.1",
		"Host: telehealth.foracare.com",
		"SOAPAction: http://www.tdcare.com/DataInterchange ",
		"Accept: gzip,deflate",
		"Cache-Control: no-cache",
		"Content-Type: text/xml; charset=UTF-8",
		"Content-Length: ".strlen($xmlformat4),
		); 
		//make the curl call
		$ch = curl_init($webURL);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $paramar["Username"].":".$paramar["Password"]);
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xmlformat4);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$execute = curl_exec($ch);
		$ex = substr($execute,507,1000);
		$nx = substr($execute,558,80);
		$result = substr($ex,20,31);
		$rcode =htmlspecialchars_decode($result);
		$sxml = simplexml_load_string($rcode);
		$ex = explode("!",$execute);
		$test = substr($ex[1],7,35);
		//print_r($test);
		$msg = array("ReCode"=>$sxml,"ReMsg"=>$test);
		if ($sxml[0] == "00"){
			require_once("SqlClass.php");
			$sclass = new SQLData();
			
			$therapeutic_meters = array("TD7015");
			$physiological_meters = array("TD3261", "TD1261", "TD1242", "TD3140", "TD4283", "TD8255", "TD2555");
			$therapeutic_cpts = array("98976", "98980", "98981");
			$physiological_cpts = array("99454", "99457", "99458");

			if (in_array($metertype, $therapeutic_meters)){
				foreach($therapeutic_cpts as $tcpt){
					$sclass->AddMeterCpt(array("pid"=>$pid, "meter"=>$meterid, "meterName"=>$metertype, "cpt"=>$tcpt, "icd"=>$icd));
				}
			}
			elseif (in_array($metertype, $physiological_meters)){
				foreach($physiological_cpts as $pcpt){
					$sclass->AddMeterCpt(array("pid"=>$pid, "meter"=>$meterid, "meterName"=>$metertype, "cpt"=>$pcpt, "icd"=>$icd));
				}
			}
		}
		$mssg = json_encode($msg);
			return $mssg;
		//return $execute;

}
public function  GetPatientProviderAssignments($accountNum,$providerid)
{
  require_once("SqlClass.php");
	$sclass = new SQLData();
	$getassignments = $sclass->GetPatientProviderAssignments($accountNum,$providerid);
	return $getassignments;
}
public function  GetPatientNursAssignments($accountNum,$nurseid)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getassignments = $sclass->GetPatientNursAssignments($accountNum,$nurseid);
	return $getassignments;
}
public function GetIndividualAid($accountnumber,$certificatenumber)
{
  require_once("SqlClass.php");
	$sclass = new SQLData();
	$getNurseInfo = $sclass->GetIndividualAid($accountnumber,$certificatenumber);
	return $getNurseInfo;
}
public function GetIndividualNurse($accountnumber,$licensenumber)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getNurseInfo = $sclass->GetIndividualNurse($accountnumber,$licensenumber);
	return $getNurseInfo;
}
public function  GetAccountPatients($accountnumber)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getpatientInfo = $sclass-> GetAccountPatients($accountnumber);
	return $getpatientInfo;
}
public function GetNurseAidList($accountNum)
{
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $getRec = $sclass->GetNursAidData($accountNum);
  return $getRec;
}
public function GetNurseList($accountNum)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getRec = $sclass->GetNursData($accountNum);
	return $getRec;
}
public function GetPatientNurseList($accountNum,$patientid)
{
	
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$nwnurseAr[] = array();
	$getRec = $sclass->GetPatientNursData($accountNum,$patientid);
	if(!empty($getRec))
	{
		//now get Information for nurse to fill out nurses card 
		foreach($getRec["nurseInfo"] as $r)
		{
			//var_dump($r["nurse_id"]);
			$getNurseInfo = $sclass->GetNursDataByPersonalID($r["nurse_id"]);
			//var_dump($getNurseInfo);
			$nwnurseAr[] = array("firstname"=>$r["firstname"],"lastname"=>$r["lastname"],"nurseid"=>$r["nurse_id"],"email"=>$getNurseInfo["nurseInfo"][0]["email"],"primaryphone"=>$getNurseInfo["nurseInfo"][0]["primaryphone"],"licensenum"=>$getNurseInfo["nurseInfo"][0]["licensenum"],"licensetype"=>$getNurseInfo["nurseInfo"][0]["licensetype"],"address"=>$getNurseInfo["nurseInfo"][0]["addr1"],"city"=>$getNurseInfo["nurseInfo"][0]["city"],"state"=>$getNurseInfo["nurseInfo"][0]["state"],"zip"=>$getNurseInfo["nurseInfo"][0]["zip"],"licenseexpdate"=>$getNurseInfo["nurseInfo"][0]["licenseexpdate"]);
		}
		//var_dump(array_filter($nwnurseAr));
		return array_filter($nwnurseAr);
	}
	//return $getRec;
}
public function GetAidAssignmentList($accountNum)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getRec = $sclass->GetAidAssignmentNursData($accountNum);
	return $getRec;
	
}
public function GetNurseAssignmentList($accountNum)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getRec = $sclass->GetAssignmentNursData($accountNum);
	return $getRec;
	
}
public function DischargeNurseByNurseID($pid,$accountnumber,$nurseid)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	//var_dump("Here");
	$discharge = $sclass->DischargeNurseByNurseID($pid,$accountnumber,$nurseid);
	//var_dump($discharge);
	if($discharge !="" && is_array($discharge))
	{
		return $discharge;
	}
	else{
		var_dump("Issue"." ".$discharge);//for testing but shouldn't need this after QA
		return $discharge;
	}
}
public function AssignPatientToNurse($sid,$accountnumber,$patientar)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$assign = $sclass->AssignPatientToNurse($sid,$accountnumber,$patientar);
	if($assign !="" && is_array($assign))
	{
		return $assign;
	}
	else{
		var_dump("Issue"." ".$assign);//for testing but shouldn't need this after QA
		return $assign;
	}
}
public function AssignPatientToProvider($pid,$accountnumber,$patientar)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$assign = $sclass->AssignPatientToProvider($pid,$accountnumber,$patientar);
	if($assign !="" && is_array($assign))
	{
		return $assign;
	}
	else{
		var_dump("Issue"." ".$assign);//for testing but shouldn't need this after QA
		return $assign;
	}
}
public function AssignNurseToPatient($pid,$accountnumber,$nursar)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$assign = $sclass->AssignNurseToPatient($pid,$accountnumber,$nursar);
	if($assign !="" && is_array($assign))
	{
		return $assign;
	}
	else{
		var_dump("Issue"." ".$assign);//for testing but shouldn't need this after QA
		return $assign;
	}
}
/*-GetNurseAssigned TO Patient----------*/
public function GetNurseAssignmentInfo($accountnumber,$unine)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$accntnumber = $accountnumber;
	$patid = $unine;
	//var_dump($patid);
	//$patid = $patientid;
	
	$getInfo = $sclass->GetNurseAssignmentInformaiton($accntnumber,$patid);
	if($getInfo !="" && is_array($getInfo))
	{
		//var_dump($getInfo);
		return $getInfo;
	}
	else{
		var_dump("Issue"." ".$getInfo);//for testing but shouldn't need this after QA
		return $getInfo;
	}

}
public function SaveNewInsurance($subid, $pid, $name, $stDate, $endDate, $incLim, $annLim){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	return $sclass->SaveNewInsurance($subid, $pid, $name, $stDate, $endDate, $incLim, $annLim);
}
public function GetPatientsAssigned($nurseInfo){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	return $sclass->GetPatientsAssigned($nurseInfo);
}
public function GetAllNursePdfs($subid, $pid){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	return $sclass->GetAllNursePdfs($subid, $pid);
}
public function AddNewPdf($subid, $externid, $pdfType, $file, $latest){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	return $sclass->AddNewPdf($subid, $externid, $pdfType, $file, $latest);
}
public function  GetIndividualUserPremissions($username,$accountnumber,$subaccount,$email)
{
	//var_dump("process class GetIndivi");
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$accntnumber = $accountnumber;
	$subnumber = $subaccount;
	$userEmail = $email;
	$userName = $username;
	$fetch =  $sclass->GetIndividualUserPremissions($userName,$accntnumber,$subnumber,$userEmail);
	if($fetch !="" && is_array($fetch))
	{
		return $fetch;
	}
	else{
		$msg = array("message"=>"UserPermisisons Variable is empty");
		return $msg;
	}
}
public function UpdateIndividualUserPremissions($username,$accountnumber,$subaccntnumber,$email,$accounttype,$cando,$cantdo)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$fetch =  $sclass->UpdateIndividualUserPremissions($username,$accountnumber,$subaccntnumber,$emal,$accounttype,$cando,$cantdo);
	if($fetch !="" && is_array($fetch))
	{
		return $fetch;
	}
	else{
		$msg = array("message"=>"UserPermisisons Variable is empty");
		return $msg;
	}
}
public function CheckGeneratedLicense($genlicense)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$checklicense = $sclass->DoesLicenseExist($genlicense);
	if($checklicense["status"]=="true")
	{
		$msg = array("message"=>"License Exist","code"=>"23DNEx");
		return $msg;
	}
	else{
		$msg = array("message"=>"License Does Not Exist","code"=>"23DEx");
		return $msg;
	}
}
public function InsertLicense($genlicense,$accountnumber,$subaccount,$status)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$insert =  $sclass->InsertLicense($genlicense,$accountnumber,$subaccount,$status);
	if($insert !="" && is_array($insert))
	{
		return $insert;
	}
	else{
		$msg = array("message"=>"Insert License Variable is empty");
		return $msg;
	}
}
public function GenerateUserLicense($length=10)
{
	// Define characters that can be used in the license number
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    // Get the length of the character set
    $charLength = strlen($characters);

    // Initialize the license number
    $licenseNumber = '';

    // Generate random characters to form the license number
    for ($i = 0; $i < $length; $i++) {
        $licenseNumber .= $characters[rand(0, $charLength - 1)];
    }

    return $licenseNumber;
}
public function  SaveUserPremissions($accountType,$username,$accountnumber,$subaccount,$premar)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$insert = $sclass->SaveUserPremissions($accountType,$username,$accountnumber,$subaccount,$premar);
	if($insert !="" && is_array($insert))
	{
		return $insert;
	}
	else{
		$msg = array("message"=>" Insert User Permisisons not successful","error"=>$insert);
		return $msg;
	}

}
public function GetUserDefaultPremissions($accounttype)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$fetch =  $sclass->GetUserDefaultPremissions($accounttype);
	if($fetch !="" && is_array($fetch))
	{
		return $fetch;
	}
	else{
		$msg = array("message"=>"UserPermisisons Variable is empty");
		return $msg;
	}
}
public function IndividualUserPremissions($accountnumber,$subaccountnumber)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$accntnumber = $accountnumber;
	$subnumber = $subaccountnumber;
	$fetch =  $sclass->IndividualUserPremissions($accntnumber,$subnumber);
	if($fetch !="" && is_array($fetch))
	{
		return $fetch;
	}
	else{
		$msg = array("message"=>"UserPermisisons Variable is empty");
		return $msg;
	}
}
public function NursysBatchGetNurseInfo($jurisdiction,$rnlicense,$ltype,$ncsid)
{	
	$methname ="nurselookup/";
    $api_url="https://api.nursys.com/api/enotify/nurselookup";
    $api_user="parkavenue";
    $api_password="Park@venue987";
   
    $headers = array(
    "POST /api.nursys.com/api/enotify/nurselookup HTTP/1.1",
    "Host: api.nursys.com",
    "Content-Type: application/json",
    "username:".$api_user,
    "password:".$api_password,
    );
	
	/*$mstrobj ='{
        "NurseLookupRequests": [';
		$i=0;
		$lngth = count($nursar); 
		
	foreach($nursar as $r)
	{
		//var_dump($r["licensenum"]);
		$i++;
		
			$i=0;
			$lngth = count($nursar);
		
		$mstrobj .='{
			"JurisdictionAbbreviation": "'.$r["state"].'",
			"LicenseNumber": "'.$r["licensenum"].'",
			"LicenseType": "'.$r["licensetype"].'",
			"NcsbnId": "'.$r["ncsbnid"].'",
			"RecordId": ""
			} ';
		 if( $i < $lngth)
		 {
			$mstrobj .=',';
		 }
		 if($i ==$lngth)
		 {
			// do nothing
		 }
	}
	$mstrobj .=' ]}'; */
    
    $mstrobj ='{
        "NurseLookupRequests": [
        {
        "JurisdictionAbbreviation": "'.$jurisdiction.'",
        "LicenseNumber": "'.$rnlicense.'",
        "LicenseType": "'.$ltype.'",
        "NcsbnId": "'.$ncsid.'",
        "RecordId": ""
        }
        
        ]} ';
	//var_dump($mstrobj);
    $lookupObj = $mstrobj;
    //setup curl information 
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$lookupObj);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $execute = curl_exec($ch);
	
    $info = curl_getinfo($ch);
    $jdata = json_decode($execute);
    $transID = $jdata->Transaction->TransactionId;
    $transSuccess =$jdata->Transaction->TransactionSuccessFlag;
	//var_dump($jdata);
    if($transSuccess==true || $transSuccess=="true")
	{
		//call the private function 

		$countran = rand(0,200000000);
		for($i=0; $i < $countran; $i++)
		{
			$pf = $this->CompleteNursyTransaction($transID,$api_url,$api_user,$api_password);

			//lets check to see if the transaction is still processsiong 
			if($pf->ProcessingCompleteFlag==false && $pf->Transaction->TransactionErrors[0]->ErrorMessage=="The nurse lookup request for the supplied TransactionId has not completed processing. ")
			{
				//do nothing because the transaction hasn't completed but when it does run the else statement
			}
			else{

				$processComplete = $pf->NurseLookupResponses[0]->SuccessFlag;
				$processCompleteFlag = $pf->ProcessingCompleteFlag;
				$hasResponse = count($pf->NurseLookupResponses);
				//var_dump($pf->NurseLookupResponses[0]->NurseLookupLicenses);
				if($processComplete==true && $processCompleteFlag==true && $hasResponse !=0)
				{
					$getnurseAr =$this->GetNurseLookupData($pf->NurseLookupResponses[0]->NurseLookupLicenses);
					if(is_array($getnurseAr) && !empty($getnurseAr))
					{
						return $getnurseAr;
						break;
					}

				}
				else{

						if($processComplete==false && $processCompleteFlag==true && $hasResponse !=0 && $pf->NurseLookupResponses[0]->Errors)
						{
							//should have error messages now 
							$msg = array("msg"=>$pf->NurseLookupResponses[0]->Errors,"status"=>"Result Not Found");
							return $msg;
							break;
						}

					}
			}


		}


	}
	
	
}
public function NursysGetNurseInfo($Ncsid,$Jurisdiction,$rNlicense,$Ltype)
{	
	$methname ="nurselookup/";
    $api_url="https://api.nursys.com/api/enotify/nurselookup";
    $api_user="parkavenue";
    $api_password="Park@venue987";
    $ncsid= $Ncsid;
    $jurisdiction=$Jurisdiction;
    $rnlicense=$rNlicense;
    $ltype=$Ltype;
    $headers = array(
    "POST /api.nursys.com/api/enotify/nurselookup HTTP/1.1",
    "Host: api.nursys.com",
    "Content-Type: application/json",
    "username:".$api_user,
    "password:".$api_password,
    );
    $mstrobj ='{
        "NurseLookupRequests": [
        {
        "JurisdictionAbbreviation": "'.$jurisdiction.'",
        "LicenseNumber": "'.$rnlicense.'",
        "LicenseType": "'.$ltype.'",
        "NcsbnId": "'.$ncsid.'",
        "RecordId": ""
        }
        
        ]
    } ';
    $lookupObj = $mstrobj;
    //setup curl information 
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$lookupObj);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $execute = curl_exec($ch);
	//var_dump($execute);
    $info = curl_getinfo($ch);
    $jdata = json_decode($execute);
	//var_dump($jdata);
    $transID = $jdata->Transaction->TransactionId;
	//var_dump($transID);
    $transSuccess =$jdata->Transaction->TransactionSuccessFlag;

    if($transSuccess==true || $transSuccess=="true")
	{
		//call the private function 

		$countran = rand(0,200000000);
		$time=0;
		sleep(30);//wait 30 seconds before running this script
		$time++;
		for($i=0; $i < $countran; $i++)
		{
			
			if($time==2)
			{
				$msg = array("status"=>"No Server Response","message"=>"Try Again in 2 minutes");
				return $msg;
			}
			$pf = $this->CompleteNursyTransaction($transID,$api_url,$api_user,$api_password);
			
			//lets check to see if the transaction is still processsiong 
			if($pf->ProcessingCompleteFlag==false && $pf->Transaction->TransactionErrors[0]->ErrorMessage=="The nurse lookup request for the supplied TransactionId has not completed processing. ")
			{
				//do nothing because the transaction hasn't completed but when it does run the else statement
				sleep(30);
				$time++;
			}
			else{

				$processComplete = $pf->NurseLookupResponses[0]->SuccessFlag;
				$processCompleteFlag = $pf->ProcessingCompleteFlag;
				$hasResponse = count($pf->NurseLookupResponses);
				if($processComplete==true && $processCompleteFlag==true && $hasResponse !=0)
				{
					$getnurseAr = $this->GetNurseLookupData($pf->NurseLookupResponses[0]->NurseLookupLicenses);
					if(is_array($getnurseAr) && !empty($getnurseAr))
					{
						return $getnurseAr;
						break;
					}

				}
				else{

						if($processComplete==false && $processCompleteFlag==true && $hasResponse !=0 && $pf->NurseLookupResponses[0]->Errors)
						{
							//should have error messages now 
							$msg = array("msg"=>$pf->NurseLookupResponses[0]->Errors,"status"=>"Result Not Found");
							return $msg;
							break;
						}

					}
			}


		}


	}
}
public function GetAccountInformation($accountNum,$userUnique)
{
	require_once("SQlClass.php");
	$sclass = new SQLData();
	$accnt = $accountNum;
	$unine = $userUnique;
	$getaccntInfo = $sclass->GetAccountInformaton($accnt,$unine);
	if(is_array($getaccntInfo) && !empty($getaccntInfo))
	{
		return $getaccntInfo;
	}
	else{
		return $getaccntInfo;
	}
}
public function InsertNurse($account,$extern,$fname,$lname,$username,$licensnum,$licensetype,$ncsbnid,$email,$primephone,$secphone,$addr1,$addr2,$ccity,$sstate,$zzip,$lastfourssn,$bdayear,$hospitalpracsetting,$notficationenabled,$reminderenabled,$locationlist,$activeStatus,$checkstatus,$licenseExp,$insuranceCompany,$insuranceStart,$insuranceEnd,$perIncidentAmnt,$annualAggAmnt)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$LicenseNumber = $licensnum;
	$LicenseType = $licensetype;
	$NcsbnId = $ncsbnid;
	$Email = $email;
	$Address1 =$addr1;
	$Address2 = $addr2;
	$City = $ccity;
	$State = $sstate;
	$Zip = $zzip;
	$LastFourSSN = $lastfourssn;
	$BirthYear = $bdayear;
	$HospitalPracticeSetting = $hospitalpracsetting;
	$NotificationsEnabled = $notficationenabled;
	$RemindersEnabled = $reminderenabled;
	$LocationList = $locationlist;
	$accountnumber = $account;
	$firstname = $fname;
	$lastname = $lname;
	$insetnr =$sclass->InsertNurse($accountnumber,$extern,$firstname,$lastname,$username,$LicenseNumber,
    $LicenseType,$NcsbnId,$Email,$primephone,$secphone,$Address1,$Address2,$City,$State,$Zip,$LastFourSSN,
	$BirthYear,$HospitalPracticeSetting,$NotificationsEnabled,$RemindersEnabled,$LocationList,$activeStatus,$checkstatus,$licenseExp,
	$insuranceCompany,$insuranceStart,$insuranceEnd,$perIncidentAmnt,$annualAggAmnt);
	return $insetnr;
}
public function UpdateNurse($account,$fname,$lname,$licensnum,$licensetype,$ncsbnid,$email,$primephone,$secphone,$addr1,$addr2,$ccity,$sstate,$zzip,$lastfourssn,$bdayear,$hospitalpracsetting,$notficationenabled,$reminderenabled,$locationlist,$activeStatus,$checkstatus)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$LicenseNumber = $licensnum;
	$LicenseType = $licensetype;
	$NcsbnId = $ncsbnid;
	$Email = $email;
	$Address1 =$addr1;
	$Address2 = $addr2;
	$City = $ccity;
	$State = $sstate;
	$Zip = $zzip;
	$LastFourSSN = $lastfourssn;
	$BirthYear = $bdayear;
	$HospitalPracticeSetting = $hospitalpracsetting;
	$NotificationsEnabled = $notficationenabled;
	$RemindersEnabled = $reminderenabled;
	$LocationList = $locationlist;
	$accountnumber = $account;
	$firstname = $fname;
	$lastname = $lname;
	$insetnr =$sclass->UpdateNurse($accountnumber,$firstname,$lastname,$LicenseNumber,
    $LicenseType,$NcsbnId,$Email,$primephone,$secphone,$Address1,$Address2,$City,$State,$Zip,$LastFourSSN,
	$BirthYear,$HospitalPracticeSetting,$NotificationsEnabled,$RemindersEnabled,$LocationList,$activeStatus,$checkstatus);
	return $insetnr;
}
public function UpdateLicenseCheckDate($updateInfo)
{
	require_once("SqlClass.php");
	$sclass =new SQLData();
	$recupdated="";
	foreach($updateInfo as $up)
	{
		//convert the LicenseExpirationDate
		$orgdt  = $up["licensexpirationDt"];
		$ndate = date('Y-m-d',strtotime($orgdt));
		$lastchecked = date('Y-m-d');
		$licen = $up["licensenum"];
		$activestat = $up["activeStatus"];
		$updtNurse = $sclass->UpdateLicenseCheckDate($lastchecked,$ndate,$licen,$activestat);
		if($updtNurse=="Updated")
		{
			//return $updtNurse;
			$recupdated="Updated";
		}
		else{
			$msg = array("messaage"=>$updtNurse);
			return $msg;
		}

	}
	return $recupdated;
	
}
public function RemoveInternalNurse($accountnum,$licensenum)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$remove = $sclass->RemoveInternalNurse($accountnum,$licensenum);
	return $remove;
}
public function ManageNursysList($subactioncode,$jursidicAbbr,$licensnum,$licensetype,$ncsbnid,$email,$addr1,$addr2,$ccity,$sstate,$zzip,$lastfourssn,$bdayear,$hospitalpracsetting,$hostpracsettingother,$notficationenabled,$reminderenabled,$locationlist,$recid)
{
	$methname ="managenurselist";
	$api_url="https://api.nursys.com/api/enotify/".$methname;
	$api_user="parkavenue";
	$api_password="Park@venue987";



/*
assign variables 
*/

	$SubmissionActionCode = $subactioncode ;
	$JurisdictionAbbreviation = $jursidicAbbr;
	$LicenseNumber = $licensnum;
	$LicenseType = $licensetype;
	$NcsbnId = $ncsbnid;
	$Email = $email;
	$Address1 =$addr1;
	$Address2 = $addr2;
	$City = $ccity;
	$State = $sstate;
	$Zip = $zzip;
	$LastFourSSN = $lastfourssn;
	$BirthYear = $bdayear;
	$HospitalPracticeSetting = $hospitalpracsetting;
	$HospitalPracticeSettingOther = $hostpracsettingother;
	$NotificationsEnabled = $notficationenabled;
	$RemindersEnabled = $reminderenabled;
	$LocationList = $locationlist;
	$RecordId = $recid;
	$headers = array(
	"POST /api.nursys.com/api/enotify/managenurselist HTTP/1.1",
	"Host: api.nursys.com",
	"Content-Type: application/json",
	"username:".$api_user,
	"password:".$api_password,
	);
	$mstrobj ='{
	 "ManageNurseListRequests": [
	 {
	 "SubmissionActionCode": "'.$SubmissionActionCode.'",
	 "JurisdictionAbbreviation": "'.$JurisdictionAbbreviation.'",
	 "LicenseNumber": "'.$LicenseNumber.'",
	 "LicenseType": "'.$LicenseType.'",
	 "NcsbnId": "'.$NcsbnId.'",
	 "Email": "'.$Email.'",
	 "Address1": "'.$Address1.'",
	 "Address2": "'.$Address2.'",
	 "City": "'.$City.'",
	 "State": "'.$State.'",
	 "Zip": "'.$Zip.'",
	 "LastFourSSN":"'.$LastFourSSN.'",
	 "BirthYear": "'.$BirthYear.'",
	 "HospitalPracticeSetting": "'.$HospitalPracticeSetting.'",
	 "HospitalPracticeSettingOther": "'.$HospitalPracticeSettingOther.'",
	 "NotificationsEnabled": "'.$NotificationsEnabled.'",
	 "RemindersEnabled": "'.$RemindersEnabled.'",
	 "LocationList": "",
	 "RecordId": ""
	 }
	 
	 ]
	}';
$lookupObj = $mstrobj;


   //setup curl information 
	$ch = curl_init($api_url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	//curl_setopt($ch, CURLOPT_USERPWD, $api_user.":".$api_password);
	curl_setopt($ch,CURLOPT_POST, 1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$lookupObj);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$execute = curl_exec($ch);
	$info = curl_getinfo($ch);
	$jdata = json_decode($execute);
	$transID = $jdata->Transaction->TransactionId;//
	$transSuccess =$jdata->Transaction->TransactionSuccessFlag;// "true";
	 $time=0;
            if($transSuccess==true || $transSuccess=="true")
            {
                //call the private function 
				sleep(30);
				$time++;
                $countran = rand(0,200000000);
                for($i=0; $i < $countran; $i++)
                {

					if($time==2)
					{
						$msg = array("status"=>"No Server Response","message"=>"Try Again in 2 minutes");
						return $msg;
					}

                    $pf = $this->CompleteNursyListTransaction($transID,$api_url,$api_user,$api_password);
			        //var_dump($pf);exit();
                    if($pf->ProcessingCompleteFlag==false && $pf->Transaction->TransactionErrors[0]->ErrorMessage=="The nurse lookup request for the supplied TransactionId has not completed processing. ")
                    {
                        //do nothing because the transaction hasn't completed but when it does run the else statement
						sleep(30);
						$time++;
                    }
                    else{
                        if($pf->ProcessingCompleteFlag==true && $pf->ManageNurseListResponses[0]->SuccessFlag==false && $pf->ManageNurseListResponses[0]->Errors)
                        {
                            $msg = array("errors"=>$pf->ManageNurseListResponses[0]->Errors,"status"=>"Nurse Not Added", "requestObject"=>json_encode($pf->ManageNurseListRequest));
                            return $msg;
                        }
                        else{
                            //lets assume the Nurse was added successfully and now we just need to return the information 
                            if($pf->ProcessingCompleteFlag==true && $pf->ManageNurseListResponses[0]->SuccessFlag==true && count($pf->ManageNurseListResponses[0]->Errors)==0)
                            {
                                //its successful and now return the information 

                                $msg = array("status"=>"Nurse Added","ManageNurseListRequstObj"=>$pf->ManageNurseListResponses[0]->ManageNurseListRequest);
                                return $msg;

                            }


                        }
                    }


                }


            }
}

private function CompleteNursyListTransaction($transid,$apiurl,$apiuser,$apipassword)
{
  //we are going to call THE API with the transaction ID until the ProcessTransitionFlag ==true
	$api_user = $apiuser;
	$api_password = $apipassword;
	$api_url = $apiurl;
    $transID = $transid;
	$nurl = $api_url."?transactionId=".$transID;
	$header2 = array(
	"GET /api.nursys.com/api/enotify/managenurselist HTTP/1.1",
	"Host: api.nursys.com",
	"Content-Type: application/json",
	"username:".$api_user,
	"password:".$api_password,
	);
	$cch = curl_init($nurl);
	curl_setopt($cch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($cch, CURLOPT_HTTPHEADER, $header2);
	$result = curl_exec($cch);
	$getInfo = json_decode($result);
	// var_dump($getInfo);
	return $getInfo;

}

private function CompleteNursyTransaction($transid,$apiurl,$apiuser,$apipassword)
{
  //we are going to call THE API with the transaction ID until the ProcessTransitionFlag ==true
	$api_user = $apiuser;
	$api_password = $apipassword;
	$api_url = $apiurl;
    $transID = $transid;
	$nurl = $api_url."?transactionId=".$transID;
	$header2 = array(
	"GET /api.nursys.com/api/enotify/nurselookup HTTP/1.1",
	"Host: api.nursys.com",
	"Content-Type: application/json",
	"username:".$api_user,
	"password:".$api_password,
	);
	$cch = curl_init($nurl);
	curl_setopt($cch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($cch, CURLOPT_HTTPHEADER, $header2);
	$result = curl_exec($cch);
	$getInfo = json_decode($result);
	//var_dump($getInfo);
	return $getInfo;

}

private function GetNurseListLookupData($lookupar)
{
	//var_dump($lookupar);
	foreach($lookupar as $lc)
	{
		$nurseAr[] = array("message"=>"Nurse Added","SubmissionActionCode"=>$lc->ManageNurseListRequest->SubmissionActionCode,"JurisdictionAbbreviation"=>$lc->ManageNurseListRequest->JurisdictionAbbreviation,"LicenseNumber"=>$lc->ManageNurseListRequest->LicenseNumber,"LicenseType"=>$lc->ManageNurseListRequest->LicenseType,"NcsbnId"=>$lc->ManageNurseListRequest->NcsbnId,"Email"=>$lc->ManageNurseListRequest->Email,"Address1"=>$lc->ManageNurseListRequest->Address1,"Address2"=>$lc->ManageNurseListRequest->Address2,"City"=>$lc->ManageNurseListRequest->City,"State"=>$lc->ManageNurseListRequest->State,"Zip"=>$lc->ManageNurseListRequest->Zip,"LastFourSSN"=>$lc->ManageNurseListRequest->LastFourSSN,"BirthYear"=>$lc->ManageNurseListRequest->BirthYear,"HospitalPracticeSetting"=>$lc->ManageNurseListRequest->HospitalPracticeSetting,"NotificationsEnabled"=>$lc->ManageNurseListRequest->NotificationsEnabled,"RemindersEnabled"=>$lc->ManageNurseListRequest->RemindersEnabled);
	}
	return $nurseAr;


}
private function GetNurseLookupData($lookupar)
{
	//var_dump($lookupar);
	foreach($lookupar as $lc)
	{
		$nurseAr[] = array("FirstName"=>$lc->FirstName,"LastName"=>$lc->LastName,"NcsbnId"=>$lc->NcsbnId,"LicenseType"=>$lc->LicenseType,"JurisdictionAbbreviation"=>$lc->JurisdictionAbbreviation,"Jurisdiction"=>$lc->Jurisdiction,"LicenseNumber"=>$lc->LicenseNumber,"Active"=>$lc->Active,"LicenseStatus"=>$lc->LicenseStatus,"LicenseOriginalIssueDate"=>$lc->LicenseOriginalIssueDate,"LicenseExpirationDate"=>$lc->LicenseExpirationDate,"CompactStatus"=>$lc->CompactStatus,"Messages"=>$lc->Messages,"NurseLookupDisciplines"=>$lc->NurseLookupDisciplines,"NurseLookupNotifications"=>$lc->NurseLookupNotifications,"NurseLookupAdvancedPractices"=>$lc->NurseLookupAdvancedPractices);
	}
	return $nurseAr;
}
public function SetNurseSettings($accountnum,$checkhowoften,$notifyexpdt,$Communicationtypes,$notifytypes,$lastchecked)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$processSettings = $sclass->InsertNurseSettings($accountnum,$checkhowoften,$notifyexpdt,$Communicationtypes,$notifytypes,$lastchecked);
	if($processSettings =="Inserted")
	{
		return $processSettings;
	}
	else{
		return $processSettings["message"];
	}
}
public function GetNurseSettings($accountnumber)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getInfo = $sclass->GetNurseSettings($accountnumber);
	//var_dump($getInfo);
	if($getInfo !="" && is_array($getInfo["records"][0]))
	{
		var_dump("I made it");
		$chkfrequency= $getInfo["records"][0]["checkfrequency"]; //how often we need to verfiy
		$whentosendNotice = $getInfo["records"][0]["checkexpirationdt"];//send notice that license is about to expiree
		$accountnum = $getInfo["records"][0]["accountnumber"]; // accountnumber to process during verification 
		
		//now go find the last time the nurse was verfified 
		$findwhen = $sclass->QueryNursesLastChecked($accountnum);
		//var_dump($findwhen);
		$lastchecked = $findwhen[0]["checkstatus"];
		
		//look at When Verification needs to happen and then take appropriate action for each setting
		$checkfrequency = $this->checkVerificationFrequency($chkfrequency,$whentosendNotice,$lastchecked,$accountnum);
	}
}
private function checkVerificationFrequency($chkfrequency,$whentosendNotice,$lastchecked,$accountnum)
{
	require_once("SqlClass.php");
	$sclass =new SQLData();
	$lstchecked = $lastchecked; //last time the date for license to be checked
	$whentosendExpNotice = $whentosendNotice; //When should the system send the email
	$freq = $chkfrequency; // How often should we check to see if license are verified
	$accnum = $accountnum;
	//var_dump("Here again");
	//var_dump($lstchecked);
	//var_dump($chkfrequency);
	switch($freq)
	{
		case"Weekly":
		{
			$chkfromdate = date('Y-m-d',strtotime('+7 days',strtotime($lstchecked))).PHP_EOL;
			var_dump($chkfromdate);
			//$benchmark = date('Y-m-d',strtotime('+30 days',strtotime($lstchecked))).PHP_EOL;
			var_dump("Inside Weekly");
			//var_dump($benchmark);
			var_dump(date('Y-m-d'));
			if(date('Y-m-d') >=$chkfromdate)
			{
				var_dump("Inside the Benchmrk");
				//past due and we need to verify nurses 
				$gdata = $sclass->GetNursData($accountnum);
				//var_dump($gdata);
				foreach($gdata["nurseInfo"] as $m)
				{
					var_dump($m["state"]);
					var_dump($m["licensenum"]);
					var_dump($m["licensetype"]);
					//$verifty = $this->NursysGetNurseInfo($ncsid,$jurisdiction,$license,$ltype);
					$verifty= $this->NursysGetNurseInfo("",$m["state"],$m["licensenum"],$m["licensetype"]);
					var_dump($verifty); 
					$Fname  = $verifty[0]["FirstName"];
					$Lname = $verifty[0]["LastName"];
					var_dump("First Name"." ".$Fname." ".$Lname);
					$activeStatus= $verifty["Active"];
					$licenseStat = $verifty["LicenseStatus"];
					$expdt = $verifty["licenseexpdate"];
					$licensenumber = $verifty["licensenumber"];
					//storage array for holding verification results 
					$resultar[]= array();
					if($verifty["LicenseType"]=="RN" && $verifty["Active"] =="YES" && $verifty["LicenseStatus"]=="UNENCUMBERED")
					{
						//Now Update The table of when the last time the nursyses status was updated 
						$updateNurse = $sclass->UpdateLicenseCheckDate($lastchecked,$expdt,$licensenumber,$activeStatus);
						var_dump($updateNurse);
						$resultar[] = array("accountNumber"=>$accountnum,"firstName"=>$Fname,"lastName"=>$Lname,"active"=>$activeStatus,"expirationDt"=>$expdt,"licensenumber"=>$licensenumber,"lastDaateChecked"=>date('Y-m-d'));

					}
					else{
						$updateNurse = $sclass->UpdateLicenseCheckDate($lastchecked,$expdt,$licensenumber,$activeStatus);
						$resultar[] = array("accountNumber"=>$accountnum,"firstName"=>$Fname,"lastName"=>$Lname,"active"=>$activeStatus,"expirationDt"=>$expdt,"licensenumber"=>$licensenumber,"lastDaateChecked"=>date('Y-m-d'));
						var_dump($updateNurse);
						var_dump($resultar);
					}
				}
			}
			break;
		}
		case"2 month":
		{
			break;
		}
		case"1 weekly":
		{
			break;
		}
		case" 2 weekly":
		{
			break;
		}
	}
}
public function CreateAccount($accountnumber,$orgname,$accountrole,$acntContact,$firstname,$lastname,$username,$password,$email,$prAddr,$prCity,$prState,$prZip,$orgaddress,$city,$orgstate,$orgzip,$phone,$packageLevel,$npi,$license,$userLicense,
$licenseState,$licenseStartDate,$licenseEndDt,$licenseCost,$accounttype,$secreteQuestion,$terms)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$m = $sclass->CreateAccount($accountnumber,$orgname,$accountrole,$acntContact,$firstname,$lastname,$username,$password,$email,$prAddr,$prCity,$prState,$prZip,$orgaddress,$city,$orgstate,$orgzip,$phone,$packageLevel,$npi,$license,$userLicense,
    $licenseState,$licenseStartDate,$licenseEndDt,$licenseCost,$accounttype,$secreteQuestion,$terms);
	return $m;
}
public function GetEMRAccountInfo($accntnum,$unine)
{
	//var_dump("process class");
	require_once("SqlClass.php");
	$accountnumber = $accntnum;
	$uniquenine = $unine;
	$sclass =new SQLData();
	$getInfo = $sclass->GetEMRAccountInfo($accountnumber,$uniquenine);
	//var_dump($getInfo);
	return $getInfo;
}
public function GetAccountUsers($accountnum)
{
	require_once("SqlClass.php");
	$accountNum = $accountnum;
	//$uniquenine = $ninedigit;
	$sclass = new SQLData();
	$records = $sclass->GetAccountUsers($accountNum);
	return $records;
}
public function GetAllUserAccounts()
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$m = $sclass->GetAllAccountUser();
	return $m;
}
public function AddContact($firstname,$lastname,$email,$phone,$address,$addr2,$city,
$state,$zip,$country,$dob,$notes,$accountnumber,$subaccnt)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$addcontact = $sclass->AddContact($firstname,$lastname,$email,$phone,$address,$addr2,$city,
	$state,$zip,$country,$dob,$notes,$accountnumber,$subaccnt);
	return $addcontact;
}
public function MoveUploadedFile($filedata,$curdir)
{
	$currentdir = $curdir;
	define("File_Path",$currentdir);
	$fileurl="";
	$result="";
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$serverName = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
	$scriptPath = $_SERVER['REQUEST_URI'];
			//$fullUrl = $protocol . $serverName . $scriptPath;
	$fullUrl = $protocol . $serverName."/";
	if(!file_exists($currentdir))
	{
		//Should never run right now because its the ......./ and we need perm updated so we can create and write to directories
		$crtdir = mkdir($currentdir."/files",0777,true);
		
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
				$success = move_uploaded_file($filedata["tmp_name"],$currentdir.$name);
				
				$fileurl = $fullUrl.$name;
				
				
				if(!$success)
				{
					
					$result = array("error"=>"File could not upload correctly","message"=>$success);
					return $result;
					//print_r(json_encode($result,JSON_PRETTY_PRINT));
				}
				else{
					
					$result = array("fileurl"=>$fileurl,"filename"=>$name,"status"=>"Successfull","error"=>"");
					return $result;
					//print_r(json_encode($result,JSON_PRETTY_PRINT));
					
					
					
				}
			}
		}
}
public function FetchMessages($subaccountnumber,$accountnumber,$status)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	//var_dump($subaccountnumber." ".$accountnumber." ".$status);
	$m = $sclass->FetchMessages($subaccountnumber,$accountnumber,$status);
	//var_dump($m);
	return $m;
}
public function SearchAllMessagesByIDS($subaccountnumber,$accountnumber,$messages)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$m = $sclass->SearchAllMessagesByIDS($subaccountnumber,$accountnumber,$messages);
	return $m;
}
public function FetchAllMessagesByIDS($subaccountnumber,$accountnumber)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$m = $sclass->FetchAllMessagesByIDS($subaccountnumber,$accountnumber);
	return $m;
}public function FetchMessagesByThreadID($subaccountnumber,$accountnumber,$threadid)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$m = $sclass->FetchMessagesByThreadID($subaccountnumber,$accountnumber,$threadid);
	return $m;
}

public function InsertContactInfo($accountid,$subaccnt,$fname,$laname,$email,$phone,$address,$address2,
$city,$state,$zip,$country,$dob,$gender,$contacttype,$notes,$creationdt)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$insert = $sclass->InsertContactInfo($accountid,$subaccnt,$fname,$laname,$email,$phone,$address,$address2,
	$city,$state,$zip,$country,$dob,$gender,$contacttype,$notes,$creationdt);
	return $insert;
}
public function UpdateAccountContact($accountid,$subaccnt,$fname,$laname,$email,$phone,$address,$address2,
$city,$state,$zip,$country,$dob,$gender,$contacttype,$notes,$creationdt)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$updatectact = $sclass->UpdateAccountContact($accountid,$subaccnt,$fname,$laname,$email,$phone,$address,$address2,
    $city,$state,$zip,$country,$dob,$gender,$contacttype,$notes,$creationdt);
	return $updatectact;
}
public function GetContactBySearch($accountnumber,$subaccountnumber,$firstname,$lastname)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$srchcontact = $sclass->GetAccountContactInfo($accountnumber,$subaccountnumber,$firstname,$lastname);
	return $srchcontact;
}
public function GetContactByGroup($accountnumber,$subaccountnumber,$srchterm)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$srchcontact = $sclass->GetContactByGroup($accountnumber,$subaccountnumber,$srchterm);
	return $srchcontact;
}
public function SearchAllContactsNurseProviders($account,$firstname,$lastname)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$result = $sclass->SearchAllContactsNurseProviders($account,$firstname,$lastname);
	return $result;
}
public function DeleteContactByIDS($accountnumber,$subaccountnumber)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$delete = $sclass->DeleteContactByIDs($accountnumber,$subaccountnumber);
	return $delete;
}
public function UpdateAccountStatusByAccountNumber($accountnumber,$status)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$m = $sclass->UpdateAccountStatusByAccountNumber($accountnumber,$status);
	return $m;
}
public function CreateAccountUser($accountnumber,$uniqueID,$username,$accnType,$firstname,$lastname,$phone,$email,$address,$city,$state,$zip,$proffisionalicense,$accntStatus,$upremission,$emrlicense,$ucreationDt,$compPreference,$insFile,$licenseFile)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$m = $sclass->CreateAccountUser($accountnumber,$uniqueID,$username,$accnType,$firstname,$lastname,$phone,$email,$address,$city,$state,$zip,$proffisionalicense,$accntStatus,$upremission,$emrlicense,$ucreationDt,$compPreference,$insFile,$licenseFile);
	return $m;

}
public function AddPatientAccount($accountnumber,$uniqueID,$firstname,$lastname,$dob,$gender,$phone,$email,$address,$city,$state,$zip,$emgcontact,$emgcontactphone,$maritalstatus,$insuranceprovider,$policynumber,$bloodtype,$allergies)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$m = $sclass->AddPatientAccount($accountnumber,$uniqueID,$firstname,$lastname,$dob,$gender,$phone,$email,$address,$city,$state,$zip,$emgcontact,$emgcontactphone,$maritalstatus,$insuranceprovider,$policynumber,$bloodtype,$allergies);
	return $m;
}
public function AddHomeAidAccount($accountnumber,$uniqueID,$username,$caregiver,$firstname,$lastname,$phone,$email,$address,$city,$state,$zip,
$ishomeHeathAid,$hh_hasCertificate,$aidcertificateNumber,$accntStatus)
{
  $sclass= new SQLData();
  $m = $sclass->AddHomeAidAccount($accountnumber,$uniqueID,$username,$caregiver,$firstname,$lastname,$phone,$email,$address,$city,$state,$zip,
  $ishomeHeathAid,$hh_hasCertificate,$aidcertificateNumber,$accntStatus);
  return $m;
}
public function InsertBillingInfo($accountnumber,$compname,$billaddr,$billcity,$billstate,$billzip,$email,$cardholdername,$ccnum,$cv3,$expDt,$pcklevel,$billfreq,$pckcost,$isCurrent,$createDt)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$m = $sclass->InsertBillingInfo($accountnumber,$compname,$billaddr,$billcity,$billstate,$billzip,$email,$cardholdername,$ccnum,$cv3,$expDt,$pcklevel,$billfreq,$pckcost,$isCurrent,$createDt);
	return $m;
}
public function InsertNotification($patientID,$notificationMsg)
{
	$pid = $patientID;
	$msg = $notificationMsg;
	$inserstData = $this->NotificationInsert($pid,$msg);
    return $inserstData;
}

private function NotificationInsert($patientID,$notificationMsg)
{
	require("consts.php");
	$pid = $patientID;
	//var_dump($pid);
	$msg = $notificationMsg;
	//var_dump($msg);
	$notificationDt = date("Y-m-d");
	$notificationStatus ="New";
	$notificationMsg ="New patient Info recieved";
	$sqlightPath = "{$DB}.db";
	//$newdb = new SQLite3($this->sqlightPath);
	$alrtMsg ="New Fora Record";
	//lets connect 
	//$mypdo = new PDO('sqlite:'.$sqlightPath);
    $mypdo = $this->dbConnect();
	$crudquery ="Insert Into {$DB}.notifications (patientid,notification_date,notification_name,notification_status,notification_message) VALUES(:pid,:noteDt,:foraAlert,:notificationStatus,:notificationMsg)";

	$query2="Select * FROM notifications";
	$stmnt = $mypdo->prepare($crudquery);
	$stmnt->bindParam(":pid",$pid);
	$stmnt->bindParam(":noteDt",$notificationDt);
	$stmnt->bindParam(":foraAlert",$alrtMsg);
	$stmnt->bindParam(":notificationStatus",$notificationStatus);
	$stmnt->bindParam(":notificationMsg",$msg);
	if($stmnt->execute())
		{
			$result ="Inserted";
			//var_dump($result);
            return $result;
		}
        else{
            $error=$stmnt->errorInfo();
            return $error;
        }
}

public function GetNotifications()
{
	require("consts.php");
    $mypdo = $this->dbConnect();
	//$crudquery ="Insert Into eventsdb.notifications (patientid,notification_date,notification_name,notification_status,notification_message) VALUES(:pid,:noteDt,:foraAlert,:notificationStatus,:notificationMsg)";
	$qstatus ="Pending";
	$query2="Select * FROM {$DB}.foradata WHERE status=:pending ";
	$stmnt = $mypdo->prepare($query2);
	$stmnt->bindParam(":pending",$qstatus);

	if($stmnt->execute())
		{
			$result = $stmnt->fetchAll();
			//var_dump($result);exit();
            return $result;
		}
        else{
            $error = $stmnt->errorInfo();
            return $error;
        }
}

public function InsetForAPIData($grpId, $patientID,$recID,$mdata,$remark,$mdtTime,$mdtTimeUtc,$mtyp,$mslot,$mval1,$mval2,$mval3,$mrefnote1,$mrefnote2,$mrefnote3,$mrefnote4,$mnote,$mpdt,$mpdtTimeUtc,$mdeviceType,$mdeviceID,$mmeterNote,$custfield1,$custfield2,$status)
{
       //vakr_dump(array("error"=>"made it"));exit();
	   require("consts.php");
		$groupID = $grpId;
		$pID = $patientID;
		$recordID = $recID;
		$MDataID =$mdata;
	    $Remark = $remark;
		$MDateTime= $mdtTime;
		$MDateTimeUTC =$mdtTimeUtc;
		$MType =$mtyp;
		$MSlot =$mslot;
		$MValue1 =$mval1;
		$MValue2 =$mval2;
		$MValue3 =$mval3;
		$MRefNote1 =$mrefnote1; 
	    $MRefNote2 =$mrefnote2;
		$MRefNote3 =$mrefnote3; 
		$MRefNote4 =$mrefnote4;
		$MNote =$mnote;
		$MPDateTime =$mpdt;
		$MPDateTimeUTC =$mpdtTimeUtc; 
		$MDeviceType =$mdeviceType;
		$MDeviceID =$mdeviceID;
		$MMeterNote =$mmeterNote;
		$cusField1 =$custfield1;
		$cusField2 = $custfield2;
		$stat = $status;
        $error="";
		$sqlightPath = "/var/www/html/keyon/{$DB}.db";
        //var_dump($sqlightPath); exit();
		//$mypdo = new PDO('sqlite:'.$sqlightPath);
        $mypdo = $this->dbConnect();
       // var_dump($mypdo ."Keyon");exit();
        //$mypdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $prequery="Select * FROM {$DB}.foradata WHERE recordId=:recid";
        $prestmnt = $mypdo->prepare($prequery);
        $prestmnt->bindParam(":recid",$recordID);
        $premsg="";
        //execute First Sql Query
        if($prestmnt->execute($prequery))
        {
            $premsg = $prestmnt->fetchAll();
        }
        if(!empty($premsg))
        {
            //Don't insert record because one already exist
            $recexist="Record Exist";
            return $recordID;
        }
        else{


		$query ="Insert Into {$DB}.foradata (groupid,patientid,recordId,MDataID,Remark,MdateTime,MDateTimeUTC,MType,MSlot,MValue1,MValue2,MValue3,MRefNote1, MRefNote2, MRefNote3,MRefNote4,MNote,MPDateTime,MPDateTimeUTC,MDeviceType,MDeviceID,MMeterNote,CustomField1,CustomField2,status)VALUES(:groupid,:pid,:recid,:mdataId,:rmark,:mDtime,:mDtimeUtc,:mtype,:mslot,:mval1,:mval2,:mval3,:mrefnote1,:mrefnote2,:mrefnote3,:mrefnote4,:mnote,:mpdt,:mpdtTimeUtc,:mdeviceType,:mdeviceID,:mmterNote,:customfield1,:customfield2,:status)";

		//$query2="Select * FROM notifications";
		$stmnt = $mypdo->prepare($query);
		$stmnt->bindParam(":groupid",$groupID);
		$stmnt->bindParam(":pid",$pID);
		$stmnt->bindParam(":recid",$recordID);
		$stmnt->bindParam(":mdataId",$MDataID);
		$stmnt->bindParam(":rmark",$Remark);
		$stmnt->bindParam(":mDtime",$MDateTime);
		$stmnt->bindParam(":mDtimeUtc",$MDateTimeUTC);
		$stmnt->bindParam(":mtype",$MType);
		$stmnt->bindParam(":mslot",$MSlot);
		$stmnt->bindParam(":mval1",$MValue1);
		$stmnt->bindParam(":mval2",$MValue2);
		$stmnt->bindParam(":mval3",$MValue3);
		$stmnt->bindParam(":mrefnote1",$MRefNote1);
		$stmnt->bindParam(":mrefnote2",$MRefNote2);
		$stmnt->bindParam(":mrefnote3",$MRefNote3);
		$stmnt->bindParam(":mrefnote4",$MRefNote4);
		$stmnt->bindParam(":mnote",$MNote);
		$stmnt->bindParam(":mpdt",$MPDateTime);
		$stmnt->bindParam(":mpdtTimeUtc",$MPDateTimeUTC);
		$stmnt->bindParam(":mdeviceType",$MDeviceType);
		$stmnt->bindParam(":mdeviceID",$MDeviceID);
		$stmnt->bindParam(":mmterNote",$MMeterNote);
		$stmnt->bindParam(":customfield1",$cusField1);
		$stmnt->bindParam(":customfield2",$CustomField2);
		$stmnt->bindParam(":status",$stat);
		try{
           if( $stmnt->execute())
           {
              // var_dump("here");exit();
			  require_once("SqlClass.php");
			  $sclass = new SQLData();
			//   $sclass->CheckNewDataCpt(array("pid"=>$pID, "date"=>$MDateTime, "meterid"=>$MDeviceID));
			  $sclass->CheckNewDataCpt2(array("pid"=>intval($pID), "date"=>$MDateTime, "meterid"=>$MDeviceID));

               $result ="Inserted";
              // var_dump(array("msg"=>$result));exit();
               return $result;
           }
           else{
              // var_dump("error but made it");exit();
               $m=$stmnt->errorInfo();
              // var_dump(array("Messae"=>$m));exit();
           }
        }
        catch(PDOException $e)
        {
            $error = $e->__toString();
           // var_dump(array("msg"=>$error));
            return $error;
        }

    }
}

public function GetForaDataByID($patientid)
{
	require("consts.php");
	$pid = $patientid;
	$sqlightPath = "{$DB}.db";
	//$mypdo = new PDO('sqlite:'.$sqlightPath);
    $mypdo = $this->dbConnect();
	$query="Select * FROM {$DB}.foradata WHERE patientid =:pid";
	$stmnt = $mypdo->prepare($query);
	$stmnt->bindParam(":pid",$pid);
	if($stmnt->execute())
	{
		//it updated 
		$result = $stmnt->fetchAll();
		return $result; 
	}
	else{
		//return $stmnt->error_get_last();
		$error = $stmnt->error_get_last();
        return $error;
	}
}
public function InsertNewProvider($pid,$ordnumber,$firstname,$lastname,$address1,$address2,$city,$state,$zip,$tel,$cell,$fax,$npinum,$status,$email)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$provider = $sclass->NewProvider($pid,$ordnumber,$firstname,$lastname,$address1,$address2,$city,$state,$zip,$tel,$cell,$fax,$npinum,$status,$email);
	return $provider;
}
public function GetAccountPendingOrders($accountId)
{
	require_once("SqlClass.php");
	$sclass= new SQLData();
	$sdata = $sclass->GetAccountPendingOrders($accountId);
	return $sdata;
}
public function VerifyLoginCode($accntnumber,$subaccnt,$loginCode)
{
	require_once("SqlClass.php");
	$accountnum =stripslashes($accntnumber);
  $subnum = stripslashes($subaccnt);
	$logcode = stripslashes($loginCode);
	$sclass = new SQLData();
	 $getInfo = $sclass->GetUserSMSStoredCode($accountnum,$subnum,$logcode);
	 //var_dump($getInfo);exit();
	 if(is_array($getInfo) && !empty($getInfo))
	 {
	 	$matchcode = $logcode;
	 	if($matchcode ==$getInfo["code"])
	 	{
	 		//match 
	 		$msg = array("email"=>$getInfo["email"],"status"=>"Code Matched","username"=>$getInfo["username"]);
	 		//var_dump($msg);
	 		return $msg;
	 	}
	 	else{
	 		$msg = array("email"=>$username,"status"=>"Can't Confirm Code","username"=>$getInfo["username"]);
	 		//var_dump($msg);
	 		return $msg;
	 	}
	 }
	 
	
}
public function GetAccountPhoneNumber($accnt,$subaccnt)
{
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $getloggeduserNumber = $sclass->GetUserPhoneForSMS($accnt,$subaccnt);
  if(!empty($getloggeduserNumber["phone"]))
  {
    $msg = array("status"=>"200 Successfull","phone"=>$getloggeduserNumber["phone"],"message"=>"Have Phone Number");
    print(json_encode($msg,JSON_PRETTY_PRINT));
  }
}
public function GetSMSCode($accntnumber,$subaccnt,$phone)
{
	
	require_once("SqlClass.php");
	require_once("Twilio/autoload.php");
  
	$sclass = new SQLData();
  
	$accountsid ="AC47708c582759b32e2a8b4febd0b225d8";
	$authtoken ="ced08978e386ec64315975b60d40a129";
	$twilnumber ="+18556730319";
	$getloggeduserNumber = $sclass->GetUserPhoneForSMS($accntnumber,$subaccnt);
  $client = new Twilio\Rest\Client($accountsid,$authtoken);
	if($getloggeduserNumber["phone"] && !empty($getloggeduserNumber["phone"]) && $getloggeduserNumber["phone"]==$phone)
	{
    //lets try and validate if the number is a valid number
    try{
      $lookup = $client->lookups->v1->phoneNumbers("+1".$getloggeduserNumber["phone"])->fetch(["type" => ["carrier"]]);
     
    }
    catch (\Twilio\Exceptions\RestException $e) {
      $msg = array("error_code"=>"HTTP 400","message"=>$e->getMessage());
      return $msg;
      //echo "Invalid number: " . $e->getMessage(); exit();
    }
		$tonumber =$getloggeduserNumber["phone"];
		//$userid = $getloggeduserNumber["userid"];
		$randcode = rand(100000,999999);
		//var_dump($randcode);
		//update the logi code 
		$updateLoginCode = $sclass->UpdateUserLoginCodeByID($accntnumber,$subaccnt,$randcode);
		if($updateLoginCode["message"]=="Updated")
		{
			
				$tomsg="Please enter the six digit code (found below) into the Park Avenue EMR Login Page, to authenticate and login to your account.\n".
			"Code:"." ".$randcode."\n".
			"Please email or Call PACMNY office if you need assistance or help with your account.";
	//var_dump($ntxtar);
			
			if(!empty($tonumber))
			{
				
					$message = $client->messages->create(
					$tonumber,
					[
						'from'=>$twilnumber,
						'body'=>$tomsg
					]
						
					);

				

				$msgar = array("status"=>$message->sid,"TwilioStatus"=>"successful","codeSent"=>$randcode);
				//var_dump($msgar);
				return $msgar;
			}
			else{

				
				$msgar = array("status"=>"Error","TwiloStatus"=>"Not successful");

				return $msgar;

			}
		}

		
	}
	else{
		$msg = array("status"=>"No Match Found","data"=>$getloggeduserNumber);
		return $msg;
	}
	

	

}
public function lookupPatientSearch($account,$subaccnt,$keyword,$filterby,$accounttype,$license)
{
  
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $patientdata = $sclass->lookupPatientSearch($account,$subaccnt,$keyword,$filterby,$accounttype,$license);
  return $patientdata;
}
public function updatePatientData($accountnumber,$subaccountnumber,$firstname,$lastname,$gender,$address,
$city,$state,$zip,$emergcontact,$martialstatus,$dob,$socnum,$phone,$email)
{
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $patientdata = $sclass->updatePatientData($accountnumber,$subaccountnumber,$firstname,$lastname,$gender,$address,
  $city,$state,$zip,$emergcontact,$martialstatus,$dob,$socnum,$phone,$email);
  return $patientdata;
}
public function dischargePatient($getproflicense,$accountnumber,$subaccntnumber,$patientid,$patientfname,$patientlname,$accounttype)
{
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $patientdata = $sclass->dischargePatient($getproflicense,$accountnumber,$subaccntnumber,$patientid,$patientfname,$patientlname,$accounttype);
  return $patientdata;
}
public function GetProvAssignedPatients($accountnumber,$subaccountnumber,$accounttype,$proflicense)
{
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $patientdata = $sclass->GetProvAssignedPatients($accountnumber,$subaccountnumber,$accounttype,$proflicense);
  return $patientdata;
}
public function getAccountType($acntnumber,$subaccntnumber,$npinumber)
{
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $accntype = $sclass->getAccountType($acntnumber,$subaccntnumber,$npinumber);
  return $accntype;
}
public function GetAllPendingOrdersByPatientID($accountID,$pid)
{
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $orderdata = $sclass->GetAllPendingOrdersByPatientID($accountID,$pid);
  return $orderdata;
}
public function GetAccountPendingOrdersByID($accountID,$orderID,$patientid)
{
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $orderdata = $sclass->GetAccountPendingOrdersByID($accountID,$orderID,$patientid);
  return $orderdata;
}
public function GetOrderGrid($patientId)
{
	require_once("SqlClass.php");
	$sclass= new SQLData();
	$sdata = $sclass->GetOrderbyPatientID($patientId);
	return $sdata;
}
public function UpdateForaDataByID($patientid,$recid,$status)
{
	require("consts.php");
	$pid = $patientid;
    $rid = $recid;
	$stat = $status;
	$sqlightPath = "{$DB}.db";
	//$mypdo = new PDO('sqlite:'.$sqlightPath);
    $mypdo = $this->dbConnect();
	$query="UPDATE {$DB}.foradata SET status =:status WHERE patientid =:pid AND recordId=:recid";
	$stmnt = $mypdo->prepare($query);
	$stmnt->bindParam(":pid",$pid);
    $stmnt->bindParam(":recid",$rid);
	$stmnt->bindParam(":status",$stat);
	if($stmnt->execute())
	{
		//it updated 
		$result = "Updated";
		return $result; 
	}
	else{
		//return $stmnt->error_get_last();
		$error = $stmnt->erroInfo();
        return $error;
	}
}

public function InsertNewOrder($orderArr){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$result = $sclass->InsertNewOrder($orderArr);
	return $result;
}

public function GetOrders($orderArr){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$result = $sclass->GetOrders($orderArr);
	return $result;
}
public function findProfLicense($accntnumber,$subaccntnumber)
{
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $license = $sclass->findProfLicense($accntnumber,$subaccntnumber);
  return $license;
}
public function OrderFilter($ordertype,$orderstatus,$accountType,$dtRange,$srchpatientname,$proflicense,$accountnumber)
{
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $ordlookup = $sclass->OrderFilter($ordertype,$orderstatus,$accountType,$dtRange,$srchpatientname,$proflicense,$accountnumber);
  return $ordlookup;
}
public function InsertOrderTemplate($pid,$ordrAr)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	//$getNumber = $sclass->GetOrdersNumber();
  $ordernumber="";
  if($ordrAr["ordernumber"]!="")
  {
    $ordernumber = $ordrAr["ordernumber"];
  }
  else{
    $ordernumber = $sclass->GetOrdersNumber();
  }
  //var_dump($ordrAr);
	$patientID = $pid; 
	//assign variables 
	$accountnumber = $ordrAr["accountnumber"];
	$ordernumber = $ordrAr["ordernumber"];
	$patientID =$pid;
	$ordDate = $ordrAr["ordDate"];
	$ordTime = $ordrAr["ordTime"];
	$ordType = $ordrAr["ordtype"];
	$abnDelivery = $ordrAr["abndeliv"];
	$ordReadback = $ordrAr["readback"];
	$ordPrimaryPhysician = $ordrAr["primephysician"];
	$secPhysicians = $ordrAr["secphysician"];
	$email = $ordrAr["email"];
	$npinumber = $ordrAr["npi"];
	$ordAddress = $ordrAr["address"];
	$ordPhone = $ordrAr["phone"];
	$ordFax = $ordrAr["fax"];
	$ordsendtophyscians = $ordrAr["sendtophysician"];
	$ordwoundcare = $ordrAr["woundcare"];
	$ordverboffer = $ordrAr["verbaloffer"];
	$ordverbOrdstrtDt = $ordrAr["verbalOrderDt"];
	$ordverbOrdtime = $ordrAr["verbalOrderTime"];
	$ordhasmed = $ordrAr["hasmed"];
	$ordSupplies = $ordrAr["hassupplies"];
	$ordValue= $ordrAr["hasValueSign"];
	$ordhasdiag = $ordrAr["hasdiag"];
	$ordDescrip = $ordrAr["description"];
	$ordstatus = $ordrAr["status"];
	$writer = $ordrAr["writer"];
  $nursesignature = $ordrAr["nursesigname"];
  $nursesigDate = $ordrAr["nursesigdate"];
  $providersignature = $ordrAr["providersignature"];
  $provsigDate = $ordrAr["provsigdate"];
  //var_dump($providersignature);
  //var_dump($provsigDate);
  //var_dump($nursesignature);
  //lets update the the codeset for Order Status
  if($ordType=="Nurses Order" ||$ordType=="Verbal Order" && $nursesignature=="" && $providersignature=="")
  {
    //ord type should stay Pending from the front-end code. Should be nothing to do here. We'll check QA and confirm
    $ordstatus="Pending";
  }
  elseif($ordType=="Nurses Order" || $ordType=="Verbal Order" && $nursesignature !="" && $providersignature=="")
  {
    //ord stype shoul still be Pedning from the front end code. Should be nothing to do here. Als need to confirm
    $ordstatus="Pending";
  }
  elseif($ordType=="Nurses Order" || $ordType=="Verbal Order" && $nursesignature !=""  && $providersignature !="")
  {
    // Order status should nonw change to Active since both Signatures have values 
    $ordstatus="Active";
  }
  elseif($ordType=="Physicians Order" && $providersignature !="" && $nursesignature =="")
  {
    //Stil lshould be Active-Pending, so do nothing 
    $ordstatus="Active-Pending";
  }
  elseif($ordType=="Physicians Order" && $providersignature !="" && $nursesignature !="")
  {
    //Okay status should now change to Active 
    $ordstatus="Active";
  }
  //var_dump($ordstatus);
	$result = $sclass->InsertOrders($accountnumber,$ordernumber,$ordDate,$patientID,$ordTime,$ordType,$abnDelivery,
	$ordReadback,$ordPrimaryPhysician, $secPhysicians,$email,$npinumber,$ordAddress,$ordPhone,$ordFax,
	$ordsendtophyscians,$ordwoundcare,$ordverboffer,$ordverbOrdstrtDt,$ordverbOrdtime,$ordhasmed,$ordSupplies,$ordValue,
	$ordhasdiag,$ordDescrip,$ordstatus,$writer,$nursesignature,$nursesigDate,$providersignature,$provsigDate);
	//check for vebal order and send notification before returning payload 
	//var_dump($result);
	if($ordType=="Verbal Order" || $ordType=="Nurses Order")
	{
		//let send out notification
	
		$sendnotification =$this->SendPhysicianEmailTemplate($ordernumber,$ordPrimaryPhysician);
	}
	return $result;
}
//update Order Function 
public function updateOrderTemplate($pid,$ordrAr)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	//$getNumber = $sclass->GetOrdersNumber();
  $ordernumber="";
  if($ordrAr["ordernumber"]!="")
  {
    $ordernumber = $ordrAr["ordernumber"];
  }
  else{
    $ordernumber = "Can't Update Order without an Order Number, Please try again";
    $result = array("result"=>$ordernumber);
    print_r(json_encode($result,JSON_PRETTY_PRINT));
  }
  //var_dump($ordrAr);
	$patientID = $pid; 
	//assign variables 
	$accountnumber = $ordrAr["accountnumber"];
	$ordernumber = $ordrAr["ordernumber"];
	$patientID =$pid;
	$ordDate = $ordrAr["ordDate"];
	$ordTime = $ordrAr["ordTime"];
	$ordType = $ordrAr["ordtype"];
	$abnDelivery = $ordrAr["abndeliv"];
	$ordReadback = $ordrAr["readback"];
	$ordPrimaryPhysician = $ordrAr["primephysician"];
	$secPhysicians = $ordrAr["secphysician"];
	$email = $ordrAr["email"];
	$npinumber = $ordrAr["npi"];
	$ordAddress = $ordrAr["address"];
	$ordPhone = $ordrAr["phone"];
	$ordFax = $ordrAr["fax"];
	$ordsendtophyscians = $ordrAr["sendtophysician"];
	$ordwoundcare = $ordrAr["woundcare"];
	$ordverboffer = $ordrAr["verbaloffer"];
	$ordverbOrdstrtDt = $ordrAr["verbalOrderDt"];
	$ordverbOrdtime = $ordrAr["verbalOrderTime"];
	$ordhasmed = $ordrAr["hasmed"];
	$ordSupplies = $ordrAr["hassupplies"];
	$ordValue= $ordrAr["hasValueSign"];
	$ordhasdiag = $ordrAr["hasdiag"];
	$ordDescrip = $ordrAr["description"];
	$ordstatus = $ordrAr["status"];
	$writer = $ordrAr["writer"];
  $nursesignature = $ordrAr["nursesigname"];
  $nursesigDate = $ordrAr["nursesigdate"];
  $providersignature = $ordrAr["providersignature"];
  $provsigDate = $ordrAr["provsigdate"];
  //var_dump($providersignature);
  //var_dump($provsigDate);
  //var_dump($nursesignature);
  //lets update the the codeset for Order Status
  if($ordType=="Nurses Order" ||$ordType=="Verbal Order" && $nursesignature=="" && $providersignature=="")
  {
    //ord type should stay Pending from the front-end code. Should be nothing to do here. We'll check QA and confirm
    $ordstatus="Pending";
  }
  elseif($ordType=="Nurses Order" || $ordType=="Verbal Order" && $nursesignature !="" && $providersignature=="")
  {
    //ord stype shoul still be Pedning from the front end code. Should be nothing to do here. Als need to confirm
    $ordstatus="Pending";
  }
  elseif($ordType=="Nurses Order" || $ordType=="Verbal Order" && $nursesignature !=""  && $providersignature !="")
  {
    // Order status should nonw change to Active since both Signatures have values 
    $ordstatus="Active";
  }
  elseif($ordType=="Physicians Order" && $providersignature !="" && $nursesignature =="")
  {
    //Stil lshould be Active-Pending, so do nothing 
    $ordstatus="Active-Pending";
  }
  elseif($ordType=="Physicians Order" && $providersignature !="" && $nursesignature !="")
  {
    //Okay status should now change to Active 
    $ordstatus="Active";
  }
  //var_dump($ordstatus);
	$result = $sclass->updateOrders($accountnumber,$ordernumber,$ordDate,$patientID,$ordTime,$ordType,$abnDelivery,
	$ordReadback,$ordPrimaryPhysician, $secPhysicians,$email,$npinumber,$ordAddress,$ordPhone,$ordFax,
	$ordsendtophyscians,$ordwoundcare,$ordverboffer,$ordverbOrdstrtDt,$ordverbOrdtime,$ordhasmed,$ordSupplies,$ordValue,
	$ordhasdiag,$ordDescrip,$ordstatus,$writer,$nursesignature,$nursesigDate,$providersignature,$provsigDate);
	//check for vebal order and send notification before returning payload 
	//var_dump($result);
	if($ordType=="Verbal Order" || $ordType=="Nurses Order")
	{
		/*let send out the Update Order Email notification
    *Jan4 2025 We still Need to Develop the Update Email notifiction template 
    */
	
		//$sendnotification =$this->SendPhysicianEmailTemplate($ordernumber,$ordPrimaryPhysician);
	}
	return $result;
}
//end Update Order Function 
public function InsertOrderMedication($orderum,$patientid,$medname,$diagcode,$medamount,$meddoseuom,$medfreq,$prn,$route,$altroute,$instruction,$medstartdt,$medenddt,$medchangetype,$drugclass,$medunderstanding,$addsettings,$status,$writer)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$medinsert = $sclass->InsertMedication($orderum,$patientid,$medname,$shorthand,$diagcode,$medamount,$meddoseuom,$medfreq,$prn,$route,$altroute,$instruction,$medstartdt,$medenddt,$medchangetype,$drugclass,$medunderstanding,$addsettings,$status,$writer);
	return $medinsert;
}
public function AddCurrentMed($medArr){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$result = $sclass->AddNewMed($medArr, null, false);
}
public function GetPatientMeds($medArr){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$result = $sclass->GetPatientMeds($medArr);
	return $result;
}
public function GetPatientProcs($procArr){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$result = $sclass->GetPatientProcs($procArr);
	return $result;
}
public function UpdatePrepourForActiveMedications($arr){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$result = $sclass->UpdatePrepourForActiveMedications($arr["pid"], $arr["accountId"]);
	return $result;
}
public function GetPrePourMeds($arr){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$result = $sclass->GetPrePourMeds($arr);
	return $result;
}
public function PopulateMedicationGrid($pid)
{
	require_once("SqlClass.php");
	$slclass = new SQLData();
	$ppid=$pid;//"45064367";
	$populate = $slclass->GetAllPatientMeds($ppid);
	//var_dump($populate);
	return $populate;
}
public function PopulateMedicationGridByOID($pid,$ordnumber)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$ppid=$pid;//"45064367";
	$populate = $sclass->GetPatientMedsByOID($ppid,$ordnumber);
	//var_dump($populate);
	return $populate;
}
public function CurrentMedicationGrid($pid)
{
	require_once("SqlClass.php");
	$slclass = new SQLData();
	$ppid=$pid;//"45064367";
	$populate = $slclass->RetrievePatientCurrentMeds($ppid);
	//var_dump($populate);
	return $populate;
}
public function UpdatePatientMedInfoByIDs($medentryid,$patientID,$medAr)
{
	require_once("SqlClass.php");
	
	$sclass = new SQLData();
	$medID = $medentryid;
	$pid = $patientID;
	$ordernumber = $medAr["ordernumber"];
	$medicationname = $medAr["medname"];
	$diagcode = $medAr["diagnosisCode"];
	$medamount = $medAr["doseamount"];
	$doesoum = $medAr["doseUOM"];
	$medfrequency = $medAr["frequency"];
	$prn = $medAr["prn"];
	$route = $medAr["route"];
	$instruction = $medAr["instruction"];
	$medstartdate = $medAr["medStartDate"];
	$medendate = $medAr["medEndDate"];
	$changetype = $medAr["changetype"];
	$drugClass = $medAr["drugCategory"];
	$understand = $medAr["directionuse"];
	$addsettings = $medAr["additionalsettings"];
	$status = $medAr["status"];
	$updateInfo = $sclass->UpdateMedication($medID,$ordernumber,$pid,$medicationname,$diagcode,$medamount,$doesoum,
	$medfrequency,$prn,$route,$instruction,$medstartdate,$medendate,$changetype,$drugClass,$understand,$addsettings,$status);
	//var_dump($updateInfo);
	$msg = json_encode(array("msg"=>$updateInfo),JSON_PRETTY_PRINT);
	return $msg;
}
public function GetPatientMedicationByID($medid)
{
	require_once("SqlClass.php");
	$slclass = new SQLData();
	$mid=$medid;
	$medar = $slclass->GetMedicationByMedEntryID($mid);
	//var_dump($populate);
	if(is_array($medar) && count($medar) >0)
	{
		$return = json_encode(array("status"=>"Has data","data"=>$medar),JSON_PRETTY_PRINT);
		return $return;
	}
	else{
		//no data 
		$return =  json_encode(array("status"=>"No Data Found","data"=>$medar));
		return $return;
	}
	
}
public function GetDiagnosisGrid($pid)
{
	require_once("SqlClass.php");
	$slclass = new SQLData();
	$diagrid = $pid;
	$diagar = $slclass->GetPatientDiagnosisGrid($diagrid);
	return $diagar;
}
public function PopulateDiagnosisGridByOID($pid,$ordnumber)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$ppid=$pid;//"45064367";
	$populate = $sclass->GetPatientDiagsByOID($ppid,$ordnumber);
	//var_dump($populate);
	return $populate;
}
public function EditDiagnosisByPatientAndDiagID($ordid,$pid)
{
	require_once("SqlClass.php");
	$slclass = new SQLData();
	$patientID = $pid;
	$orderid = $ordid;
	$editar = $slclass->EditDiagnosisByOrdPID($orderid,$patientID);
	if(is_array($editar) && count($editar) >0)
	{
		$return = json_encode(array("status"=>"Has data","data"=>$editar),JSON_PRETTY_PRINT);
		return $return;
	}
	else{
		//no data 
		$return =  json_encode(array("status"=>"No Data Found","data"=>$editar));
		return $return;
	}
	//return $diagar;
}
public function UpdateDiagnosis($pid,$ordnum,$diagId,$diagtype,$diagcode,$proccode,$onset,$diagDate,$controlrate)
{
	
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$updateDiag = $sclass->UpdateDiagnosisInfo($pid,$ordnum,$diagId,$diagtype,$diagcode,$proccode,$onset,$diagDate,$controlrate);
	return $updateDiag;
}
public function InsertDiagnosis($patientid,$ordnum,$diagtype,$diagcode,$proccode,$procShorthand,$systemRating,$diagexacerbation,$diagdate,$endDate,$status,$instruction,$writer)
{
	//var_dump($proccode);exit();
	require_once("SqlClass.php");
	$sclass = new SQLData();
	
	$insertDiag = $sclass->ProcessDiagnosisInfo($patientid,$ordnum,$diagtype,$diagcode,$proccode,$procShorthand,$systemRating,$diagexacerbation,$diagdate,$endDate,$status,$instruction,$writer);
	if($diagtype=="Procedure")
	{
		$msg = array("result"=>$insertDiag,"Type"=>"Procedure");
		return json_encode($msg);
	}
	else{
		$msg = array("result"=>$insertDiag,"Type"=>"Diagnosis");
		return json_encode($msg);
	}
	

}
public function DeleteDiagnosis($diagId,$patientid)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$deletstat = $sclass->DeleteDiagnosisByPID($patientid,$diagId);
	return $deletstat;
}
public function GetGlobalOrderNumber()
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getNumber = $sclass->GetOrdersNumber();
	$numar = array("ordernumber"=>$getNumber);
	return $numar;
}
public function DeleteMedication($patientid,$medentryid)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$remove = $sclass->DeleteMedicationByPidOrdnum($patientid,$medentryid);
	return $remove;
}
public function DeleteProvider($pid)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$remove = $sclass->DeleteProviderById($pid);
	return $remove;
}
public function SearchProcedureCodes_ByCode($code)
{
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $getCodes = $sclass->SearchProcedureCodesByCode($code);
  return $getCodes;
}
public function SearchProcedureCodes_ByDescription($description)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getCodes = $sclass->SearchProcedureCodesByDescription($description);
	return $getCodes;
}
public function GetAllPatientOrders($pid)
{
  require_once("SqlClass.php");
  $sclass = new SQLData();
  $getorders = $sclass->GetPatientOrders_ByID($pid);
  return $getorders;
}
public function ChangeOrderStatus($ordnum, $status)
{
	require_once("SqlClass.php");
  	$sclass = new SQLData();
	$result = $sclass->ChangeOrderStatus($ordnum, $status);
	return $result;
}
public function GetNotes($pid,$startdate,$enddate)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getnotes = $sclass->GetNotes($pid,$startdate,$enddate);
	return $getnotes;
}
public function GetAllNotes($pid)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getnotes = $sclass->GetAllNotes($pid);
	return $getnotes;
}
public function GetNotesById($pid,$noteid)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getnotes = $sclass->GetNotesById($pid,$noteid);
	return $getnotes;
}
public function GetOrderByStartAndEndDT($pid,$stdate,$enddate)
{
	//var_dump($pid." ".$stdate." ".$enddate);
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getorders = $sclass->GetOrdersByDates($pid,$stdate, $enddate);
	return $getorders;
}
public function GetOrderByCombo($pid,$stdate,$enddate,$properties,$orderstatus)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getorders = $sclass->GetOrderByCombo($pid,$stdate,$enddate,$properties,$orderstatus);
	return $getorders;
}
public function GeOrderInfoByAccountNOrderID($accountID,$orderid)
{
  require_once("SqlClass.php");
	$oid = $orderid;
	$sclass = new SQLData();
  $getinfo = $sclass->GeOrderInfoByAccountNOrderID($accountID,$orderid);
  return $getinfo;
}
public function GetOrderbyID($orderid)
{
	require_once("SqlClass.php");
	$oid = $orderid;
	$sclass = new SQLData();
	$getsingle = $sclass->GetSingleOrderByID($oid);
	return $getsingle;
}
public function UpdateMedDoseEndDate($patientID,$ordernum,$enddate,$medentry,$transactiontype)
{

	require_once("SqlClass.php");

	$sclass = new SQLData();
	$medId = $medentry;
	$ordnum = $ordernum;
	$pid = $patientID;
	$edate = $enddate;
	$transtype = $transactiontype;

	//var_dump($medId." ".$ordnum." ".$pid." ".$edate." ".$transtype);
	$updateresult = $sclass->UpdateMedEndDate_ByMedID($pid,$ordnum,$edate,$medId,$transtype);
	return $updateresult;
}
public function DiscontinueMed($patientid,$medid,$ordnum,$dcdate)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$pid = $patientid;
	$medentryid = $medid;
	$ordernum = $ordnum;
	$dcdate = $dcdate;
	$transtype ="Discountinue";
	$dcmed = $sclass->DiscontinueMedByDate($pid,$medentryid,$ordernum,$dcdate,$transtype);
	return $dcmed;
}
public function DeleteBothMedDiagbyORDID($ordnumber,$status)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$result = $sclass->DeleteBothMednDiagByOrdID($ordnumber,$status);
	return $result;
}
public function SendInternalhMessages($reciversubaccnt,$sendersubaccnt,$senderaccountnum,$message,$status,$date,$sender,$action,$fileurl,$filename,$subject)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$result = $sclass->SendInternalhMessages($reciversubaccnt,$sendersubaccnt,$senderaccountnum,$message,$status,$date,$sender,$action,$fileurl,$filename,$subject);
	if(is_array($result) && !empty($result))
	{
		return $result;
	}
	else{
		$msg = array("message"=>"SQL Class didn't return a result, assuming there is an issue","error"=>$result);
		return $msg;
	}
}
public function SendConferenceText($confnums, $confmsg)
{
	require_once("Twilio/autoload.php");
	$accountsid ="AC47708c582759b32e2a8b4febd0b225d8";
	$authtoken ="ced08978e386ec64315975b60d40a129";
	$twilnumber ="+18556730319";
	//$tonumber =$tonum;
	$tomsg = $confmsg;
	//lets exploaid the tosg to make sure it doesn't have multiple emails to text 

	//var_dump($confnums);
	$client = new Twilio\Rest\Client($accountsid,$authtoken);
	if(is_array($confnums))
	{
		foreach($confnums as $t)
		{
			$message = $client->messages->create(
			$t,
			[
				'from'=>$twilnumber,
				'body'=>$tomsg
			]
			);

		}

		$msgar = array("status"=>$message->sid,"TwilioStatus"=>"successfull");

		return $msgar;
	}
	else{

		$message = $client->messages->create(
		$tonumber,
		[
			'from'=>$twilnumber,
			'body'=>$tomsg
		]
		);
		$msgar = array("status"=>$message->sid,"TwilioStatus"=>"successfull");

		return $msgar;

	}

}
public function SendProviderCall($tonum,$povnumber)
{
	//var_dump($tonum);
	require_once("Twilio/autoload.php");
	$accountsid ="AC47708c582759b32e2a8b4febd0b225d8";
	$authtoken ="ced08978e386ec64315975b60d40a129";
	$twilnumber ="+18556730319";
	$provtonumber = $povnumber;//$tonum;
	$client = new Twilio\Rest\Client($accountsid,$authtoken);
	$message = $client->account->calls->create(
		$tonum,
		$twilnumber,
			array(
				"twiml" => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
						    <Response>
						        <Say>Call From Park Avenue Concerige Medicine Office</Say>
						        <Dial>".$provtonumber."</Dial>
						    </Response>"
			)
		);
            $msg = array("status"=>$message->sid);
	return $msg;

}
public function StartConferenceCall($moderator,$callsar)
{
	require_once("Twilio/autoload.php");
	$accountsid ="AC47708c582759b32e2a8b4febd0b225d8";
	$authtoken ="ced08978e386ec64315975b60d40a129";
	/*$response = new VoiceResponse();
	$dial = $response->dial('');
	$dial->conference('Room 1234');
	return $response;*/
	$callar = array("7652937359","3176403937");
	$client = new Twilio\Rest\Client($accountsid,$authtoken);
	foreach($callar as $r)
	{
			$participant = $client->conferences("Park Avenue Conference")
                      ->participants
                      ->create("8556730319", // from
                                "7652937359", // to
                               [
                                   "label" => "customer",
                                   "earlyMedia" => True,
                                   "beep" => "onEnter",
                                   "statusCallback" => "https://willowbuilt.it/fora/provider/providerwebhook.php",
                                   "statusCallbackEvent" => ["ringing"],
                                   "record" => True,
                                   array(
									"twiml" => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
											    <Response>
											        <Say>Conference Call From Park Avenue Concerige Medicine Office</Say>
											        <Dial>".$r."</Dial>
											    </Response>"
								)

                               ]
                      );
	}
	/*$participant = $client->conferences("Park Avenue Conference")
                      ->participants
                      ->create("8556730319", // from
                                "7652937359", // to
                               [
                                   "label" => "customer",
                                   "earlyMedia" => True,
                                   "beep" => "onEnter",
                                   "statusCallback" => "https://willowbuilt.it/fora/provider/providerwebhook.php",
                                   "statusCallbackEvent" => ["ringing"],
                                   "record" => True,
                                   array(
									"twiml" => "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
											    <Response>
											        <Say>Conference Call From Park Avenue Concerige Medicine Office</Say>
											        <Dial>".$callar[1]."</Dial>
											    </Response>"
								)
                               ]
                      );*/

	return $participant->callSid;	
}
public function SendProviderSMS($tonum,$tomsg)
{
	require_once("Twilio/autoload.php");
	$accountsid ="AC47708c582759b32e2a8b4febd0b225d8";
	$authtoken ="ced08978e386ec64315975b60d40a129";
	$twilnumber ="+18556730319";
	//lets exploaid the tosg to make sure it doesn't have multiple emails to text 
	$txtar = explode(",",$tonum);
	$ntxtar = array_filter($txtar);
	var_dump($ntxtar);
	$client = new Twilio\Rest\Client($accountsid,$authtoken);
	if(is_array($ntxtar))
	{
		foreach($ntxtar as $t)
		{
			var_dump($t);
			$message = $client->messages->create(
			$t,
			[
				'from'=>$twilnumber,
				'body'=>$tomsg
			]
			);

		}

		$msgar = array("status"=>$message->sid,"TwilioStatus"=>"successful");

		return $msgar;
	}
	else{

		$message = $client->messages->create(
		$tonumber,
		[
			'from'=>$twilnumber,
			'body'=>$tomsg
		]
		);
		$msgar = array("status"=>$message->sid,"TwiloStatus"=>"successful");

		return $msgar;

	}



}
// public function LookUpInternalProvider($providername)
// {
// 	require_once("SqlClass.php");
// 	$sclass = new SQLData();
// 	$provname = stripcslashes($providername);
// 	$getinternalprov = $sclass->LookUpInternalProvider($provname);
// 	return $getinternalprov;
// }
public function LookupProviderByEmail($emailaddr)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$provemail = stripslashes($emailaddr);
	$lookuprov = $sclass->FindProviderbyEmail($provemail);
   // var_dump($lookuprov);
	$prefillhtml="";
	if(is_array($lookuprov["result"]))
	{
		foreach($lookuprov["result"] as $r)
		{
			//bult the html 
			/*$html .="<li class=\"emailli\">
			            <div class=\"emailblock\">
			                <a href=\"mailto:".$r["email"]."\" id=\"emaillink\">".$r["email"]."</a>
			                <span class=\"xout\">X</span>
			            </div>
			       </li>";*/

			$prefillhtml .="<div class=\"mmailbox\">
                              <span id=\"selectemailp\">".$r["email"]."</span>
                              <div id=\"hiddenmail\">
                              <li class=\"emailli\">
					            	<div class=\"emailblock\">
						                <a href=\"mailto:".$r["email"]."\" id=\"emaillink\">".$r["email"]."</a>
						                <span class=\"xout\"><a href='#xclose' id='xout'>X</a></span>
						            </div>
			       				</li>
                              </div>
                             </div><br>";

		}
		//now return html 
		//var_dump($prefillhtml);
		$nwar = array("prehtml"=>$prefillhtml, "results"=>$lookuprov["result"]);
		//var_dump($nwar);
		return $nwar;
	}
	return $lookuprov;
}
public function GetProviderInfo($providerid){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$getproviderInfo = $sclass->GetProviderInformation($providerid);
	return $getproviderInfo;
}
public function GetSingleClientProvider($patid,$providid)
{
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$singleproviderInfo = $sclass->GetSingleProvider($patid,$providid);
	return $singleproviderInfo;
}
public function GetAccountProvider($accntnumber)
{
  require_once("SqlClass.php");
	$sclass = new SQLData();
	$allproviderInfo = $sclass->GetAccountProviders($accntnumber);
	return $allproviderInfo;
}
public function GetAllClientProvider($patid){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$allproviderInfo = $sclass->GetAllProviders($patid);
	return $allproviderInfo;
}
public function UpdateProviderInfo($providerid,$email,$pid,$ordnumber,$firstname,$lastname,$address1,$address2,$city,$state,$zip,$tel,$fax,$npinum,$status,$cell)
{

	require_once("SqlClass.php");
	$sclass = new SQLData();
	$updateproviderInfo = $sclass->UpdateProviderInfo($providerid,$email,$pid,$ordnumber,$firstname,$lastname,$address1,$address2,$city,$state,$zip,$tel,$fax,$npinum,$status,$cell);
	return $updateproviderInfo;
}
public function GetAllergies($pid){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$allergy = $sclass->GetAllergies($pid);
	return $allergy;
}
public function AddAllergies($pid,$name){
	require_once("SqlClass.php");
	$sclass = new SQLData();
	$allergy = $sclass->AddAllergies($pid,$name);
	return $allergy;
}



}//end of class bracket
