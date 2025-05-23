<?php 
/*
* @api Key: AIzaSyDecgnMgYWFtLnBUO551Jua9nJpfQckjNE
* Google Address Validation Script
*/
$secretkey="AIzaSyDecgnMgYWFtLnBUO551Jua9nJpfQckjNE";
$endpointurl="https://addressvalidation.googleapis.com/v1:validateAddress?key=".$secretkey;

/*Address must be sent over json
*
* 
{
 "address": {
    "regionCode": "US",
    "locality": "Mountain View",
    "addressLines": ["1600 Amphitheatre Pkwy"]
  }
}
*/
$ad = new stdClass();
$ad->address = new stdClass();
$ad->address->regionCode ="US";
$ad->address->locality ="Indianapolis";
$ad->address->addressLines="18002 Traders Hollow Lane";
//curlsetup 
//setupheader
$headers = array(
	"Content-Type:application/json;charset=utf-8",
	//"Content-Type:application/x-www-form-urlencoded",
	);
$ch = curl_init($endpointurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_USERPWD, $paramar["Username"].":".$paramar["Password"]);
curl_setopt($ch,CURLOPT_POST, 1);
curl_setopt($ch,CURLOPT_POSTFIELDS,  json_encode((array) $ad,JSON_PRETTY_PRINT));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
$resultdata = json_decode($result);
//var_dump($resultdata);
$mr = array();
$valdata=[];
//var_dump($result->result);
if($resultdata->result)
{
    var_dump("ready to get result");
    $addresscomplete = $resultdata->result->verdict->addressComplete;
   //var_dump($addresscomplete);exit();
    switch($addresscomplete)
    {
        case"true":
            {
                //now lets grab components of the address and confirm where they are valid or not
                //var_dump("its true");
                //lets get the USPS CASS Address since the uspsData field has value
                $uspdata = $resultdata->result->uspsData;
                //var_dump($upsdata);
                $addAr = $resultdata->result->address->addressComponents;
                //var_dump($addAr);
                foreach($addAr as $r)
                {
                    $cdata[] =array("componentName"=>$r->componentName->text,"componentType"=>$r->componentType,"confirmationLevel"=>$r->confirmationLevel);
                    if($r->confirmationLevel !="CONFIRMED")
                    {
                        //lets create an array that logs which components of the address that didn't valide
                        $valdata[] = array("componentName"=>$r->componentName->text,"componentType"=>$r->componentType);
                    }
                }
                //var_dump($cdata);
                if(sizeof($cdata)!==0 && sizeof($valdata)==0)
                {
                    $upsaddressformated =$uspdata->standardizedAddress->firstAddressLine;
                    $upsaddressformated .=",".$uspdata->standardizedAddress->cityStateZipAddressLine;
                    var_dump($upsaddressformated);
                    $msgreturned = array("msg"=>"addresss valid","validatedAddress"=>$upsaddressformated);
                    var_dump($msgreturned);
                }
                else{
                     var_dump($valdata);
                     $msgreturned = array("msg"=>"address didn't validate","notvalidcomponent"=>$valdata);
                     var_dump($msgreturned);
                }
               
                break;
            }
    }
}
//var_dump($result);
?>