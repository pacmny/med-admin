<?php 

class SQLData{
    const DatabaseName ="willown9_fora";
    const SQLUsername ="willown9_admin";
    const SQLPassword ="Wi!!0w123";
    const ServerName = "localhost";
    
    private $con;

    public function __construct()
    {
       $con = new PDO("mysql:host=".self::ServerName.";dbname=".self::DatabaseName,self::SQLUsername,self::SQLPassword);
       $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
       $this->con =$con;
      // return $con;
    }

    public function GetOrdersNumber()
    {
       
        $number = $this->IncrementOrdersNumber();
        return $number;
    }
    public function NewProvider($pid,$ordnumber,$firstname,$lastname,$address1,$address2,$city,$state,$zip,$tel,$fax,$npinum,$status)
    {

        $fname = $firstname;
        $lname = $lastname;
        $addr1 =$address1;
        $addr2 = $address2;
        $ncity = $city;
        $nstate = $state;
        $nzip = $zip;
        $ntel =$tel;
        $nfax = $fax;
        $nnpi = $npinum;
        $ordnumber = $ordnumber;
        $patientid = $pid;
        $status="pending";
        $sql="Insert Into providers (npinumber,ordernumber,patientid,addr1,addr2,city,state,postalcode,tel,fax,firstname,lastname,status) VALUES(:npinum,:ordernum,:pid,:addr1,:addr2,:city,:state,:zip,:tel,:fax,:fname,:lname,:stat)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":npinum",$npinum);
        $stmnt->bindParam(":ordernum",$ordnumber);
        $stmnt->bindParam(":pid",$pid);
        $stmnt->bindParam(":addr1",$addr1);
        $stmnt->bindParam(":addr2",$addr2);
        $stmnt->bindParam(":city",$ncity);
        $stmnt->bindParam(":state",$nstate);
        $stmnt->bindParam(":zip",$nzip);
        $stmnt->bindParam(":tel",$ntel);
        $stmnt->bindParam(":fax",$nfax);
        $stmnt->bindParam(":fname",$fname);
        $stmnt->bindParam(":lname",$lname);
        $stmnt->bindParam(":stat",$status);
        if($stmnt->execute())
        {

            //now send email 
            
            $result ="Inserted";
            $msgar = array("message"=>$result);
            return $msgar;
                
        }
        else{
            $error = $stmnt->error_get_last();
            $msg = array("message"=>$error);
            return $msg;
        }
    }
    public function GetOrderbyPatientID($patientId)
    {
        $ordresult = $this->GetPatientOrders($patientId);
        $orderHtmlGrid = $this->buildOrderGrid($ordresult);
        $ordarray = array("html"=>$orderHtmlGrid);
        print(json_encode($ordarray,JSON_PRETTY_PRINT));
    }
    public function GetOrderMedication($patientId)
    {
        $allMeds = $this->GetPatientMedications($patientId);
        //should have data now 
        $patientHtmlGrid = $this->buildMedGrid($allMeds);
        $resultar = array("html"=>$patientHtmlGrid);
        print(json_encode($resultar,JSON_PRETTY_PRINT));
    }
    public function InsertOrders($ordnum,$orderdate,$patientid,$ordertime,$ordertype,$abndelivered,$ordReadback,$primphysician,$sndphysician,$orderTracking,$npi,$address,$ordphone,$ordfax,$sendtophyscians,$woundcare,$verbalorder,$verborderDate,$verbalorderTime,$ordMedication,$ordSupplies,$ordValue,$ordDiagnosis,$ordDescription,$ostatus)
    {

        $sql="Insert Into orders (ordernumber,patientid,orderdate,ordertime,ordertype,abndelivery,readorderback,primary_physician,sec_physician,ord_trackgroup,npinumber,address,phone,fax,sendtophys,woundcare,verbalorder,verborder_date,verborder_time,medication,supplies,valuesign,diagnosis,orderdescription,status)"
       ." Values(:ordnumber,:patientid,:ordDate,:ordTime,:ordType,:abdeliv,:ordReadback,:primphysician,:secphysician,:ordtrackgrp,:npinum,:ordAddress,:ordPhone,:ordFax,:sendphys,:woundcare,:verborder,:verborddate,:verbordtime,:medication,:supplies,:valsign,:orddiagnosis,:ordescrip,:ordstatus)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":ordnumber",$ordnum);
        $stmnt->bindParam(":patientid",$patientid);
        $stmnt->bindParam(":ordDate",$orderdate);
        $stmnt->bindParam(":ordTime",$ordertime);
        $stmnt->bindParam(":ordType",$ordertype);
        $stmnt->bindParam(":abdeliv",$abndelivered);
        $stmnt->bindParam(":ordReadback",$ordReadback);
        $stmnt->bindParam(":primphysician",$primphysician);
        $stmnt->bindParam(":secphysician", $sndphysician);
        $stmnt->bindParam(":ordtrackgrp", $orderTracking);
        $stmnt->bindParam(":npinum", $npi);
        $stmnt->bindParam(":ordAddress",$address);
        $stmnt->bindParam(":ordPhone",$ordphone);
        $stmnt->bindParam(":ordFax", $ordfax);
        $stmnt->bindParam(":sendphys", $sendtophyscians);
        $stmnt->bindParam(":woundcare", $woundcare);
        $stmnt->bindParam(":verborder", $verbalorder);
        $stmnt->bindParam(":verborddate",$verborderDate);
        $stmnt->bindParam(":verbordtime", $verbalorderTime);
        $stmnt->bindParam(":medication", $ordMedication);
        $stmnt->bindParam(":supplies", $ordSupplies);
        $stmnt->bindParam(":valsign", $ordValue);
        $stmnt->bindParam(":orddiagnosis",$ordDiagnosis);
        $stmnt->bindParam(":ordescrip",$ordDescription);
        $stmnt->bindParam(":ordstatus",$ostatus);
        try{
            if($stmnt->execute())
			{
                //lets check to see if there are meds attached
                $msg = "Inserted";
                $result = array("result"=>$msg);
                return json_encode($result);
                //return $msg;
               /* if($ordMedication=="true" || $ordDiagnosis=="true")
                {
                    
                    //run med sql if the value is true 
                    if($ordMedication=="true")
                    {
                        
                       $ordernum= $ordnum;
                       $patientid =$medar["patientid"];
                       $diagcode = $medar["dignosiscode"];
                       $medname = $medar["medicationname"];
                       $medamount = $medar["medamount"];
                       $meddoseuom = $medar["doseUOM"];
                       $medfreq = $medar["frequency"];
                       $prn = $medar["prn"];
                       $route = $medar["route"];
                        $altroute = $medar["altroute"];
                        $instruction = $medar["instruction"];
                        $medstartdt = $medar["medstartdt"];
                        $medenddt = $medar["medenddt"];
                        $medchangetype=$medar["changetype"];
                        $drugclass = $medar["drugclassification"];
                        $medunderstanding = $medar["medunderstanding"];
                        $addsettings = $medar["additionalsettings"];
                        
                        $medresult= $this->ProcessMedication($ordernum,$patientid,$medname,$diagcode,$medamount,$meddoseuom,$medfreq,$prn,$route,$altroute,$instruction,$medstartdt,$medenddt,$medchangetype,$drugclass,$medunderstanding,$addsettings);
                        //return $medresult;
                    }
                    if($hasdiagnosis["hasdiagnosis"]=="true")
                    {
                    
                        
                        //lets process diagnosis data
                        var_dump("Has data"." ".$hasdiagnosis["icd10"]);
                        $diagtype = $hasdiagnosis["diagtype"];
                        $diagcode = $hasdiagnosis["icd10"];
                      
                        $systemRating = $hasdiagnosis["syscontrolRating"];
                        $diagexacerbation = $hasdiagnosis["onsetexacerbation"];
                        $diagdate = $hasdiagnosis["diagdate"];
                        $diagresult = $this->ProcessDiagnosis($patientid,$ordnum,$diagtype,$diagcode,$systemRating,$diagexacerbation,$diagdate);
                        return $diagresult;
                    }
                }*/

            }
        }
        catch(PDOException $e )
        {
            $result = $e->__toString();
            return $result;
        }
       
    }
    public function ProcessDiagnosisInfo($patientid,$ordnum,$diagtype,$diagcode,$systemRating,$diagexacerbation,$diagdate)
    {

        $process = $this->ProcessDiagnosis($patientid,$ordnum,$diagtype,$diagcode,$systemRating,$diagexacerbation,$diagdate);
        return $process;

    }
    public function InsertMedication($ordernum,$patientid,$medname,$diagcode,$medamount,$meddoseuom,$medfreq,$prn,$route,$altroute,$instruction,$medstartdt,$medenddt,$medchangetype,$drugclass,$medunderstanding,$addsettings)
    {
       
        $medresult= $this->ProcessMedication($ordernum,$patientid,$medname,$diagcode,$medamount,$meddoseuom,$medfreq,$prn,$route,$altroute,$instruction,$medstartdt,$medenddt,$medchangetype,$drugclass,$medunderstanding,$addsettings);
        return $medresult;
    }
    public function GetAllPatientMeds($pid)
    {
        $patientID = $pid;
        $t = $this->GetPatientMedications($patientID);
        $buildgrid = $this->buildMedGrid($t);
        $buildgridar = array("data"=>$t,"gridhtml"=>$buildgrid);
        return json_encode($buildgridar,JSON_PRETTY_PRINT);

    }
    public function PullPatientArciveMeds($pid)
    {
        $patientID = $pid;
        $t = $this->GetArciveMeds($patientID);
        return $t;
    }
    public function RetrievePatientCurrentMeds($pid)
    {
        $patientID = $pid; 
        $t = $this->GetCurrentMeds($patientID);
        $buildgrid = $this->buildMedGrid($t);
        $buildgridar = array("data"=>$t,"gridhtml"=>$buildgrid);
        return json_encode($buildgridar,JSON_PRETTY_PRINT);
        //return $t;
    }
    public function GetAllCurrent_ArchiveMeds($pid)
    {
        $patientID = $pid;
        $t = $this->GetCurrentArchiveMeds($pid);
        return $t;
    }
    public function GetPatientDiagnosis($pid)
    {
       
        $patientID = $pid;
        $t = $this->GetPDiagnosis($patientID);
        return $t;
    }
    public function GetPatientDiagnosisGrid($pid)
    {
       
        $patientID = $pid;
        $t = $this->GetPDiagnosis($patientID);
        $diagGrid = $this->buildDiagnosisGrid($t);
        $diagGridar = array("data"=>$t,"gridhtml"=>$diagGrid);
        return json_encode($diagGridar,JSON_PRETTY_PRINT);
        //return $t;
    }
    public function DeleteMEdByPaitentID($patientID,$orderID)
    {
        $pid = $patientID; 
        $orderID = $orderID;
        $t = $this->DeleteMedsByOrdPID($pid,$orderID);
        return $t;
    }
    public function UpdateMedication($medID,$ordernumber, $patientID,$medicationname,$diagcode,$medamount,$doesoum,$medfrequency,$prn,$route,$instruction,$medstartdate,$medendate,$changetype,$drugClass,$understand,$addsettings)
    {
        $t = $this->UpdateMedsByOrderID($medID,$ordernumber,$patientID,$medicationname,$diagcode,$medamount,$doesoum,$medfrequency,$prn,$route,$instruction,$medstartdate,$medendate,$changetype,$drugClass,$understand,$addsettings);
        return $t;
    }
    public function GetMedicationByMedEntryID($medid)
    {
        $medentryID = $medid;
        $t = $this->GetMedicatinByMedEntry($medentryID);
        return $t;
    }
    public function UpdateDiagnosisInfo($pid,$ordnum,$diagId,$diagtype,$diagcode,$onset,$diagDate,$controlrate)
    {
        $t = $this->UpdateDiagnosis($pid,$ordnum,$diagId,$diagtype,$diagcode,$onset,$diagDate,$controlrate);
        return $t;
    }
    public function EditDiagnosisByOrdPID($did, $pid)
    {
        $dnoseID = $did;
        $patID = $pid; 
        $t = $this-> GetPDiagnosisByDiagIdAndPId($dnoseID,$patID);
        return $t;
    }
    private function buildMedGrid($meddata)
    {
       
        //loop through the code and build out the table row
        if(is_array($meddata) && !empty($meddata))
        {
            //build grid
           foreach($meddata as $m)
           {

             if($m["prn"]=="true")
             {
                    $html .="<div class='columnrow prn'>
                    <div class='col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row'>
                            <span id=\"medid\" class=\"coltext\" medid=".$m["medentryid"].">".$m["order_number"]."</span>
                    </div>
                    <div class='col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row'>
                            <span id=\"row\" class=\"coltext\">".$m["medchangetype"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["additional_settings"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["medname"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["med_amount"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["med_frequency"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["prn"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext msgiconbtn\">".$m["route"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["alt_route"]."</span>
                    </div>
                </div>";
             }
             else{
                    $html .="<div class='columnrow'>
                    <div class='col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row'>
                            <span id=\"medid\" class=\"coltext\" medid=".$m["medentryid"].">".$m["order_number"]."</span>
                    </div>
                    <div class='col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row'>
                            <span id=\"row\" class=\"coltext\">".$m["medchangetype"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["additional_settings"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["medname"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["med_amount"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["med_frequency"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["prn"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext msgiconbtn\">".$m["route"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["alt_route"]."</span>
                    </div>
                </div>";
             }
           
           }
        return $html;

        }
    }
    private function buildOrderGrid($orddata)
    {
       
        //loop through the code and build out the table row
        if(is_array($orddata) && !empty($orddata))
        {
           // var_dump($diagdata);
            //build grid
           foreach($orddata as $m)
           {

           $orderID= $m["orddiagid"];
            $html .="<div class='columnrow' data-diagID='".$orderID."'>
            <div class='col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row'>
                    <span id=\"row\" class=\"coltext\">".$m["orderid"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\">".$m["patientid"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\">".$m["orderdate"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\">".$m["ordertype"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\">".$m["primary_physician"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\">".$m["verbalorder"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext msgiconbtn\">".$m["medication"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\">".$m["diagnosis"]."</span>
            </div>
        </div>";
           }
        return $html;

        }
    }
    private function buildDiagnosisGrid($diagdata)
    {
       
        //loop through the code and build out the table row
        if(is_array($diagdata) && !empty($diagdata))
        {
           // var_dump($diagdata);
            //build grid
           foreach($diagdata as $m)
           {

           $diagnosisID= $m["orddiagid"];
            $html .="<div class='columnrow' data-diagID='".$diagnosisID."'>
            <div class='col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row'>
                    <span id=\"row\" class=\"coltext\">".$m["order_id"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\">".$m["patientID"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\">".$m["diagnosis_code"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\">".$m["diagnosis_type"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\">".$m["system_controlrating"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\">".$m["onset_exacerbation"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext msgiconbtn\">".$m["diagnosis_date"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\">".$m["diagrelease"]."</span>
            </div>
        </div>";
           }
        return $html;

        }
    }
    private function UpdateMedsByOrderID($medID,$ordernumber,$patientID,$medicationname,$diagcode,$medamount,$doesoum,$medfrequency,$prn,$route,$instruction,$medstartdate,$medendate,$changetype,$drugClass,$understand,$addsettings)
    {
        
        $sql="Update medications SET order_number=:ordnumber,patient_id=:pid,medname=:medName,diagnose_code=:diagcode,med_amount=:medamount,
        med_doseuom=:doseuom,med_frequency=:medfrequency,prn=:Prn,route=:route,instruction=:instruction,med_startdate=:medstrtdate,med_enddate=:medenddt,
        medchangetype=:changetype,drug_classification=:drugclass,med_understanding=:understanding,additional_settings=:addsettings WHERE medentryid=:medid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":medid",$medID);
        $stmnt->bindParam(":ordnumber",$ordernumber);
        $stmnt->bindParam(":pid",$patientID);
        $stmnt->bindParam(":medName",$medicationname);
        $stmnt->bindParam(":diagcode",$diagcode);
        $stmnt->bindParam(":medamount",$medamount);
        $stmnt->bindParam(":doseuom",$doesoum);
        $stmnt->bindParam(":medfrequency",$medfrequency);
        $stmnt->bindParam(":Prn",$prn);
        $stmnt->bindParam(":route",$route);
        $stmnt->bindParam(":instruction",$instruction);
        $stmnt->bindParam(":medstrtdate",$medstartdate);
        $stmnt->bindParam(":medenddt",$medendate);
        $stmnt->bindParam(":changetype",$changetype);
        $stmnt->bindParam(":drugclass",$drugClass);
        $stmnt->bindParam(":understanding",$understand);
        $stmnt->bindParam(":addsettings",$addsettings);
        try{
            if($stmnt->execute())
            {
                $result="Updated";
                return $result; 
            }
        }
        catch(PDOException $e)
        {
            $result = $e->__toString();
            return $result; 
        }

    }
    private function DeleteMedsByOrdPID($pid,$orderID)
    {
        $sql="Delete FROM medications WHERE patientID=:pid AND order_id=:orderID";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
        $stmnt->bindParam(":orderID",$orderID);
        try
        {
            if($stmnt->execute())
            {
                $result = "Deleted";
                return $result;
            }
        }
        catch(PDOException $e)
        {
            $error = $e->__toString();
            return $error;
        }
    }


    private function GetPDiagnosis($patientID)
    {
       
        $sql="Select * FROM orders_diagnosis WHERE patientID=:pid ORDER BY order_id ASC";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$patientID);
       try{
            if($stmnt->execute())
            {
                $result =$stmnt->fetchAll();
                return $result;
            }
       }
       catch(PDOException $e){
           return $e->__toString();
       }
    }
    private function GetPDiagnosisByDiagIdAndPId($diagid, $patientID)
    {
        //var_dump($diagid );
       // var_dump($patientID);
       
        $sql="Select * FROM orders_diagnosis WHERE patientID=:pid AND orddiagid=:diagID ORDER BY orddiagid ASC";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$patientID);
        $stmnt->bindParam(":diagID",$diagid);
       try{
            if($stmnt->execute())
            {
                $result =$stmnt->fetchAll();
                //var_dump($result);
                return $result;
            }
       }
       catch(PDOException $e){
           return $e->__toString();
       }
    }
    private function GetCurrentMeds($pid)
    {

        $sql="Select * FROM medications WHERE patient_id=:pid AND DATE(med_enddate) >= CURDATE()  ORDER BY order_number ASC";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
       try{
            if($stmnt->execute())
            {
                $result =$stmnt->fetchAll();
                return $result;
            }
       }
       catch(PDOException $e){
           return $e->__toString();
       }
    }
    private function GetArciveMeds($pid)
    {
        
        $sql="Select * FROM medications as ArchiveMed WHERE patient_id=:pid AND DATE(med_enddate) < CURDATE() ORDER BY order_number ASC";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
       try{
            if($stmnt->execute())
            {
                $result =$stmnt->fetchAll();
                return $result;
            }
       }
       catch(PDOException $e){
           return $e->__toString();
       }
    }
    private function GetCurrentArchiveMeds($pid)
    {
        //Group SQL query to get all but group archived data together
        $sql="Select * FROM medications as `ArchiveDates` WHERE patient_id=:pid AND DATE(med_enddate) < CURDATE() OR (SELECT DATE(med_enddate) as `CurrentDate` ) >= CURDATE() ORDER BY order_number ASC";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
       try{
            if($stmnt->execute())
            {
                $result =$stmnt->fetchAll();
                return $result;
            }
       }
       catch(PDOException $e){
           return $e->__toString();
       }
    }
    private function GetPatientMedications($pid)
    {
        $sql="Select * FROM medications where patient_id=:pid";
        $stmnt =$this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
        try{
            if($stmnt->execute())
            {
                //if the sql statement execture correctly, we should have some data 
                $results = $stmnt->fetchAll();
                if(count($results) > 0)
                {
                    //we have data, lets send it back 
                   // var_dump($results);
                    return $results;
                }
                else{
                    $norecords ="No Records or Data Found";
                    return $norecords;
                }
            }
        }catch(PDOException $e)
        {
            $msg = $e->__toString();
            return $msg;
        }
    }
    private function GetMedicatinByMedEntry($medID)
    {
        $sql="Select * FROM medications WHERE medentryid=:medentry";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":medentry",$medID);
        try{
            if($stmnt->execute())
            {
                $result = $stmnt->fetchAll();
                return $result;
            }
        }
        catch(PDOException $e)
        {
            $result = $e->__toString();
            return $result;
        }
    }
    private function ProcessDiagnosis($pid,$ordid,$diagtype,$diagcode,$systemrating,$exacerbation,$diagDt)
    {
        //$con = $this->con;
        $sql="Insert Into orders_diagnosis (patientID,order_id,diagnosis_type,diagnosis_code,system_controlrating,onset_exacerbation,diagnosis_date) VALUES(:pid,:ordId,:diagtype,:diagcode,:syscontrolrating,:exacebration,:diagDate)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
        $stmnt->bindParam(":ordId",$ordid);
        $stmnt->bindParam(":diagtype",$diagtype);
        $stmnt->bindParam(":diagcode",$diagcode);
        $stmnt->bindParam(":syscontrolrating",$systemrating);
        $stmnt->bindParam(":exacebration",$exacerbation);
        $stmnt->bindParam(":diagDate",$diagDt);
        try
        {
            if($stmnt->execute())
            {
                $result ="Inserted";
                return $result;
            }
        }
        catch(PDOExeception $e)
        {
            $result = $e->__toString();
            return $result;
        }
    }
    
    private function UpdateDiagnosis($pid,$ordid,$diagId,$diagtype,$diagcode,$onset,$diagDate,$controlrate)
    {
        //$con = $this->con;
        
        $sql="UPDATE orders_diagnosis SET diagnosis_type=:diagtype,diagnosis_code=:diagcode,system_controlrating=:syscontrolrating,onset_exacerbation=:exacebration,diagnosis_date=:diagDate WHERE patientID=:pid AND orddiagid=:diagID";
        $stmnt = $this->con->prepare($sql);
       
        $stmnt->bindParam(":diagtype",$diagtype);
        $stmnt->bindParam(":diagcode",$diagcode);
        $stmnt->bindParam(":syscontrolrating",$controlrate);
        $stmnt->bindParam(":exacebration",$onset);
        $stmnt->bindParam(":diagDate",$diagDate);
        $stmnt->bindParam(":diagID",$diagId);
        $stmnt->bindParam(":pid",$pid);
        //$stmnt->bindParam(":ordId",$ordid);
        try
        {
            if($stmnt->execute())
            {
                $result ="Updated";
                $msg = array("result"=>$result);

                return json_encode($msg);
            }
        }
        catch(PDOExeception $e)
        {
            $result = $e->__toString();
            return $result;
        }
    }
    private function GetPatientOrders($patientId)
    {
        $pid = $patientId;
        $sql = "Select * FROM orders WHERE patientid=:pid ORDER BY orderid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
        try{

            if($stmnt->execute())
            {
                $result  = $stmnt->fetchAll();
               
                return $result;
            }
        }
        catch(PDOException $e)
        {
            $result = $e->__toString();
            return $result;
        }

    }
    private function IncrementOrdersNumber()
    {
       $presql="Select * FROM orders ORDER BY orderid DESC LIMIT 1";
       $stmnt = $this->con->prepare($presql);
       
       if($stmnt->execute())
       {
            //get records and count length 
            $records = $stmnt->fetchAll();
            $numofrecords = count($records);
            
          
            if($numofrecords ==1)
            {
                //get the order number and increment 
                foreach($records as $r)
                {
                    
                    
                        $nwordnum = (int)$r["ordernumber"] + 1;
                        //now assign new ordernumber to the previous ordervariable 
                        $ordnum = $nwordnum;
                        return $ordnum;
                    
                }
                
            }
        }
    }
    private function ProcessMedication($orderum,$patientid,$medname,$diagcode,$medamount,$meddoseuom,$medfreq,$prn,$route,$altroute,$instruction,$medstartdt,$medenddt,$medchangetype,$drugclass,$medunderstanding,$addsettings)
    {
       // $con = $this->con;
         
        $sql ="Insert Into medications (order_number,patient_id,medname,diagnose_code,med_amount,med_doseuom,med_frequency,prn,route,alt_route,instruction,med_startdate,med_enddate,medchangetype,drug_classification,med_understanding,additional_settings) VALUES(:ordnum,:paitentId,:medname,:diagcode,:medamount,:medoseuom,:medfreq,:prn,:route,:altroute,:instruction,:medstdate,:medenddt,:medchngetype,:drugclassification,:medunderstanding,:addsettings)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":ordnum",$orderum);
        $stmnt->bindParam(":paitentId",$patientid);
        $stmnt->bindParam(":medname",$medname);
        $stmnt->bindParam(":diagcode",$diagcode);
        $stmnt->bindParam(":medamount", $medamount);
        $stmnt->bindParam(":medoseuom",$meddoseuom);
        $stmnt->bindParam(":medfreq", $medfreq);
        $stmnt->bindParam(":prn",$prn);
        $stmnt->bindParam(":route",$route);
        $stmnt->bindParam(":altroute", $altroute);
        $stmnt->bindParam(":instruction",$instruction);
        $stmnt->bindParam(":medstdate",$medstartdt);
        $stmnt->bindParam(":medenddt",$medenddt);
        $stmnt->bindParam(":medchngetype",$medchangetype);
        $stmnt->bindParam(":drugclassification",$drugclass);
        $stmnt->bindParam(":medunderstanding", $medunderstanding);
        $stmnt->bindParam(":addsettings",$addsettings);

        try{

            if($stmnt->execute())
            {
               
                $result = "Inserted";
                $msg = array("sqlresult"=>$result);
                return $msg;//$result;
            }
        }
        catch(PDOException $e){
            var_dump($e->__toString());
            $result = $e->__toString();
            return $result;
        }
        
    
    }


}
?>