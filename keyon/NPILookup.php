<?php 
/*
*  
* NPI Validation Script
*/
$secretkey="none needed for this API";
//$endpointurl="https://npiregistry.cms.hhs.gov/RegistryBack/search";
//$endpointurl="https://npiregistry.cms.hhs.gov/api/?version=2.1";
//$workingurl="https://npiregistry.cms.hhs.gov/api/?number=&enumeration_type=NPI-1&taxonomy_description=&first_name=Joseph&use_first_name_alias=false&last_name=Mulvehill&organization_name=&address_purpose=&city=New%20York&state=NY&postal_code=&country_code=&limit=&skip=&pretty=on&version=2.1";
$endpointurl="https://npiregistry.cms.hhs.gov/api/?";


header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
/*Address must be sent over HTTP GET
*
* 
query string parameter
number
taxonomy_description
first_name
use_first_name_alias
last_name
organization_name
address_purpose
city
state
postal_code
country_code
limit
skip
pretty
version

*/

if(isset($_POST))
{
    $mdata = file_get_contents('php://input');
    //var_dump($mdata);
    $mmdata = json_decode($mdata);
    //lets grap Post items to pass to the function 
   // var_dump($_POST);
    $name = explode(" ",$mmdata->primphysician);
    if(isset($mmdata->organization) && $mmdata->organization !="")
    {
        $orgname=$mmdata->organization;
    }
    else{
        $orgname="";
    }
    $methname = $mmdata->methname;
    $postalcode = $mmdata->postalCode;
    $enum = $mmdata->enumeration;
    // $npinumber = $_POST["npinumber"];
    if (count($name) == 1){
        $getlname = $name[0] . "*";
    }
    else {
        $getfname = $name[0];
        $getlname = $name[1] . "*";
    }
}
$number=$npinumber;
//var_dump($number);exit();
if(isset($enum) && $enum=="NPI-2")
{
    $enumeration="NPI-2";
}
else{
    $enumeration="NPI-1";
}
//$enumeration="NPI-1";
$tax="";
$fname=$getfname;
$namealias="false";
$lname=$getlname;
//$orgname="";
$addrpurpose="";
$city="";
$state="";
if($postalcode !="")
{
    $postcode=$postalcode;
}
else{
   $postcode=""; 
}

$country="";
$limit=200;
$skip="";
$pretty="on";
$version="2.1";
$npival = new NPIAddressVal();
//var_dump(ucwords($fname)." ".ucwords($lname));
switch($methname)
{
    case"FindNPI":
    {
        $getnpi = $npival->FindNPI($number,$enumeration,$tax,$fname,$namealias,$lname,$orgname,$addrpurpose,$city,$state,$postcode,$country,$limit,$skip,$pretty,$version);
        //var_dump($getnpi);
        print(json_encode($getnpi));
        break;
    }
    case"GetCallInfo":
    {
        $getaddrfax = $npival->GetCallFaxIInfo($number,$enumeration,$tax,$fname,$namealias,$lname,$orgname,$addrpurpose,$city,$state,$postcode,$country,$limit,$skip,$pretty,$version);
        var_dump($getaddrfax);
        break;
    }
    case"GetAllInfo":
    {
        if($enumeration=="NPI-2")
        {
            $getiniInfo1 =$npival->GetAllPharmNPIInfo($enumeration,$orgname,$city,$state,$postcode,$country,$limit,$skip,$pretty,$version);
            print(json_encode($getiniInfo1));
            break;
        }
        else{
            $namealias="true";
        $getiniInfo =$npival->GetAllNPIInfo($number,$enumeration,$tax,$fname,$namealias,$lname,$orgname,$addrpurpose,$city,$state,$postcode,$country,$limit,$skip,$pretty,$version);
        print(json_encode($getiniInfo));
        break;
        }
        
    }
    case"GetNPIAddr":
    {
       //$namealias=false;
        $getiniInfo =$npival->GetCallFaxIInfo($number,$enumeration,$tax,$fname,$namealias,$lname,$orgname,$addrpurpose,$city,$state,$postcode,$country,$limit,$skip,$pretty,$version);
       //var_dump($getiniInfo);
       if(is_array($getiniInfo))
       {
            foreach($getiniInfo as $gInfo)
            {
                $tempaddr[] = array("address"=>$gInfo["address"],"tel"=>$gInfo["tel"],"fax"=>$gInfo["fax"],"npinumber"=>$gInfo["npinumber"]);
            }
           //var_dump("temp");
            //var_dump($tempaddr);
            print(json_encode($tempaddr));
       }
        break;
    }
    case"Pacmny":
    {
        $posturl = "http://10.10.3.7/pacmny-be/web/index.php/npi/query";
        $npiobj = new stdClass();
        $npiobj->npi=$npinumber;
        //curl setup 
        $headers = array(
            "Content-Type:application/json;charset=utf-8",
            );
            $ch = curl_init($posturl);
            //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($ch,CURLOPT_POST, 1);
           curl_setopt($ch,CURLOPT_POSTFIELDS,  json_encode((array) $npiobj,JSON_PRETTY_PRINT));
           curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
           $execute = curl_exec($ch);
           var_dump(curl_getinfo($ch));
           var_dump($execute);
        break;
    }
    
}



//var_dump($result);

class NPIAddressVal{
    
    private  $endpointurl="https://npiregistry.cms.hhs.gov/api/?";

    
    public function GetCallFaxIInfo($number,$enumeration,$tax,$fname,$namealias,$lname,$orgname,$addrpurpose,$city,$state,$postcode,$country,$limit,$skip,$pretty,$version)
    {
        $namealias="false";
        $param = array(
            "number"=>$number,
            "enumeration_type"=>$enumeration,
            "taxonomy_description"=>$tax,
            "first_name"=>$fname,
            "use_first_name_alias"=>$namealias,
            "last_name"=>$lname,
            "organization_name"=>$orgname,
            "address_purpose"=>$addrpurpose,
            "city"=>$city,
            "state"=>$state,
            "postal_code"=>$postcode,
            "country_code"=>$postcode,
            "limit"=>$limit,
            "skip"=>$skip,
            "pretty"=>$pretty,
            "version"=>$version
        );
        
        
        $parameters = http_build_query($param);
        
        //curlsetup 
        //setupheader
        
        $ch = curl_init($this->endpointurl.$parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $resultdata = json_decode($result);
       // var_dump($resultdata);exit();
        $count = $resultdata->result_count;
       
        if($count!=0)
        {
            $resultar = $resultdata->results;
            //lets loop through and grap the NPI
           
            foreach($resultar as $lr)
            {
               
                $addressar =$lr->addresses;
                $npinumber = $lr->number;
                foreach($addressar as $ar)
                {
                
                    $addrphoneAr[] = array("address"=>$ar->address_1.",".$ar->city." ".$ar->state." ".$ar->postal_code,"tel"=>$ar->telephone_number,"fax"=>$ar->fax_number,"npinumber"=>$npinumber);
                    
                }
            }
           
                
            if($addrphoneAr && count($addrphoneAr) >0)
            {
                
               // var_dump($addrphoneAr);
                return $addrphoneAr;
            } 
            else{
                $msg = array("msg"=>"No Address,Phone,or Fax Number Not Found");
                return $msg;
            }

                
           
           
        }
        else{
            $msg =array("results"=>"No Reuslts Found","count"=>$count);
            return $msg;
        }
    }
    /*5/16/25 Adding for Pharmacy Lookup */
    public function GetAllPharmNPIInfo($enumeration,$orgname,$city,$state,$postcode,$country,$limit,$skip,$pretty,$version)
    {
       
        $param = array(
            
            "enumeration_type"=>$enumeration,
            "organization_name"=>$orgname,
            "city"=>$city,
            "state"=>$state,
            "zip"=>$postcode,
            "country_code"=>$country,
            "limit"=>$limit,
            "skip"=>$skip,
            "pretty"=>$pretty,
            "version"=>$version
        );
        
        
        $parameters = http_build_query($param);
        
        //curlsetup 
        //setupheader
        
        $ch = curl_init($this->endpointurl.$parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $resultdata = json_decode($result);
       // return $resultdata;
      // var_dump($resultdata);
        $count = $resultdata->result_count;
       
        if($count!=0)
        {
            $resultar = $resultdata->results;
            //lets loop through and grap the NPI
           
            foreach($resultar as $lr)
            {
               
                $addressar =$lr->addresses;
                $tax = $lr->taxonomies;
                
                foreach($addressar as $ar)
                {
                
                    $addrphoneAr[] = array("address"=>$this->ucname($ar->address_1).",".$this->ucname($ar->city)." ".$this->ucname($ar->state)." ".$this->ucname($ar->postal_code),"tel"=>$ar->telephone_number,"fax"=>$ar->fax_number);
                    
                }
                foreach($tax as $tx)
                {
                    $taxinfo[] = array("taxcode"=>$tx->code,"taxonomy"=>$tx->desc,"state"=>$tx->state,"license"=>$tx->license);
                }
                //lets run the name through the function to help clean up our search 
                $cleanorgname = $this->ucname($lr->basic->organization_name);
               // $cleanlname = $this->ucname($lr->basic->last_name);
               // $cleanmname = $this->ucname($lr->basic->middle_name);
                $namear[] = array("name"=>$cleanorgname,"addresses"=>$addrphoneAr,"npinumber"=>$lr->number,"taxonomies"=>$taxinfo);
            }
           
                
            if($namear && count($namear) >0)
            {
                return $namear;
                
            } 
            else{
                $msg = array("msg"=>"No Address,Phone,or Fax Number Not Found");
                return $msg;
            }

                
           
           
        }
        else{
            $msg =array("results"=>"No Results Found","count"=>$count);
            return $msg;
        }
    }
    public function GetAllNPIInfo($number,$enumeration,$tax,$fname,$namealias,$lname,$orgname,$addrpurpose,$city,$state,$postcode,$country,$limit,$skip,$pretty,$version)
    {
       
        $param = array(
            "number"=>$number,
            "enumeration_type"=>$enumeration,
            "taxonomy_description"=>$tax,
            "first_name"=>$fname,
            "use_first_name_alias"=>$namealias,
            "last_name"=>$lname,
            "organization_name"=>$orgname,
            "address_purpose"=>$addrpurpose,
            "city"=>$city,
            "state"=>$state,
            "zip"=>$postcode,
            "country_code"=>$country,
            "limit"=>$limit,
            "skip"=>$skip,
            "pretty"=>$pretty,
            "version"=>$version
        );
        
        
        $parameters = http_build_query($param);
        
        //curlsetup 
        //setupheader
        
        $ch = curl_init($this->endpointurl.$parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $resultdata = json_decode($result);
       // return $resultdata;
       //var_dump($resultdata);
        $count = $resultdata->result_count;
       
        if($count!=0)
        {
            $resultar = $resultdata->results;
            //lets loop through and grap the NPI
           
            foreach($resultar as $lr)
            {
               
                $addressar =$lr->addresses;
                $tax = $lr->taxonomies;
                
                foreach($addressar as $ar)
                {
                
                    $addrphoneAr[] = array("address"=>$this->ucname($ar->address_1).",".$this->ucname($ar->city)." ".$this->ucname($ar->state)." ".$this->ucname($ar->postal_code),"tel"=>$ar->telephone_number,"fax"=>$ar->fax_number);
                    
                }
                foreach($tax as $tx)
                {
                    $taxinfo[] = array("taxcode"=>$tx->code,"taxonomy"=>$tx->desc,"state"=>$tx->state,"license"=>$tx->license);
                }
                //lets run the name through the function to help clean up our search 
                $cleanfname = $this->ucname($lr->basic->first_name);
                $cleanlname = $this->ucname($lr->basic->last_name);
                $cleanmname = $this->ucname($lr->basic->middle_name);
                $namear[] = array("name"=>$cleanfname." ".$cleanmname." ".$cleanlname,"addresses"=>$addrphoneAr,"npinumber"=>$lr->number,"taxonomies"=>$taxinfo);
            }
           
                
            if($namear && count($namear) >0)
            {
                return $namear;
                
            } 
            else{
                $msg = array("msg"=>"No Address,Phone,or Fax Number Not Found");
                return $msg;
            }

                
           
           
        }
        else{
            $msg =array("results"=>"No Results Found","count"=>$count);
            return $msg;
        }
    }
   private function ucname($string) {
        $string =ucwords(strtolower($string));
    
        foreach (array('-', '\'') as $delimiter) {
          if (strpos($string, $delimiter)!==false) {
            $string =implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
          }
        }
        return $string;
    }
    public function FindNPI($number,$enumeration,$tax,$fname,$namealias,$lname,$orgname,$addrpurpose,$city,$state,$postcode,$country,$limit,$skip,$pretty,$version)
    {
        
        $param = array(
            "number"=>$number,
            "enumeration_type"=>$enumeration,
            "taxonomy_description"=>$tax,
            "first_name"=>$fname,
            "use_first_name_alias"=>$namealias,
            "last_name"=>$lname,
            "organization_name"=>$orgname,
            "address_purpose"=>$addrpurpose,
            "city"=>$city,
            "state"=>$state,
            "postal_code"=>$postcode,
            "country_code"=>$postcode,
            "limit"=>$limit,
            "skip"=>$skip,
            "pretty"=>$pretty,
            "version"=>$version
        );
        
        
        $parameters = http_build_query($param);
        
        //curlsetup 
        //setupheader
        
        $ch = curl_init($this->endpointurl.$parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        //var_dump($result);
        $resultdata = json_decode($result);
        //var_dump($resultdata);
        
        //lets grab variable to process or to pass back to the front end | WE dont have a definitive yet
        $count = $resultdata->result_count;
        if($count!=0)
        {
            $resultar = $resultdata->results;
            //lets loop through and grap the NPI
            foreach($resultar as $lr)
            {
                $npi=$lr->number;
                
            }
           $msg = array("result"=>"found","npinumber"=>$npi);
            return $msg;
            
        }
        else{
            $msg =array("results"=>"No Reuslts Found","count"=>$count);
            return $msg;
        }
    }

    public function GetNPIAddr($number,$enumeration,$tax,$fname,$namealias,$lname,$orgname,$addrpurpose,$city,$state,$postcode,$country,$limit,$skip,$pretty,$version)
    {
        
        $param = array(
            "number"=>$number,
            "enumeration_type"=>$enumeration,
            "taxonomy_description"=>$tax,
            "first_name"=>$fname,
            "use_first_name_alias"=>$namealias,
            "last_name"=>$lname,
            "organization_name"=>$orgname,
            "address_purpose"=>$addrpurpose,
            "city"=>$city,
            "state"=>$state,
            "postal_code"=>$postcode,
            "country_code"=>$postcode,
            "limit"=>$limit,
            "skip"=>$skip,
            "pretty"=>$pretty,
            "version"=>$version
        );
        
        
        $parameters = http_build_query($param);
        
        //curlsetup 
        //setupheader
        
        $ch = curl_init($this->endpointurl.$parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        //var_dump($result);
        $resultdata = json_decode($result);
        //var_dump($resultdata);
        
        //lets grab variable to process or to pass back to the front end | WE dont have a definitive yet
        $count = $resultdata->result_count;
        if($count!=0)
        {
            $resultar = $resultdata->results;
            //lets loop through and grap the NPI
            foreach($resultar as $lr)
            {
               
                $addressar =$lr->addresses;
                $npinumber = $lr->number;
            }
            
            if(count($addressar) >0 )
            {
                $msg = array("numberofaddrfound"=>count($addressar),"addresses"=>$addressar,"npinumber"=>$npinumber);
                 return $msg;
            }
           
        }
        else{
            $msg =array("results"=>"No Reuslts Found","count"=>$count);
            var_dump($msg);
        }
    }
}
?>