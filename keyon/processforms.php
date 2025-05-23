<?php 
//header("Access-Control-Allow-Origin: http://localhost:8888");
error_reporting(E_ALL & ~E_NOTICE);

/*Setting Header Parameters*/
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Origin: *');
require_once("SQClass.php");
$sclass = new SQLData();

$mmdata = file_get_contents('php://input');
//var_dump($mdata);
$jdata = json_decode($mmdata);
var_dump($jdata);exit();
$frmid = $jdata->Forms->formNumber;

//check the markup table to get the table Name 
$getTbleName = $sclass->GetFormNameByID($frmid);
if($getTbleName !="" && $getTbleName["tablename"] !="")
{
	//var_dump($getTbleName);
	$tablename = $getTbleName["tablename"];
	$phase = $getTbleName["records"][0]["phase"];
	$frmNumber = $getTbleName["records"][0]["formid"];
	//var_dump($phase);exit();
}


foreach($jdata->Forms as $key => $value)
{
	//var_dump($key);
	if($key !="API_Meth")
	{
		$collumnname[]  = array("columName"=>$key,"value"=>$value);
	}

}
//now loop through and map the fields
$insertData = $sclass->InsertData($collumnname,$tablename,$phase,$frmNumber);
if(is_array($insertData) && $insertData !="")
{
	$returnmsg = json_encode($insertData,JSON_PRETTY_PRINT);
 	//print($returnmsg);
}
var_dump($collumnname);
var_dump($insertData);




?>