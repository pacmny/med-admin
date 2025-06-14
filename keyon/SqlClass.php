<?php 
class SQLData{
    const host ="localhost";
    const DatabaseName ="eventdb2";
    const SQLUsername ="keyon";
    const SQLPassword ="500452k";

    
    private $con;

    private $cptCosts = array("99453"=>0, "99454"=>0, "99457"=>0, "99458"=>0, "98975"=>19.38, "98976"=>55.72, "98980"=>50.18, "98981"=>40.84);

    public function __construct()
    {
       //require("consts.php");
       $con =new PDO("mysql:host=localhost;dbname=eventsdb2;charset=utf8mb4", "keyon", "500452k");//new PDO("mysql:host=$host;dbname=$DatabaseName;charset=utf8mb4", $SQLUsername, $SQLPassword);
       
       $con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
       $this->con =$con;
      // return $con;
    }
    public function getPatientInfobyPatId($accountnumber,$patientid)
    {
        $getpatinfo = $this->GetPatientAssignmentByAccntNPatientId($accountnumber,$patientid);
        return $getpatinfo;
    }
    public function grabOldMedListByMedId($accountnumber,$ordnumber,$medicationid,$patientid)
    {
        $sql="SELECT * FROM medications WHERE medentryid=:medid AND accountnumber=:accnt AND patient_id=:patid AND order_number=:ordnumber";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":medid",$medicationid);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":ordnumber",$ordnumber);
        try{

            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                $msgar = array("code"=>"200 Succesfull","results"=>$records,"count"=>count($records));
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar = array("code"=>"700-SQL Error","message"=>$e->__toString());
            return $msgar;
        }
    }
    public function InsertMedLog($accountnumber,$patientid,$patientname,$ordernumber,
    $providername,$providerid,$medicationid,$administrated_at,$time,$status,$yearmedtime,$notes,$providersignature,$provinitials)
    {
       $sql="INSERT INTO `medicationlog`(`accountnumber`, `patientid`, `patientname`, `ordernumber`, `providername`, `providerid`,
         `medicationid`, `administrated_at`, `time`, `status`, `yearmedtime`, `notes`, `providersignature`, `provinitials`)
         VALUE(:accnt,:patid,:patname,:ordnumber,:providname,:provid,:medid,:admin_at,:tme,:stat,:yrmedtime,:notes,:provsig,:provinit)";

       $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid); //status para,
        $stmnt->bindParam(":patname",$patientname);
        $stmnt->bindParam(":ordnumber",$ordernumber); //jso data should be updated here 
        $stmnt->bindParam(":providname",$providername);
        $stmnt->bindParam(":provid",$providerid);
        $stmnt->bindParam(":medid",$medicationid);
        $stmnt->bindParam(":admin_at",$administrated_at); //medication param 
         $stmnt->bindParam(":tme",$time);
         $stmnt->bindParam(":stat",$status);
        $stmnt->bindParam(":yrmedtime",$yearmedtime);
        $stmnt->bindParam(":notes",$notes);
        $stmnt->bindParam(":provsig",$providersignature);
        $stmnt->bindParam(":provinit",$provinitials); 
        try{
            if($stmnt->execute())
            {
                $msg = "Inserted";
                $msgar = array("code"=>"200-Successfull","results"=>"Inserted");
                return $msgar; 
            }
        }
        catch(PDOException $e)
        {
            $msgar = array("code"=>"700-Sql","error"=>$e->__toString());
            return $msgar;
        }
          
           
    }
    public function cloneOrderInfo($accountnumber,$patientid,$ordernumber)
    {
        $sql="SELECT * FROM orders WHERE ordernumber=:ordnumb AND accountnumber=:accnt AND patientid=:patid LIMIT 1";
        $stmnt= $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":ordnumb",$ordernumber);
        $stmnt->bindParam(":patid",$patientid);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                $msgar = array("status"=>"200-Successfull","records"=>$records);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar = array("code"=>"700-Sql","status"=>"Sql statement Failed","error"=>$e->__toString());
            return $msgar;
        }
    }
    public function updatePrevMedlogTble($accountnumber,$ordernumber,$medicationid,$patientid,$status,$changereason)
    {
       
        $sql="UPDATE medicationlog SET `status`=:stat,notes=:changereason WHERE medicationid=:medid AND accountnumber=:accnt AND patientid=:patid AND ordernumber=:ord";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":stat",$status);
        $stmnt->bindParam(":changereason",$changereason);
        $stmnt->bindParam(":medid",$medicationid);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":ord",$ordernumber);
        try{
            if($stmnt->execute())
            {
                $msg ="Updated";
                $msgar = array("status"=>"200-Successfull","message"=>"Updated records succesfull","results"=>$msg);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar = array("code"=>"700-Sql","status"=>"Sql statement Failed","error"=>$e->__toString());
            return $msgar;
        }
    }
    public function pastMedList($ordernumber,$accountnumber,$patientid,$medendDt,$status,$adminDate,$changereason,$medicationid)
    {
        var_dump("Dump params");
        var_dump($adminDate);
        var_dump($patientid);
        var_dump($medendDt);
        var_dump($medicationid);
        var_dump($status);
        var_dump($changereason);
        var_dump($ordernumber);
        var_dump($accountnumber);
        $sql="UPDATE `medications` SET med_enddate=:medendDt, medchangetype=:stat, dt_medchanged=:endDt, medchangreason=:chngreason, `status`=:stat WHERE patient_id=:patid AND order_number=:ordnumb
        AND medentryid=:meid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":medendDt",$medendDt);
        $stmnt->bindParam(":stat",$status);
        $stmnt->bindParam(":endDt",$adminDate);
        $stmnt->bindParam(":chngreason",$changereason);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":ordnumb",$ordernumber);
        $stmnt->bindParam(":meid",$medicationid);
       
       
        try{

            if($stmnt->execute())
            {
                $msg ="Updated";
                $msgar = array("status"=>"200 Successfull","results"=>$msg);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar = array("code"=>"700-SQL Error","message"=>$e->__toString());
            return $msgar;
        }
    }
    public function upatePrevOrder($accountnumber,$patientid,$ordernumber,$medicationid,$status,$changereason,$providersignature,$provinit)
    {
        var_dump($ordernumber);
        var_dump($patientid);
        var_dump($ordernumber);
        var_dump($status);
       
        $sql="UPDATE orders SET status=:stat, orderdescription=:chngnotes, providersignature=:provsig WHERE patientid=:patid AND ordernumber=:ordnumb";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":stat",$status);
        $stmnt->bindParam(":chngnotes",$changereason);
        $stmnt->bindParam(":provsig",$providersignature);
       // $stmnt->bindParam(":provinitials",$provinit);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":ordnumb",$ordernumber);
        try{

            if($stmnt->execute())
            {
                $msg ="Updated";
                $msgar = array("status"=>"200 Successfull","results"=>$msg);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar = array("code"=>"700-SQL Error","message"=>$e->__toString());
            return $msgar;
        }
    }
    public function UpdateMedLog($accountnumber,$patientid,$medicationid,$administeredat,$medadmintimes,$status,$ctime)
    {
        /*Not going to updadte the administered_at times because it breaks the relation between the medlogtimes table | Updating times only 
        *@administrated_at=:admindate,
        */
        $sql="UPDATE medicationlog SET  yearmedtime=:medtime, status=:stat, time=:tme
        WHERE accountnumber=:accnt AND patientid=:patid AND medicationid=:medid";
        $stmnt = $this->con->prepare($sql);
       // $stmnt->bindParam(":admindate",$administeredat);
        $stmnt->bindParam(":stat",$status); //status para,
        $stmnt->bindParam(":medtime",$medadmintimes); //json data should be updated here 
        $stmnt->bindParam(":tme",$ctime);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":medid",$medicationid); //medication param
        try{
            if($stmnt->execute())
            {
                $msg = "Updated";
                $msgar = array("code"=>"200-Successfull","result"=>$msg);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar = array("code"=>"700-Sql","error"=>$e->__toString());
            return $msgar;
        }
       
    }
    public function checkMedlogtablenfo($accountnumber,$patientid,$adminDate,$medid)
    {
        //var_dump($medid);
        $sql="SELECT * FROM `medicationlog` WHERE accountnumber=:accnt AND patientid=:patid AND medicationid=:medid";
       
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
       // $stmnt->bindParam(":admindate",$adminDate);
        $stmnt->bindParam(":medid",$medid);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                $count = count($records);
                $msgar = array("code"=>"200-Successfull","results"=>$records,"count"=>$count);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar =array("code"=>"700-Sql","error"=>$e->__toString());
            return $msgar;
        }
    }
    public function SearchMedLog($accountnumber,$patientid,$medicationid,$ordernumber,$administeredat)
    {
        $sql="SELECT * FROM `medicationlog` WHERE patientid=:patid AND accountnumber=:accnt AND ordernumber=:ordnumber";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":ordnumber",$ordernumber);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                $count = count($records);
                $msgar = array("code"=>"200-Successfull","results"=>$records,"count"=>$count);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar =array("code"=>"700-Sql","error"=>$e->__toString());
            return $msgar;
        }
        
    }
    public function getMedlogtbleInfo($accountnumber,$patientid,$medid,$adminDate,$admintimes,$provinitials,$provsignature)
    {
        $lgtimesar = array();
        $sql="SELECT * FROM medlogtimes WHERE accountnumber=:accnt AND patientid=:patid AND administeredate=:adminDt AND time=:tims";
        $stmnt = $this-con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":adminDt",$adminDate);
        $stmnt->bindParam(":tims",$admintimes);
        if(!empty($admintimes) && is_array($admintimes))
        {
            foreach($lgtime as $admintimes)
            {
                try{
                    $record = $stmnt-fetchAll();
                    $lgtimesar = array_push($lgtimesar,$record["time"]);
                }
                catch(PDOException $e)
                {
                    $msgar = array("code"=>"700-Sql","error"=>$e->__toString());
                    return $msgar;
                }
                
            }
            if(!empty($lgtimesar))
            {
                //return array 
                $count = count($lgtimesar);
                $msgar = array("code"=>"200-Successfull","count"=>$count,"results"=>array_filter($lgtimesar));
                return $msgar;
            }
            else{
                $lgtimesar = [];
                return $lgtimesar;
            }
        }
    }
    public function getmedtimeEntries($adminDate,$accountnumber,$patientid)
    {
       // var_dump($adminDate);
        $sql="SELECT * FROM medlogtimes WHERE accountnumber=:accnt AND patientid=:patid AND administerdate=:admindt";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":admindt",$adminDate);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                $msgar = array("status"=>"200-Successfull","results"=>$records,"count"=>count($records));
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar = array("code"=>"700-SQL error","error"=>$e->__toString());
            return $msgar;
        }
    }
    public function holdMedlogstatus($accountnumber,$patientid,$medchangestat,$medentryid)
    {
       
        $sql="UPDATE `medicationlog` SET status=:medchngstatus WHERE accountnumber=:accnt AND patientid=:patid AND medicationid=:mid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":medchngstatus",$medchangestat);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":mid",$medentryid);
        try{

            if($stmnt->execute())
            {
                $msgar = array("code"=>"200 Successfull","results"=>"Updated");
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar = array("code"=>"700-Sql error","error"=>$e->__toString(),"message"=>"Sorry, we're not able to hold this medication (med log tbl) at this time due to system failaure. Please contact 
            you system administrator should this error persist");
            return $msgar;
        }
    }
    public function HoldOrderByOrdnumPatId($accountnumber,$pid,$ordernum,$status)
    {
       // var_dump($pid." ".$medentryid." ".$ordernum." ".$dcdate." ".$transtype);
        $sql="UPDATE orders SET status=:chngstatus WHERE patientid=:patid and accountnumber=:accnt AND ordernumber=:ordnum";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":chngstatus",$status);
        $stmnt->bindParam(":patid",$pid);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":ordnum",$ordernum);
        try{
            if($stmnt->execute())
            {
                $result = "Updated";
                $msgar = array("code"=>"200 Successfully","result"=>$result);
               //var_dump($msgar);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msar = array("error"=>$e->__toString(),"message"=>"Sorry, the app is experiencing technical difficulties and please notify you're administrator should the problem persist.");
            return $msar;
        }
    }
    public function changedMedicationStatusByAPMID($patientid,$accountnumber,$medname,$medentryid,$medchangestat,$medchangereason,$medtimes,$dtrange)
    {
        $todaydt = new DateTime();
        $formatdate = $todaydt->format('Y-m-d H:i:s');
        $sql="UPDATE `medications` SET medchangetype=:medchngstatus,dt_medchanged=:medDtchange,status=:medchngstatus,medchangreason=:changereason,medholddates=:changedates,medstatustimes=:medtimes WHERE accountnumber=:accnt AND patient_id=:patid AND medentryid=:mid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":medchngstatus",$medchangestat);
        $stmnt->bindParam(":medDtchange",$formatdate);
        $stmnt->bindParam(":changereason",$medchangereason);
        $stmnt->bindParam(":changedates",$dtrange);
        $stmnt->bindParam(":medtimes",$medtimes);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":mid",$medentryid);
        try{

            if($stmnt->execute())
            {
                $msgar = array("code"=>"200 Successfull","results"=>"Updated");
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar = array("code"=>"700-Sql error","error"=>$e->__toString(),"message"=>"Sorry, we're not able to hold this medication at this time due to system failaure. Please contact 
            you system administrator should this error persist");
            return $msgar;
        }
    }
    public function updateRemainingTabs($accountnumber,$patientid,$medid,$remainingtablets)
    {
        $sql="UPDATE `medications` SET available=:tabsavailable WHERE accountnumber=:accnt AND patient_id=:patid AND medentryid=:mid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":tabsavailable",$remainingtablets);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":mid",$medid);
        try{

            if($stmnt->execute())
            {
                $msgar = array("code"=>"200 Successfull","results"=>"Updated");
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar = array("code"=>"700-Sql error","error"=>$e->__toString());
            return $msgar;
        }
    }
    public function UpdateMedLogandLogtimes($accountnumber,$patientid,$medid,$finltimeslot,$finlslotreason,$takentime,$status,$signoffdate,$signoffnurse,$signoffinit,$earlyreason)
    {
        $sql="UPDATE `medlogtimes` SET `takentime`=:taktime, `status`=:stat, `reason`=:earlyreas, `provsignoffsignature`=:provsignoff,`provsignoffinitials`=:provsignoffinit
        WHERE accountnumber=:accnt AND patientid=:patid AND medid=:mid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":taktime",$takentime);
        $stmnt->bindParam(":stat",$status);
        $stmnt->bindParam(":earlyreas",$earlyreason);
        $stmnt->bindParam(":provsignoff",$signoffnurse);
        $stmnt->bindParam(":provsignoffinit",$signoffinit);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":mid",$medid);
        try{
            if($stmnt->execute())
            {
                $msgar = array("code"=>"200-SQL","results"=>"Updated");
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar =array("code"=>"700-SQL error","error"=>$e->__toString());
            return $msgar;
        }
    }
    public function updateMedlogtableInfo($logentry,$accountnumber,$patientid,$medid,$adminDate,$admintimes,$provinitials,$provsignature)
    {
        $sql="UPDATE `medlogtimes` SET `administerdate`=:adminDt, `time`=:admintimes, `providinitials`=:provinit, `provsignature`=:provsig
        WHERE accountnumber=:accnt AND patientid=:patid AND logid=:lid AND `medid`=:med";
        /*
         $sql="UPDATE `medlogtimes` SET `accountnumber`=:accnt, `patientid`=:patid, `medid`=:med, `administerdate`=:adminDt, `time`=:admintimes, `providinitials`=:provinit, `provsignature`=:provsig
        WHERE accountnumber=:accnt AND patientid=:patid AND logid=:lid AND `medid`=:med";
        */
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":med",$medid);
        $stmnt->bindParam(":adminDt",$adminDate);
        $stmnt->bindParam(":admintimes",$admintimes);
        $stmnt->bindParam(":provinit",$provinitials);
        $stmnt->bindParam(":provsig",$provsignature);
        $stmnt->bindParam(":lid",$logentry);
        try{

            if($stmnt->execute())
            {
                $updtmsg = "Updated";
                $msg = array("code"=>"200-Succuessfull","results"=>$updtmsg);
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("code"=>"700-SQL Error","error"=>$e->__toString());
           // var_dump($msg);
            return $msg;
        }

    }
    public function checklastmedlogtime($medid,$accountnumber)
    {
    
   
        $sql="SELECT * FROM medlogtimes WHERE medid=:medid AND accountnumber=:accnt ORDER BY administerdate DESC LIMIT 1";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":medid",$medid);
        $stmnt->bindParam(":accnt",$accountnumber);
        try{

            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
               // var_dump($records);
                $msgar = array("code"=>"200 Successfull","records"=>$records,"count"=>count($records));
               
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar = array("code"=>"700-Sql Error","message"=>$e->__toString());
            return $msgar;
        }
    }
    public function insertMedlogtableInfo($accountnumber,$patientid,$medid,$adminDate,$admintimes,$provinitials,$provsignature)
    {
       
        $sql="INSERT INTO `medlogtimes`(`accountnumber`, `patientid`, `medid`, `administerdate`, `time`, `providinitials`, `provsignature`) 
        VALUES (:accnt,:patid,:med,:adminDt,:admintimes,:provinit,:provsig)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":med",$medid);
        $stmnt->bindParam(":adminDt",$adminDate);
        $stmnt->bindParam(":admintimes",$admintimes);
        $stmnt->bindParam(":provinit",$provinitials);
        $stmnt->bindParam(":provsig",$provsignature);
        try{

            if($stmnt->execute())
            {
                $response ="Insert";
              
                $msg = array("code"=>"200-Succuessfull","results"=>$response);
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("code"=>"700-SQL Error","error"=>$e->__toString());
            return $msg;
        }

    }
    /*5/16/25 Looking up patient assigned pharmacy*/ 
    public function lookupPatientPharmacy($accountnumber,$patientid)
    {
        $sql="SELECT * FROM patpharmacy WHERE accountnumber=:accnt AND patientid=:patid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        try{
             if($stmnt->execute())
             {
                $records = $stmnt->fetchAll();
                $count = count($records);
                $msg = array("code"=>"200-Successfull","count"=>$count,"results"=>$records);
                return $msg;
             }
        }
        catch(PDOException $e)
        {
            $msg =array("code"=>"700-Sql","error"=>$e->__toString());
            return $msg;
        }
    }
    /*5/13/25 Lookup Existing */
    public function findPharmacy($accountnumber, $pharmacyname,$npinumber)
    {
        $sql="SELECT * FROM `pharmacy` WHERE `pharmacyname`=:phrmname AND npi=:npi AND accountnumber=:accnt";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":phrmname",$pharmacyname);
        $stmnt->bindParam(":npi",$npinumber);
        $stmnt->bindParam(":accnt",$accountnumber);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                $msgar = array("code"=>"200 Successfull","count"=>count($records),"records"=>$records);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("code"=>"700-Sql","error"=>$e->__toString());
            return $msg;
        }
    }
    /*5/13 Adding Pharmacy Information */
    public function InsertPharmacy($accountnumber,$pharmacyname,$pharmnpi,$pharmaddr,$pharmacyOffice,$pharmacyCell,$pharmdeanumber,$pharmacyEmail)
    {
        /*
        *@city, 
       * @state,
        *@zip all param that are hardcoded for now. We'll adjust it later when using the api to find Pharmacy information
        */
        $city="";
        $state="";
        $zip=""; 
        $sql="INSERT INTO `pharmacy`(`accountnumber`,`pharmacyname`, `deanumber`, `npi`, `address`, `city`, `state`, `zip`, `officenumber`, `cellphone`, `email`)
         VALUES (:accnt,:phrmname,:deanum,:npi,:addr,:city,:states,:zip,:officephn,:officeCell,:phrmemail)";
         $stmnt = $this->con->prepare($sql);
         $stmnt->bindParam(":accnt",$accountnumber);
         $stmnt->bindParam(":phrmname",$pharmacyname);
         $stmnt->bindParam(":deanum",$pharmdeanumber);
         $stmnt->bindParam(":npi",$pharmnpi);
         $stmnt->bindParam(":addr",$pharmaddr);
         $stmnt->bindParam(":city",$city);
         $stmnt->bindParam(":states",$state);
         $stmnt->bindParam(":zip",$zip);
         $stmnt->bindParam(":officephn",$pharmacyOffice);
         $stmnt->bindParam(":officeCell",$pharmacyCell);
         $stmnt->bindParam(":phrmemail",$pharmacyEmail);
         try{
             if($stmnt->execute())
             {
                $msg="Inserted";
                $msgar = array("code"=>"200 Successfull","results"=>$msg);
                return $msgar;
             }
         }
         catch(PDOException $e)
         {
            $msg = array("code"=>"700-Sql","error"=>$e->__toString());
            return $msg;
         }
    }
    /*5/13 Adding Patient Pharmacy Information */
    public function logpaPharmacy($accountnumber,$subaccountnumber,$patientid, $provnpi, $pharmacyname,$pharmaddr, $pharmdeanumber,$pharmnpi,$assigndt,$notes)
    {
        $sql="INSERT INTO `patpharmacy`(`accountnumber`, `subaccountnumber`, `patientid`, `provnpi`, `pharmacyname`, `pharmaddress`, `pharmdea`, `npinumber`, `assigned_date`, `notes`)
         VALUES (:accnt,:accnt,:patid,:provnpi,:pharmname,:phrmaddr,:pharmdea,:npinum,:assigndt,:notes)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":subaccnt",$subaccountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":provnpi",$provnpi);
        $stmnt->bindParam(":pharmname",$pharmacyname);
        $stmnt->bindParam(":phrmaddr",$pharmaddr);
        $stmnt->bindParam(":pharmdea",$pharmdeanumber);
        $stmnt->bindParam(":npinum",$pharmnpi);
        $stmnt->bindParam(":assigndt",$assigndt);
        $stmnt->bindParam(":notes",$notes);
       // $stmnt->bindParam(":pharmcell",$pharmcell);
       // $stmnt->bindParam(":phemail",$pharmemail);
        try{
            if($stmnt->execute())
            {
                $msg="Inserted";
                $msgar = array("code"=>"200-Successfull","results"=>$msg);
                return $msgar;
            }
        }
        catch(PODException $e)
        {
            $msgar = array("code"=>"700-Sql","error"=>$e->__toString());
            return $msgar;
        }
    }
    /* 5/12/25 Insert Provider Information */
    public function logpaProvider($accountnumber,$subaccountnumber,$firstname,$lastname,$npinumber,$provemail,$deanumber,$taxonomy,
    $ordernumber,$patientid,$addr1,$phone,$fax,$proffisionalicense)
    {
       
        $sql="INSERT INTO paproviders (npinumber,accountnumber,subaccountnumber,licensenumber,deanumber,taxonomy,ordernumber,patientid,addr1,tel,fax,firstname,lastname,email) 
        VALUES (:npi,:accnt,:subaccnt,:proflicense,:deanum,:tax,:ordnum,:patid,:addr,:phn,:fax,:fname,:lname,:email)";
        $stmnt = $this->con->prepare($sql);
         $stmnt->bindParam(":npi",$npinumber);
        $stmnt->bindparam(":accnt",$accountnumber);
        $stmnt->bindParam(":subaccnt",$subaccountnumber);
        $stmnt->bindParam(":proflicense",$proffisionalicense);
        $stmnt->bindParam(":deanum",$deanumber);
        $stmnt->bindParam(":tax",$taxonomy);
        $stmnt->bindParam(":ordnum",$ordernumber);
        $stmnt->bindParam(":patid",$patientid);  
        $stmnt->bindParam(":addr",$addr1); 
        $stmnt->bindParam(":phn",$phone);
        $stmnt->bindParam(":fax",$fax);
        $stmnt->bindParam(":fname",$firstname);
        $stmnt->bindParam(":lname",$lastname);
        $stmnt->bindParam(":email",$provemail);
        try{
            if($stmnt->execute())
            {
                $result="Inserted";
                $msg = array("code"=>"200-Successfull","results"=>$result);
                return $msg;

            }
        }
        catch(PDOException $e)
        {
            $msg = array("code"=>"700-Sql","error"=>$e->__toString());
            return $msg;
        }
    }
    /*5/10/2025 - Insert Perscription */
    public function InsertPerscription($accountnumber,$patientid,$medname, $rxnumber, $dtfilled,$refills,$startdate,$enddate,$refillreminderdt,$refillexpirationdt)
    {
        $sql="INSERT INTO prescription (accountnumber,patientid,medicationame,rxnumber,filldate,numofrefills,startdate,enddate,refill_reminderDate,expiration_refillDate)
        VALUES (:accnt,:patid,:medname,:rxnum,:filldt,:refills,:strtdt,:enddt,:refilldt,:refillexpdt)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":medname",$medname);
        $stmnt->bindParam(":rxnum",$rxnumber);
        $stmnt->bindParam(":filldt",$dtfilled);
        $stmnt->bindParam(":refills",$refills);
        $stmnt->bindParam(":strtdt",$startdate);
        $stmnt->bindParam(":enddt",$enddate);
        $stmnt->bindParam(":refilldt",$refillreminderdt);
        $stmnt->bindParam(":refillexpdt",$refillexpirationdt);
        try{
            if($stmnt->execute())
            {
                $msg ="Inserted";
                $msgar = array("code"=>"200 Successfull","results"=>$msg);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("code"=>"700-Sql","error"=>$e->__toString());
            return $msg;
        }
    }
    /*5/9/2025 Adding Administration Medication Function */
    public function InsertAdminMecationInfo($accountnumber,$ordernumber,$patientid,$ndcnumber,$rx,$prn,$newmedsettings,$totalTabs,$route,$diagnois,$freq,$dosage,$medname,$instruction,$medchangetype)
    {
        $shorthand=$medname;
        $status="pending";
        $writer="System";// Laster we need to go back and pass in the writer | Should be an Office Admin, Nurse and or Provider vs System
        if($prn==null || $prn==Null)
        {
            $prn="";
        }
        $sql="INSERT INTO medications (accountnumber,order_number,patient_id,ndcnumber,rxnorns,prn,additional_settings,total,`route`,diagnose_code,med_frequency,alt_route,med_amount,med_doseuom,medname,med_startdate,shorthand,
        instruction,medchangetype,status,writer)
        VALUES(:accnt,:ordnum,:patid,:ndcnum,:rxnum,:prn,:adminsetting,:totaltabs,:route,:diag,:freq,:altroute,:medamnt,:dosage,:medname,:medstrtdt,:shorthand,:instruction,:medchange,:stat,:writer)";
        $startdate = date('Y-m-d');
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":ordnum",$ordernumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":ndcnum",$ndcnumber);
        $stmnt->bindParam(":rxnum",$rx);
        $stmnt->bindParam(":prn",$prn);
        $stmnt->bindParam(":adminsetting",$newmedsettings);
        $stmnt->bindParam(":totaltabs",$totalTabs);
        $stmnt->bindParam(":route",$route);
        $stmnt->bindParam(":diag",$diagnois);
        $stmnt->bindParam(":freq",$freq);
        $stmnt->bindParam(":altroute",$freq);
        $stmnt->bindParam(":medamnt",$dosage);
        $stmnt->bindParam(":dosage",$dosage);
        $stmnt->bindParam(":medname",$medname);
        $stmnt->bindParam(":medstrtdt",$startdate);
        $stmnt->bindParam(":shorthand",$shorthand);
        $stmnt->bindParam(":instruction",$instruction);
        $stmnt->bindParam(":medchange",$medchangetype);
        $stmnt->bindParam(":stat",$status);
        $stmnt->bindParam(":writer",$writer);

        try{
            if($stmnt->execute())
            {
                $msg="Inserted";
                $msgar = array("code"=>"200-Successfull","result"=>$msg);
                
                $sql2 = "SELECT * FROM medications WHERE order_number=:ordnumber ORDER BY med_startdate DESC LIMIT 1";
                $stmnt2 = $this->con->prepare($sql2);
                $stmnt2->bindParam(":ordnumber",$ordernumber);
                try{

                    if($stmnt2->execute())
                    {
                        $record2 = $stmnt2->fetchAll();
                        $newmedentryid = "newEntryId"."=>".$records[0]["medentryid"];
                        array_push($msgar,$newmedentryid);

                    }
                }
                catch(PDOException $e)
                {
                    $ermsg = array("error"=>"700-SQL error","message"=>$e->__toString(),"firstsqlstatus"=>$msg);
                    return $ermsg;
                }
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msgar = array("code"=>"700-SQL","error"=>$e->__toString());
            return $msgar;
        }
    }
    /*5/9/2025 Adding functio To Grab Patient Addresss
    *@patientid
    @return associative array - address
    */
    public function grabPatientAddressbyID($patientid)
    {
        $sql="SELECT `address`,`city`,`state`,`zip` FROM patients WHERE psersonalID=:patid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":patid",$patientid);
        try{

            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                $msg = array("code"=>"200-Successfull","address"=>$records[0]["address"]);
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $errmsg = $e->__toString();
            $msg = array("code"=>"700-Sql","error"=>$errmsg);
            return $msg;
        }
    }
    public function GetOrdersNumber()
    {
       
        $number = $this->IncrementOrdersNumber();
        return $number;
    }
    public function ImportPatients($mainid,$accountnumber,$uniqueID,$username,$accnType,$firstname,$lastname,$phone,
    $email,$address,$city,$state,$zip,$proffisionalicense,$accntStatus,$upremission,$emrlicense,$ucreationDt,
    $compPreference,$dob,$soclast4,$emgcontact,$emgcontactphone,$emcontactphone2,$watchlist)
    {
        
        if($accnType=="Provider")
        {
            //var_dump("Provider");exit();
            $crtsubaccnt = $this->CreateAccountUser($accountnumber,$uniqueID,$username,$accnType,$firstname,$lastname,$phone,
            $email,$address,$city,$state,$zip,$proffisionalicense,$accntStatus,$upremission,$emrlicense,$ucreationDt,
            $compPreference,$uploadfileurl);
           
            //var_dump($crtsubaccnt);
            return $crtsubaccnt;
        }
        else{
            //there all patients and need both accounts 
           // var_dump("Here...Patients");
            $crtsubaccnt = $this->CreateAccountUser($accountnumber,$uniqueID,$username,$accnType,$firstname,$lastname,$phone,
            $email,$address,$city,$state,$zip,$proffisionalicense,$accntStatus,$upremission,$emrlicense,$ucreationDt,
            $compPreference,$uploadfileurl);
            //var_dump($crtsubaccnt);
            if($crtsubaccnt["status"]=="Successfull")
            {
               // var_dump("Inside Success Statement");
                //now Import into Patient Table  
                $gender="Male-Import";
                $maritalstatus="Import";
                $insuranceprovider="Import";
                $policynumber="Import";
                $bloodtype="Import";
                $allergies="Import";
                $accntStatus="Active";
                $insertpat = $this->AddPatientAccountImport($mainid,$accountnumber,$uniqueID,$firstname,$lastname,$dob,$soclast4,$gender,$phone,$email,$address,$city,$state,$zip,$emgcontact,$emgcontactphone,$maritalstatus,$insuranceprovider,$policynumber,$bloodtype,$allergies,$watchlist,$accntStatus);
               // var_dump($insertpat);exit();
                return $insertpat;
            }
        }
        
      //  var_dump($crtsubaccnt);
           
    }
   private function AddPatientAccountImport($mainId,$accountnumber,$uniqueID,$firstname,$lastname,$dob,$socnum,$gender,$phone,$email,$address,$city,$state,$zip,$emgcontact,$emgcontactphone,$maritalstatus,$insuranceprovider,$policynumber,$bloodtype,$allergies,$watchlist,$accntStatus)
    {
        $accountstatus="Active";
      // var_dump($watchlist);
      var_dump(json_decode($watchlist));
      $de = json_decode($watchlist);
      $en = json_encode($de);
      $watchlist = $en;
      var_dump($watchlist);
      var_dump($dob);
      if($dob=="NULL" || $dob=='null')
      {
        $dob=date('Y-m-d');
      }
       if($watchlist==null)
       {
       // var_dump(json_decode($watchlist));
       }
        $sql="INSERT INTO `patients`(`mid`, `accountnumber`, `psersonalID`, `first_name`, `lastname`, `date_of_birth`, `socnum`, `gender`, `contact_number`, `email`, `address`, `city`, `state`, `zip`, `emergency_contactname`, `emergency_contact_number`, `matial_status`, `insurance_provider`, `insurance_policy_number`, `bloodtype`, `allegeries`, `watchmeasurement`, `accntstatus`) 
        VALUES (:mid, :accntnumber,:unid,:ufname,:ulname,:dob,:last4,:ggender,:uphone,:uemail,:addr,:city,:ustate,:uzip,:emgcontact,:emgcontactnumber,:maritalstatus,:insuranceprov,:policynumber,:bloodtype,:allergies,:wtchmeasurement,:accntstatus)";
           // var_dump($sql);
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":mid",$mainId);
            $stmnt->bindParam(":accntnumber",$accountnumber);
            $stmnt->bindParam(":unid",$uniqueID);
            $stmnt->bindParam(":ufname",$firstname);
            $stmnt->bindParam(":ulname",$lastname);
            $stmnt->bindParam(":dob",$dob);
            $stmnt->bindParam(":last4",$socnum);
            $stmnt->bindParam(":ggender",$gender);
            $stmnt->bindParam(":uphone",$phone);
            $stmnt->bindParam(":uemail",$email);
            $stmnt->bindParam(":addr",$address);
            $stmnt->bindParam(":city",$city);
            $stmnt->bindParam(":ustate",$state);
            $stmnt->bindParam(":uzip",$zip);
            $stmnt->bindParam(":emgcontact",$emgcontact);
            $stmnt->bindParam(":emgcontactnumber",$emgcontactphone);
            $stmnt->bindParam(":maritalstatus",$maritalstatus);
            $stmnt->bindParam(":insuranceprov",$insuranceprovider);
            $stmnt->bindParam(":policynumber",$policynumber);
            $stmnt->bindParam(":bloodtype",$bloodtype);
            $stmnt->bindParam(":allergies",$allergies);
            $stmnt->bindParam(":wtchmeasurement",$watchlist);
            $stmnt->bindParam(":accntstatus",$accountstatus);
           // var_dump("Now ready to try sql statement");
            try{

                if($stmnt->execute())
                {
                    $msg="Patient Created";
                    $msgar = array("status"=>"Successfull","message"=>$msg);
                   // var_dump($msgar);
                    return $msgar;
                }
            }
            catch(PDOException $e)
            {
                $msg = $e->__toString();
                var_dump($msg);
                return $msg;
            }
    }
    public function GetAllAccountUser()
    {
        $sql="SELECT * FROM `account_user`";
        $stmnt = $this->con->prepare($sql);
        try{

            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                $msg = array("status"=>"200 Successfull","records"=>$records);
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    private function CreateAccountUserImport($accountnumber,$uniqueID,$username,$accnType,$firstname,$lastname,$phone,$email,$address,$city,$state,$zip,$proffisionalicense,$accntStatus,$upremission,$emrlicense,$ucreationDt,$compPreference)
    {
      
        $sql="INSERT INTO `account_user`(`accountnumber`, `unique_ID`, `username`, `account_type`, `user_Fname`, `user_Lname`, `user_phone`, `user_email`, `address`, `city`, `state`, `zip`, `proffesionalLicense`, `account_status`, `user_premission`, `emr_licensenumber`, `userCreationDt`, `communication_preference`) VALUES (
            :accntnumber,:unid,:username,:accnttype,:ufname,:ulname,:uphone,:uemail,:addr,:city,:ustate,:uzip,:proflicense,:accntstatus,:upremission,:emrlicense,:ucreationDt,:compref)";
            $stmnt = $this->con->prepare($sql);
          //  $stmnt->bindParam(":mid",$mainid);
            $stmnt->bindParam(":accntnumber",$accountnumber);
            $stmnt->bindParam(":unid",$uniqueID);
            $stmnt->bindParam(":username",$username);
            $stmnt->bindParam(":accnttype",$accnType);
            $stmnt->bindParam(":ufname",$firstname);
            $stmnt->bindParam(":ulname",$lastname);
            $stmnt->bindParam(":uphone",$phone);
            $stmnt->bindParam(":uemail",$email);
            $stmnt->bindParam(":addr",$address);
            $stmnt->bindParam(":city",$city);
            $stmnt->bindParam(":ustate",$state);
            $stmnt->bindParam(":uzip",$zip);
            $stmnt->bindParam(":proflicense",$proffisionalicense);
            $stmnt->bindParam(":accntstatus",$accntStatus);
            $stmnt->bindParam(":upremission",$upremission);
            $stmnt->bindParam(":emrlicense",$emrlicense);
            $stmnt->bindParam(":ucreationDt",$ucreationDt);
            $stmnt->bindParam(":compref",$compPreference);
           
            try{

                if($stmnt->execute())
                {
                    $msg="Accounted Created";
                    $msg = array("status"=>"Successfull","message"=>$msg);
                    return $msg;
                }
            }
            catch(PDOException $e)
            {
                $msg = $e->__toString();
                return $msg;
            }
    }
    public function CreateAccount($accountnumber,$orgname,$accountrole,$acntContact,$firstname,$lastname,$username,$password,$email,$prAddr,$prCity,$prState,$prZip,$orgaddress,$orgcity,$orgstate,$orgzip,$phone,$packageLevel,$npi,$license,$userLicense,
    $licenseState,$licenseStartDate,$licenseEndDt,$licenseCost,$accounttype,$secreteQuestion,$terms)
    {
        $presql="SELECT * FROM `accounts` WHERE accountnumber=:accntnumber";
        $stmnt2 = $this->con->prepare($presql);
        $stmnt2->bindParam(":accntnumber",$accountnumber);
        try{

            if($stmnt2->execute())
            {
                $records = $stmnt2->fetchAll();
                if(count($records) >=1)
                {
                    $mar = array("status"=>"Account Exist");
                    return $mar;
                }
                else{
                    $sql="INSERT INTO `accounts`( `accountnumber`, `organization`, `account_role`, `account_contact`,`firstname`,`lastname`, `username`, `password`, `email`, `org_address`, `org_city`, `org_state`, `org_zip`, `address`, `city`, `state`, `zip`, `phone`, `packagelevel`, `npi`, `license`, `userlicense`, `license_status`, `license_startDt`, `license_endDt`, `license_cost`, `account_type`, `secret_question`, `terms`) VALUES (
                        :accntnumber,:orgname,:accountrole,:account_contact,:fname,:lname,:uname,:pword,:email,:orgaddr,:orgcity,:orgstate,:orgzip,:addr,:city,:prstate,:zip,:phone,:pckglevel,:npi,:license,:userlicense,:licensestat,:licensStrtDt,:licenseEndDt,:licenseCost,:accounttype,:secretquestion,:terms)";
                        $stmnt = $this->con->prepare($sql);
                        $stmnt->bindParam(":accntnumber",$accountnumber);
                        $stmnt->bindParam(":orgname",$orgname);
                        $stmnt->bindParam(":accountrole",$accountrole);
                        $stmnt->bindParam(":account_contact",$acntContact);
                        $stmnt->bindParam(":fname",$firstname);
                        $stmnt->bindParam(":lname",$lastname);
                        $stmnt->bindParam(":uname",$username);
                        $stmnt->bindParam(":pword",$password);
                        $stmnt->bindParam(":email",$email);
                        $stmnt->bindParam(":orgaddr",$orgaddress);
                        $stmnt->bindParam(":orgcity",$orgcity);
                        $stmnt->bindParam(":orgstate",$orgstate);
                        $stmnt->bindParam(":orgzip",$orgzip);
                        $stmnt->bindParam(":addr",$prAddr);
                        $stmnt->bindParam(":city",$prCity);
                        $stmnt->bindParam(":prstate",$prState);
                        $stmnt->bindParam(":zip",$prZip);
                        $stmnt->bindParam(":phone",$phone);
                        $stmnt->bindParam(":pckglevel",$packageLevel);
                        $stmnt->bindParam(":npi",$npi);
                        $stmnt->bindParam(":license",$license);
                        $stmnt->bindParam(":userlicense",$userLicense);
                        $stmnt->bindParam(":licensestat",$licenseState);
                        $stmnt->bindParam(":licensStrtDt",$licenseStartDate);
                        $stmnt->bindParam(":licenseEndDt",$licenseEndDt);
                        $stmnt->bindParam(":licenseCost",$licenseCost);
                        $stmnt->bindParam(":accounttype",$accounttype);
                        $stmnt->bindParam(":secretquestion",$secreteQuestion);
                        $stmnt->bindParam(":terms",$terms);
                        try{
            
                            if($stmnt->execute())
                            {
                                $msg="Accounted Created";
                                $msg = array("status"=>"Inserted","message"=>$msg);
                                return $msg;
                            }
                        }
                        catch(PDOException $e)
                        {
                            $msg = $e->__toString();
                            return $msg;
                        }
                }
            }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
       
    }
    public function UpdateAccountInfoByID($accountnumber,$orgname,$accountrole,$acntContact,$username,$password,$email,$orgaddress,$city,$orgstate,$orgzip,$phone,$packageLevel,$npi,$license,$userLicense,$licenseState,$licenseStartDate,$licenseEndDt,$licenseCost,$accounttype,$secreteQuestion,$terms)
    {
        
        $sql="UPDATE `accounts` SET `accountnumber`=:accntnumber, `organization`=:orgname, `account_role`=:accountrole, `account_contact`=:account_contact, `username`=:uname, `password`=:pword, `email`=:email, `org_address`=:orgaddr, `org_city`=:orgcity, `org_state`=:orgstate, `org_zip`=:orgzip, `phone`=:phone, `packagelevel`=:pckglevel, `npi`=:npi, `license`=:license, `userlicense`=:userlicense, `license_status`=:licensestat, `license_startDt`=:licenseStrDt, `license_endDt`=:licenseEndDt, `license_cost`=:licenseCost, `account_type`=:accounttype, `secret_question`=:sec, `terms`=:terms WHERE accountnumber=:accntnumber)";
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":accntnumber",$accountnumber);
            $stmnt->bindParam(":orgname",$orgname);
            $stmnt->bindParam(":accountrole",$accountrole);
            $stmnt->bindParam(":account_contact",$acntContact);
            $stmnt->bindParam(":uname",$username);
            $stmnt->bindParam(":pword",$password);
            $stmnt->bindParam(":email",$email);
            $stmnt->bindParam(":orgaddr",$orgaddress);
            $stmnt->bindParam(":orgcity",$city);
            $stmnt->bindParam(":orgstate",$orgstate);
            $stmnt->bindParam(":orgzip",$orgzip);
            $stmnt->bindParam(":phone",$phone);
            $stmnt->bindParam(":pckglevel",$packageLevel);
            $stmnt->bindParam(":npi",$npi);
            $stmnt->bindParam(":license",$license);
            $stmnt->bindParam(":userlicense",$userLicense);
            $stmnt->bindParam(":licensestat",$licenseState);
            $stmnt->bindParam(":licensStrtDt",$licenseStartDate);
            $stmnt->bindParam(":licenseEndDt",$licenseEndDt);
            $stmnt->bindParam(":licenseCost",$licenseCost);
            $stmnt->bindParam(":accounttype",$accounttype);
            $stmnt->bindParam(":secretquestion",$secreteQuestion);
            $stmnt->bindParam(":terms",$terms);
            try{

                if($stmnt->execute())
                {
                    $msg="Accounted Updated";
                    $msg = array("status"=>"Successfull","message"=>$msg);
                    return $msg;
                }
            }
            catch(PDOException $e)
            {
                $msg = $e->__toString();
                return $msg;
            }
    }
    public function UpdateAccountStatusByAccountNumber($accountnumber,$status)
    {
        
        $sql="UPDATE `accounts` SET `accountnumber`=:accntnumber, `license_status`=:stat)";
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":accntnumber",$accountnumber);
            $stmnt->bindParam(":stat",$status);
            
            try{

                if($stmnt->execute())
                {
                    $msg="Account Updated";
                    $msg = array("status"=>"Successfull","message"=>$msg);
                    return $msg;
                }
            }
            catch(PDOException $e)
            {
                $msg = $e->__toString();
                return $msg;
            }
    }
    public function DeleteAccountByAccntID($accountID)
    {
        
        $sql="DELETE FROM `accounts` WHERE accountnumber=:accntID";
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":accntID",$accountID);
            try{

                if($stmnt->execute())
                {
                    $msg="Accounted Created";
                    $msg = array("status"=>"Successfull","message"=>$msg);
                    return $msg;
                }
            }
            catch(PDOException $e)
            {
                $msg = $e->__toString();
                return $msg;
            }
    }
    public function GetPendingAccountInfo($accountype,$unine)
    {
        $sql="SELECT * FROM `account_user` WHERE `account_user`.account_type=:accntType AND `account_user`.unique_ID=:uuID";
     
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accntType",$accountype);
        $stmnt->bindParam(":uuID",$unine);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                if(!empty($records) && count($records)>=1)
                {
                    $msg = array("status"=>"Successful","message"=>"Successfull","data"=>$records);
                    //var_dump($msg);
                    return $msg;
                }
                else{
                    $msg = array("status"=>"No Records Found","data"=>count($records));
                    return $msg;
                }
            }
        }
        catch(PDOException $e)
        {
            $msg =array("status"=>"SqlError","message"=>$e->__toString());
           // var_dump($msg);
            return $msg;
        }
    }
    public function UpdatePendingAccountInfo($accountNumber,$unine,$accntStat)
    {
        $sql="UPDATE`account_user` SET `account_user`.`account_status`=:accntStat WHERE `account_user`.accountnumber=:accntNum AND `account_user`.unique_ID=:uuID";
     
        $stmnt = $this->con->prepare($sql);
         $stmnt->bindParam(":accntStat",$accntStat);
        $stmnt->bindParam("accntNum",$accountNumber);
        $stmnt->bindParam(":uuID",$unine);
        try{
            if($stmnt->execute())
            {
                //If updated successfully now send back the new status 
                $records = $this->GetUpdatedAccountStatus($unine, $accountNumber);
                if(!empty($records) && count($records)>=1)
                {
                    $msg = array("status"=>"Successful","message"=>"Account Update Successfull","data"=>$records);
                    //var_dump($msg);
                    return $msg;
                }
                else{
                    $msg = array("status"=>"No Records Found","data"=>count($records));
                    return $msg;
                }
            }
        }
        catch(PDOException $e)
        {
            $msg =array("status"=>"SqlError","message"=>$e->__toString());
           // var_dump($msg);
            return $msg;
        }
    }
    private function GetUpdatedAccountStatus($uniqueID, $accountnumber)
    {
        $sql="SELECT `account_status` FROM`account_user`WHERE `account_user`.accountnumber=:accntNum AND `account_user`.unique_ID=:uuID";
     
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam("accntNum",$accountnumber);
        $stmnt->bindParam(":uuID",$uniqueID);
        try{
            if($stmnt->execute())
            {
                //If updated successfully now send back the new status 
                $records = $stmnt->fetchAll();
                if(!empty($records) && count($records)>=1)
                {
                    $msg = array("status"=>"200 Successful","message"=>"Account Status Records","data"=>$records);
                    //var_dump($msg);
                    return $msg;
                }
                else{
                    $msg = array("status"=>"No Records Found","data"=>count($records));
                    return $msg;
                }
            }
        }
        catch(PDOException $e)
        {
            $msg =array("status"=>"SqlError","message"=>$e->__toString());
           // var_dump($msg);
            return $msg;
        }
    }
    public function GetEMRAccountInfo($accntnum,$unine)
    {
        $sql="SELECT * FROM `account_user` LEFT JOIN `accounts` ON `accounts`.accountnumber=`account_user`.accountnumber WHERE `accounts`.accountnumber=:accntNum AND `account_user`.unique_ID=:uuID";
     
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accntNum",$accntnum);
        $stmnt->bindParam(":uuID",$unine);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                if(!empty($records) && count($records)>=1)
                {
                    $msg = array("status"=>"Successful","message"=>"Successfull","data"=>$records);
                    //var_dump($msg);
                    return $msg;
                }
                else{
                    $msg = array("status"=>"No Records Found","data"=>count($records));
                    return $msg;
                }
            }
        }
        catch(PDOException $e)
        {
            $msg =array("status"=>"SqlError","message"=>$e->__toString());
           // var_dump($msg);
            return $msg;
        }
    }
    public function GetAccountUsers($accountnum)
    {
        $sql="SELECT account_user.accntid,account_user.accountnumber,account_user.unique_ID,account_user.account_type,account_user.username,user_email,user_Fname,user_Lname,account_user.`address`,user_phone FROM `account_user` INNER JOIN `accounts` ON `account_user`.accountnumber=`accounts`.accountnumber WHERE `account_user`.accountnumber=:accntNum";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accntNum",$accountnum);
       // $stmnt->bindParam(":uuID",$uniquenine);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                if(!empty($records) && count($records)>=1)
                {
                    $msg = array("status"=>"Successful","message"=>"Sql Executed Successfully","data"=>$records);
                   // var_dump($msg);
                    return $msg;
                }
                else{
                    $msg = array("status"=>"No Records Found","data"=>count($records));
                    return $msg;
                }
            }
        }
        catch(PDOException $e)
        {
            $msg =array("status"=>"SqlError","message"=>$e->__toString());
           // var_dump($msg);
            return $msg;
        }
    }
    //homehealth aid sub account 
    public function AddHomeAidAccount($accountnumber,$uniqueID,$username,$caregiver,$firstname,$lastname,$phone,$email,$address,$city,$state,$zip,
    $ishomeHeathAid,$hh_hasCertificate,$aidcertificateNumber,$accntStatus)
    {
      
      if($ishomeHeathAid=="Yes")
      {
        $careType=$caregiver;
      }
      $sqlchk ="Select * FROM nurses WHERE acountnumber=:accntnum AND email=:email";
      $stmnt2 = $this->con->prepare($sqlchk);
      $stmnt2->bindParam(":accntnum",$accountnumber);
      $stmnt2->bindParam(":email",$email);
      try{

          if($stmnt2->execute())
          {
              $record = $stmnt2->fetchAll();
              if(count($record) >=1)
              {
                  $message = array("message"=>"Record Exist");
                  return $message;
              }
              else{
                  $sql="Insert Into nurses (`acountnumber`,`providerID`,`username`,`firstname`,`lastname`,`certificatenumber`,`email`,`primaryphone`,`addr1`,`city`,`state`,`zip`,`activestatus`,`havecertificate`,`caregivetype`) VALUES (:accnum,:extern,:uname,:fname,:lname,:certnum,:email,:primephone,:addr1,:city,:state,:zip,:activestatus,:hvcert,:caretype)";
                  $stmnt = $this->con->prepare($sql);
                  $stmnt->bindParam(":accnum",$accountnumber);
                  $stmnt->bindParam(":extern", $uniqueID);
                  $stmnt->bindParam(":uname",$username);
                  $stmnt->bindParam(":fname",$firstname);
                  $stmnt->bindParam(":lname",$lastname);
                  $stmnt->bindParam(":certnum",$aidcertificateNumber);
                  $stmnt->bindParam(":email",$email);
                  $stmnt->bindParam(":primephone",$phone);
                 
                  $stmnt->bindParam(":addr1",$address);
                 
                  $stmnt->bindParam(":city",$city);
                  $stmnt->bindParam(":state",$state);
                  $stmnt->bindParam(":zip",$zip);
                  $stmnt->bindParam(":activestatus",$accntStatus);
                  $stmnt->bindParam(":hvcert",$hh_hasCertificate);
                  $stmnt->bindParam(":caretype",$caregiver);
                 
                  try{
          
                      if($stmnt->execute())
                  {
          
                      //now send email 
                      
                      $result ="Inserted";
                      $msgar = array("message"=>$result);
                      return $msgar;
                          
                  }
                  }catch(PDOException $e)
                  {
                      $error = $e->__toString();
                      return $error;
                  }
              }
          }
      }catch(PDOException $e)
      {
          $error = $e->__toString();
          return $error;
      }
    }
    //subaccount 
    public function AddPatientAccount($accountnumber,$uniqueID,$firstname,$lastname,$dob,$gender,$phone,$email,$address,$city,$state,$zip,$emgcontact,$emgcontactphone,$maritalstatus,$insuranceprovider,$policynumber,$bloodtype,$allergies)
    {
        $accountstatus="Active";
        $sql="INSERT INTO `patients`( `accountnumber`, `psersonalID`, `first_name`, `lastname`, `date_of_birth`, `gender`, `contact_number`, `email`, `address`, `city`, `state`, `zip`, `emergency_contactname`, `emergency_contact_number`, `matial_status`, `insurance_provider`, `insurance_policy_number`, `bloodtype`, `allegeries`, `accntstatus`) VALUES (
            :accntnumber,:unid,:ufname,:ulname,:dob,:gender,:uphone,:uemail,:addr,:city,:ustate,:uzip,:emgcontact,:emgcontactnumber,:maritalstatus,:insuranceprov,:policynumber,:bloodtype,:allergies,:accntstatus)";
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":accntnumber",$accountnumber);
            $stmnt->bindParam(":unid",$uniqueID);
            $stmnt->bindParam(":ufname",$firstname);
            $stmnt->bindParam(":ulname",$lastname);
             $stmnt->bindParam(":dob",$dob);
             $stmnt->bindParam(":gender",$gender);
            $stmnt->bindParam(":uphone",$phone);
            $stmnt->bindParam(":uemail",$email);
            $stmnt->bindParam(":addr",$address);
            $stmnt->bindParam(":city",$city);
            $stmnt->bindParam(":ustate",$state);
            $stmnt->bindParam(":uzip",$zip);
            $stmnt->bindParam(":emgcontact",$emgcontact);
            $stmnt->bindParam(":emgcontactnumber",$emgcontactphone);
            $stmnt->bindParam(":maritalstatus",$maritalstatus);
            $stmnt->bindParam(":insuranceprov",$insuranceprovider);
            $stmnt->bindParam(":policynumber",$policynumber);
            $stmnt->bindParam(":bloodtype",$bloodtype);
            $stmnt->bindParam(":allergies",$allergies);
            $stmnt->bindParam(":accntstatus",$accountstatus);
            try{

                if($stmnt->execute())
                {
                    $msg="Patient Created";
                    $msg = array("status"=>"Successfull","message"=>$msg);
                    return $msg;
                }
            }
            catch(PDOException $e)
            {
                $msg = $e->__toString();
               // var_dump($msg);
                return $msg;
            }
    }
    public function CheckAccountType($subid, $pid){
        $sql = 'SELECT `account_type` FROM `account_user` WHERE accountnumber=:subid AND unique_ID=:pid';
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":subid", $subid);
        $stmnt->bindParam(":pid", $pid);
        try {
            if ($stmnt->execute()){
                $records = $stmnt->fetchAll();
                return $records[0][0];
            }
            else {
                return 'None';
            }
        }
        catch (Exception $e){
            return 'None';
        }
    }
    public function AddNewPdf($subid, $externid, $pdfType, $file, $latest){
        $path = $this->MovePDFFile($subid, $externid, $file, $pdfType);
        if ($latest){
            if ($pdfType == 'license'){
                $sql = "UPDATE `account_user` SET `licenseFile`=:licPath WHERE `accountnumber`=:subid AND `unique_ID`=:externid";
                $stmnt = $this->con->prepare($sql);
                $stmnt->bindParam(":licPath", $path);
                $stmnt->bindParam(":subid", $subid);
                $stmnt->bindParam(":externid", $externid);
                
                $sql2 = "UPDATE `nurses` SET `licenseFile`=:licPath WHERE `acountnumber`=:subid AND `providerID`=:externid";
                $stmnt2 = $this->con->prepare($sql2);
                $stmnt2->bindParam(":licPath", $path);
                $stmnt2->bindParam(":subid", $subid);
                $stmnt2->bindParam(":externid", $externid);

                try {
                    if ($stmnt->execute() && $stmnt2->execute()){
                        return array("status"=>"Successful");
                    }
                }
                catch(PDOException $e){
                    return array("status"=>$e->__toString());
                }
            }
            else if ($pdfType == 'insurance'){
                $sql = "UPDATE `account_user` SET `insuranceFile`=:insPath WHERE `accountnumber`=:subid AND `unique_ID`=:externid";
                $stmnt = $this->con->prepare($sql);
                $stmnt->bindParam(":insPath", $path);
                $stmnt->bindParam(":subid", $subid);
                $stmnt->bindParam(":externid", $externid);
                
                $sql2 = "UPDATE `nurses` SET `insuranceFile`=:insPath WHERE `acountnumber`=:subid AND `providerID`=:externid";
                $stmnt2 = $this->con->prepare($sql2);
                $stmnt2->bindParam(":insPath", $path);
                $stmnt2->bindParam(":subid", $subid);
                $stmnt2->bindParam(":externid", $externid);

                try {
                    if ($stmnt->execute() && $stmnt2->execute()){
                        return array("status"=>"Successful");
                    }
                }
                catch(PDOException $e){
                    return array("status"=>$e->__toString());
                }
            }
        }
        return array("status"=>"Successful");
    }
    public function MovePDFFile($accountnumber,$uniqueid,$file,$type){
        if (!$file){
            return "";
        }
        $pdfFolder = '/var/www/html/new_pdfs';
        $path = $pdfFolder . '/' . $accountnumber . '/' . $uniqueid . '/' . $type;
        if (!file_exists($path))
            mkdir($path, 0775, true);
        $fileName = $file["name"];
        $pathinfo = pathinfo($fileName);
        $i = 0;
        while (file_exists($path . '/' . $fileName)){
            $i++;
            $fileName = $pathinfo["filename"] . "-" . $i . "." . $pathinfo["extension"];
        }
        $result = move_uploaded_file($file["tmp_name"], $path . '/' . $fileName);
        return '/new_pdfs' . '/' . $accountnumber . '/' . $uniqueid . '/' . $type . '/' . $fileName;
    }
    public function GetSubDirs($path, $subid, $flag = true) {
        $list = array();
        $pidDir = scandir($path);
        foreach ($pidDir as $key => $value){
            if (!in_array($value, array('.', '..'))){
                if (is_dir($path . '/' . $value)){
                    if ($flag){
                        $names = $this->GetNameByIDs($subid, $value);
                        $fullName = $names[0]["user_Fname"] . ' ' . $names[0]["user_Lname"];
                        $list[$value]["fullName"] = $fullName;
                        $list[$value]["pdfs"] = $this->GetSubDirs($path . '/' . $value, $subid, false);
                    }
                    else
                        $list[$value] = $this->GetSubDirs($path . '/' . $value, $subid, false);
                }
                else{
                    $list[] = $value;
                }
            }
        }
        return $list;
    }
    public function GetNameByIDs($subid, $externid){
        $sql = "SELECT user_Fname, user_Lname from account_user WHERE accountnumber=:subid AND unique_ID=:externid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":subid", $subid);
        $stmnt->bindParam(":externid", $externid);
        $stmnt->execute();
        return $stmnt->fetchAll();
    }
    public function GetAllNursePdfs($subid, $pid){
        $accountType = $this->CheckAccountType($subid, $pid);
        $list = array();
        if ($accountType != 'Manager'){
            $patientPath = '/var/www/html/new_pdfs/' . $subid . '/' . $pid;
            if (!file_exists($patientPath))
                return $list;
            if (file_exists($patientPath . '/insurance')) {
                $scannedIns = array_diff(scandir($patientPath . '/insurance'), array('..', '.'));
                if ($scannedIns)
                    $list['insurance'] = $scannedIns;
                else
                    $list['insurance'] = []; 
            }
            if (file_exists($patientPath . '/license')) {
                $scannedLic = array_diff(scandir($patientPath . '/license'), array('..', '.'));
                if ($scannedLic)
                    $list['license'] = $scannedLic;
                else
                    $list['license'] = [];
            }
            if (file_exists($patientPath. '/other')){
                $scannedOth = array_diff(scandir($patientPath . '/other'), array('..', '.'));
                if ($scannedOth)
                    $list['other'] = $scannedOth;
                else
                    $list['other'] = [];
            }
        }
        else if ($accountType === 'Manager'){
            $managerPath = '/var/www/html/new_pdfs/' . $subid;
            $answer = $this->GetSubDirs($managerPath, $subid);
            $answer['manager'] = true;
            return $answer;
        }
        return $list;
    }
    public function SaveNewInsurance($subid, $pid, $name, $stDate, $endDate, $incLim, $annLim){
        $sql = "UPDATE account_user SET insuranceCompany=:name, insuranceStart=:stDate, insuranceEnd=:endDate, perIncLimit = :incLim, annLimit = :annLim WHERE accountnumber = :subid AND unique_ID = :pid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":name", $name);
        $stmnt->bindParam(":stDate", $stDate);
        $stmnt->bindParam(":endDate", $endDate);
        $stmnt->bindParam(":incLim", $incLim);
        $stmnt->bindParam(":annLim", $annLim);
        $stmnt->bindParam(":subid", $subid);
        $stmnt->bindParam(":pid", $pid);

        $sql2 = "UPDATE nurses SET insuranceCompany=:name, insuranceStart=:stDate, insuranceEnd=:endDate, perIncidentAmnt = :incLim, annualAggAmnt = :annLim WHERE acountnumber = :subid AND providerID = :pid";
        $stmnt2 = $this->con->prepare($sql2);
        $stmnt2->bindParam(":name", $name);
        $stmnt2->bindParam(":stDate", $stDate);
        $stmnt2->bindParam(":endDate", $endDate);
        $stmnt2->bindParam(":incLim", $incLim);
        $stmnt2->bindParam(":annLim", $annLim);
        $stmnt2->bindParam(":subid", $subid);
        $stmnt2->bindParam(":pid", $pid);

        try{
            if ($stmnt->execute() && $stmnt2->execute()){
                return array("status"=>"Success");
            }
            else {
                return array("status"=>"Failure");
            }
        }
        catch (PDOException $e){
            return array("status"=>$e);
        }
    }
    //Contact Insert 
    public function InsertContactInfo($accountid,$subaccnt,$fname,$laname,$email,$phone,$address,$address2,
    $city,$state,$zip,$country,$dob,$gender,$contacttype,$notes,$creationdt)
    {
        $sql="INSERT INTO `contacts` ( `accountnumber`, `subaccountnumber`, `Firstname`, `Lastname`, `email`, `phone`, `address`, `address2`, `city`, `state`, `zip`,
         `country`, `dob`, `gender`, `contacttype`, `notes`, `contact_created`) VALUES (:accnt,:subaccnt,:fname,:lname,:email,:phone,:addr,:addr2,
         :city,:zip,:statt,:zip,:country,:dob,:gender,:contacttype,:notes,:ccreated)";
          $stmnt = $this->con->prepare($sql);
          $stmnt->bindParam(":accnt",$accountid);
          $stmnt->bindParam(":subaccnt",$subaccnt);
          $stmnt->bindParam(":fname",$fname);
          $stmnt->bindParam(":lname",$lname);
          $stmnt->bindParam(":email",$email);
          $stmnt->bindParam(":phone",$phone);
          $stmnt->bindParam(":addr",$adrr);
          $stmnt->bindParam(":addr2",$addr2);
          $stmnt->bindParam(":city",$city);
          $stmnt->bindParam(":statt",$state);
          $stmnt->bindParam(":zip",$zip);
          $stmnt->bindParam(":country",$country);
          $stmnt->bindParam(":dob",$dob);
          $stmnt->bindParam(":gender",$gender);
          $stmnt->bindParam(":contacttype",$contacttype);
          $stmnt->bindParam(":notes",$notes);
          $stmnt->bindParam(":ccreated",$creationdt);
          try{

            if($stmnt->execute())
            {
                $msg="Contact Created";
                $msg = array("status"=>"Successfull","message"=>$msg);
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = $e->__toString();
            return $msg;
        }
          
    }
    public function SearchAllContactsNurseProviders($account,$firstname,$lastname)
    {
        //$searchpattern ="'%".$srchterm."%'";
        
        /*$sql="SELECT `patients`.`first_name`,`patients`.`lastname`,`patients`.`email`,`patients`.`psersonalId`,`nurses`.`firstname`,`nurses`.lastname,`providers`.`firstname`,`providers`.`lastname` FROM `patients` 
        INNER JOIN nurses ON `patients`.`accountnumber`= nurses.acountnumber
        INNER JOIN providers ON nurses.acountnumber = `patients`.`accountnumber`
        WHERE `patients`.`first_name`LIKE '%".$firstname."%' AND patients.lastname LIKE '%".$lastname."%' AND patients.accountnumber=:accnt ORDER BY `pid`  DESC
         ";*/
         $mrgar=array();
         $mrgar2=array();
         $mrgar3=array();
         $final="";
         $sql="SELECT *, `first_name` AS firstname, `psersonalID` as subaccountnumber FROM `patients` WHERE `first_name` LIKE '%".$firstname."%' AND `lastname` LIKE '%".$lastname."%'AND `accountnumber`=:accnt";
         $nurssql ="SELECT *, `acountnumber` AS accountnumber  FROM `nurses` WHERE `firstname` LIKE '%".$firstname."%' AND `lastname` LIKE '%".$lastname."%' AND acountnumber=:accnt";
         $providersql ="SELECT *, `npinumber` AS subaccountnumber FROM `providers` WHERE `firstname` LIKE '%".$firstname."%' AND `lastname` LIKE '%".$lastname."%' AND accountnumber=:accnt";

        // var_dump($sql);
        $stmnt = $this->con->prepare($sql);
        $stmnt2 = $this->con->prepare($nurssql);
        $stmnt3 = $this->con->prepare($providersql);
        $stmnt->bindParam(":accnt",$account);
        $stmnt2->bindParam(":accnt",$account);
        $stmnt3->bindParam(":accnt",$account);
       // $stmnt->bindParam(":srchterm",$searchpattern);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                if(count($records) >0)
                {
                   // array_push($mrgar,$records);
                   $msg = array("code"=>"200","status"=>"Successfull","records"=>$records);
                   return $msg;
                 // array_push($mrgar,$records);
                }
                //now lets run  the nurses codes 
               // $msg = array("code"=>"200","records"=>$records);
                //return $msg;
            }
           
            if($stmnt2->execute())
            {
                $records2 = $stmnt2->fetchAll();
                if(count($records2) >0)
                {
                   // array_push($mrgar,$records2);
                   $msg = array("code"=>"200","status"=>"Successfull","records"=>$records2);
                   return $msg;
                    //array_push($mrgar2,$records2);
                }
            }
            if($stmnt3->execute())
            {
                $records3 = $stmnt3->fetchAll();
                if(count($records3) >0)
                {
                   // array_push($mrgar, $records3);
                   $msg = array("code"=>"200","status"=>"Successfull","records"=>$records3);
                   return $msg;
                  // array_push( $mrgar3, $records3);
                }
            }
           /* $final = array_merge($mrgar,$mrgar2,$mrgar3);
            if(count(array_filter($final)) >0)
            {
                //return array
                $msg = array("code"=>"200","status"=>"Successfull","records"=>$final);
                return $msg;
            }
            else{
                $msg = array("code"=>"200","records"=>"");
                return $msg;
            }*/
        }
        catch(PDOException $e)
        {
            $msg =array("code"=>"Sql-700","message"=>$e->__toString());
            return $msg;
        }

    }
    public function GetAccountContactInfo($accountid,$subaccount,$firstname,$lastname)
    {
       // $sterm = $searchterm;
        $fnamepattern ="'%$firstname%'";
        $lnamepattern ="'%$lastname%'";
       
        $sql="SELECT * FROM `contacts` WHERE FirstName LIKE $fnamepattern AND LastName LIKE $lnamepattern AND accountnumber=$accountid ";
       // var_dump($accountid);
        //var_dump($sql);
         $stmnt = $this->con->prepare($sql);
        // $stmnt->bindParam(":fterm",$fnamepattern);
        // $stmnt->bindParam(":lterm",$lnamepattern);
        // $stmnt->bindParam(":subaccnt",$subaccount);
         //$stmnt->bindParam(":accnt",$accountid);
         
         try{

           if($stmnt->execute())
           {
               $records= $stmnt->fetchAll();
              // var_dump($records);
               $msg = array("status"=>"Successfull","records"=>$records);
              // var_dump($msg);
               return $msg;
           }
       }
       catch(PDOException $e)
       {
           $msg = $e->__toString();
           var_dump($msg);
           return $msg;
       }
    }
    public function GetContactByGroup($accountnumber,$subaccount,$srchterm)
    {
        // $sterm = $searchterm;
        $srchpattern ="'%$srchterm%'";
       //var_dump($accountnumber);

       
        $sql="SELECT * FROM `providers_group` WHERE `groupname` LIKE $srchpattern AND accountnumber=:accnt AND subaccountnumber=:subaccnt ";
       // var_dump($accountid);
       // var_dump($sql);
         $stmnt = $this->con->prepare($sql);
         $stmnt->bindParam(":accnt",$accountnumber);
        // $stmnt->bindParam(":lterm",$lnamepattern);
         $stmnt->bindParam(":subaccnt",$subaccount);
         //$stmnt->bindParam(":accnt",$accountid);
         
         try{

           if($stmnt->execute())
           {
               $records= $stmnt->fetchAll();
              // var_dump($records);
               $msg = array("status"=>"Successfull","records"=>$records);
              // var_dump($msg);
               return $msg;
           }
       }
       catch(PDOException $e)
       {
           $msg = $e->__toString();
           var_dump($msg);
           return $msg;
       }
    }
    //Update Contact 
    public function UpdateAccountContact($accountid,$subaccnt,$fname,$laname,$email,$phone,$address,$address2,
    $city,$state,$zip,$country,$dob,$gender,$contacttype,$notes,$creationdt)
    {
        
        $sql="UPDATE `contacts` SET `accountnumber`=:accnt, `subaccountnumber`=:subaccnt, `Firstname`=:fname, `Lastname`=:lname, `email`=:email, `phone`=:phone, `address`=:addr, `address2`=:addr2, `city`=:city, `state`=:statt, `zip`=:zip,
         `country`=:country, `dob`=:dob, `gender`=:gender, `contacttype`=:contacttype, `notes`=:notes, `contact_created`=:ccreated WHERE `subaccountnumber`=:subaccnt";
          $stmnt = $this->con->prepare($sql);
          $stmnt->bindParam(":accnt",$accountid);
          $stmnt->bindParam(":subaccnt",$subaccnt);
          $stmnt->bindParam(":fname",$fname);
          $stmnt->bindParam(":lname",$lname);
          $stmnt->bindParam(":email",$email);
          $stmnt->bindParam(":phone",$phone);
          $stmnt->bindParam(":addr",$adrr);
          $stmnt->bindParam(":addr2",$addr2);
          $stmnt->bindParam(":city",$city);
          $stmnt->bindParam(":statt",$state);
          $stmnt->bindParam(":zip",$zip);
          $stmnt->bindParam(":country",$country);
          $stmnt->bindParam(":dob",$dob);
          $stmnt->bindParam(":gender",$gender);
          $stmnt->bindParam(":contacttype",$contacttype);
          $stmnt->bindParam(":notes",$notes);
          $stmnt->bindParam(":ccreated",$creationdt);
          try{

            if($stmnt->execute())
            {
                $msg="Contact Updated";
                $msg = array("status"=>"Successfull","message"=>$msg);
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = $e->__toString();
            return $msg;
        }
          
    
    }
    public function DeleteContactByIDS($accountnumber,$subaccountnumber)
    {
        $sql="DELETE FROM contacts WHERE accountnumber=:accnt AND subaccountnumber=:subaccnt";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":sbaccnt",$subaccountnumber);
        try{

            if($stmnt->execute())
            {
                $msg="Contact Deleted";
                $msg = array("message"=>"Contact Deleted","status"=>$msg);
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("message"=>"7800-sql Error","errorMessage"=>$e->__toString());
            return $msg;
        }
    }
    //Accout User SQL
    public function CreateAccountUser($accountnumber,$uniqueID,$username,$accnType,$firstname,$lastname,$phone,$email,$address,$city,$state,$zip,$proffisionalicense,$accntStatus,$upremission,$emrlicense,$ucreationDt,$compPreference,$insFile,$licenseFile)
    {
        $licensePath = $this->MovePDFFile($accountnumber, $uniqueID, $licenseFile, "license");
        $insPath = $this->MovePDFFile($accountnumber, $uniqueID, $insFile, "insurance");
        $sql="INSERT INTO `account_user`(`accountnumber`, `unique_ID`, `username`, `account_type`, `user_Fname`, `user_Lname`, `user_phone`, `user_email`, `address`, `city`, `state`, `zip`, `proffesionalLicense`, `account_status`, `user_premission`, `emr_licensenumber`, `userCreationDt`, `communication_preference`,`insuranceFile`,`licenseFile`) VALUES (
            :accntnumber,:unid,:username,:accnttype,:ufname,:ulname,:uphone,:uemail,:addr,:city,:ustate,:uzip,:proflicense,:accntstatus,:upremission,:emrlicense,:ucreationDt,:compref,:insFile,:licenseFile)";
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":accntnumber",$accountnumber);
            $stmnt->bindParam(":unid",$uniqueID);
            $stmnt->bindParam(":username",$username);
            $stmnt->bindParam(":accnttype",$accnType);
            $stmnt->bindParam(":ufname",$firstname);
            $stmnt->bindParam(":ulname",$lastname);
            $stmnt->bindParam(":uphone",$phone);
            $stmnt->bindParam(":uemail",$email);
            $stmnt->bindParam(":addr",$address);
            $stmnt->bindParam(":city",$city);
            $stmnt->bindParam(":ustate",$state);
            $stmnt->bindParam(":uzip",$zip);
            $stmnt->bindParam(":proflicense",$proffisionalicense);
            $stmnt->bindParam(":accntstatus",$accntStatus);
            $stmnt->bindParam(":upremission",$upremission);
            $stmnt->bindParam(":emrlicense",$emrlicense);
            $stmnt->bindParam(":ucreationDt",$ucreationDt);
            $stmnt->bindParam(":compref",$compPreference);
            $stmnt->bindParam(":insFile",$insPath);
            $stmnt->bindParam(":licenseFile",$licensePath);
           
            try{

                if($stmnt->execute())
                {
                    $msg="Accounted Created";
                    $msg = array("status"=>"Successfull","message"=>$msg);
                    return $msg;
                }
            }
            catch(PDOException $e)
            {
                $msg = $e->__toString();
                return $msg;
            }
    }
    public function UpateAccountUser($accountnumber,$uniqueID,$firstname,$lastname,$phone,$email,$address,$city,$state,$zip,$proffisionalicense,$emrlicense,$ucreationDt,$compPreference)
    {
        
        $sql="UPDATE `account_user` `accountnumber`=:accntuser, `unique_ID`=:unid, `account_type`=:accnttype, `user_Fname`=:ufname, `user_Lname`=:ulname, `user_phone`=:uphone, `user_email`=:uemail, 
        `address`=:addr, `city`=:city, `state`=:ustate, `zip`=:uzip, `proffesionalLicense`=:proflicense, `account_status`=:accntstatus, `user_premission`=upremission, `emr_licensenumber`=:emrlicense,
         `userCreationDt`=:ucreationDt, `communication_preference`=:compref WHERE accountnumber=:accntnumber";
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":accntnumber",$accountnumber);
            $stmnt->bindParam(":unid",$uniqueID);
            $stmnt->bindParam(":accnttype",$accnType);
            $stmnt->bindParam(":ufname",$firstname);
            $stmnt->bindParam(":ulname",$lastname);
            $stmnt->bindParam(":uphone",$phone);
            $stmnt->bindParam(":uemail",$email);
            $stmnt->bindParam(":addr",$address);
            $stmnt->bindParam(":city",$city);
            $stmnt->bindParam(":ustate",$state);
            $stmnt->bindParam(":uzip",$zip);
            $stmnt->bindParam(":proflicense",$proffisionalicense);
            $stmnt->bindParam(":emrlicense",$emrlicense);
            $stmnt->bindParam(":ucreationDt",$ucreationDt);
            $stmnt->bindParam(":compref",$compPreference);
           
            try{

                if($stmnt->execute())
                {
                    $msg="Accounted Created";
                    $msg = array("status"=>"Successfull","message"=>$msg);
                    return $msg;
                }
            }
            catch(PDOException $e)
            {
                $msg = $e->__toString();
                return $msg;
            }
    }
    public function SelectAccountUser($accountnumber)
    {
        
        $sql="SELECT * FROM `account_user` INNER JOIN accounts ON account.accountnumber = account_user.accountnumber WHERE accountnumber=:accntnumber";
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":accntnumber",$accountnumber);
            try{

                if($stmnt->execute())
                {
                    $records= $stmnt->fetchAll();
                    $msg = array("status"=>"Successfull","records"=>$records);
                    return $msg;
                }
            }
            catch(PDOException $e)
            {
                $msg = $e->__toString();
                return $msg;
            }
    }
    
    public function InsertBillingInfo($accountnumber,$compname,$billaddr,$billcity,$billstate,$billzip,$email,$cardholdername,$ccnum,$cv3,$expDt,$pcklevel,$billfreq,$pckcost,$isCurrent,$createDt)
    {
        
        $sql="INSERT INTO billing (`accountnumber`, `companyname`, `billing_address`, `billing_city`, `billing_state`, `billing_zip`, `billing_email`, `name_of_cardholder`, 
        `cc_number`, `cc_cvv`, `cc_expirationDt`, `packagelevel`, `billing_frequency`, `package_cost`, `bill_status_isCurrent`, `created_at`) 
        VALUES (:accntnumber,:compname,:billaddr,:billcity,:billstate,:billzip,:billemail,:cardholdname,:ccnum,:cclst3,:ccexpdt,:packlvl,:billfreq,:pckgcost,:billiscurrent,
        :createat)";
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":accntnumber",$accountnumber);
            $stmnt->bindParam(":compname",$compname);
            $stmnt->bindParam(":billaddr",$billaddr);
            $stmnt->bindParam(":billcity",$billcity);
            $stmnt->bindParam(":billstate",$billstate);
            $stmnt->bindParam(":billzip",$billzip);
            $stmnt->bindParam(":billemail",$email);
            $stmnt->bindParam(":cardholdname",$cardholdername);
            $stmnt->bindParam(":ccnum",$ccnum);
            $stmnt->bindParam(":cclst3",$cv3);
            $stmnt->bindParam(":ccexpdt",$expDt);
            $stmnt->bindParam(":packlvl",$pcklevel);
            $stmnt->bindParam(":billfreq",$billfreq);
            $stmnt->bindParam(":pckgcost",$pckcost);
            $stmnt->bindParam(":billiscurrent",$isCurrent);
            $stmnt->bindParam(":createat",$createDt);
            try{

                if($stmnt->execute())
                {
                    $msg="Inserted";
                    $msg = array("status"=>"Inserted","records"=>$msg);
                    return $msg;
                }
            }
            catch(PDOException $e)
            {
                $msg = $e->__toString();
                return $msg;
            }
    }
    public function UpdateBillingInfo($accountnumber,$compname,$billaddress,$billcity,$billstate,$billzip,$billemail,$cardholdername,$cardnumber,$cardlastthree,
    $cardexpdt,$packagelevel,$billfrequency,$packagecost,$billiscurrent,$createat)
    {
        
        $sql="UPDATE `billing` `accountnumber`=:accntnumber, `companyname`=:compname, `billing_address`=:billaddr, `billing_city`=:billcity, `billing_state`=:billstate, `billing_zip`=:billzip, `billing_email`=:billemail,
         `name_of_cardholder`=:cardholdname,`cc_number`=:ccnum, `cc_cvv`=:cclst3,`cc_expirationDt`=:ccexpdt, `packagelevel`=:pckcost,
          `billing_frequency`=:billfreq, `package_cost`=:pckcost, `bill_status_isCurrent`=:billiscurrent, `created_at`=:createat) 
        VALUES (:accntnumber,:compname,:billaddr,:billcity,:billstate,:billzip,:billemail,:cardholdname,:ccnum,:cclst3,:ccexpdt,:packlvl,billfreq,:pckgcost,:billiscurrent,
        :createat)";
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":accntnumber",$accountnumber);
            $stmnt->bindParam(":compname",$compname);
            $stmnt->bindParam(":billaddr",$billaddress);
            $stmnt->bindParam(":billcity",$billcity);
            $stmnt->bindParam(":billstate",$billstate);
            $stmnt->bindParam(":billzip",$billzip);
            $stmnt->bindParam(":billemail",$billemail);
            $stmnt->bindParam(":cardholdname",$cardholdername);
            $stmnt->bindParam(":ccnum",$cardnumber);
            $stmnt->bindParam(":cclst3",$cardlastthree);
            $stmnt->bindParam(":ccexptdt",$cardexpdt);
            $stmnt->bindParam(":packlvl",$packagelevel);
            $stmnt->bindParam(":billfreq",$billfrequency);
            $stmnt->bindParam(":pckcost",$packagecost);
            $stmnt->bindParam(":billiscurrent",$billiscurrent);
            $stmnt->bindParam(":createat",$createat);
            try{

                if($stmnt->execute())
                {
                    $msg="Inserted";
                    $msg = array("status"=>"Successfull","records"=>$msg);
                    return $msg;
                }
            }
            catch(PDOException $e)
            {
                $msg = $e->__toString();
                return $msg;
            }
    }
    public function UpdateProvOnTeam($provid, $value)
    {
        $sql='UPDATE providers SET onTeam = :value WHERE providerid = :provid';
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":value", $value);
        $stmnt->bindParam(":provid", $provid);
        try {
            if ($stmnt->execute())
            {
                $arr = array("status"=>"Success");
                return $arr;
            }
        }
        catch(PDOException $e){
            $arr = array("status"=>"Error: ".$e->__toString());
            return json_encode($arr);
        }
    }
    /*
    *Adding Sql Syntax for Premission
    */
    public function DoesLicenseExist($genlicense)
    {
        $sql="SELECT * FROM `userlicense` WHERE accountnumber=:accntNum AND subaccountnumber=:subaccntNum AnD licensenumber=:lnumber ";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accntNum",$accountnumber);
        $stmnt->bindParam(":subaccntNum",$subaccount);
        $stmnt->bindParam(":lnumber",$genlicense);
        //$stmnt->bindParam(":uname",$uname);
       
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                if(count($records >0))
                {
                    $msg = array("message"=>"License Exist","status"=>$records["status"]);
                    return $msg;
                }
                else{
                    $msg = array("message"=>"Doesn'Exist","status"=>"No License Found");
                    return $msg;
                }
                return $records;
            }
           
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>"700 SQL Error","message"=>$e->__toString());
            return $msg;
        }
    }
    public function InsertLicense($genlicense,$accountnumber,$subaccount,$status)
    {
        $sql="INSERT INTO `userlicense` (`accountnumber`, `subaccountnumber`,`licensenumber`,`status`) VALUES(:accnt,:subaccnt,:licensnum,:stat)   ";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":subaccnt",$subaccount);
        $stmnt->bindParam(":licensnum",$genlicense);
        $stmnt->bindParam(":stat",$status);
        //$stmnt->bindParam(":uname",$uname);
       
        try{
            if($stmnt->execute())
            {
                
                
                    $msg = array("message"=>"Inserted","status"=>"successfull");
                    return $msg;
                
            }
           
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>"700 SQL Error","message"=>$e->__toString());
            return $msg;
        }
    }
   
    public function DoesPremissionsExist($accountnumber,$subaccount)
  {

    $sql="SELECT * FROM `premission_features` WHERE accountnumber=:accntNum AND subaccountnumber=:subaccntNum ";
    $stmnt = $this->con->prepare($sql);
    $stmnt->bindParam(":accntNum",$accountnumber);
    $stmnt->bindParam(":subaccntNum",$subaccount);
    //$stmnt->bindParam(":uname",$uname);
   
    try{
        if($stmnt->execute())
        {
            $records = $stmnt->fetchAll();
            return $records;
        }
       
    }
    catch(PDOException $e)
    {
        $msg = array("error"=>"700 SQL Error","message"=>$e->__toString());
        return $msg;
    }
       
   
  }

  public function SaveUserPremissions($accountType,$username,$accountnumber,$subaccount,$premar)
  {
   
    $accounttype = $accountType;
    if($username=="" || $username==null)
    {
        $uname="user_null";
    }
    else{
        $uname=$username;
    }
    //$uname = $username;
    $acntnumber = $accountnumber;
    $subaccnt = $subaccount;
    foreach($premar["records"] as $r)
    {
        //var_dump((int)$r['premID']);exit();
       
        $accnttype = $r["acountType"];
        $cancreateAccnt = $r["can_create_accnt"];
        $cancreateSubaccnt = $r["can_create_subaccnt"];
        $createspecialityAccnt = $r["can_create_specialtyaccnt"];
        $revokeAccnt = $r["can_revoke_account"];
        $updateAccntInfo = $r["can_update_accntInfo"];
        $scheduleTask = $r["can_schedule_task"];
        $viewTask = $r["can_view_task"];
        $deleteTask = $r["can_delete_task"];
        $editTask = $r["can_edit_task"];
        $assignTask = $r["can_assign_task"];
        $markTaskComplete = $r["can_mark_taskComplete"];
        $viewUserAccount = $r["can_view_user_account"];
        $upDownloadBox = $r["can_box_upload_download"];
        $moveBoxfiles = $r["can_movefiles_box"];
        $addDeleteBox = $r["can_add_delete_box"];
        $createForms = $r["can_create_forms"];
        $viewForms = $r["can_view_forms"];
        $deleteForms = $r["can_delete_forms"];
        $viewFrmEntries = $r["can_view_form_entries"];
        $addDocs = $r["can_add_docs"];
        $viewDocs = $r["can_view_docs"];
        $deleteDocs = $r["can_delete_docs"];
        $sendDocs = $r["can_send_docs"];
        $transferAccnts = $r["can_transfer_accounts"];
        $continueIntake = $r["can_continue_intake"];
        $sendClientDocs = $r["can_send_clientdocs"];
        $sendClientMsg = $r["can_send_client_msg"];
        $schedAppointment = $r["can_schedule_appointment"];
        //var_dump($accnttype);
        $sql="INSERT INTO `premission_features` (accountnumber, subaccountnumber, username, acountType, can_create_accnt, can_create_subaccnt, can_create_specialtyaccnt, can_revoke_account, can_update_accntInfo, can_schedule_task, can_view_task, can_delete_task, can_edit_task, can_assign_task, can_mark_taskComplete, can_view_user_account, can_box_upload_download, can_movefiles_box, can_add_delete_box, can_create_forms, can_view_forms, can_delete_forms, can_view_form_entries, can_add_docs, can_view_docs, can_delete_docs,can_send_docs, can_transfer_accounts, can_continue_intake, can_send_clientdocs, can_send_client_msg,can_schedule_appointment) VALUES(:accntNum,:subaccntNum,:uname,:accntType,:createAccnt,:createSubAccnt,:createSpecialityAccnt,:revokeAccnt,:updateAccntInfo,:scheduleTask,:viewTask,:deleteTask,:editTask,:assignTask,:markTaskComplete,:vwUseraccnt,:upDownloadBox,:moveBoxfiles,:addDeleteBox,:createForms,:viewForms,:deleteForms,:viewFrmEntries,:addDocs,:viewDocs,:deleteDocs,:sendDocs,:transferAccnts,:continueIntake,:sendClientDocs,:sendClientMsg,:schedappnt) ";
       //var_dump($sql);
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accntNum",$acntnumber);
        $stmnt->bindParam(":subaccntNum",$subaccnt);
        $stmnt->bindParam(":uname",$uname);
        $stmnt->bindParam(":accntType",$accnttype);
        $stmnt->bindParam(":createAccnt",$cancreateAccnt);
        $stmnt->bindParam(":createSubAccnt",$cancreateSubaccnt);
        $stmnt->bindParam(":createSpecialityAccnt",$createspecialityAccnt);
        $stmnt->bindParam(":revokeAccnt",$revokeAccnt);
        $stmnt->bindParam(":updateAccntInfo",$updateAccntInfo);
        $stmnt->bindParam(":scheduleTask",$scheduleTask);
        $stmnt->bindParam(":viewTask", $viewTask);
        $stmnt->bindParam(":deleteTask",$deleteTask);
        $stmnt->bindParam(":editTask",$editTask);
        $stmnt->bindParam(":assignTask",$assignTask);
        $stmnt->bindParam(":markTaskComplete", $markTaskComplete);
        $stmnt->bindParam(":vwUseraccnt",$viewUserAccount);
        $stmnt->bindParam(":upDownloadBox", $upDownloadBox);
        $stmnt->bindParam(":moveBoxfiles", $moveBoxfiles);
        $stmnt->bindParam(":addDeleteBox",$addDeleteBox);
        $stmnt->bindParam(":createForms",$createForms);
        $stmnt->bindParam(":viewForms",$viewForms);
        $stmnt->bindParam(":deleteForms",$deleteForms);
        $stmnt->bindParam(":viewFrmEntries", $viewFrmEntries);
        $stmnt->bindParam(":addDocs", $addDocs);
        $stmnt->bindParam(":viewDocs",$viewDocs);
        $stmnt->bindParam(":deleteDocs",$deleteDocs);
        $stmnt->bindParam(":sendDocs", $sendDocs);
        $stmnt->bindParam(":transferAccnts", $transferAccnts);
        $stmnt->bindParam(":continueIntake",$continueIntake);
        $stmnt->bindParam(":sendClientDocs",$sendClientDocs);
        $stmnt->bindParam(":sendClientMsg",$sendClientMsg);
        $stmnt->bindParam(":schedappnt",$schedAppointment);
        try{
            if($stmnt->execute())
            {
                $msg = array("message"=>"Inserted","status"=>"200");
                
                return $msg;
            }
           
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>"700 SQL Error","message"=>$e->__toString());
            var_dump($msg);
            return $msg;
        }
       
    }
  }
   public function UpdateUserPremissions($accountType,$username,$accountnumber,$subaccount,$premar)
  {
    $accounttype = $accountType;
    $uname = $username;
    $acntnumber = $accountnumber;
    $subaccnt = $subaccount;
    foreach($premar as $r)
    {
         $accnttype = $r["acountType"];
        $cancreateAccnt = $r["can_create_accnt"];
        $cancreateSubaccnt = $r["can_create_subaccnt"];
        $createspecialityAccnt = $r["can_create_specialtyaccnt"];
        $revokeAccnt = $r["can_revoke_account"];
        $updateAccntInfo = $r["can_update_accntInfo"];
        $scheduleTask = $r["can_schedule_task"];
        $viewTask = $r["can_view_task"];
        $deleteTask = $r["can_delete_task"];
        $editTask = $r["can_edit_task"];
        $assignTask = $r["can_assign_task"];
        $markTaskComplete = $r["can_mark_taskComplete"];
        $accessBox = $r["can_access_box"];
        $upDownloadBox = $r["can_box_upload_download"];
        $moveBoxfiles = $r["can_movefiles_box"];
        $addDeleteBox = $r["can_add_delete_box"];
        $createForms = $r["can_create_forms"];
        $viewForms = $r["can_view_forms"];
        $deleteForms = $r["can_delete_forms"];
        $viewFrmEntries = $r["can_view_form_entries"];
        $addDocs = $r["can_add_docs"];
        $viewDocs = $r["can_view_docs"];
        $deleteDocs = $r["can_delete_docs"];
        $sendDocs = $r["can_send_docs"];
        $transferAccnts = $r["can_transfer_accounts"];
        $continueIntake = $r["can_continue_intake"];
        $sendClientDocs = $r["can_send_clientdocs"];
        $sendClientMsg = $r["can_send_client_msg"];
        $schedAppt = $r["can_schedule_appointments"];
        $createorders = $r["can_create_orders"];
        $modifyorders = $r["can_modify_orders"];
        $archiveorders = $r["can_archive_orders"];
        $createprocedures = $r["can_create_procedures"];
        $assignmeds = $r["can_assign_medications"];
        $modifymeds = $r["can_modify_medications"];
        $deletemeds = $r["can_delete_medications"];

        $sql="UPDATE `premission_features` SET `accountnumber`=:accntnum, `subaccountnumber`=:subaccnt, `username`=:uname, `acountType`=:accntType, `can_create_accnt`=:cncreate, `can_create_subaccnt`=:cnsubaccnt, `can_create_specialtyaccnt`=:cncreatespecial, `can_revoke_account`=:cnrevoke, `can_update_accntInfo`=:cnupdateaccnt, `can_schedule_task`=:cnscheduletsk, `can_view_task`=:cnviewtsk, `can_delete_task`=:cndeletetsk, `can_edit_task`=:cnedittsk, `can_assign_task`=:cnassigntsk, `can_mark_taskComplete`=:cnmarktskcomplete, `can_access_box`=:cnaccessbox, `can_box_upload_download`=:cnboxupdownload, `can_movefiles_box`=:cnmoveboxfiles, `can_add_delete_box`=:cndeleteboxfiles, `can_create_forms`=:cncreateforms, `can_view_forms`=:cncviewforms, `can_delete_forms`=:cndeleteforms, `can_view_form_entries`=:cnviewentries, `can_add_docs`=:cnadddocs, `can_view_docs`=:cnviewdocs, `can_delete_docs`=:cndeletedocs, `can_send_docs`=:cnsenddocs, `can_transfer_accounts`=:cntransferaccounts, `can_continue_intake`=:cncontinueintake, `can_send_clientdocs`=:cnsendclientdocs, `can_send_client_msg`=:cnsendclientmsg, `can_schedule_appoointments`=:cnscheduleappointments,`can_create_orders`=:creatorder, `can_modify_orders`=:modorder, `can_archive_orders`=:archiveord, `can_create_procedures`=:createprocedures, `can_assign_medications`=:assignmeds, `can_modify_medications`=:cnmodmeds, `can_delete_medications`=:deletemeds  WHERE accountnumber=:accntum AND subacountnumber=:subaccnt AND username=:uname";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":acctnum",$acntnumber);
        $stmnt->bindParam(":subaccnt",$subaccnt);
        $stmnt->bindParam(":uname",$uname);
        $stmnt->bindParam(":accntType",$accnttype);
        $stmnt->bindParam(":cncreate", $cancreateAccnt);
        $stmnt->bindParam(":cnsubaccnt", $cancreateSubaccnt);
        $stmnt->bindParam(":cncreatespecial", $createspecialityAccnt);
        $stmnt->bindParam(":cnrevoke", $revokeAccnt);
        $stmnt->bindParam(":cnupdateaccnt",$updateAccntInfo);
        $stmnt->bindParam(":cnscheduletsk", $scheduleTask);
        $stmnt->bindParam(":cnviewtsk",$viewTask);
        $stmnt->bindParam(":cndeletetsk",$deleteTask);
        $stmnt->bindParam(":cnedittsk",$editTask);
        $stmnt->bindParam(":cnassigntsk",$assignTask);
        $stmnt->bindParam(":can_mark_taskComplete", $markTaskComplete);
        $stmnt->bindParam(":cnaccessbox",$accessBox);
        $stmnt->bindParam(":cnboxupdownload",$upDownloadBox);
        $stmnt->bindParam(":cnmoveboxfiles", $moveBoxfiles);
        $stmnt->bindParam(":cndeleteboxfiles",$addDeleteBox);
        $stmnt->bindParam(":cncreateforms", $createForms);
        $stmnt->bindParam(":cnviewforms",$viewForms);
        $stmnt->bindParam(":cndeleteforms",$deleteForms);
        $stmnt->bindParam(":cnviewentries",$viewFrmEntries);
        $stmnt->bindParam(":cnadddocs",$addDocs);
        $stmnt->bindParam(":cnviewdocs", $viewDocs);
        $stmnt->bindParam(":cndeletedocs",$deleteDocs);
        $stmnt->bindParam(":cnsenddocs",$sendDocs);
        $stmnt->bindParam(":transferaccnt", $transferAccnts);
        $stmnt->bindParam(":cncontinueintake", $continueIntake);
        $stmnt->bindParam(":cnsendclientdocs",$sendClientDocs);
        $stmnt->bindParam(":cnsendclientmsg", $sendClientMsg);
        $stmnt->bindParam(":cnscheduleappointments",$schedAppt);
        $stmnt->bindParam(":creatorder",$corder);
        $stmnt->bindParam(":modorder",$modifyorder);
        $stmnt->bindParam(":archiveord", $archiveorder);
        $stmnt->bindParam(":createprocedures",$createprocedures);
        $stmnt->bindParam(":assignmeds", $assignmeds);
        $stmnt->bindParam(":cnmodmeds", $modifymeds);
        $stmnt->bindParam(":deletemeds",$deletemeds);

        try{
            $msg = array("message"=>"Updated","status"=>"200");
            return $msg;
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>"700 SQL Error","message"=>$e->__toString());
            return $msg;
        }
    }
  }
   public function DeletePremissions($accountType,$username,$accountnumber,$subaccount)
  {
    $accounttype = $accountType;
    $uname = $username;
    $acntnumber = $accountnumber;
    $subaccnt = $subaccount;
    foreach($premar as $r)
    {
        $sql="DELETE `premission_features` WHERE accountnumber=:accntum AND subacountnumber=:subaccnt AND username=:uname";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":acctnum",$acntnumber);
        $stmnt->bindParam(":subaccnt",$subaccnt);
        $stmnt->bindParam(":uname",$uname);

        try{
            $msg = array("message"=>"Delete","status"=>"200");
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>"700 SQL Error","message"=>$e->__toString());
            return $msg;
        }
    }
  }
  public function IndividualUserPremissions($accntnumber,$subnumber)
  {
    $sql="SELECT * FROM `premission_features` WHERE accountnumber=:accntNum AND subaccountnumber=:subaccntNum ";
    $stmnt = $this->con->prepare($sql);
    $stmnt->bindParam(":accntNum",$accntnumber);
    $stmnt->bindParam(":subaccntNum",$subnumber);
    //$stmnt->bindParam(":uname",$uname);
   
    try{
        if($stmnt->execute())
        {
            $records = $stmnt->fetchAll();
            return $records;
        }
       
    }
    catch(PDOException $e)
    {
        $msg = array("error"=>"700 SQL Error","message"=>$e->__toString());
        return $msg;
    }
  }
  public function GetUserDefaultPremissions($accounttype)
  {
    
    $sql="SELECT * FROM `default_premissions` WHERE `acountType`=:acntype";
    $stmnt = $this->con->prepare($sql);
    $stmnt->bindParam(":acntype",$accounttype);
    try{

        if($stmnt->execute())
        {
            $records = $stmnt->fetchAll();
         
            $msg = array("status"=>"200","message"=>"Has Records","records"=>$records);
            return $msg;
        }
    }
    catch(PDOException $e)
    {
        $msg= array("error"=>$e->__toString());
    }
  }
  public function UpdateIndividualUserPremissions($username,$accountnumber,$subaccntnumber,$email,$accounttype,$cando,$cantdo)
  {
    
    $setpair="UPDATE `premission_features`";
    $setpair .="SET ";
    $i=0;
    if($username=="")
    {
        var_dump("null");
        $username="update_was_null";
    }
    foreach($cando as $key => $value)
    {
       $i++;
        $setpair .="`".$value."`='true',";
        if($i ==count($cando))
        {
          
            $setpair .="`".$value."`='true',";
          
            
            break;
        }
         
    }
    $i=0;
    foreach($cantdo as $key =>$val)
    {
         $i++;
        $setpair .="`".$val."`='false',";

        if($i ==count($cantdo))
        {
          
            $setpair .="`".$val."`='false'";
            $i=0;
            break;
        }
      
       
    }
    $setpair .=",`acountType`=:accntype";
    $setpair .=" WHERE `accountnumber`=:accnt AND `subaccountnumber`=:subaccnt AND username=:uname";
    //var_dump($setpair);
        $stmnt = $this->con->prepare($setpair);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":subaccnt",$subaccntnumber);
        $stmnt->bindParam(":uname",$username);
        $stmnt->bindParam(":accntype",$accounttype);

        try{
            if($stmnt->execute())
            {
                //$records = $stmnt->fetchAll();
                 $msg = array("message"=>"Updated","status"=>"200");
                 //var_dump($msg);
                 return $msg;
            }
           
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>"700 SQL Error","message"=>$e->__toString(),"sql"=>$setpair);
            var_dump($msg);
            return $msg;
        }
  }
   public function GetIndividualUserPremissions($userName,$accntnumber,$subnumber,$userEmail)
  {
   
    
    
    //var_dump($uname);
    //var_dump($acntnumber);
    //var_dump($subaccnt);
        $sql="SELECT * FROM `premission_features` WHERE accountnumber=:accntnum AND subaccountnumber=:subaccnt AND username=:uname";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accntnum",$accntnumber);
        $stmnt->bindParam(":subaccnt",$subnumber);
        $stmnt->bindParam(":uname",$userName);

        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                 $msg = array("message"=>"Fetch Records Successfully","status"=>"200","records"=>$records);
                 //var_dump($msg);
                 return $msg;
            }
           
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>"700 SQL Error","message"=>$e->__toString());
            return $msg;
        }
   
  }
  public function getDefaultPremissions($accounttype)
  {
    switch($accounttype)
    {
        case"Administrator":
        {
            $sql="SELECT * FROM `default_premissions` WHERE acountType=:admin";
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":admin",$accounttype);
            try
            {
                if($stmnt->execute())
                {
                    $records = $stmnt->fetchAll();
                    return $records;
                }

            }
            catch(PDOException $e)
            {
                $msg= array("error"=>"700-Sql","message"=>$e->__toString());
                return $msg;
            }
            break;
        }
        case"View":
        {
            $sql="SELECT * FROM `default_premissions` WHERE acountType=:admin";
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":admin",$accounttype);
            try
            {
                if($stmnt->execute())
                {
                    $records = $stmnt->fetchAll(PDO::FETCH_ASSOC);
                    //var_dump($records);
                    return $records;
                }

            }
            catch(PDOException $e)
            {
                $msg= array("error"=>"700-Sql","message"=>$e->__toString());
                return $msg;
            }
            break;
        }
        case"Read Only":
        {
            $sql="SELECT * FROM `default_premissions` WHERE acountType=:viewprem";
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":viewprem",$accounttype);
            try
            {
                if($stmnt->execute())
                {
                    $records = $stmnt->fetchAll();
                    //var_dump($records);
                    return $records;
                }

            }
            catch(PDOException $e)
            {
                $msg= array("error"=>"700-Sql","message"=>$e->__toString());
                return $msg;
            }
        }
    }
  }
  //Grad Orders for Mediacation Admin By Patient ID 
  public function GrabOrderbyPatientID($patientid)
  {
    $ordresult = $this->GetPatientOrders($patientid);
    $result = array("orders"=>$ordresult);
    return $result;
  }
  //Update Medication Information For Administration 
  public function UpdateMedicationInfo($accountnumber,$ordernumber,$patientid,$ndcnumber,$rx,$prn,
  $medsettings,$totalTabs)
  {
    //I added available, and tabletqnty for now but prob will need to adust the avaialbel tables (especially if tablets are remaning from the previous refill)
    $sql="UPDATE medications SET ndcnumber=:ndcnum,prn=:prn,rxnorns=:rx,tabletqnty=:totaltbs,total=:totaltbs,available=:totaltbs, additional_settings=:medsetting
    WHERE order_number=:ordnum AND accountnumber=:accnt AND patient_id=:patid ";
    $stmnt = $this->con->prepare($sql);
    $stmnt->bindParam(":accnt",$accountnumber);
    $stmnt->bindParam(":ordnum",$ordernumber);
    $stmnt->bindParam(":patid",$patientid);
    //$stmnt->bindParam(":medname",$medname);
   // $stmnt->bindParam(":npinum",$npinumber);
    $stmnt->bindParam(":ndcnum",$ndcnumber);
    $stmnt->bindParam(":rx",$rx);
    //$stmnt->bindParam(":diag",$diagnois);
   // $stmnt->bindParam(":dos",$dosage);
   // $stmnt->bindParam(":freq",$freq);
   // $stmnt->bindParam(":route",$route);
    $stmnt->bindParam(":prn",$prn);
    //$stmnt->bindParam(":qnty",$quantity);
    $stmnt->bindParam(":medsetting",$medsettings);
    $stmnt->bindParam(":totaltbs",$totalTabs);
    try{
        if($stmnt->execute())
        {
            //Updated Successfully
            $updtmsg = "Updated Successfully";
            $updatemsg = array("code"=>"200-Successful","message"=>$updtmsg);
            return $updatemsg;
        }
    }
    catch(PDOException $e)
    {
        $ermsg = $e->__toString();
        $ermsgar = array("code"=>"SQL-700","message"=>"SQL Error","error"=>$ermsg);
        return $ermsgar;
    }
  }
  //Find MedbyName and Active Status for AdministrationApplication 
   public function DoesMedExist($accountnumber,$ordernumber,$npinumber,$patientid,$medname,$status)
   {
   
    // $sql="SELECT * FROM medications
    // LEFT JOIN orders ON orders.ordernumber = medications.order_number
    // WHERE orders.accountnumber=:accnt AND orders.patientid=:patid AND orders.npinumber=:npi AND medications.status='Active' AND medications.medname=:medname";
     $sql="SELECT * FROM medications
     LEFT JOIN orders ON orders.ordernumber = medications.order_number
     WHERE orders.accountnumber=:accnt AND orders.patientid=:patid AND orders.npinumber=:npi AND orders.status='Active' AND medications.medname=:medname";
    
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":npi",$npinumber);
        $stmnt->bindParam(":patid",$patientid);
        $stmnt->bindParam(":medname",$medname);
       // $stmnt->bindParam(":stat",$status);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                //var_dump($records);
                $numofrecords = count($records);
                $successmsg = array("code"=>"200Successfull","records"=>$records,"count"=>$numofrecords);
                return $successmsg;
            }
        }
        catch(PDOException $e)
        {
            $errormsg = array("code"=>"700-Sql Error","message"=>$e->__toString());
            return $errormsg;
        }
   }
    //end new sql syntax
    public function findpatientactiveMedOrders($accountnumber,$npinumber,$patientid)
    {
       /*
       SELECT * FROM medications 
      LEFT JOIN orders ON  orders.ordernumber = medications.order_number
      LEFT JOIN patientPharmacy ON patientpharmacy.ordernumber = orders.ordernumber
      LEFT JOIN medicationlog ON medicationlog.ordernumber = patientpharmacy.ordernumber
      WHERE orders.accountnumber=:accnt AND orders.patientid=:patid and orders.npinumber=:npi AND orders.status='Active' AND additional_settings='Administered' ";

      @Adusted last 2 item that we can look at later 
      LEFT JOIN medicationlog ON medicationlog.patientid = orders.patientid
        LEFT JOIN medlogtimes ON medlogtimes.medid = medicationlog.medicationid
        ----Problem---
        Due to multiple times being in the medlogtimes with the same foreign keys - when I add it to the LEFT joiont statement it products multiple Medication results in order to bring back
        multiple medlog times shoud the frequency be more than 1 
      */
     /* $sql="SELECT * FROM medications 
      LEFT JOIN orders ON  orders.ordernumber = medications.order_number
      LEFT JOIN medicationlog ON medicationlog.medicationid = medications.medentryid
      LEFT JOIN medlogtimes ON medlogtimes.medid = medicationlog.medicationid
      WHERE orders.accountnumber=:accnt AND orders.patientid=:patid and orders.npinumber=:npi AND orders.status='Active' AND additional_settings='Administered' ";
      *6/10/25 Need to adjust the sql to account for Meciation.status to be included in the WHERE clause
      *6/10/25 Also added medication status =hold OR adjustment to include active or held medications 
     */
    $sql="SELECT medications.*,
    orders.*,
    medicationlog.*,
      GROUP_CONCAT(medlogtimes.time ORDER BY medlogtimes.time
      SEPARATOR ', ') AS times,
      GROUP_CONCAT(medlogtimes.takentime ORDER BY medlogtimes.takentime SEPARATOR ', ') AS takentimes, 
      GROUP_CONCAT(medlogtimes.reason ORDER BY medlogtimes.reason SEPARATOR ', ') AS earlyReason,   
      GROUP_CONCAT(medlogtimes.status ORDER BY medlogtimes.status SEPARATOR ', ') AS temporaryStatus
      FROM 
      medications
      LEFT JOIN orders ON  orders.ordernumber = medications.order_number
      LEFT JOIN medicationlog ON medicationlog.medicationid = medications.medentryid
      LEFT JOIN medlogtimes ON medlogtimes.medid = medicationlog.medicationid
      WHERE orders.accountnumber=:accnt AND orders.patientid=:patid and orders.npinumber=:npi AND orders.status='Active' OR orders.status='hold' AND medications.status='Active' OR medications.status='hold' AND additional_settings='Administered'
      GROUP BY
      medications.medentryid,  orders.orderid,medicationlog.phid,orders.ordernumber, medicationlog.medicationid";
      $stmnt = $this->con->prepare($sql);
      $stmnt->bindParam(":accnt",$accountnumber);
      $stmnt->bindParam(":npi",$npinumber);
      $stmnt->bindParam(":patid",$patientid);
     // $stmnt->bindParam(":npi",$npinumber);
      try{
         if($stmnt->execute())
         {
          $records = $stmnt->fetchAll();
          $successmsg = array("code"=>"200-Successfull","records"=>$records);
          return $successmsg;
         }
      }
      catch(PDOException $e)
      {
        $errormsg = array("code"=>"700-Sql Error","message"=>$e->__toString());
        return $errormsg;
      }
    }
    /*6/10/2025 Adding SQL to Locate Med Log Records and add New Dates to be administered for any medications that's in the Log Table*/

    public function GetMeasurements($arr){
        $sql = "SELECT * FROM foradata WHERE patientId=:pid AND MDeviceID = :meterid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid", $arr["pid"]);
        $stmnt->bindParam(":meterid", $arr["meterid"]);
        try {
            if ($stmnt->execute()) {
                $result = $stmnt->fetchAll();
                return json_encode(array("data"=>$result, "status"=>"Success"));
            }
        }
        catch(PDOException $e){
            $arr = array("status"=>"Error: ".$e->__toString());
            return json_encode($arr);
        }
    }
    public function GetMeasurements2($arr){
        $sql = "SELECT * FROM foradata WHERE patientId=:pid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid", $arr["pid"]);
        try {
            if ($stmnt->execute()) {
                $result = $stmnt->fetchAll();
                return json_encode(array("data"=>$result, "status"=>"Success"));
            }
        }
        catch(PDOException $e){
            $arr = array("status"=>"Error: ".$e->__toString());
            return json_encode($arr);
        }
    }
    public function MarkBilled($arr){
        $sql = "";
        if ($arr['current'] == 0) {
            $sql = "UPDATE cpt_billing_history2 SET status = 2, icd = :icd WHERE fid=:fid";
        }
        else {
            $sql = "UPDATE cpt_billing2 SET status = 2, icd = :icd WHERE fid=:fid";
        }
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":fid", $arr['fid']);
        $stmnt->bindParam(":icd", $arr['icd']);
        try {
            if ($stmnt->execute()){
                return json_encode(array("status"=>"Success"));
            }
            else {
                return json_encode(array("status"=>"Fail"));
            }
        }
        catch(PDOException $e){
            $arr = array("status"=>"Error: ".$e->__toString());
            return json_encode($arr);
        }
    }

    public function AddNewTimeCpt($pid, $code, $nextCode, $lastCycle, $time){
        $zero = 0;
        $one = 1;
        $null = null;
        $nextLastCycle = $lastCycle ? $lastCycle + 1 : null;
        $date = date("Y-m-d");
        $firstDate = date("Y-m-01", strtotime($date));
        $lastDate = date("Y-m-t", strtotime($date));
        $newDataSql = "INSERT INTO cpt_billing2 (pid, cpt, cost, status, firstDate, lastDate, cycleCount, latestDateAdded, fidLastCycle, fidNextCycle) VALUES(:pid, :cpt, :cost, :status, :firstDate, :lastDate, :cycleCount, :latestDateAdded, :fidLastCycle, :fidNextCycle)";
        $newStmnt = $this->con->prepare($newDataSql);
        $newStmnt->bindParam(":pid", $pid);
        $newStmnt->bindParam(":cpt", $code);
        $newStmnt->bindParam(":cost", $this->cptCosts[$code]);
        $newStmnt->bindParam(":firstDate", $firstDate);
        $newStmnt->bindParam(":lastDate", $lastDate);
        $newStmnt->bindParam(":latestDateAdded", $date);
        $newStmnt->bindParam(":fidLastCycle", $lastCycle);
        $newStmnt->bindParam(":fidNextCycle", $null);
        if ($time <= 1200) {
            if ($time == 1200) {
                $newStmnt->bindParam(":status", $one);
            }
            else {
                $newStmnt->bindParam(":status", $zero);
            }
            $newStmnt->bindParam(":cycleCount", $time);
            try {
                $newStmnt->execute();
            }
            catch (PDOException $e){
                error_log("ERROR: ----------------------" . $e->__toString());
            }
            $newStmnt->bindParam(":pid", $pid);
            $newStmnt->bindParam(":cpt", $nextCode);
            $newStmnt->bindParam(":cost", $this->cptCosts[$nextCode]);
            $newStmnt->bindParam(":firstDate", $firstDate);
            $newStmnt->bindParam(":lastDate", $lastDate);
            $newStmnt->bindParam(":latestDateAdded", $null);
            $newStmnt->bindParam(":fidLastCycle", $nextLastCycle);
            $newStmnt->bindParam(":fidNextCycle", $null);
            $newStmnt->bindParam(":status", $zero);
            $newStmnt->bindParam(":cycleCount", $zero);
            try {
                $newStmnt->execute();
            }
            catch (PDOException $g){
                error_log("ERROR: ----------------------" . $g->__toString());
            }
            return;
        }
        else {
            $firstTime = 1200;
            $newStmnt->bindParam(":status", $one);
            $newStmnt->bindParam(":cycleCount", $firstTime);
            try {
                $newStmnt->execute();
            }
            catch (PDOException $e){
                error_log("ERROR: ----------------------" . $e->__toString());
            }
            
            $secondTime = $time - 1200;
            $newDataSql2 = "INSERT INTO cpt_billing2 (pid, cpt, cost, status, firstDate, lastDate, cycleCount, latestDateAdded, fidLastCycle, fidNextCycle) VALUES(:pid, :cpt, :cost, :status, :firstDate, :lastDate, :cycleCount, :latestDateAdded, :fidLastCycle, :fidNextCycle)";
            $newStmnt2 = $this->con->prepare($newDataSql2);
            $newStmnt2->bindParam(":pid", $pid);
            $newStmnt2->bindParam(":cpt", $nextCode);
            $newStmnt2->bindParam(":cost", $this->cptCosts[$nextCode]);
            $newStmnt2->bindParam(":firstDate", $firstDate);
            $newStmnt2->bindParam(":lastDate", $lastDate);
            $newStmnt2->bindParam(":latestDateAdded", $date);
            $newStmnt2->bindParam(":fidLastCycle", $nextLastCycle);
            $newStmnt2->bindParam(":fidNextCycle", $null);
            $newStmnt2->bindParam(":cycleCount", $secondTime);
            if ($secondTime >= 1200) {
                $newStmnt2->bindParam(":status", $one);
            }
            else {
                $newStmnt2->bindParam(":status", $zero);
            }
            try {
                $newStmnt2->execute();
            }
            catch (PDOException $e){
                error_log("ERROR: ----------------------" . $e->__toString());
            }
            return;
        }
    }

    public function CheckNewTimeCpt($arr){
        $code = "";
        $nextCode = "";
        if ($arr["type"] == "9898X"){
            $code = "98980";
            $nextCode = "98981";
        }
        else if ($arr["type"] == "9945X"){
            $code = "99457";
            $nextCode = "99458";
        }
        else {
            return;
        }
        $zero = 0;
        $one = 1;
        $null = null;
        $time = intval($arr["time"]);
        $firstSql = "SELECT * FROM cpt_billing2 WHERE pid=:pid AND cpt=:cpt";
        $stmnt = $this->con->prepare($firstSql);
        $stmnt->bindParam(":pid", $arr["pid"]);
        $stmnt->bindParam(":cpt", $code);
        if (!$stmnt->execute()){
            throw new PDOException("Error: cannot search in cpt_meters for meter " . $arr["meterid"] . " for patient " . $arr["pid"]);
        }
        $results = $stmnt->fetchAll();
        if (count($results) == 0) {
            $this->AddNewTimeCpt($arr["pid"], $code, $nextCode, null, $time);
        }
        else {
            $result = $results[0];
            $date = date("Y-m-d");
            if ($date > $result["lastDate"]) {
                $fidLastId = $result["fid"];
                $this->AddNewTimeCpt($arr["pid"], $code, $nextCode, $fidLastId, $time);
                $oldFirstDataSql = "INSERT INTO cpt_billing_history2 (fid, pid, cpt, cost, status, firstDate, lastDate, cycleCount, latestDateAdded, fidNextCycle, fidLastCycle) SELECT fid,pid,cpt,cost,status,firstDate,lastDate,cycleCount,latestDateAdded,:fidNextCycle,fidLastCycle FROM cpt_billing2 WHERE fid = :fid";
                $oldSecondDataSql = "INSERT INTO cpt_billing_history2 (fid, pid, cpt, cost, status, firstDate, lastDate, cycleCount, latestDateAdded, fidNextCycle, fidLastCycle) SELECT fid,pid,cpt,cost,status,firstDate,lastDate,cycleCount,latestDateAdded,:fidNextCycle,fidLastCycle FROM cpt_billing2 WHERE fid = :fid";

                $fstStmnt = $this->con->prepare($oldFirstDataSql);
                $lastId = intval($this->con->lastInsertId()) - 1; 
                $fstStmnt->bindParam(":fid", $fidLastId);
                $fstStmnt->bindParam(":fidNextCycle", $lastId);

                $sndStmnt = $this->con->prepare($oldSecondDataSql);
                $nextFid = intval($fidLastId) + 1;
                $nextLastId = intval($this->con->lastInsertId());
                $sndStmnt->bindParam(":fid", $nextFid);
                $sndStmnt->bindParam(":fidNextCycle", $nextLastId);

                try {
                    $fstStmnt->execute();
                    $sndStmnt->execute();
                }
                catch (PDOException $m){
                    error_log("ERROR: ----------------------" . $m->__toString());
                }

                $deleteSql1 = "DELETE FROM cpt_billing2 WHERE fid=:fid";
                $deleteSql2 = "DELETE FROM cpt_billing2 WHERE fid=:fid";
                $stmntDel1 = $this->con->prepare($deleteSql1);
                $stmntDel2 = $this->con->prepare($deleteSql2);
                $stmntDel1->bindParam(":fid", $fidLastId);
                $stmntDel2->bindParam(":fid", $nextFid);
                try {
                    $stmntDel1->execute();
                    $stmntDel2->execute();
                }
                catch (PDOException $x){
                    error_log("ERROR: ----------------------" . $x->__toString());
                }
            }
            else {
                $curTime = $result["cycleCount"];
                $newTime = $time + $curTime;
                if ($newTime <= 1200){
                    $newStatus = $newTime >= 1200 ? 1 : 0;
                    $updateSql = "UPDATE cpt_billing2 SET status = :status, cycleCount=:cycleCount, latestDateAdded=:latestDateAdded WHERE pid=:pid AND cpt=:cpt";
                    $updateStmnt = $this->con->prepare($updateSql);
                    $updateStmnt->bindParam(":status", $newStatus);
                    $updateStmnt->bindParam(":cycleCount", $newTime);
                    $updateStmnt->bindParam(":latestDateAdded", $date);
                    $updateStmnt->bindParam(":pid", $arr["pid"]);
                    $updateStmnt->bindParam(":cpt", $code);
                    try {
                        $updateStmnt->execute();
                    }
                    catch (PDOException $k){
                        error_log("ERROR: ----------------------" . $k->__toString());
                    }
                }
                else {
                    $nextCodeSql = "SELECT * FROM cpt_billing2 WHERE pid=:pid AND cpt=:cpt";
                    $nextCodeStmnt = $this->con->prepare($nextCodeSql);
                    $nextCodeStmnt->bindParam(":pid", $arr["pid"]);
                    $nextCodeStmnt->bindParam(":cpt", $nextCode);
                    if (!$nextCodeStmnt->execute()){
                        throw new PDOException("Error: cannot search in cpt_meters for meter " . $arr["meterid"] . " for patient " . $arr["pid"]);
                    }
                    $nextResult = $nextCodeStmnt->fetchAll()[0];
                    $nextCurTime = $nextResult["cycleCount"];
                    if ($curTime == 1200){
                        $nextNewTime = $nextCurTime + $time;
                        $updateTimeSql = "UPDATE cpt_billing2 SET status = :status, cycleCount=:cycleCount, latestDateAdded=:latestDateAdded WHERE pid=:pid AND cpt=:cpt";
                        $updateTimeStmnt = $this->con->prepare($updateTimeSql);
                        $updateTimeStmnt->bindParam(":pid", $arr["pid"]);
                        $updateTimeStmnt->bindParam(":cycleCount", $nextNewTime);
                        $updateTimeStmnt->bindParam(":latestDateAdded", $date);
                        $updateTimeStmnt->bindParam(":cpt", $nextCode);
                        $nextStatus = $nextNewTime >= 1200 ? 1 : 0;
                        $updateTimeStmnt->bindParam(":status", $nextStatus);
                        try {
                            $updateTimeStmnt->execute();
                        }
                        catch (PDOException $t){
                            error_log("ERROR: ----------------------" . $t->__toString());
                        }
                    }
                    else {
                        $maxTime = 1200;
                        $nextNewTime = $newTime - 1200 + $nextCurTime;
                        $updateCurTimeSql = "UPDATE cpt_billing2 SET status = :status, cycleCount=:cycleCount, latestDateAdded=:latestDateAdded WHERE pid=:pid AND cpt=:cpt";
                        $updateCurTimeStmnt = $this->con->prepare($updateCurTimeSql);
                        $updateCurTimeStmnt->bindParam(":pid", $arr["pid"]);
                        $updateCurTimeStmnt->bindParam(":cycleCount", $maxTime);
                        $updateCurTimeStmnt->bindParam(":latestDateAdded", $date);
                        $updateCurTimeStmnt->bindParam(":cpt", $code);
                        $updateCurTimeStmnt->bindParam(":status", $one);

                        $updateNextTimeSql = "UPDATE cpt_billing2 SET status = :status, cycleCount=:cycleCount, latestDateAdded=:latestDateAdded WHERE pid=:pid AND cpt=:cpt";
                        $updateNextTimeStmnt = $this->con->prepare($updateNextTimeSql);
                        $updateNextTimeStmnt->bindParam(":pid", $arr["pid"]);
                        $updateNextTimeStmnt->bindParam(":cycleCount", $nextNewTime);
                        $updateNextTimeStmnt->bindParam(":latestDateAdded", $date);
                        $updateNextTimeStmnt->bindParam(":cpt", $nextCode);
                        $updateStatus = $nextNewTime >= 1200 ? 1 : 0;
                        $updateNextTimeStmnt->bindParam(":status", $updateStatus);

                        try {
                            $updateCurTimeStmnt->execute();
                            $updateNextTimeStmnt->execute();
                        }
                        catch (PDOException $v){
                            error_log("ERROR: ----------------------" . $v->__toString());
                        }
                    }
                }
            }
        }
    }
    public function CheckNewDataCpt2($arr){
        // Get all CPT codes that are linked to pid-meter pair
        $firstSql = "SELECT cpt FROM cpt_meters WHERE pid=:pid AND meter = :meter";
        $stmnt = $this->con->prepare($firstSql);
        $stmnt->bindParam(":pid", $arr["pid"]);
        $stmnt->bindParam(":meter", $arr["meterid"]);
        if (!$stmnt->execute()) {
            throw new PDOException("Error: cannot search in cpt_meters for meter " . $arr["meterid"] . " for patient " . $arr["pid"]);
        }
        $results = $stmnt->fetchAll();
        // Some calculations/setting the table for future operations
        $dataDate = new DateTime($arr["date"]);
        $newDate = $dataDate->format('Y-m-d');
        date_add($dataDate, date_interval_create_from_date_string("29 days"));
        $lastDate30Days = $dataDate->format('Y-m-d');
        $zero = 0;
        $one = 1;
        $null = null;
        // Go through each CPT
        foreach ($results as $cpt){
            $cptCode = $cpt["cpt"];
            // Ignore CPTs involving communication as they have nothng to do with incoming device data
            if ($cptCode == "99457" || $cptCode == "99458" || $cptCode == "98980" || $cptCode == "98981"){
                continue;
            }
            // Get all current CPT records for each CPT-pid pair
            $secondSql = "SELECT * FROM cpt_billing2 WHERE pid=:pid AND cpt = :cpt";
            $stmnt = $this->con->prepare($secondSql);
            $stmnt->bindParam(":pid", $arr["pid"]);
            $stmnt->bindParam(":cpt", $cptCode);
            if (!$stmnt->execute()) {
                throw new PDOException("Error: cannot search in cpt_billing for meter " . $arr["meterid"] . " for patient " . $arr["pid"]);
            }
            $answer = $stmnt->fetchAll();
            // If there is no record for CPT-pid pair, make a new one that's most current
            if (empty($answer)) {
                $newDataSql = "INSERT INTO cpt_billing2 (pid, cpt, cost, status, firstDate, lastDate, cycleCount, latestDateAdded, fidLastCycle, fidNextCycle) VALUES(:pid, :cpt, :cost, :status, :firstDate, :lastDate, :cycleCount, :latestDateAdded, :fidLastCycle, :fidNextCycle)";
                $newStmnt = $this->con->prepare($newDataSql);
                $newStmnt->bindParam(":pid", $arr["pid"]);
                $newStmnt->bindParam(":cpt", $cptCode);
                $newStmnt->bindParam(":cost", $this->cptCosts[$cptCode]);
                $newStmnt->bindParam(":status", $zero);
                $newStmnt->bindParam(":firstDate", $newDate);
                $newStmnt->bindParam(":lastDate", $lastDate30Days);
                $newStmnt->bindParam(":cycleCount", $one);
                $newStmnt->bindParam(":latestDateAdded", $newDate);
                $newStmnt->bindParam(":fidLastCycle", $null);
                $newStmnt->bindParam(":fidNextCycle", $null);
                // if ($cptCode == "99453" || $cptCode == "99454" || $cptCode == "98975" || $cptCode == "98976" || $cptCode == "99091") {
                //     $newStmnt->bindParam(":lastDate", $lastDate30Days);
                // }
                try {
                    $newStmnt->execute();
                }
                catch (PDOException $e){
                    error_log("ERROR: ----------------------" . $e->__toString());
                }
            }
            // If there is a record for CPT-pid pair already
            else {
                $cptObj = $answer[0];
                // Grab relevant values for record
                $dataDate2 = new DateTime($arr["date"]);
                $newDate2 = $dataDate2->format('Y-m-d');
                $firstDate = (new DateTime($cptObj["firstDate"]))->format('Y-m-d');
                $lastDate = (new DateTime($cptObj["lastDate"]))->format('Y-m-d');
                $latestDate = (new DateTime($cptObj["latestDateAdded"]))->format(('Y-m-d'));
                // Case for when new data falls within time range, and is a new unique date
                if (($firstDate <= $newDate2 && $newDate2 <= $lastDate) && ($newDate2 > $latestDate)) {
                    $updateSql = "UPDATE cpt_billing2 SET status=:status, cycleCount=:cycleCount, latestDateAdded=:latestDateAdded WHERE pid=:pid AND cpt=:cpt";
                    $updateStmnt = $this->con->prepare($updateSql);
                    // Update cycleCount and status accordingly
                    $newCycleCount = $cptObj["cycleCount"] + 1;
                    $newStatus = $newCycleCount >= 16 ? 1 : 0;

                    $updateStmnt->bindParam(":status", $newStatus);
                    $updateStmnt->bindParam(":cycleCount", $newCycleCount);
                    $updateStmnt->bindParam(":latestDateAdded", $newDate2);
                    $updateStmnt->bindParam(":pid", $arr["pid"]);
                    $updateStmnt->bindParam(":cpt", $cptObj["cpt"]);
                    try {
                        $updateStmnt->execute();
                    }
                    catch(PDOException $e){
                        error_log("ERROR: ----------------------" . $e->__toString());
                    }
                }
                // Case for when new data comes after current record, and is not one of the setup CPTs
                if ($newDate2 > $lastDate && $cptCode != "99453" && $cptCode != "98975") {
                    $newRecordSql = "INSERT INTO cpt_billing2 (pid, cpt, cost, status, firstDate, lastDate, cycleCount, latestDateAdded, fidLastCycle, fidNextCycle) SELECT pid, cpt, cost, :status, :firstDate, :lastDate, :cycleCount, :latestDateAdded, :fidLastCycle, fidNextCycle FROM cpt_billing2 WHERE fid=:fid";
                    $newRecordStmnt = $this->con->prepare($newRecordSql);
                    // $lastDateObj = new DateTime($cptObj["lastDate"]);
                    // If newDate is past current range, make newDate start of next range and calc end of new range from newDate
                    $lastDate2 = date_add($dataDate2, date_interval_create_from_date_string('29 days'))->format('Y-m-d');
                    
                    $newRecordStmnt->bindParam(":firstDate", $newDate2);
                    $newRecordStmnt->bindParam(":lastDate", $lastDate2);
                    $newRecordStmnt->bindParam(":status", $zero);
                    $newRecordStmnt->bindParam(":cycleCount", $one);
                    $newRecordStmnt->bindParam(":latestDateAdded", $newDate2);
                    $newRecordStmnt->bindParam(":fid", $cptObj["fid"]);
                    $newRecordStmnt->bindParam(":fidLastCycle", $cptObj["fid"]);
                    try {
                        $newRecordStmnt->execute();
                    }
                    catch(PDOException $e){
                        error_log("ERROR: ----------------------" . $e->__toString());
                    }
                    // File old CPT for recordkeeping in its own table
                    $oldDataSql = "INSERT INTO cpt_billing_history2 (fid, pid, cpt, cost, status, firstDate, lastDate, cycleCount, latestDateAdded, fidNextCycle, fidLastCycle) SELECT fid,pid,cpt,cost,status,firstDate,lastDate,cycleCount,latestDateAdded,:fidNextCycle,fidLastCycle FROM cpt_billing2 WHERE fid = :fid";
                    $stmntOld = $this->con->prepare($oldDataSql);
                    $stmntOld->bindParam(":fid", $cptObj["fid"]);
                    $stmntOld->bindParam(":fidNextCycle", $this->con->lastInsertId());

                    //Delete the old record in cpt_billing2
                    $deleteSql = "DELETE FROM cpt_billing2 WHERE fid=:fid";
                    $stmntDel = $this->con->prepare($deleteSql);
                    $stmntDel->bindParam(":fid", $cptObj["fid"]);
                    try {
                        if ($stmntOld->execute())
                            $stmntDel->execute();
                    }
                    catch(PDOException $p){
                        error_log("ERROR: ----------------------" . $p->__toString());
                    }
                }
            }
        }
    }
    public function CheckNewDataCpt($arr){
        //$arr-> date, pid, meterid
        $firstSql = "SELECT * FROM cpt_billing WHERE pid=:pid AND meterid = :meterid";
        $stmnt = $this->con->prepare($firstSql);
        $stmnt->bindParam(":pid", $arr["pid"]);
        $stmnt->bindParam(":meterid", $arr["meterid"]);
        if (!$stmnt->execute()) {
            throw new PDOException("Error: cannot search in cpt_billing for meter " . $arr["meterid"] . " for patient " . $arr["pid"]);
        }
        $results = $stmnt->fetchAll();
        foreach ($results as $cptObj) {
            $dataDate = new DateTime($arr["date"]);
            $cptCode = $cptObj["cpt"];
            // Case where data is the first for device + CPT combo
            if (!$cptObj["firstDate"]){
                $secondSql = "UPDATE cpt_billing SET firstDate=:firstDate, lastDate=:lastDate, cycleCount=:cycleCount, latestDateAdded = :latestDateAdded WHERE pid=:pid AND meterid=:meterid AND cpt=:cpt";
                $stmnt = $this->con->prepare($secondSql);

                // Initializing fields for first data read
                $cycleCountCalc = $cptObj["cycleCount"] + 1;
                $firstDate = $dataDate->format('Y-m-d');
                // Last day of calendar month
                $lastMonthDate = $dataDate->format('Y-m-t');
                date_add($dataDate,date_interval_create_from_date_string("30 days"));
                // 30 days past first reading
                $lastAfterThirtyDate = $dataDate->format('Y-m-d');

                $stmnt->bindParam(":firstDate", $firstDate);
                if ($cptCode == "99457" || $cptCode == "99458") {
                    $stmnt->bindParam(":lastDate", $lastMonthDate);
                }
                else {
                    $stmnt->bindParam(":lastDate", $lastAfterThirtyDate);
                }
                $stmnt->bindParam(":cycleCount", $cycleCountCalc);
                $stmnt->bindParam(":latestDateAdded", $firstDate);
                $stmnt->bindParam(":pid", $cptObj["pid"]);
                $stmnt->bindParam(":meterid", $cptObj["meterid"]);
                $stmnt->bindParam(":cpt", $cptCode);
                try {
                    $stmnt->execute();
                }
                catch(PDOException $e){
                    error_log("ERROR: ----------------------" . $e->__toString());
                }
            }
            // Device + CPT combo already has data counted for it
            else {
                $firstDate = (new DateTime($cptObj["firstDate"]))->format('Y-m-d');
                $lastDate = (new DateTime($cptObj["lastDate"]))->format('Y-m-d');
                $latestDate = (new DateTime($cptObj["latestDateAdded"]))->format(('Y-m-d'));
                $newDate = $dataDate->format('Y-m-d');
                // Case for when new data falls within time range, for '99453' and '98975' this is for the first month ONLY
                if ($cptCode == "99453" || $cptCode == "99454" || $cptCode == "98975" || $cptCode == "98976") {
                    // Ignore (for now) if new data is past $lastDate or if new data is not from a new day (compared to $latestDate)
                    if (($firstDate <= $newDate && $newDate <= $lastDate) && ($newDate > $latestDate)) {
                        $thirdSql = "UPDATE cpt_billing SET status=:status, cycleCount=:cycleCount, latestDateAdded = :latestDateAdded WHERE pid=:pid AND meterid=:meterid AND cpt=:cpt";
                        $stmnt = $this->con->prepare($thirdSql);

                        $newStatus = 0;
                        $newCycleCount = $cptObj["cycleCount"] + 1;
                        // Status is 1/true when requirements for CPT have been met, ie. 16 days of measurements in 30 day span
                        if ($newCycleCount >= 16) {
                            $newStatus = 1;
                        }

                        $stmnt->bindParam(":status", $newStatus);
                        $stmnt->bindParam(":cycleCount", $newCycleCount);
                        $stmnt->bindParam(":latestDateAdded", $newDate);
                        $stmnt->bindParam(":pid", $cptObj["pid"]);
                        $stmnt->bindParam(":meterid", $cptObj["meterid"]);
                        $stmnt->bindParam(":cpt", $cptCode);
                        try {
                            $stmnt->execute();
                        }
                        catch(PDOException $e){
                            error_log("ERROR: ----------------------" . $e->__toString());
                        }
                    }
                }
                // For the 30-day codes when new data's date is past the current cycle date
                if ($cptCode == "99454" || $cptCode == "98976") {
                    if ($newDate > $lastDate) {
                        // Insert new record first to get access to accurate latest fid
                        $newDataSql = "INSERT INTO cpt_billing (pid, meterid, cpt, cost, status, firstDate, lastDate, cycleCount, latestDateAdded, fidLastCycle, fidNextCycle) SELECT pid,meterid,cpt,cost,:status,:firstDate,:lastDate,:cycleCount,:latestDateAdded,:fidLastCycle, fidNextCycle FROM cpt_billing WHERE fid=:fid";
                        $stmntNew = $this->con->prepare($newDataSql);
                        
                        $lastDateObj = new DateTime($cptObj["lastDate"]);
                        // Keep adding 30 days for next cycle that current date fits in
                        while ($newDate > $lastDate) {
                            $lastDate = date_add($lastDateObj, date_interval_create_from_date_string("30 days"))->format('Y-m-d');
                        }
                        // Get first date of new cycle 
                        $firstDate = date_sub($lastDateObj, date_interval_create_from_date_string("30 days"))->format('Y-m-d');
                        // Reset fields for new cycle
                        $newStatus = 0;
                        $newCycleCount = 1;

                        $stmntNew->bindParam(":firstDate", $firstDate);
                        $stmntNew->bindParam(":lastDate", $lastDate);
                        $stmntNew->bindParam(":status", $newStatus);
                        $stmntNew->bindParam(":cycleCount", $newCycleCount);
                        $stmntNew->bindParam(":latestDateAdded", $newDate);
                        $stmntNew->bindParam(":fid", $cptObj["fid"]);
                        $stmntNew->bindParam(":fidLastCycle", $cptObj["fid"]);
                        try {
                            $stmntNew->execute();
                        }
                        catch(PDOException $e){
                            error_log("ERROR: ----------------------" . $e->__toString());
                        }

                        // File this old CPT assignment for recordkeeping, different table so it doesn't get tampered when dates, etc. are updated
                        $oldDataSql = "INSERT INTO cpt_billing_history (fid,pid,meterid,cpt,cost,status,firstDate,lastDate,cycleCount,latestDateAdded,fidNextCycle,fidLastCycle) SELECT fid,pid,meterid,cpt,cost,status,firstDate,lastDate,cycleCount,latestDateAdded,:fidNextCycle,fidLastCycle FROM cpt_billing WHERE fid = :fid";
                        $stmntOld = $this->con->prepare($oldDataSql);
                        $stmntOld->bindParam(":fid", $cptObj["fid"]);
                        $stmntOld->bindParam(":fidNextCycle", $this->con->lastInsertId());

                        // Delete the old record in cpt_billing
                        $deleteSql = "DELETE FROM cpt_billing WHERE fid=:fid";
                        $stmntDel = $this->con->prepare($deleteSql);
                        $stmntDel->bindParam(":fid", $cptObj["fid"]);
                        try {
                            if ($stmntOld->execute())
                                $stmntDel->execute();
                        }
                        catch(PDOException $p){
                            error_log("ERROR: ----------------------" . $p->__toString());
                        }
                    }
                }
            }
        }
    }
    public function AddPatCpt($arr)
    {
        $sql="INSERT INTO cpt_billing (pid, meterid, cpt, cost, status, cycleCount, fidLastCycle, fidNextCycle) VALUES(:pid, :meterid, :cpt, :cost, :status, :cycleCount, :fidLast, :fidNext)";
        $count = 0;
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid", $arr["pid"]);
        $stmnt->bindParam(":meterid", $arr["meterid"]);
        $stmnt->bindParam(":cpt", $arr["cpt"]);
        $stmnt->bindParam(":cost", $arr["cost"]);
        $stmnt->bindParam(":status", $arr["status"]);
        $stmnt->bindParam(":cycleCount", $count);
        $fLast = null;
        $fNext = null;
        $stmnt->bindParam(":fidLast", $fLast);
        $stmnt->bindParam(":fidNext", $fNext);
        try{
            if($stmnt->execute())
            {
                $mar = array("status"=>"Success");
                return json_encode($mar);
            }
        }
        catch(PDOException $e)
        {
                $mar = array("status"=>"Error: ".$e->__toString());
                return json_encode($mar);
        }
    }
    public function AddMeterCpt($arr){
        $sql = "INSERT INTO cpt_meters (pid, meter, meterName, cpt, icd) VALUES (:pid, :meter, :meterName, :cpt, :icd)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid", $arr["pid"]);
        $stmnt->bindParam(":meter", $arr["meter"]);
        $stmnt->bindParam(":meterName", $arr["meterName"]);
        $stmnt->bindParam(":cpt", $arr["cpt"]);
        $stmnt->bindParam(":icd", $arr["icd"]);
        try {
            if($stmnt->execute())
            {
                $mar = array("status"=>"Success");
                return json_encode($mar);
            }
        }
        catch(PDOException $e)
        {
            $mar = array("status"=>"Error: ".$e->__toString());
            return json_encode($mar);
        }
    }
    public function GetMeterCpt($arr){
        $sql = "SELECT cpt, meter, meterName, icd FROM cpt_meters WHERE pid=:pid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid", $arr["pid"]);

        try {
            if ($stmnt->execute()){
                $result = $stmnt->fetchAll();
                return json_encode(array("status"=>"Success", "result"=>$result, "count"=>count($result)));
            }
        }
        catch(PDOException $e){
            $mar = array("status"=>"Error: ".$e->__toString());
            return json_encode($mar);
        }
    }
    public function GetPatCpt2($arr)
    {
        $currentSql="SELECT * FROM cpt_billing2 WHERE pid=:pid";
        $stmnt1 = $this->con->prepare($currentSql);
        $stmnt1->bindParam(":pid", $arr["pid"]);

        $historicalSql = "SELECT * FROM cpt_billing_history2 WHERE pid=:pid";
        $stmnt2 = $this->con->prepare($historicalSql);
        $stmnt2->bindParam(":pid", $arr["pid"]);

        $listSql = "SELECT * FROM cpt_meters WHERE pid=:pid";
        $stmnt3 = $this->con->prepare($listSql);
        $stmnt3->bindParam(":pid", $arr["pid"]);

        try {
            if($stmnt1->execute() && $stmnt2->execute() && $stmnt3->execute())
            {
               $currentResults = $stmnt1->fetchAll();
               $historicalResults = $stmnt2->fetchAll();
               $listResults = $stmnt3->fetchAll();

               $currentCpts = array();
               $historicalCpts = array();
               $listCpts =  array();

               foreach ($currentResults as $curr){
                $currentCpts[$curr["cpt"]] = $curr;
               }
               foreach ($historicalResults as $hist){
                $historicalCpts[$hist["cpt"]][$hist["fid"]] = $hist;
               }
               foreach ($listResults as $cpt){
                $listCpts[$cpt["cpt"]][] = [$cpt["meter"], $cpt["meterName"]];
               }

               $response = array("current"=>$currentCpts, "historical"=>$historicalCpts, "cptList"=>$listCpts);
               return $response;
            }
        }
        catch(PDOException $e)
        {
            $mar = array("status"=>"Error: ".$e->__toString());
            return json_encode($mar);
        }
    }
    public function GetPatICD($pid){
        $sql = "SELECT cpt, icd FROM cpt_meters WHERE pid=:pid";
        $stmnt1 = $this->con->prepare($sql);
        $stmnt1->bindParam(":pid", $pid);
        try {
            if ($stmnt1->execute()){
                $results = $stmnt1->fetchAll();
                return $results;
            }
        }
        catch (PDOException $e){
            $mar = array("status"=>"Error: ".$e->__toString());
            return json_encode($mar);
        }
    }
    public function GetPatCpt($arr, $order = false)
    {
        $currentSql="SELECT * FROM cpt_billing WHERE pid=:pid";
        $stmnt1 = $this->con->prepare($currentSql);
        $stmnt1->bindParam(":pid", $arr["pid"]);

        $historicalSql = "SELECT * FROM cpt_billing_history WHERE pid=:pid";
        $stmnt2 = $this->con->prepare($historicalSql);
        $stmnt2->bindParam(":pid", $arr["pid"]);
        try {
            if($stmnt1->execute() && $stmnt2->execute())
            {
               $currentResults = $stmnt1->fetchAll();
               $historicalResults = $stmnt2->fetchAll();

               $currentCpts = array();
               $historicalCpts = array();
               $cptList = array();
               foreach ($currentResults as $curr){
                if ($order){
                    $currentCpts[$curr["cpt"]][$curr["meterid"]] = $curr;
                    $cptList[$curr["cpt"]][] = $curr["meterid"];
                }
                else {
                    $currentCpts[$curr["meterid"]][$curr["cpt"]] = $curr;
                    $cptList[$curr["meterid"]][] = $curr["cpt"];
                }
               }
               foreach ($historicalResults as $hist){
                if ($order){
                    $historicalCpts[$hist["cpt"]][$hist["meterid"]][$hist["fid"]] = $hist;
                }
                else{
                    $historicalCpts[$hist["meterid"]][$hist["cpt"]][$hist["fid"]] = $hist;
                }
               }

               $response = array("current"=>$currentCpts, "historical"=>$historicalCpts, "cptList"=>$cptList);
               return $response;
            }
        }
        catch(PDOException $e)
        {
            $mar = array("status"=>"Error: ".$e->__toString());
            return json_encode($mar);
        }
    }
    public function NewProvider($pid,$ordnumber,$firstname,$lastname,$address1,$address2,$city,$state,$zip,$tel,$cell,$fax,$npinum,$status,$email)
    {
        $fname = $firstname;
        $lname = $lastname;
        $addr1 =$address1;
        $addr2 = $address2;
        $ncity = $city;
        $nstate = $state;
        $nzip = $zip;
        $ntel =$tel;
        $ncell = $cell;
        $nfax = $fax;
        $nnpi = $npinum;
        $ordnumber = $ordnumber;
        $patientid = $pid;
        $nemail = $email;
        $status="Pending";
        $sql="Insert Into providers (npinumber,ordernumber,patientid,addr1,addr2,city,state,postalcode,tel,cell,fax,firstname,lastname,status,email) VALUES(:npinum,:ordernum,:pid,:addr1,:addr2,:city,:state,:zip,:tel,:cell,:fax,:fname,:lname,:stat,:email)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":npinum",$nnpi);
        $stmnt->bindParam(":ordernum",$ordnumber);
        $stmnt->bindParam(":pid",$patientid);
        $stmnt->bindParam(":addr1",$addr1);
        $stmnt->bindParam(":addr2",$addr2);
        $stmnt->bindParam(":city",$ncity);
        $stmnt->bindParam(":state",$nstate);
        $stmnt->bindParam(":zip",$nzip);
        $stmnt->bindParam(":tel",$ntel);
        $stmnt->bindParam(":cell",$ncell);
        $stmnt->bindParam(":fax",$nfax);
        $stmnt->bindParam(":fname",$fname);
        $stmnt->bindParam(":lname",$lname);
        $stmnt->bindParam(":stat",$status);
        $stmnt->bindParam(":email",$nemail);
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
    public function GetNeb($pid)
    {
        $sql = "SELECT MDateTimeUTC,MValue1,MDeviceID FROM foradata WHERE MType='32' AND patientId=:pid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid", $pid);
        try {
            if ($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                // $msg = array("nebData"=>$records);
                // return $msg;
                return json_encode($records);
            }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function GetProviderbyNPI($npiinfo)
    {
       // var_dump("made it"." ".$npiinfo);
         $pid = $npiinfo;

         $sql="Select * FROM providers WHERE npinumber=:npinum";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":npinum",$pid);
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
               // var_dump($records);
                $msg = array("provider"=>$records);
                return $msg;
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }

    }
    
    public function DeleteGroupByAcccount($account,$provowner,$group)
    {
        $sql="DELETE FROM providers_group WHERE accountnumber=:accnt AND provowner_name=:prvowner AND groupname=:gpname";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$account);
        $stmnt->bindParam(":prvowner",$provowner);
        $stmnt->bindParam(":gpname",$group);
        try{
            if($stmnt->execute())
            {
               $msgar = array("status"=>"200","message"=>"Deleted");
               return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $mar = array("status"=>"740-sqlerror","message"=>$e->__toString());
            return $mar;
        }
    }
    public function GetGroupByAccount($account,$provowner,$grptype)
    {
        $sql="SELECT * FROM providers_group WHERE accountnumber=:accnt AND provowner_name=:prvowner";
        if ($grptype != ""){
            $sql = $sql . " AND group_type=:grptype";
        }
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$account);
        $stmnt->bindParam(":prvowner",$provowner);
        if ($grptype != ""){
            $stmnt->bindParam(":grptype",$grptype);
        }
        try{
            if($stmnt->execute())
            {
                $results = $stmnt->fetchAll();
                if(is_array($results))
                {
                    //lets make this an associative array that has already pre-filter the information in a way that works more effectively with vue
                    $groupnamear[] = array();
                    $grname ="";
                    $provassocar[] = array();
                    $i=0;
                    foreach($results as $r)
                    {
                        //set group name 
                        if($grname !=$r["groupname"] && $r["groupname"])
                        {

                            $grname =$r["groupname"];
                           $groupnamear[] = array("groupname"=>$r["groupname"]);
                        }
                        
                        
                    }
                    if(!empty(array_filter($groupnamear)))
                    {
                        //loop throught to get the providers informaiton thats assocaited with the group 
                        $count = count($results);
                        $count2 =count($groupnamear);
                        $i=0;
                        $d=0;
                         $provlistar[] = array();
                        foreach(array_filter($groupnamear) as $n)
                        {
                            
                            //$set group name 
                            $name = $n["groupname"];
                          
                            foreach($results as $r)
                            {

                                $i++;
                                if($name === $r["groupname"])
                                {
                                    $provlistar[] = array("firstname"=>$r["grp_provider_fname"],"lastname"=>$r["grp_provider_lname"],"email"=>$r["grp_provideremail"],"phone"=>$r["phone"],"npi"=>$r["npinumber"]);
                                }
                             
                              if($i==$count)
                              {
                                //done looping and now lets define and set our final array
                                $provassocar[] = array("groupname"=>$name,"providerslist"=>array_filter($provlistar));
                                //reseting the povlist and offset counter
                                $provlistar = array();
                                $i=0;
                                break ;
                                
                              }
                              
                            }
                        }
                    }
                    
                     return array_filter($provassocar);
                    
                    
                }
            }
        }
        catch(PDOException $e)
        {
            $mar = array("status"=>"740-sqlerror","message"=>$e->__toString());
            return $mar;
        }
    }
    public function DeleteGroupbyName($grpname)
    {
        $sql="DELETE FROM providers_group WHERE groupname=:grpname";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":grpname",$grpname);
        try{

             if($stmnt->execute())
             {
                $records = "Deleted";
                $msgar = array("status"=>"200","message"=>"Deleted");
                return $msgar;

             }
        }
        catch(PDOException $e)
        {
            $msgar = array("status"=>"741-sql","message"=>$e->__toString());

            return $msgar;
        }
    }
    /*---------------------------------------------
    Account Information Method
    @accountNumber 
    @uniqueNine
    ----------------------------------------------*/
    public function GetAccountInformaton($accnt,$unine)
    {
        $sql="SELECT accounts.accountnumber, organization,account_role,firstname,lastname,user_license,account_user.unique_ID,account_user.username,account,account_user.user_Fname,account_user.user_Lname,
        account_user.user_phone, account_user.user_email FROM accounts INNER JOIN account_user ON account_user.accountnumber = accounts.accountnumber  WHERE accounts.accountnumber=:accntNumber AND account_user.unique_ID=:unine";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accntNumber",$accnt);
        $stmnt->bindParam(":unine",$unine);
        try{
            if($stmnt->execute())
            {
                //successful
                $records = $stmnt->fetchAll();
                $msgar = array("message"=>"Successfull","records"=>$records);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msg = $e->__toString();
            $msgar = array("message"=>$msg,"error"=>"700-sql");
            return $msgar;
        }
    }
    public function InsertNurseSettings($accountnumber,$checkhowoften,$notifyexpdt,$Communicationtypes,$notifytypes,$lastchecked)
    {
        
        $sql="INSERT INTO `nurses_settings` (`accountnumber`,`notificationtype`,`checkfrequency`,`checkexpirationdt`,`typeofcomfeat`,`lastchecked`) VALUES(:accnum,:ntype,:howoften,:expdt,:comtype,:lstchk)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnum",$accountnumber);
        $stmnt->bindParam(":ntype",$notifytypes);
        $stmnt->bindParam(":howoften",$checkhowoften);
        $stmnt->bindParam(":expdt",$notifyexpdt);
        $stmnt->bindParam(":comtype",$Communicationtypes);
        $stmnt->bindParam(":lstchk",$lastchecked);
        try{
            if($stmnt->execute())
            {
                $msg ="Inserted";
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>"700-sql","message"=>$e->__toString());
            return $msg;
        }
    }
    public function GetNurseSettings($accountnumber)
    {
        $sql="SELECT * FROM`nurses_settings` WHERE accountnumber=:accnum";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnum",$accountnumber);
       
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                $msg = array("message"=>"200","records"=>$records);
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>"700-sql","message"=>$e->__toString());
            return $msg;
        }
    }
    public function GetIndividualAid($accountnumber,$certificatenumber)
    {
        
        $sql="SELECT * FROM nurses WHERE acountnumber=:accntnum AND `caregivetype`='Home Health Aid' AND certificatenumber=:licens";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accntnum",$accountnumber);
        $stmnt->bindParam(":licens",$certificatenumber);
        try{
            if($stmnt->execute())
            {
                //execution was good now fetch the data
                $records = $stmnt->fetchAll();
                $msg = array("status"=>"200","records"=>$records);
                return $msg;
            }
        }
        catch(PDOException $e){
            $msg = $e->__toString();
            return $msg;
        }
    }
    public function GetIndividualNurse($accountnumber,$licensenumber)
    {
        
        $sql="SELECT * FROM nurses WHERE acountnumber=:accntnum AND licensenum=:licens";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accntnum",$accountnumber);
        $stmnt->bindParam(":licens",$licensenumber);
        try{
            if($stmnt->execute())
            {
                //execution was good now fetch the data
                $records = $stmnt->fetchAll();
                $msg = array("status"=>"200","records"=>$records);
                return $msg;
            }
        }
        catch(PDOException $e){
            $msg = $e->__toString();
            return $msg;
        }
    }
    public function QueryNursesLastChecked($accountnumber)
    {
        $sqlchk ="Select * FROM nurses WHERE acountnumber=:accntnum";
        $stmnt2 = $this->con->prepare($sqlchk);
        $stmnt2->bindParam(":accntnum",$accountnumber);
        try{
            if($stmnt2->execute())
            {
                $getRec = $stmnt2->fetchAll();
                if(is_array($getRec) && !empty($getRec))
                {
                    return $getRec;
                }
            }
        }
        catch(PDOException $e)
        {
           $msg = array("msg"=>$e->__toString());
            return $msg;
        }
    }
    public function InsertNurse($accountnumber,$extern,$firstname,$lastname,$username,$LicenseNumber,
    $LicenseType,$NcsbnId,$Email,$primephone,$secphone,$Address1,$Address2,$City,$State,$Zip,$LastFourSSN,
	$BirthYear,$HospitalPracticeSetting,$NotificationsEnabled,$RemindersEnabled,$LocationList,$activeStatus,$checkstatus,$licenseExp,
    $insuranceCompany,$insuranceStart,$insuranceEnd,$perIncidentAmnt,$annualAggAmnt)
    {
        
        $sqlchk ="Select * FROM nurses WHERE licensenum=:licensnum";
        $stmnt2 = $this->con->prepare($sqlchk);
        $stmnt2->bindParam(":licensnum",$LicenseNumber);
        try{

            if($stmnt2->execute())
            {
                $record = $stmnt2->fetchAll();
                if(count($record) >=1)
                {
                    $message = array("message"=>"Record Exist");
                    return $message;
                }
                else{
                    $sql="Insert Into nurses (`acountnumber`,`providerID`,`username`,`firstname`,`lastname`,`licensenum`,`licensetype`,`email`,`primaryphone`,`secondaryphone`,`addr1`,`addr2`,`ncsbnid`,`city`,`state`,`zip`,`socialnum`,`dob`,`hospsetting`,`notficationEnabled`,`reminderEnabled`,`locationlist`,`activestatus`,`checkstatus`,`licenseexpdate`,`insuranceCompany`,`insuranceStart`,`insuranceEnd`,`perIncidentAmnt`,`annualAggAmnt`) VALUES (:accnum,:extern,:username,:fname,:lname,:licensnm,:licenstype,:email,:primephone,:secphone,:addr1,:addr2,:nsbid,:city,:state,:zip,:social,:dob,:hospitalsetting,:notificatEnabled,:reminderEnabled,:locationlist,:activestatus,:checkstatus,:licenseExp,:insuranceCompany,:insuranceStart,:insuranceEnd,:perIncidentAmnt,:annualAggAmnt)";
                    $stmnt = $this->con->prepare($sql);
                    $stmnt->bindParam(":accnum",$accountnumber);
                    $stmnt->bindParam(":extern", $extern);
                    $stmnt->bindParam(":username", $username);
                    $stmnt->bindParam(":fname",$firstname);
                    $stmnt->bindParam(":lname",$lastname);
                    $stmnt->bindParam(":licensnm",$LicenseNumber);
                    $stmnt->bindParam(":licenstype",$LicenseType);
                    $stmnt->bindParam(":email",$Email);
                    $stmnt->bindParam(":primephone",$primephone);
                    $stmnt->bindParam(":secphone",$secphone);
                    $stmnt->bindParam(":addr1",$Address1);
                    $stmnt->bindParam(":addr2",$Address2);
                    $stmnt->bindParam(":nsbid",$NcsbnId);
                    $stmnt->bindParam(":city",$City);
                    $stmnt->bindParam(":state",$State);
                    $stmnt->bindParam(":zip",$Zip);
                    $stmnt->bindParam(":social",$LastFourSSN);
                    $stmnt->bindParam(":dob",$BirthYear);
                    $stmnt->bindParam(":hospitalsetting",$HospitalPracticeSetting);
                    $stmnt->bindParam(":notificatEnabled",$NotificationsEnabled);
                    $stmnt->bindParam(":reminderEnabled",$RemindersEnabled);
                    $stmnt->bindParam(":locationlist",$LocationList);
                    $stmnt->bindParam(":activestatus",$activeStatus);
                    $stmnt->bindParam(":checkstatus",$checkstatus);
                    $stmnt->bindParam(":licenseExp", $licenseExp);
                    $stmnt->bindParam(":insuranceCompany",$insuranceCompany);
                    $stmnt->bindParam(":insuranceStart",$insuranceStart);
                    $stmnt->bindParam(":insuranceEnd",$insuranceEnd);
                    $stmnt->bindParam(":perIncidentAmnt",$perIncidentAmnt);
                    $stmnt->bindParam(":annualAggAmnt",$annualAggAmnt);
                    try{
            
                        if($stmnt->execute())
                    {
            
                        //now send email 
                        
                        $result ="Inserted";
                        $msgar = array("message"=>$result);
                        return $msgar;
                            
                    }
                    }catch(PDOException $e)
                    {
                        $error = $e->__toString();
                        return $error;
                    }
                }
            }
        }catch(PDOException $e)
        {
            $error = $e->__toString();
            return $error;
        }
       
       
    }
    public function RemoveInternalNurse($accountnum,$licensenum)
    {
        $sql="DELETE FROM nurses WHERE `acountnumber`=:accnum AND `licensenum`=:licensnm";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnum",$accountnum);
        $stmnt->bindParam(":licensnm",$licensenum);
        try{

            if($stmnt->execute())
            {
                $msg = array("status"=>"200","message"=>"Deleted");
                return $msg;
            }

        }catch(PDOException $e)
        {
            $msg = array("status"=>"700-sql","message"=>$e->__toString());
            return $msg;
        }
    }
    public function UpdateLicenseCheckDate($lastchecked,$ndate,$licensenumber,$activestatus)
    {
        $sql="UPDATE nurses SET licenseexpdate=:expdt,checkstatus=:checkstatus,activestatus=:actstatus WHERE licensenum=:licensnm";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":expdt",$ndate);
        $stmnt->bindParam(":checkstatus",$lastchecked);
        $stmnt->bindParam(":licensnm",$licensenumber);
        $stmnt->bindParam(":actstatus",$activestatus);
        try{
            if($stmnt->execute())
            {
                $msg ="Updated";
                return $msg;
            }
        }catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function UpdateNurseSettings($accountnum,$lastchecked)
    {
        $sql="UPDATE nurses_settings SET lastchecked=:lstchk WHERE accountnumber=:accntnum";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":lstchk",$lastchecked);
        $stmnt->bindParam(":accntnum",$accountnum);
        try{
            if($stmnt->execute())
            {
                $msg ="Updated";
                return $msg;
            }
        }catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function UpdateNurse($accountnumber,$firstname,$lastname,$LicenseNumber,
    $LicenseType,$NcsbnId,$Email,$primephone,$secphone,$Address1,$Address2,$City,$State,$Zip,$LastFourSSN,
	$BirthYear,$HospitalPracticeSetting,$NotificationsEnabled,$RemindersEnabled,$LocationList,$activeStatus,$checkstatus)
    {
        
       
                    $sql="UPDATE nurses SET `acountnumber`=:accnum,`firstname`=:fname,`lastname`=:lname,`licensenum`=:licensnm,`licensetype`=:licenstype,`email`=:email,
                    `primaryphone`=:primephone,`secondaryphone`=:secphone,`addr1`=:addr1,`addr2`=:addr2,`ncsbnid`=:nsbid,`city`=:city,`state`=:states,`zip`=:zip,
                    `socialnum`=:social,`dob`=:dob,`hospsetting`=:hospitalsetting,`notficationEnabled`=:notificatEnabled,`reminderEnabled`=:reminderEnabled,
                    `locationlist`=:locationlist,`activestatus`=:activestatus,`checkstatus`=:checkstatus WHERE `licensenum`=:licensnm";
                    $stmnt = $this->con->prepare($sql);
                    $stmnt->bindParam(":accnum",$accountnumber);
                    $stmnt->bindParam(":fname",$firstname);
                    $stmnt->bindParam(":lname",$lastname);
                    $stmnt->bindParam(":licensnm",$LicenseNumber);
                    $stmnt->bindParam(":licenstype",$LicenseType);
                    $stmnt->bindParam(":email",$Email);
                    $stmnt->bindParam(":primephone",$primephone);
                    $stmnt->bindParam(":secphone",$secphone);
                    $stmnt->bindParam(":addr1",$Address1);
                    $stmnt->bindParam(":addr2",$Address2);
                    $stmnt->bindParam(":nsbid",$NcsbnId);
                    $stmnt->bindParam(":city",$City);
                    $stmnt->bindParam(":states",$State);
                    $stmnt->bindParam(":zip",$Zip);
                    $stmnt->bindParam(":social",$LastFourSSN);
                    $stmnt->bindParam(":dob",$BirthYear);
                    $stmnt->bindParam(":hospitalsetting",$HospitalPracticeSetting);
                    $stmnt->bindParam(":notificatEnabled",$NotificationsEnabled);
                    $stmnt->bindParam(":reminderEnabled",$RemindersEnabled);
                    $stmnt->bindParam(":locationlist",$LocationList);
                    $stmnt->bindParam(":activestatus",$activeStatus);
                    $stmnt->bindParam(":checkstatus",$checkstatus);
                    try{
            
                        if($stmnt->execute())
                    {
            
                        //now send email 
                        
                        $result ="Updated";
                        $msgar = array("message"=>$result);
                        return $msgar;
                            
                    }
                    }catch(PDOException $e)
                    {
                        $error = $e->__toString();
                        return $error;
                    }
            
       
       
       
    }
    public function GetNursAidData($accountNum)
    {
      $sql="SELECT * FROM nurses WHERE acountnumber=:accnt AND `caregivetype`='Home Health Aid'";
      $stmnt = $this->con->prepare($sql);
      $stmnt->bindParam(":accnt",$accountNum);
      //$stmnt->bindParam(":provID",$providerId);
     // $stmnt->bindParam(":groupname",$groupname);
      try{
           if($stmnt->execute())
           {
              //successful 
              $records = $stmnt->fetchAll();
              if(count($records) >0)
              {
                  $mgar = array("status"=>"Success","nurseInfo"=>$records);
                  return $mgar;
              }
             // var_dump($records);
            /* $recar[] = array();
             foreach($records as $r)
             {
              
              $recar[] = array("jurisdiction"=>$r["state"],"licensenumber"=>$r["licensenum"],"licensetype"=>$r["licensetype"],"ncsbnid"=>$r["ncsbnid"]);
             }
             if($recar !="" || !empty($recar))
             {
              return $recar;
             }*/
              //return $records;
           }
      }
      catch(PDOException $e)
      {
          $msg = array("error"=>$e->__toString());
          return $msg;
      }
    }
    public function GetNursData($accountNum)
    {

    //    $npinum = $npi;

         $sql="SELECT * FROM nurses WHERE acountnumber=:accnt";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountNum);
        //$stmnt->bindParam(":provID",$providerId);
       // $stmnt->bindParam(":groupname",$groupname);
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
                if(count($records) >0)
                {
                    $mgar = array("status"=>"Success","nurseInfo"=>$records);
                    return $mgar;
                }
               // var_dump($records);
              /* $recar[] = array();
               foreach($records as $r)
               {
                
                $recar[] = array("jurisdiction"=>$r["state"],"licensenumber"=>$r["licensenum"],"licensetype"=>$r["licensetype"],"ncsbnid"=>$r["ncsbnid"]);
               }
               if($recar !="" || !empty($recar))
               {
                return $recar;
               }*/
                //return $records;
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    //Patient to Provider Assignements 
    public function GetPatientProviderAssignments($accountNum,$providerid)
    {

    //    $npinum = $npi;
        //var_dump($patientid);
         $sql="SELECT *,`assignment_id` as pid FROM providerassignment
         LEFT JOIN patients ON providerassignment.patient_id =patients.psersonalID
          WHERE providerassignment.accountnumber=:accnt AND providerassignment.providerid=:nsid AND providerassignment.assignment_date IS NOT NULL AND providerassignment.discharge_date IS NULL ";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountNum);
        $stmnt->bindParam(":nsid",$providerid);
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
                
                   // $mgar = array("status"=>"Success","nurseInfo"=>$records);
                    //var_dump($records);
                    return $records;
                
               
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function GetPatientNursAssignments($accountNum,$nurseid)
    {

    //    $npinum = $npi;
        //var_dump($patientid);
         $sql="SELECT *,`assignment_id` as pid FROM nurseassignment
         LEFT JOIN patients ON nurseassignment.patient_id =patients.psersonalID
          WHERE nurseassignment.accountnumber=:accnt AND nurseassignment.nurse_id=:nsid AND nurseassignment.assignment_date IS NOT NULL AND nurseassignment.discharge_date IS NULL ";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountNum);
        $stmnt->bindParam(":nsid",$nurseid);
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
                
                   // $mgar = array("status"=>"Success","nurseInfo"=>$records);
                    //var_dump($records);
                    return $records;
                
               
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function GetPatientNursData($accountNum,$patientid)
    {

    //    $npinum = $npi;
        //var_dump($patientid);
         $sql="SELECT * FROM nurseassignment WHERE nurseassignment.accountnumber=:accnt AND nurseassignment.patient_id=:pid AND nurseassignment.assignment_date IS NOT NULL AND nurseassignment.discharge_date IS NULL ";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountNum);
        $stmnt->bindParam(":pid",$patientid);
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
                if(count($records) >0)
                {
                    $mgar = array("status"=>"Success","nurseInfo"=>$records);
                   // var_dump($mgar);
                    return $mgar;
                }
               
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function GetNursDataByPersonalID($nursid)
    {

    
         $sql="SELECT * FROM nurses WHERE providerID=:provid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":provid",$nursid);
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
                if(count($records) >0)
                {
                    $mgar = array("status"=>"Success","nurseInfo"=>$records);
                   // var_dump($mgar);
                    return $mgar;
                }
               
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function GetAccountPatients($accountnumber)
    {

        $sql="SELECT * FROM patients WHERE accountnumber=:accnt ORDER BY lastname ASC";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
                //var_dump($records);
               
                    return $records;
                
               // var_dump($records);
              
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function GetAidAssignmentNursData($accountNum)
    {

    //    $npinum = $npi;
       
         $sql="SELECT * FROM nurses WHERE caregivertype='Home Health Aid' AND acountnumber=:accnt ORDER BY lastname ASC";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountNum);
        
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
                //var_dump($records);
                if(count($records) >0)
                {
                   
                    return $records;
                }
               // var_dump($records);
              
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function GetAssignmentNursData($accountNum)
    {

    //    $npinum = $npi;
       
         $sql="SELECT * FROM nurses WHERE acountnumber=:accnt ORDER BY lastname ASC";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountNum);
        
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
                //var_dump($records);
                if(count($records) >0)
                {
                   
                    return $records;
                }
               // var_dump($records);
              
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    /*---------------------------------------------------------
    Patients Table that will belong to the overarching account
    @`accountnumber`,
     @`psersonalID`, 
     @`first_name`,
     @ `lastname`,
     @ `date_of_birth`, 
     @`gender`, 
     @`contact_number`, 
     @`email`,
     @ `address`, 
     @`emergency_contactname`, 
     @`emergency_contact_number`,
     @ `matial_status`, 
     @`insurance_provider`,
     @ `insurance_policy_number`,
     @ `bloodtype`, 
     @`allegeries`
    -----------------------------------------------------------*/
    public function InsertPatientInfo($accountnumber,$pid,$fname,$lname,$dob,$gender,$contactnum,$email,$address,$emergencycontact,$emergencynumber,$materialstatus,$insuranceprov,$insurancepolicynum,$bloodtype,$allergies,$status)
    {
      $sql="INSERT INTO `patients` (accountnumber`,`personalID`,`first_name`,`lastname`,`date_of_birth`,`gender`,`contact_number`,`email`,`address`,`emergency_contact,`emergency_contact_number`,`matial_status`,`insurance_provider`,`insurance_policy_number`,`bloodtype`,`allegeries`,`accntstatus`)  VALUE(:accnt,:pid,:fname,:lname,:dob,:gender,:contactnum,:email,:addr,:emergcontact,:emergcontactnum,:mstatus,:insprov,:inspolicynum,:bloodtype,:allergies,:accntstatus)";
      $stmnt = $this->con->prepare($sql);
      $stmnt->bindParam(":accnt",$accountnumber);
      $stmnt->bindParam(":pid",$pid);
      $stmnt->bindParam(":fname",$fname);
      $stmnt->bindParam(":lname",$lname);
      $stmnt->bindParam(":dob",$dob);
      $stmnt->bindParam(":gender",$gender);
      $stmnt->bindParam(":contactnum",$contactnum);
      $stmnt->bindParam(":email",$email);
      $stmnt->bindParam(":addr",$address);
      $stmnt->bindParam(":emergcontact",$emergencycontact);
      $stmnt->bindParam(":emergcontactnum",$emergencynumber);
      $stmnt->bindParam(":mstatus",$materialstatus);
      $stmnt->bindParam(":insprov",$insuranceprov);
      $stmnt->bindParam(":inspolicynum",$insurancepolicynum);
      $stmnt->bindParam(":bloodtype",$bloodtype);
      $stmnt->bindParam(":allergies",$allergies);
      $stmnt->bindParam(":accntstatus",$status);
      try{
            if($stmnt->execute())
            {
                $msg = array("message"=>"Inserted","status"=>"200");
                return $msg;
            }
      }
      catch(PDOException $e)
      {
        $msg= array("error"=>"Sql-700","message"=>$e->__toString());
        return $msg;
      }
    }
    /*-------------------------------------------------------
    Update Patient Information in this section 
    --------------------------------------------------------*/
    public function UpdatePatientInfo($accountnumber,$pid,$fname,$lname,$dob,$gender,$contactnum,$email,$address,$emergencycontact,$emergencynumber,$materialstatus,$insuranceprov,$insurancepolicynum,$bloodtype,$allergies,$status)
    {
      $sql="UPDATE `patients` SET `first_name`=:fname,`lastname`=:lname,`date_of_birth`=:dob,`gender`=:gender,`contact_number`=:contactnum,`email`=:email,`address`=:addr,`emergency_contact`=:emergcontact,
      `emergency_contact_number`=:emeergcontactnum,`matial_status`=:mstatus,`insurance_provider`=:insprov,`insurance_policy_number`=:inspolicynum,`bloodtype`=:bloodtype,`allegeries`=:allergies,`accntstatus`=:accntstat  WHERE `accountnumber`=:accnt AND `personalID`=:pid";
      $stmnt = $this->con->prepare($sql);
      $stmnt->bindParam(":accnt",$accountnumber);
      $stmnt->bindParam(":pid",$pid);
      $stmnt->bindParam(":fname",$fname);
      $stmnt->bindParam(":lname",$lname);
      $stmnt->bindParam(":dob",$dob);
      $stmnt->bindParam(":gender",$gender);
      $stmnt->bindParam(":contactnum",$contactnum);
      $stmnt->bindParam(":email",$email);
      $stmnt->bindParam(":addr",$address);
      $stmnt->bindParam(":emergcontact",$emergencycontact);
      $stmnt->bindParam(":emergcontactnum",$emergencynumber);
      $stmnt->bindParam(":mstatus",$materialstatus);
      $stmnt->bindParam(":insprov",$insuranceprov);
      $stmnt->bindParam(":inspolicynum",$insurancepolicynum);
      $stmnt->bindParam(":bloodtype",$bloodtype);
      $stmnt->bindParam(":allergies",$allergies);
      $stmnt->bindParam(":accntstat",$status);
      try{
            if($stmnt->execute())
            {
                $msg = array("message"=>"Inserted","status"=>"200");
                return $msg;
            }
      }
      catch(PDOException $e)
      {
        $msg= array("error"=>"Sql-700","message"=>$e->__toString());
        return $msg;
      }
    }
    /*--------------------------Archive Patient Data----------
    @accountnumber
    @personalID
    @accountstatus 
    ----------------------------------------------------------*/
    public function ArchivePatientInfo($accountnumber,$pid,$status)
    {
      $sql="UPDATE `patients` SET `accntstatus`=:accntstat  WHERE `accountnumber`=:accnt AND `personalID`=:pid";
      $stmnt = $this->con->prepare($sql);
      $stmnt->bindParam(":accnt",$accountnumber);
      $stmnt->bindParam(":pid",$pid);
      $stmnt->bindParam(":accntstat",$status);
      try{
            if($stmnt->execute())
            {
                $msg = array("message"=>"Archived","status"=>"200");
                return $msg;
            }
      }
      catch(PDOException $e)
      {
        $msg= array("error"=>"Sql-700","message"=>$e->__toString());
        return $msg;
      }
    }
    /*------------------------------------------------------
    Get Patient Information From the function in this section 
    @accountnumber
    
    ---------------------------------------------------------*/
    public function GetPatientInfo($accountnumber)
    {
      $sql="SELECT * FROM `patients` WHERE accountnumber=:accnt ORDER BY ASC";
      $stmnt = $this->con->prepare($sql);
      $stmnt->bindParam(":accnt",$accountnumber);
      try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                $msg = array("message"=>"Fetch Data Successfully","status"=>"200","data"=>$records);
                return $msg;
            }
      }
      catch(PDOException $e)
      {
        $msg= array("error"=>"Sql-700","message"=>$e->__toString());
        return $msg;
      }
    }
    /*-----------------------------------------------------------------------
    This Section Will retreivew Nurse Patient assignment Information by Account
    Number
    @accountnumer
    @nurses uniqueID
    -------------------------------------------------------------------------*/
    public function GetNurseAssignmentInformaiton($accountnumber,$unine)
    {
        //var_dump($accountnumber);
        //var_dump($unine);
        $sql="SELECT pmid, providerID,`nurses`.`firstname`, `nurses`.`lastname`, licensenum,licensetype,email,accountnumber FROM `nurses` 
         LEFT JOIN `nurseassignment` ON `nurses`.`providerID` = `nurseassignment`.`nurse_id`
        WHERE `nurseassignment`.accountnumber=:accnt AND `nurseassignment`.patient_id=:pid AND `nurseassignment`.assignment_date IS NOT NULL AND `nurseassignment`.discharge_date IS NULL ORDER BY `lastname`ASC";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        //$stmnt->bindParam(":unine",$unine);
        $stmnt->bindParam(":pid",$unine);
        try{
           if($stmnt->execute())
           {
             $records = $stmnt->fetchAll();
          
            return $records;
             
           }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>"700-sql Error","message"=>$e->__toString());
           // var_dump($msg);
            return $msg;
        }
    }
    public function updatePatientData($accountnumber,$subaccountnumber,$firstname,$lastname,$gender,$address,
    $city,$state,$zip,$emergcontact,$maritalstatus,$dob,$socnum,$phone,$email)
    {
      $sql="Update patients SET first_name=:fname, lastname=:lname,gender=:gender,
      address=:addr,city=:city,state=:state,zip=:zip,emergency_contactname=:emgcnt,matial_status=:mrtstatus,date_of_birth=:dob,socnum=:ssnum,
      contact_number=:cntnumber,email=:email WHERE  accountnumber=:accntnumber AND  psersonalID=:subaccnt";
     
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accntnumber",$accountnumber);
        $stmnt->bindParam(":subaccnt",$subaccountnumber);
        $stmnt->bindParam(":fname",$firstname);
        $stmnt->bindParam(":lname",$lastname);
        $stmnt->bindParam(":gender",$gender);
        $stmnt->bindParam(":addr",$address);
        $stmnt->bindParam(":city",$city);
        $stmnt->bindParam(":state",$state);
        $stmnt->bindParam(":zip",$zip);
        $stmnt->bindParam(":emgcnt",$emergcontact);
        $stmnt->bindParam(":mrtstatus",$maritalstatus);
        $stmnt->bindParam(":dob",$dob);
        $stmnt->bindParam(":ssnum",$socnum);
        $stmnt->bindParam(":cntnumber",$phone);
        $stmnt->bindParam(":email",$email);
        try{
          if($stmnt->execute())
          {
            $msg = array("status"=>"200 Updated","message"=>"Updated");
            return $msg;
          }
      }
      catch(PDOException $e)
      {
        $msg = array("Error"=>"Error Updating PatientInfo", "message"=>$e->__toString());
              return $msg;
      }
    }
    public function GetProvAssignedPatients($accountnumber,$subaccountnumber,$accounttype,$proflicense)
    {
     // var_dump($accounttype);
      //var_dump($proflicense);
      if($accounttype=="Provider")
      {

      
      $sql = "SELECT patients.accountnumber,patients.psersonalID AS patient_id,patients.first_name, patients.lastname AS patients_lname, patients.date_of_birth,patients.socnum,patients.gender,patients.contact_number,
      patients.email,patients.address,patients.city,patients.state,patients.zip,patients.emergency_contactname,patients.emergency_contact_number,
      patients.matial_status,patients.allegeries,patients.accntstatus FROM `patients` 
      LEFT JOIN `providerassignment` ON `patients`.`psersonalID` = `providerassignment`.`patient_id`
      WHERE `providerassignment`.`accountnumber`=:accntnumber AND `providerassignment`.providerid=:license AND `providerassignment`.`discharge_date` IS NULL AND `providerassignment`.`assignment_date` IS NOT NULL";
      }
      if($accounttype=="Registered Nurse")
      {
        $sql = "SELECT patients.accountnumber,patients.psersonalID AS patient_id,patients.first_name, patients.lastname AS patients_lname, patients.date_of_birth,patients.socnum,patients.gender,patients.contact_number,
        patients.email,patients.address,patients.city,patients.state,patients.zip,patients.emergency_contactname,patients.emergency_contact_number,
        patients.matial_status,patients.allegeries,patients.accntstatus FROM `patients` 
      LEFT JOIN `nurseassignment` ON `patients`.`psersonalID` = `nurseassignment`.`patient_id`
      WHERE  `nurseassignment`.`accountnumber`=:accntnumber AND `nurseassignment`.`status`='Active' AND `nurseassignment`.`nurse_id`=:license AND `nurseassignment`.`discharge_date` IS NULL AND `nurseassignment`.`assignment_date` IS NOT NULL";
      }
     // var_dump($sql);
      $stmnt = $this->con->prepare($sql);
      $stmnt->bindParam(":accntnumber", $accountnumber);
     // $stmnt->bindParam(":subaccnt",$subaccountnumber);
      $stmnt->bindParam(":license",$proflicense);
          try {
              if ($stmnt->execute()){
                  $records = $stmnt->fetchAll();
                  $assignedpatient = array("patients"=>$records,"status"=>"200 Successfull","count"=>count($records),"accountType"=>$accounttype,"provLicense"=>$proflicense);
                  return $assignedpatient;
              }
          }
          catch (PDOException $e){
              $msg = array("Error"=>"Error in GetPatientsAssigned", "message"=>$e->__toString());
              return $msg;
          }
      
     
    }
    public function GetPatientsAssigned($nurseInfo){
        $sql = "SELECT patient_id, assignment_date FROM nurseassignment WHERE nurse_id=:nurse_id AND assignment_date IS NOT NULL AND discharge_date IS NULL";
        $stmnt = $this->con->prepare($sql);
        $nurses = $nurseInfo["nurseInfo"];
        $assignments = array();
        foreach ($nurses as $nurse){
            $stmnt->bindParam(":nurse_id", $nurse["providerID"]);
            try {
                if ($stmnt->execute()){
                    $records = $stmnt->fetchAll();
                    $assignments[$nurse["providerID"]] = $records;
                }
            }
            catch (PDOException $e){
                $msg = array("Error"=>"Error in GetPatientsAssigned", "message"=>$e->__toString());
                return $msg;
            }
        }
        $nurseInfo['assignments'] = $assignments;
        return $nurseInfo;
    }
    private function NurseInfoAsssignmentLookUp($accountnumber,$nurseid)
    {
        $sql="SELECT * FROM `nurses` WHERE providerID=:nsid AND acountnumber=:accnt";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":nsid",$nurseid);
        try{
            if($stmnt->execute())
           {
            $records = $stmnt->fetchAll();
           // $msgar = array("records"=>$records);
             return $records;
           }

        }
        catch(PDOException $e)
        {
            $msg = array("error"=>"700-sql Error","message"=>$e->__toString());
            var_dump($msg);
            return $msg;
        }
    }
    private function GetPatientAssignmentByAccntNPatientId($accountnumber,$pid)
    {
      $sqlfetch="SELECT * FROM patients WHERE psersonalId=:pid AND accountnumber=:accnt";
      $stmnt4 = $this->con->prepare($sqlfetch);
     // $existingnurse[] = array();
      $stmnt4->bindParam(":pid",$pid);
      $stmnt4->bindParam(":accnt",$accountnumber);
     
     
      try{
          if($stmnt4->execute())
          {
              $existingpatient = $stmnt4->fetchAll();
             // var_dump($existingnurse);
              return $existingpatient;
          }
      }
      catch(PDOException $e)
      {
          $msg = array("status"=>"Error","msg"=>$e->__toString());
          return $msg;
         // var_dump($msg);
      }
    }
    private function GetNurseAssignmentByAccntNPatientId($accountnumber,$pid,$nurseid)
    {
        $sqlfetch="SELECT * FROM nurseassignment WHERE patient_id=:pid AND nurse_id=:nursid AND accountnumber=:accnt AND assignment_date IS NOT NULL AND discharge_date IS NULL";
        $stmnt4 = $this->con->prepare($sqlfetch);
       // $existingnurse[] = array();
        $stmnt4->bindParam(":pid",$pid);
        $stmnt4->bindParam(":nursid",$nurseid);
        $stmnt4->bindParam(":accnt",$accountnumber);
       
       
        try{
            if($stmnt4->execute())
            {
                $existingnurse = $stmnt4->fetchAll();
               // var_dump($existingnurse);
                return $existingnurse;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("status"=>"Error","msg"=>$e->__toString());
            return $msg;
           // var_dump($msg);
        }
    }
    public function dischargePatient($getproflicense,$accountnumber,$subaccntnumber,$patientid,$patientfname,$patientlname,$accounttype)
    {
       //$dcnow = $this->DischargePatientAssignment($accountnumber,$patientid,$getproflicense,$accounttype);
       $dcnow = $this->DischargeNurseByNurseID($patientid,$accountnumber,$getproflicense,$accounttype);
       return $dcnow;
    }
    private function DischargePatientAssignment($accnt,$patid,$nurseid)
    {
      //needs to be both nurse and patientID to make sure the patient is discharged from the correct Nurse 
        $dischargePatient = $this->GetPatientAssignmentByAccntNPatientId($accnt,$patid);
        $sql3="UPDATE `nurseassignment` SET discharge_date=:disdate,`status`='Non Active' WHERE nurse_id=:nursid AND patient_id=:pid AND accountnumber=:accnt";
        $stmnt3 = $this->con->prepare($sql3);
        $stmnt3->bindParam(":disdate",date('Y-m-d H:i:s'));
        $stmnt3->bindParam(":nursid",$nurseid);
        $stmnt3->bindParam(":accnt",$accnt);
        $stmnt3->bindParam(":pid",$patid);
        try{
            if($stmnt3->execute())
            {
                //this nurse should now be discharged 
             
                $dischargear[] = array($dischargePatient[0]["firs_tname"]." ".$dischargePatient[0]["lastname"]);
                return $dischargear;
               
            }
        }catch(PDOException $e)
        {
            $msg = array("sqlError"=>"700-sql","message"=>$e->__toString());
            //var_dump($msg);
            return $msg;
        }
    }
    private function DischargeNurseAssignment($accnt,$patid,$nurseid)
    {
        $dischargeNurse = $this->GetNurseAssignmentByAccntNPatientId($accnt,$patid,$nurseid);
       // var_dump($dischargeNurse);
        $sql3="UPDATE `nurseassignment` SET discharge_date=:disdate WHERE nurse_id=:nursid AND patient_id=:pid AND accountnumber=:accnt";
        $stmnt3 = $this->con->prepare($sql3);
        $stmnt3->bindParam(":disdate",date('Y-m-d H:i:s'));
        $stmnt3->bindParam(":nursid",$nurseid);
        $stmnt3->bindParam(":accnt",$accnt);
        $stmnt3->bindParam(":pid",$patid);
        try{
            if($stmnt3->execute())
            {
                //this nurse should now be discharged 
             
                $dischargear[] = array($dischargeNurse[0]["firstname"]." ".$dischargeNurse[0]["lastname"]);
                return $dischargear;
               
            }
        }catch(PDOException $e)
        {
            $msg = array("sqlError"=>"700-sql","message"=>$e->__toString());
            //var_dump($msg);
            return $msg;
        }
    }
    public function lookupPatientSearch($account,$subaccnt,$keyword,$filterby,$accounttype,$license)
    {
      
      $sql="";
      //$keyword = '%' . $keyword . '%';
      if($accounttype=="Provider")
      {
        if($filterby!="" && $filterby !="Patient Status Filter")
        {
          switch($filterby)
           {
            case"Active":
              {
               
                $sql="SELECT patients.accountnumber,patients.psersonalID AS patient_id,patients.first_name, patients.lastname AS patients_lname, patients.date_of_birth,patients.socnum,patients.gender,patients.contact_number,
                patients.email,patients.address,patients.city,patients.state,patients.zip,patients.emergency_contactname,patients.emergency_contact_number,
                patients.matial_status,patients.allegeries,providerassignment.status,providerassignment.assignment_date FROM `patients` 
                LEFT JOIN providerassignment ON patients.psersonalID = providerassignment.patient_id 
                WHERE providerassignment.accountnumber=:accnt AND providerassignment.providerid=:npi AND providerassignment.assignment_date IS NOT NULL AND `providerassignment`.`discharge_date` IS NULL AND `providerassignment`.`status`=:flterby AND (patients.first_name LIKE '%$keyword%' OR patients.lastname LIKE '%$keyword%')";
                //var_dump($sql);
                break;
              }
            case"Non-Active":
              {
                //var_dump("Non-Active");
                $sql="SELECT patients.accountnumber,patients.psersonalID AS patient_id,patients.first_name, patients.lastname AS patients_lname, patients.date_of_birth,patients.socnum,patients.gender,patients.contact_number,
                patients.email,patients.address,patients.city,patients.state,patients.zip,patients.emergency_contactname,patients.emergency_contact_number,
                patients.matial_status,patients.allegeries,providerassignment.status,providerassignment.assignment_date FROM `patients` 
                LEFT JOIN providerassignment ON patients.psersonalID = providerassignment.patient_id 
                WHERE providerassignment.accountnumber=:accnt AND providerassignment.providerid=:npi AND providerassignment.discharge_date IS NOT NULL AND `providerassignment`.`status`=:flterby AND (patients.first_name LIKE '%$keyword%' OR patients.lastname LIKE '%$keyword%')";
                
               //var_dump($sql);
                break;
              }
            }
         /* $sql="SELECT patients.accountnumber,patients.psersonalID AS patient_id,patients.first_name, patients.lastname AS patients_lname, patients.date_of_birth,patients.socnum,patients.gender,patients.contact_number,
          patients.email,patients.address,patients.city,patients.state,patients.zip,patients.emergency_contactname,patients.emergency_contact_number,
          patients.matial_status,patients.allegeries,patients.accntstatus,providerassignment.assignment_date FROM `patients` 
          LEFT JOIN providerassignment ON patients.psersonalID = providerassignment.patient_id 
          WHERE providerassignment.accountnumber=:accnt AND providerassignment.providerid=:npi AND providerassignment.assignment_date IS NOT NULL AND providerassignment.discharge_date IS NULL  AND `providerassignment`.`status`=:flterby AND (patients.first_name LIKE :kyword OR patients.lastname LIKE :kyword)";*/
          //continue;//break; 
         
        }
        else{
          $sql="SELECT patients.accountnumber,patients.psersonalID AS patient_id,patients.first_name, patients.lastname AS patients_lname, patients.date_of_birth,patients.socnum,patients.gender,patients.contact_number,
          patients.email,patients.address,patients.city,patients.state,patients.zip,patients.emergency_contactname,patients.emergency_contact_number,
          patients.matial_status,patients.allegeries,providerassignment.status,providerassignment.assignment_date FROM `patients` 
          LEFT JOIN providerassignment ON patients.psersonalID = providerassignment.patient_id 
          WHERE providerassignment.accountnumber=:accnt AND providerassignment.providerid=:npi AND providerassignment.assignment_date IS NOT NULL AND providerassignment.discharge_date IS NULL AND (patients.first_name LIKE '%$keyword%' OR patients.lastname LIKE '%$keyword%')";
          //continue;//break;
        }
          
      }
      if($accounttype=="Registered Nurse")
      {
        
        if($filterby !="" && $filterby !="Patient Status Filter")
        {
           //now run a switch case 
          
           switch($filterby)
           {
            case"Active":
              {
                $sql="SELECT patients.accountnumber,patients.psersonalID AS patient_id,patients.first_name, patients.lastname AS patients_lname, patients.date_of_birth,patients.socnum,patients.gender,patients.contact_number,
                patients.email,patients.address,patients.city,patients.state,patients.zip,patients.emergency_contactname,patients.emergency_contact_number,
                patients.matial_status,patients.allegeries,nurseassignment.status,nurseassignment.assignment_date FROM `patients`
                 LEFT JOIN nurseassignment ON patients.psersonalID = nurseassignment.patient_id 
                 WHERE nurseassignment.accountnumber=:accnt AND nurseassignment.nurse_id=:npi AND nurseassignment.assignment_date IS NOT NULL AND nurseassignment.discharge_date IS NOT NULL AND `nurseassignment`.`status`=:flterby AND (patients.first_name LIKE '%$keyword%' OR patients.lastname LIKE '%$keyword%')";    
                break;
              }
            case"Non-Active":
              {
                
                $sql="SELECT patients.accountnumber,patients.psersonalID AS patient_id,patients.first_name, patients.lastname AS patients_lname, patients.date_of_birth,patients.socnum,patients.gender,patients.contact_number,
                patients.email,patients.address,patients.city,patients.state,patients.zip,patients.emergency_contactname,patients.emergency_contact_number,
                patients.matial_status,patients.allegeries,nurseassignment.status,nurseassignment.assignment_date FROM `patients`
                 LEFT JOIN nurseassignment ON patients.psersonalID = nurseassignment.patient_id 
                 WHERE nurseassignment.accountnumber=:accnt AND patients.accountnumber=:accnt AND nurseassignment.nurse_id=:npi AND `nurseassignment`.`status`=:flterby AND (patients.first_name LIKE '%$keyword%' OR patients.lastname LIKE '%$keyword%')";
              
                break;
              }
           }
          /*$sql="SELECT patients.accountnumber,patients.psersonalID AS patient_id,patients.first_name, patients.lastname AS patients_lname, patients.date_of_birth,patients.socnum,patients.gender,patients.contact_number,
            patients.email,patients.address,patients.city,patients.state,patients.zip,patients.emergency_contactname,patients.emergency_contact_number,
            patients.matial_status,patients.allegeries,patients.accntstatus,nurseassignment.assignment_date FROM `patients`
             LEFT JOIN nurseassignment ON patients.psersonalID = nurseassignment.patient_id 
             WHERE nurseassignment.nurse_id=:npi AND nurseassignment.assignment_date IS NOT NULL AND nurseassignment.discharge_date IS NULL AND `nurseassignment`.`status`=:flterby AND (patients.first_name LIKE '%$keyword%' OR patients.lastname LIKE '%$keyword%')";*/
          
        }
        else{
          $sql="SELECT patients.accountnumber,patients.psersonalID AS patient_id,patients.first_name, patients.lastname AS patients_lname, patients.date_of_birth,patients.socnum,patients.gender,patients.contact_number,
          patients.email,patients.address,patients.city,patients.state,patients.zip,patients.emergency_contactname,patients.emergency_contact_number,
          patients.matial_status,patients.allegeries,nurseassignment.status,nurseassignment.assignment_date FROM `patients`
           LEFT JOIN nurseassignment ON patients.psersonalID = nurseassignment.patient_id 
           WHERE patients.accountnumber=:accnt AND nurseassignment.nurse_id=:npi AND nurseassignment.assignment_date IS NOT NULL AND nurseassignment.discharge_date IS NULL AND (patients.first_name LIKE '%$keyword%' OR patients.lastname LIKE '%$keyword%')";
        
        }
             
         // break;
      }
       //var_dump($sql);
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$account);
        $stmnt->bindParam(":npi",$license);
        //$stmnt->bindParam(":keyword",$keyword);
        if($filterby !="" && $filterby !="Patient Status Filter")
        {
          $stmnt->bindParam(":flterby",$filterby);
        }
        //var_dump($stmnt->debugDumpParams());
       // $stmnt->bindParam(":accnt",$acntnumber)
            try{
              if($stmnt->execute())
              {
  
                $records = $stmnt->fetchAll();
                //var_dump($records);
                $pendorders = array("status"=>"200 Successfull","records"=>$records,"accounttype"=>$accounttype, "status"=>$filterby);
                return $pendorders;
              }
          }
          catch(PDOException $e)
          {
            $msg = array("error"=>"700-sql","message"=>$e->__toString());
            return $msg;
          }
      

    }
    public function DischargeNurseByNurseID($pid,$accountnumber,$nurseid,$accounttype)
    {
              //lets discharge now and bind new nurseid with the assignedFilter['nurse_id']
              //we need to determine if the accounttype to determin which assignment table to call
              $dischargePatient = $this->GetPatientAssignmentByAccntNPatientId($accountnumber,$pid);
              if($accounttype=="Registered Nurse")
              {

              
                $sql3="UPDATE `nurseassignment` SET discharge_date=:disdate, `nurseassignment`.`status`='Non-Active' WHERE nurse_id=:nursid AND accountnumber=:accnt AND patient_id=:pid ";
              }
              if($accounttype=="Provider")
              {
                $sql3="UPDATE `providerassignment` SET discharge_date=:disdate, `providerassignment`.`status`='Non-Active' WHERE providerid=:nursid AND accountnumber=:accnt AND patient_id=:pid ";
              }
              $stmnt3 = $this->con->prepare($sql3);
              $stmnt3->bindParam(":disdate",date('Y-m-d H:i:s'));
              $stmnt3->bindParam(":nursid",$nurseid);
              $stmnt3->bindParam(":accnt",$accountnumber);
              $stmnt3->bindParam(":pid",$pid);
              try{
                  if($stmnt3->execute())
                  {
                      //this nurse should now be discharged 
                      //var_dump("Updated");
                    // $msg = array("message"=>"Nurse Discharged Successfully");
                     $dischargear[] = array($dischargePatient[0]["firstname"]." ".$dischargePatient[0]["lastname"]);
                     return $dischargear;
                    // return $msg;
                     
                  }
              }catch(PDOException $e)
              {
                  $msg = array("sqlError"=>"700-sql","message"=>$e->__toString());
                 // var_dump($msg);
                  return $msg;
              } 
    }
    public function AssignNurseToPatient($pid,$accountnumber,$nursar)
    {
        
      
        if(/*!empty($nursar) &&*/ is_array($nursar))
        {
            //lets
             $dischargear = array();
             $nrassignedar = array();
             $foundcurrentNurse= array();
            //lets pull a list of nurses that may already be assigned to the patient 
            $sqlfetch="SELECT * FROM nurseassignment WHERE patient_id=:pid AND accountnumber=:accnt AND assignment_date IS NOT NULL AND discharge_date IS NULL";
            $stmnt4 = $this->con->prepare($sqlfetch);
            $existingnurse[] = array();
            $stmnt4->bindParam(":pid",$pid);
            $stmnt4->bindParam(":accnt",$accountnumber);
           
            try{
                if($stmnt4->execute())
                {
                    $existingnurse = $stmnt4->fetchAll();
                   
                         $assignedFilter = array_filter($existingnurse);
                         $picklistar[] = array();
                         $nurselistar[] =array();
                         //var_dump($assignedFilter);
                         if(is_array($assignedFilter) && !empty($assignedFilter) && !empty($nursar) )
                         {
                             /*Lets get explode the Piclist list of nurses and put them into a multidemensional array
                             * Running thise because we want to check the nurse already assigned (assignedFilter) against the
                             * PiclistAr
                             */
                            
                            
                              foreach($nursar as $nnr)
                             {
                                 $getid = explode("-",$nnr);
                                 $nurseid = $getid[0];
                                 $xxplodename = explode(" ",$getid[1]);
                                 $fname = $xxplodename[0]." ".$xxplodename[1];//fierstname and middle name 
                                 if(count($xxplodename)==3)
                                 {
                                  //there must be an extr space somewhere and we need to account for it so the last name isn't null
                                  $lname = $xxplodename[2];
                                 }
                                 else{
                                  
                                  $lname  = $xxplodename[1];
                                 }
                                 //$lname = $xxplodename[2];
                                 array_push($picklistar,$nurseid);
                                 
                                $nurselistar[] = array("firstname"=>$fname,"lastname"=>$lname,"nurseid"=>$nurseid,"accountnumber"=>$accountnumber);
                               
                             }
                               $nwpicklistar = array_filter($picklistar); //filter out the empty values 
                              
                             /*now lets compare this array to the one that was passed over and if a nurse is missing from 
                             the array we just pulled, we are going to discharge that nurse with todays date 
                             @neeed to loop through the parameter array that was passed over in order to evaluate it
                             */
                             //$foundcurrentNurse= array();
                             foreach($assignedFilter as $f)
                             {
                               
                                 if(in_array($f["nurse_id"],$nwpicklistar))
                                 {
                                     //if its there do nothing these nurse should remain assigned to the client
                                    // var_dump("already assigned"." ".$f["nurse_id"]);
                                    //Pushing already Assinged Nurses into the foundcurrentNurse Array
                                    array_push($foundcurrentNurse,$f["nurse_id"]);
                                 }
                                 else{
                                    
                                     //lets discharge now and bind new nurseid with the assignedFilter['nurse_id']
                                    $sql3="UPDATE `nurseassignment` SET discharge_date=:disdate WHERE nurse_id=:nursid AND accountnumber=:accnt ";
                                     $stmnt3 = $this->con->prepare($sql3);
                                     $stmnt3->bindParam(":disdate",date('Y-m-d H:i:s'));
                                     $stmnt3->bindParam(":nursid",$f["nurse_id"]);
                                     $stmnt3->bindParam(":accnt",$accountnumber);
                                     try{
                                         if($stmnt3->execute())
                                         {
                                             //this nurse should now be discharged 
                                             $dischargear[] = array($f["firstname"]." ".$f["lastname"], $f["nurse_id"]);
                                            
                                         }
                                     }catch(PDOException $e)
                                     {
                                         $msg = array("sqlError"=>"700-sql","message"=>$e->__toString());
                                        // var_dump($msg);
                                         return $msg;
                                     } 
                                 }
                                
                             }
                             //now loop through the nurselistar and insert the new nurses that hasn't already been assigned 
                             foreach(array_filter($nurselistar) as $n)
                             {
                               
                                $fname = $n["firstname"];
                                $lname = $n["lastname"];
                                $nursid =$n["nurseid"];
                                $accountnumber = $n["accountnumber"];
                                $dateassigned = date("Y-m-d H:i:s");
                                //make sure we don't add the nurses that have already been assigned
                                if(!in_array($nursid,array_filter($foundcurrentNurse)))
                                {

                                
                                    $sql="INSERT INTO `nurseassignment`( `nurse_id`, `patient_id`, `assignment_date`, `accountnumber`,`firstname`,`lastname`,`status`) VALUES 
                                    (:nursid,:pid,:assignDT,:accnt,:fname,:lname,'Active')";
                                    $stmnt = $this->con->prepare($sql);
                                    $stmnt->bindParam(":accnt",$accountnumber);
                                    $stmnt->bindParam(":nursid",$nursid);
                                    $stmnt->bindParam(":pid",$pid);
                                    $stmnt->bindParam(":assignDT",$dateassigned);
                                    $stmnt->bindParam(":fname",$fname);
                                    $stmnt->bindParam(":lname",$lname);
                                    try{
                                            if($stmnt->execute())
                                            {
                                                $nrassignedar[]=array($fname." ".$lname, $nursid);
                                            
                                                //$nrassignedar[]=array("nurseid"=>$nursid,"assignmentDate"=>$dateassigned,"message"=>"Insertd Successfully");
                                            }
                                    }
                                    catch(PDOEXception $e)
                                    {
                                        $msg = array("error"=>$e->__toString());
                                        return $msg;
                                    }
                                }
                                
                             }
                            
                         
                        }
                        else{
                            //its empty now insert nurse ar IF nursar is not empty
                            if(empty($nursar) && !empty($existingnurse))
                            {
                                // if nursar is empty and existing nurse is there then let disccharge all nurses if they are assigned
                                // var_dump("nursesAr is empty and exsting nurse not empty");
                                foreach($existingnurse as $ds)
                                {
                                   // var_dump($ds["nurse_id"]);
                                    $move = $this->DischargeNurseAssignment($accountnumber,$pid,$ds["nurse_id"]);
                                   //var_dump($move);
                                    if(is_array($move))
                                    {
                                       $dischargear[] =array($move[0][0], $ds["nurse_id"]);//$move;
                                    }
                                }
                                
                            }
                            else{
                               // var_dump("Exsting Nurse must be empty and nursar isn't emplty");
                               // var_dump($nursar);
                                //the foreach loop for the nursar to assign nurses should go here 
                                foreach($nursar as $r)
                                {
                                
                                     $getpmid = explode("-",$r);
                                     $nursid = $getpmid[0];
                                     $xplodename = explode(" ",$getpmid[1]);
                                     $fname = $xplodename[0]." ".$xplodename[1];//fierstname and middle name 
                                     $lname = $xplodename[2];
                                     //check to see if this nurse is already added 
                                     
                                     $sql1 ="SELECT * FROM `nurseassignment` WHERE nurse_id=:nursid AND patient_id=:pid AND accountnumber=:accnt AND assignment_date IS NOT NULL AND discharge_date IS NULL";
                                     $stmnt2 = $this->con->prepare($sql1);
                                     $stmnt2->bindParam(":nursid",$nursid);
                                     $stmnt2->bindParam(":accnt",$accountnumber);
                                     $stmnt2->bindParam(":pid",$pid);
                                    
                                     try{
                                        if($stmnt2->execute())
                                        {
                                             $isassgined = $stmnt2->fetchAll();
                                             
                                             if(count($isassgined)>0)
                                             {
                                                //record is already assigned to this patient
                                               
                                             }
                                             else{
                                                //record is ready to be inserted
                                                $dateassigned = date("Y-m-d H:i:s");
                                                $sql="INSERT INTO `nurseassignment`( `nurse_id`, `patient_id`, `assignment_date`, `accountnumber`,`firstname`,`lastname`) VALUES 
                                                (:nursid,:pid,:assignDT,:accnt,:fname,:lname)";
                                                $stmnt = $this->con->prepare($sql);
                                                $stmnt->bindParam(":accnt",$accountnumber);
                                                $stmnt->bindParam(":nursid",$nursid);
                                                $stmnt->bindParam(":pid",$pid);
                                                $stmnt->bindParam(":assignDT",$dateassigned);
                                                $stmnt->bindParam(":fname",$fname);
                                                $stmnt->bindParam(":lname",$lname);
                                                if($stmnt->execute())
                                                {
                                                    $nrassignedar[]=array($fname." ".$lname, $nursid);
                                                  
                                                    //$nrassignedar[]=array("nurseid"=>$nursid,"assignmentDate"=>$dateassigned,"message"=>"Insertd Successfully");
                                                }
                                             }
                                        }
                                        
                                     }
                                     catch(PDOException $e)
                                     {
                                       $msg =array("erro"=>"SQL-700","message"=>$e->__toString());
                                     
                                       return $msg;
                                     }
                                    
                                     
                                }//end last 4 each
                            }
                           
                        }
                   // foreach to insert Nurses go here 
                   
                  
                   
                   
                }
            }
            catch(PDOException $e)
            {
                $mst = array($e->__toString());
              
                return $mst;
            }
           
           
            //lets check to see if the nurseassginedar is not empy and if its not, that means nurse was assigend successfully
            if(!empty($dischargear) || !empty($nrassignedar))
            {
                $payload = array("message"=>"Nurse Assigned Successfully","nurseInfo"=>$nrassignedar,"dischargeInfo"=>$dischargear);
                return $payload;
            }
            elseif(empty($discharge) && empty($nrassignedar) && !empty($foundcurrentNurse))
            {
                $payload = array("message"=>"Nurse(s) Already Assigned","nurseInfo"=>$foundcurrentNurse);
                return $payload;
            }
            else{
                $payload = array("message"=>"Nurse Not Assigned Successfully","status"=>"400","code"=>$dischargear);
                return $payload;
            }
           
            
        }
        else{
            $msg =array("status"=>"Not Successfull","error"=>"Nurses data doesnt exist","data"=>$nursar);
            return $msg;
        }
       
    }
    //AssignPatientstoPRoviders
    public function AssignPatientToProvider($pid,$accountnumber,$patientar)
    {
             if(!empty($patientar) && is_array($patientar))
        {
         
            //lets
           // var_dump($patientar);
           // var_dump($sid);
            //var_dump($accountnumber);
            //var_dump($pid);
             $dischargear = array();
             $ptassignedar = array();
             $foundcurrentPatient= array();
            //lets pull a list of [patients] that may already be assigned to the nurse 
            $sqlfetch="SELECT * FROM providerassignment WHERE providerid=:pid AND accountnumber=:accnt AND assignment_date IS NOT NULL AND discharge_date IS NULL";
            $stmnt4 = $this->con->prepare($sqlfetch);
            $existingpatients[] = array();
            $stmnt4->bindParam(":pid",$pid);
            $stmnt4->bindParam(":accnt",$accountnumber);
           
            try{
                if($stmnt4->execute())
                {
                    $existingpatients = $stmnt4->fetchAll();
                   // var_dump("Gettng possible assignments already");
                   //var_dump($existingpatients);
                         $assignedFilter = array_filter($existingpatients);
                         $picklistar[] = array();
                         $patientlistar[] =array();
                         //var_dump($assignedFilter);
                         if(is_array($assignedFilter) && !empty($assignedFilter) && !empty($patientar) )
                         {
                             /*Lets get explode the Piclist list of patients and put them into a multidemensional array
                             * Running thise because we want to check the patients that are already assigned (assignedFilter) against the
                             * PiclistAr
                             */
                            
                            
                              foreach($patientar as $nnr)
                             {
                                 $getid = explode("-",$nnr);
                               // var_dump($getid);
                                 $patientid = $getid[0];
                               
                                 $xxplodename = explode(" ",$getid[1]);
                                // var_dump($xxplodename);
                                 $fname = $xxplodename[0]." ".$xxplodename[1];//fierstname and middle name 
                                 if(count($xxplodename)==3)
                                 {
                                  //there must be an extr space somewhere and we need to account for it so the last name isn't null
                                  $lname = $xxplodename[2];
                                 }
                                 else{
                                  
                                  $lname  = $xxplodename[1];
                                 }
                                
                                 //$lname = $xxplodename[2];
                                 array_push($picklistar,$patientid);
                                $patientlistar[] = array("firstname"=>$fname,"lastname"=>$lname,"patientid"=>$patientid,"accountnumber"=>$accountnumber);
                               
                             }
                               $nwpicklistar = array_filter($picklistar); //filter out the empty values and new array should have only pid
                               //var_dump($nwpicklistar);
                               //var_dump(array_filter($patientlistar));
                              
                             /*now lets compare this array to the one that was passed over and if a patient is missing from 
                             the array we just pulled, we are going to discharge that patient with todays date 
                             @neeed to loop through the parameter array that was passed over in order to evaluate it
                             */
                             //$foundcurrentNurse= array();
                             foreach($assignedFilter as $f)
                             {
                               
                                 if(in_array($f["patient_id"],$nwpicklistar))
                                 {
                                     //if its there do nothing these nurse should remain assigned to the client
                                    // var_dump("already assigned"." ".$f["nurse_id"]);
                                    //Pushing already Assinged Nurses into the foundcurrentNurse Array
                                    array_push($foundcurrentPatient,$f["patient_id"]);
                                 }
                                 else{
                                    
                                     //lets discharge now and bind new nurseid with the assignedFilter['nurse_id']
                                    $sql3="UPDATE `providerassignment` SET discharge_date=:disdate WHERE patient_id=:patid AND accountnumber=:accnt ";
                                     $stmnt3 = $this->con->prepare($sql3);
                                     $stmnt3->bindParam(":disdate",date('Y-m-d H:i:s'));
                                     $stmnt3->bindParam(":patid",$f["patient_id"]);
                                     $stmnt3->bindParam(":accnt",$accountnumber);
                                     try{
                                         if($stmnt3->execute())
                                         {
                                             //this nurse should now be discharged 
                                             $dischargear[] = array($f["first_name"]." ".$f["lastname"], $f["patient_id"]);
                                            
                                         }
                                     }catch(PDOException $e)
                                     {
                                         $msg = array("sqlError"=>"700-sql","message"=>$e->__toString());
                                        // var_dump($msg);
                                         return $msg;
                                     } 
                                 }
                                
                             }
                             //now loop through the patientlistar and insert the new patients that hasn't already been assigned 
                             foreach(array_filter($patientlistar) as $n)
                             {
                               
                                $fname = $n["firstname"];
                                $lname = $n["lastname"];
                                $patientid =$n["patientid"];
                                $accountnumber = $n["accountnumber"];
                                $dateassigned = date("Y-m-d H:i:s");
                                //make sure we don't add the patients that have already been assigned
                                if(!in_array($patientid,array_filter($foundcurrentPatient)))
                                {

                                
                                    $sql="INSERT INTO `providerassignment`( `providerid`, `patient_id`, `assignment_date`, `accountnumber`,`firstname`,`lastname`) VALUES 
                                    (:provid,:pid,:assignDT,:accnt,:fname,:lname)";
                                    $stmnt = $this->con->prepare($sql);
                                    $stmnt->bindParam(":accnt",$accountnumber);
                                    $stmnt->bindParam(":provid",$pid);
                                    $stmnt->bindParam(":pid",$patientid);
                                    $stmnt->bindParam(":assignDT",$dateassigned);
                                    $stmnt->bindParam(":fname",$fname);
                                    $stmnt->bindParam(":lname",$lname);
                                    try{
                                            if($stmnt->execute())
                                            {
                                                $ptassignedar[]=array($fname." ".$lname, $patientid);
                                            
                                                //$nrassignedar[]=array("nurseid"=>$nursid,"assignmentDate"=>$dateassigned,"message"=>"Insertd Successfully");
                                            }
                                    }
                                    catch(PDOEXception $e)
                                    {
                                        $msg = array("error"=>$e->__toString());
                                        return $msg;
                                    }
                                }
                                
                             }
                            
                         
                        }
                        else{
                            //its empty now insert patient ar IF patientar is not empty
                            if(empty($patientar) && !empty($existingpatients))
                            {
                                // if patientar is empty and existing patient is there then let disccharge all patients if they are assigned
                                // var_dump("patientAr is empty and exsting nurse not empty");
                                foreach($existingpatients as $ds)
                                {
                                   // var_dump($ds["nurse_id"]);
                                 // $move = $this->DischargeNurseAssignment($accountnumber,$sid,$ds["patient_id"]);
                                  $move = $this->DischargePatientAssignment($accountnumber,$pid,$ds["patient_id"]);
                                   //var_dump($move);
                                    if(is_array($move))
                                    {
                                       $dischargear[] =array($move[0][0], $ds["patient_id"]);//$move;
                                    }
                                }
                                
                            }
                            else{
                               // var_dump("Exsting Patient must be empty and patientar isn't emplty");
                               // var_dump($nursar);
                                //the foreach loop for the patientar to assign patient should go here 
                                foreach($patientar as $r)
                                {
                                
                                     $getpmid = explode("-",$r);
                                     $patientid = $getpmid[0];
                                     $xplodename = explode(" ",$getpmid[1]);
                                     if(count($xplodename)==3)
                                    {
                                      //there must be an extr space somewhere and we need to account for it so the last name isn't null
                                      $fname = $xplodename[0]." ".$xplodename[1];//fierstname and middle name
                                      $lname = $xplodename[2];
                                    }
                                    else{
                                      var_dump("something weird lets look");
                                      $fname = $xplodename[0];//fierstname and middle name
                                      $lname  = $xplodename[1];
                                    }
                                    // $fname = $xplodename[0]." ".$xplodename[1];//fierstname and middle name 
                                    // $lname = $xplodename[2];
                                     //check to see if this nurse is already added 
                                     
                                     $sql1 ="SELECT * FROM `providerassignment` WHERE providerid=:provid AND patient_id=:pid AND accountnumber=:accnt AND assignment_date IS NOT NULL AND discharge_date IS NULL";
                                     $stmnt2 = $this->con->prepare($sql1);
                                     $stmnt2->bindParam(":provid",$pid);
                                     $stmnt2->bindParam(":accnt",$accountnumber);
                                     $stmnt2->bindParam(":pid",$patientid);
                                    
                                     try{
                                        if($stmnt2->execute())
                                        {
                                             $isassgined = $stmnt2->fetchAll();
                                             
                                             if(count($isassgined)>0)
                                             {
                                                //record is already assigned to this patient
                                               
                                             }
                                             else{
                                                //record is ready to be inserted
                                                $dateassigned = date("Y-m-d H:i:s");
                                                $sql="INSERT INTO `providerassignment`( `providerid`, `patient_id`, `assignment_date`, `accountnumber`,`firstname`,`lastname`) VALUES 
                                                (:provid,:pid,:assignDT,:accnt,:fname,:lname)";
                                                $stmnt = $this->con->prepare($sql);
                                                $stmnt->bindParam(":accnt",$accountnumber);
                                                $stmnt->bindParam(":provid",$pid);
                                                $stmnt->bindParam(":pid",$patientid);
                                                $stmnt->bindParam(":assignDT",$dateassigned);
                                                $stmnt->bindParam(":fname",$fname);
                                                $stmnt->bindParam(":lname",$lname);
                                                if($stmnt->execute())
                                                {
                                                    $ptassignedar[]=array($fname." ".$lname, $patientid);
                                                  
                                                    //$nrassignedar[]=array("nurseid"=>$nursid,"assignmentDate"=>$dateassigned,"message"=>"Insertd Successfully");
                                                }
                                             }
                                        }
                                        
                                     }
                                     catch(PDOException $e)
                                     {
                                       $msg =array("erro"=>"SQL-700","message"=>$e->__toString());
                                     
                                       return $msg;
                                     }
                                    
                                     
                                }//end last 4 each
                            }
                           
                        }
                   // foreach to insert Nurses go here 
                   
                  
                   
                   
                }
            }
            catch(PDOException $e)
            {
                $mst = array($e->__toString());
              
                return $mst;
            }
           
           
            //lets check to see if the nurseassginedar is not empy and if its not, that means nurse was assigend successfully
            if(!empty($dischargear) || !empty($ptassignedar))
            {
                $payload = array("message"=>"Patient Assigned Successfully","patientInfo"=>$ptassignedar,"dischargeInfo"=>$dischargear);
                return $payload;
            }
            elseif(empty($discharge) && empty($ptassignedar) && !empty($foundcurrentNurse))
            {
                $payload = array("message"=>"Patient(s) Already Assigned","patientInfo"=>$foundcurrentNurse);
                return $payload;
            }
            else{
                $payload = array("message"=>"Patients Not Assigned Successfully","status"=>"400","code"=>$dischargear);
                return $payload;
            }
           
            
        }
        else{
            $msg =array("status"=>"Not Successfull","error"=>"Patient data doesnt exist","data"=>$patientar);
            return $msg;
        }
    }
    //endPatienttoPRoviders
    public function AssignPatientToNurse($sid,$accountnumber,$patientar)
    {
             if(!empty($patientar) && is_array($patientar))
        {
        //  var_dump($sid);
            //lets
           // var_dump($patientar);
           // var_dump($sid);
            //var_dump($accountnumber);
             $dischargear = array();
             $ptassignedar = array();
             $foundcurrentPatient= array();
            //lets pull a list of [patients] that may already be assigned to the nurse 
            $sqlfetch="SELECT * FROM nurseassignment WHERE nurse_id=:sid AND accountnumber=:accnt AND assignment_date IS NOT NULL AND discharge_date IS NULL";
            $stmnt4 = $this->con->prepare($sqlfetch);
            $existingpatients[] = array();
            $stmnt4->bindParam(":sid",$sid);
            $stmnt4->bindParam(":accnt",$accountnumber);
           
            try{
                if($stmnt4->execute())
                {
                    $existingpatients = $stmnt4->fetchAll();
                   // var_dump("Gettng possible assignments already");
                  // var_dump($existingpatients);
                         $assignedFilter = array_filter($existingpatients);
                         $picklistar[] = array();
                         $patientlistar[] =array();
                        // var_dump($assignedFilter);
                        // var_dump($patientar);
                         if(is_array($assignedFilter) && !empty($assignedFilter) && !empty($patientar) )
                         {
                             /*Lets get explode the Piclist list of patients and put them into a multidemensional array
                             * Running thise because we want to check the patients that are already assigned (assignedFilter) against the
                             * PiclistAr
                             */
                            
                            
                              foreach($patientar as $nnr)
                             {
                                 $getid = explode("-",$nnr);
                                
                                 $patientid = $getid[0];
                               
                                 $xxplodename = explode(" ",$getid[1]);
                              // var_dump($xxplodename);
                               if(count($xxplodename)==3)
                               {
                                //the array has a length of three we need to so [0] [2] is the first and lastname
                                $fname = $xxplodename[0];
                                $lname = $xxplodename[2];
                               }
                               else{
                                // array length is 2 we're assuming so answer is [0] [1] for first anad lastname 
                                $fname = $xxplodename[0];
                                $lname = $xxplodename[1];
                               }
                                // $fname = trim($xxplodename[0]);//." ".$xxplodename[1];//fierstname and middle name 
                                // $lname = trim($xxplodename[1]);
                                // var_dump($fname);
                                // var_dump($lname);
                                 array_push($picklistar,$patientid);
                                $patientlistar[] = array("firstname"=>$fname,"lastname"=>$lname,"patientid"=>$patientid,"accountnumber"=>$accountnumber);
                               
                             }
                               $nwpicklistar = array_filter($picklistar); //filter out the empty values 
                             // var_dump("new picklist");
                             // var_dump($nwpicklistar);
                             /*now lets compare this array to the one that was passed over and if a patient is missing from 
                             the array we just pulled, we are going to discharge that patient with todays date 
                             @neeed to loop through the parameter array that was passed over in order to evaluate it
                             */
                             //$foundcurrentNurse= array();
                             foreach($assignedFilter as $f)
                             {
                               
                                 if(in_array($f["patient_id"],$nwpicklistar))
                                 {
                                     //if its there do nothing these nurse should remain assigned to the client
                                    // var_dump("already assigned"." ".$f["nurse_id"]);
                                    //Pushing already Assinged Nurses into the foundcurrentNurse Array
                                    array_push($foundcurrentPatient,$f["patient_id"]);
                                 }
                                 else{
                                    
                                     //lets discharge now and bind new nurseid with the assignedFilter['nurse_id']
                                    $sql3="UPDATE `nurseassignment` SET discharge_date=:disdate WHERE patient_id=:patid AND accountnumber=:accnt ";
                                     $stmnt3 = $this->con->prepare($sql3);
                                     $stmnt3->bindParam(":disdate",date('Y-m-d H:i:s'));
                                     $stmnt3->bindParam(":patid",$f["patient_id"]);
                                     $stmnt3->bindParam(":accnt",$accountnumber);
                                     try{
                                         if($stmnt3->execute())
                                         {
                                             //this nurse should now be discharged 
                                             $dischargear[] = array($f["first_name"]." ".$f["lastname"], $f["patient_id"]);
                                             var_dump($dischargear);
                                            
                                         }
                                     }catch(PDOException $e)
                                     {
                                         $msg = array("sqlError"=>"700-sql","message"=>$e->__toString());
                                        // var_dump($msg);
                                         return $msg;
                                     } 
                                 }
                                
                             }
                           //  var_dump("Current Patients");
                           //  var_dump($foundcurrentPatient);
                             //now loop through the patientlistar and insert the new patients that hasn't already been assigned 
                             foreach(array_filter($patientlistar) as $n)
                             {
                               
                                $fname = $n["firstname"];
                                $lname = $n["lastname"];
                                $patientid =$n["patientid"];
                              //  var_dump("PtientID");
                               // var_dump($patientid);
                                $accountnumber = $n["accountnumber"];
                                $dateassigned = date("Y-m-d H:i:s");
                                //make sure we don't add the patients that have already been assigned
                                if(!in_array($patientid,array_filter($foundcurrentPatient)))
                                {

                                
                                    $sql="INSERT INTO `nurseassignment`( `nurse_id`, `patient_id`, `assignment_date`, `accountnumber`,`firstname`,`lastname`) VALUES 
                                    (:nursid,:pid,:assignDT,:accnt,:fname,:lname)";
                                    $stmnt = $this->con->prepare($sql);
                                    $stmnt->bindParam(":accnt",$accountnumber);
                                    $stmnt->bindParam(":nursid",$sid);
                                    $stmnt->bindParam(":pid",$patientid);
                                    $stmnt->bindParam(":assignDT",$dateassigned);
                                    $stmnt->bindParam(":fname",$fname);
                                    $stmnt->bindParam(":lname",$lname);
                                    try{
                                            if($stmnt->execute())
                                            {
                                                $ptassignedar[]=array($fname." ".$lname, $patientid);
                                               // var_dump("Inserted Into it");
                                               // var_dump($ptassignedar);
                                            
                                                //$nrassignedar[]=array("nurseid"=>$nursid,"assignmentDate"=>$dateassigned,"message"=>"Insertd Successfully");
                                            }
                                    }
                                    catch(PDOEXception $e)
                                    {
                                        $msg = array("error"=>$e->__toString());
                                        return $msg;
                                    }
                                }
                                
                             }
                            
                         
                        }
                        else{
                            //its empty now insert patient ar IF patientar is not empty
                            if(empty($patientar) && !empty($existingpatients))
                            {
                                // if patientar is empty and existing patient is there then let disccharge all patients if they are assigned
                                // var_dump("patientAr is empty and exsting nurse not empty");
                                foreach($existingpatients as $ds)
                                {
                                   // var_dump($ds["nurse_id"]);
                                 // $move = $this->DischargeNurseAssignment($accountnumber,$sid,$ds["patient_id"]);
                                  $move = $this->DischargePatientAssignment($accountnumber,$sid,$ds["patient_id"]);
                                 
                                    if(is_array($move))
                                    {
                                       $dischargear[] =array($move[0][0], $ds["patient_id"]);//$move;
                                    }
                                }
                                
                            }
                            else{
                               // var_dump("Exsting Patient must be empty and patientar isn't emplty");
                               // var_dump($nursar);
                                //the foreach loop for the patientar to assign patient should go here 
                                foreach($patientar as $r)
                                {
                                
                                     $getpmid = explode("-",$r);
                                     $patientid = $getpmid[0];
                                     $xplodename = explode(" ",$getpmid[1]);
                                     //var_dump($xplodename);
                                     $fname = trim($xplodename[0]);//." ".$xplodename[1];//fierstname and middle name 
                                     if(count($xplodename)==3)
                                     {
                                      $fname = $xplodename[0];
                                      $lname = $xplodename[2];
                                     }
                                     else{
                                      $fname = $xplodename[0];
                                       $lname = $xplodename[1];
                                     }
                                    
                                    // var_dump($fname);
                                    // var_dump($lname);
                                    // var_dump("Insert Section");
                                     //check to see if this nurse is already added 
                                     
                                     $sql1 ="SELECT * FROM `nurseassignment` WHERE nurse_id=:nursid AND patient_id=:pid AND accountnumber=:accnt AND assignment_date IS NOT NULL AND discharge_date IS NULL";
                                     $stmnt2 = $this->con->prepare($sql1);
                                     $stmnt2->bindParam(":nursid",$sid);
                                     $stmnt2->bindParam(":accnt",$accountnumber);
                                     $stmnt2->bindParam(":pid",$patientid);
                                    
                                     try{
                                        if($stmnt2->execute())
                                        {
                                             $isassgined = $stmnt2->fetchAll();
                                             
                                             if(count($isassgined)>0)
                                             {
                                                //record is already assigned to this patient
                                                var_dump("record is already asigned");
                                               
                                             }
                                             else{
                                                //record is ready to be inserted
                                                $dateassigned = date("Y-m-d H:i:s");
                                                $sql="INSERT INTO `nurseassignment`( `nurse_id`, `patient_id`, `assignment_date`, `accountnumber`,`firstname`,`lastname`) VALUES 
                                                (:nursid,:pid,:assignDT,:accnt,:fname,:lname)";
                                                $stmnt = $this->con->prepare($sql);
                                                $stmnt->bindParam(":accnt",$accountnumber);
                                                $stmnt->bindParam(":nursid",$sid);
                                                $stmnt->bindParam(":pid",$patientid);
                                                $stmnt->bindParam(":assignDT",$dateassigned);
                                                $stmnt->bindParam(":fname",$fname);
                                                $stmnt->bindParam(":lname",$lname);
                                                if($stmnt->execute())
                                                {
                                                    $ptassignedar[]=array($fname." ".$lname, $patientid);
                                                  
                                                    //$nrassignedar[]=array("nurseid"=>$nursid,"assignmentDate"=>$dateassigned,"message"=>"Insertd Successfully");
                                                }
                                             }
                                        }
                                        
                                     }
                                     catch(PDOException $e)
                                     {
                                       $msg =array("erro"=>"SQL-700","message"=>$e->__toString());
                                     
                                       return $msg;
                                     }
                                    
                                     
                                }//end last 4 each
                            }
                           
                        }
                   // foreach to insert Nurses go here 
                   
                  
                   
                   
                }
            }
            catch(PDOException $e)
            {
                $mst = array($e->__toString());
              
                return $mst;
            }
           
           
            //lets check to see if the nurseassginedar is not empy and if its not, that means nurse was assigend successfully
           // var_dump($dischargear);
           // var_dump($ptassignedar);
            if(!empty($dischargear) || !empty($ptassignedar))
            {
                $payload = array("message"=>"Patient Assigned Successfully","patientInfo"=>$ptassignedar,"dischargeInfo"=>$dischargear);
                return $payload;
            }
            elseif(empty($discharge) && empty($ptassignedar) && !empty($foundcurrentNurse))
            {
                $payload = array("message"=>"Patient(s) Already Assigned","patientInfo"=>$foundcurrentNurse);
                return $payload;
            }
            else{
                $payload = array("message"=>"Patients Not Assigned Successfully","status"=>"400","code"=>$dischargear);
                return $payload;
            }
           
            
        }
        else{
            $msg =array("status"=>"Not Successfull","error"=>"Patient data doesnt exist","data"=>$patientar);
            return $msg;
        }
    }
    public function ViewGroupMembers($accountnumber,$ownername,$groupname,$grptype)
    {

    //    $npinum = $npi;

         $sql="SELECT * FROM providers_group WHERE accountnumber=:accnt AND provowner_name=:ownername AND groupname=:groupname AND group_type=:grptype";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":ownername",$ownername);
        $stmnt->bindParam(":groupname",$groupname);
        $stmnt->bindParam(":grptype",$grptype);
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
               // var_dump($records);

                return $records;
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function SaveGroupMembers($accountnumber,$ownername,$groupname,$firstname,$lastname,$npi,$email,$phone,$grptype)
    {

       $npinum = $npi;

         $sql="INSERT INTO providers_group (accountnumber,provowner_name,groupname,grp_provider_fname,grp_provider_lname,provnpinumber,grp_provideremail,phone,group_type) VALUES(:accnt,:ownername,:groupname,:fname,:lname,:npi,:email,:tel,:grptype)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":ownername",$ownername);
        $stmnt->bindParam(":groupname",$groupname);
        $stmnt->bindParam(":fname",$firstname);
        $stmnt->bindParam(":lname",$lastname);
        $stmnt->bindParam(":npi",$npinum);
        $stmnt->bindParam(":email",$email);
        $stmnt->bindParam(":tel",$phone);
        $stmnt->bindParam(":grptype",$grptype);
        try{
             if($stmnt->execute())
             {
                //successful 
                $records ="Inserted";
               // var_dump($records);
                $msg = array("status"=>$records);
                return $msg;
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function SaveGroupMembersWithEmail($accountnumber,$ownername,$groupname,$firstname,$lastname,$email,$phone,$grptype)
    {

       $npinum = "";

         $sql="INSERT INTO providers_group (accountnumber,provowner_name,groupname,grp_provider_fname,grp_provider_lname,provnpinumber,grp_provideremail,phone,group_type) VALUES(:accnt,:ownername,:groupname,:fname,:lname,:npi,:email,:tel,:grptype)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":ownername",$ownername);
        $stmnt->bindParam(":groupname",$groupname);
        $stmnt->bindParam(":fname",$firstname);
        $stmnt->bindParam(":lname",$lastname);
        $stmnt->bindParam(":npi",$npinum);
        $stmnt->bindParam(":email",$email);
        $stmnt->bindParam(":tel",$phone);
        $stmnt->bindParam(":grptype",$grptype);
        try{
             if($stmnt->execute())
             {
                //successful 
                $records ="Inserted";
               // var_dump($records);
                $msg = array("status"=>$records);
                return $msg;
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function GetProvidersByAccountNumber($patid)
    {
        $pid = $patid;

         $sql="Select * FROM providers WHERE accountnumber=:accnumber";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnumber",$pid);
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
               // var_dump($records);
                $msg = array("provider"=>$records);
                return $msg;
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }

    }
    public function GetAccountProviders($accntnumber)
    {
      $accountnumber = $accntnumber;
      $sql="Select * FROM providers WHERE accountnumber=:accntid";
     $stmnt = $this->con->prepare($sql);
     $stmnt->bindParam(":accntid",$accntnumber);
     try{
          if($stmnt->execute())
          {
             //successful 
             $records = $stmnt->fetchAll();
            // var_dump($records);
             $msg = array("provider"=>$records);
             return $msg;
          }
     }
     catch(PDOException $e)
     {
         $msg = array("error"=>$e->__toString());
         return $msg;
     }
    }
    public function GetAllProviders($patid)
    {
        $pid = $patid;
         $sql="Select * FROM providers WHERE patientid=:pid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
               // var_dump($records);
                $msg = array("provider"=>$records);
                return $msg;
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }

    }
    public function FindProviderbyEmail($provemail)
    {

        $lookupemail = $provemail;

        $sql="SELECT * FROM providers WHERE email LIKE '%$lookupemail%'";//=:lookupemail";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":lookupemail",$lookupemail);
        try{

            if($stmnt->execute())
            {
                 $result = $stmnt->fetchAll();
                 $msg = array("status"=>"200","result"=>$result);
                 //var_dump($msg);
                 return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("sqlerror"=>"701-sqlerror","sql"=>$e->__toString());
            return $msg;
        }
    }
    public function GetSingleProvider($patid,$providerid)
    {
        $pid = $patid;
        $provid = $providerid;
        $sql="Select * FROM providers WHERE patientid=:patid AND providerid=:provid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":patid",$patid);
        $stmnt->bindParam(":provid",$providerid);
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
               // var_dump($records);
                $msg = array("provider"=>$records);
                return $msg;
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function FetchMessages($subaccountnumber,$accountnumber,$status)
    {
       // var_dump("Heres");
        $sql="SELECT * FROM `pprov_communication` WHERE msgStatus=:mstatus AND subaccnt_Id=:subaccnt";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":mstatus",$status);
      // $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":subaccnt",$subaccountnumber);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                //var_dump($records);
                $msg = array("status"=>"Successfull","records"=>$records);
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg; 
        }
    }
    public function FetchAllMessagesByIDS($subaccountnumber,$accountnumber)
    {
       // var_dump("Heres");
     
        $sql="SELECT * FROM `pprov_communication` WHERE accnt=:accntnum AND subaccnt_Id=:subaccnt";
        $stmnt = $this->con->prepare($sql);
        //$stmnt->bindParam(":mstatus",$status);
         $stmnt->bindParam(":accntnum",$accountnumber);
        $stmnt->bindParam(":subaccnt",$subaccountnumber);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
              //  var_dump($records);
                $msg = array("status"=>"Successfull","records"=>$records);
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg; 
        }
    }
    public function SearchAllMessagesByIDS($subaccountnumber,$accountnumber,$message)
    {
       // var_dump($accountnumber);
        //var_dump($subaccountnumber);
        $sql="SELECT * FROM `pprov_communication` WHERE accnt=:accntnum AND subaccnt_Id=:subaccnt AND `message` LIKE '%$message%'  ";
       // var_dump($sql);
        $stmnt = $this->con->prepare($sql);
       // $stmnt->bindParam(":mstatus",$status);
        $stmnt->bindParam(":accntnum",$accountnumber);
        $stmnt->bindParam(":subaccnt",$subaccountnumber);
       // $stmnt->bindParam(":thrdid",$threadid);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
               // var_dump($records);
               if($records !="" && count($records)>=5)
               {
                 $msg = array("status"=>"Successfull","records"=>$records);
                return $msg;
               }
               elseif($records=="")
               {
                $sql2="SELECT * FROM `pprov_communication` WHERE accnt=:accntnum AND subaccnt_Id=:subaccnt AND `subject` LIKE '%$message%'  ";
                $stmnt2 = $this->con->prepare($sql);
                // $stmnt->bindParam(":mstatus",$status);
                 $stmnt2->bindParam(":accntnum",$accountnumber);
                 $stmnt2->bindParam(":subaccnt",$subaccountnumber);
                 $rec = $stmnt2->execute();
                 $records2 = $stmnt2->fetchAll();
                if($records2 !="" && count($records2)>=5)
                {
                    $msg = array("status"=>"Successfull","records"=>$records2);
                    return $msg;
                }

               }
               
            }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg; 
        }
    }
    public function FetchMessagesByThreadID($subaccountnumber,$accountnumber,$threadid)
    {
       // var_dump("Heres");
        $sql="SELECT * FROM `pprov_communication` WHERE accnt=:accntnum AND subaccnt_Id=:subaccnt AND thread_id=:thrdid";
        $stmnt = $this->con->prepare($sql);
       // $stmnt->bindParam(":mstatus",$status);
        $stmnt->bindParam(":accntnum",$accountnumber);
        $stmnt->bindParam(":subaccnt",$subaccountnumber);
        $stmnt->bindParam(":thrdid",$threadid);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                //var_dump($records);
                $msg = array("status"=>"Successfull","records"=>$records);
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg; 
        }
    }
    public function AddContact($firstname,$lastname,$email,$phone,$address,$addr2,$city,
	$state,$zip,$country,$dob,$notes,$accountnumber,$subaccnt)
    {
        $dtcreated = new DateTime();
        $dttime = date_format($dtcreated,'Y-m-d H:i:s');
        $sql="INSERT INTO contacts (`accountnumber`, `subaccountnumber`, `Firstname`, `Lastname`, `email`, `phone`, `address`, `address2`, `city`, `state`, `zip`, `country`, `dob`, `notes`, `contact_created`) VALUES (:accnt,:subaccnt,:fname,:lname,:email,:phone,:addr,:addr2,:city,:stat,:zip,:country,:dob,:notes,:ccreated)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":fname",$firstname);
        $stmnt->bindParam(":lname",$lastname);
        $stmnt->bindParam(":email",$email);
        $stmnt->bindParam(":phone",$phone);
        $stmnt->bindParam(":addr",$address);
        $stmnt->bindParam(":addr2",$addr2);
        $stmnt->bindParam(":city",$city);
        $stmnt->bindParam(":stat",$state);
        $stmnt->bindParam(":zip",$zip);
        $stmnt->bindParam(":country",$state);;
        $stmnt->bindParam(":dob",$dob);
        $stmnt->bindParam(":notes",$notes);
        // $stmnt->bindParam(":furl",$fileurl);
        $stmnt->bindParam(":ccreated",$dttime);
        $stmnt->bindParam(":accnt",$accountnumber);
        $stmnt->bindParam(":subaccnt",$subaccnt);
       
        try{

            if($stmnt->execute())
            {
                $msg = array("status"=>"Successfull","message"=>"Inserted");
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("message"=>"Error SQL Error","errorCode"=>$e->__toString(),"status"=>"700-Sql");
            return $msg;
        }
    }
    public function SendInternalhMessages($reciversubaccnt,$sendersubaccnt,$senderaccountnum,$message,$status,$date,$sender,$action,$fileurl,$filename,$subject)
    {
        $sql="Insert `pprov_communication` (`accnt`, `subaccnt_Id`, `sender_id`, `recipient_id`, `pt_name`, `message`, `subject`,`msgDate`, `msgTimestamp`, `msgStatus`, `actions`,`thread_attachment`,`thread_attachment_url`)"
       ." Values(:accnt,:subaccnt,:sid,:rid,:ptname,:msg,:subject,:mdate,:mtime,:status,:action,:threadattach,:threadattachurl)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$senderaccountnum);
        $stmnt->bindParam(":subaccnt",$sendersubaccnt);
        $stmnt->bindParam(":sid",$sendersubaccnt);
        $stmnt->bindParam(":rid",$reciversubaccnt);
        $stmnt->bindParam(":ptname",$sender);
        $stmnt->bindParam(":msg",$message);
        $stmnt->bindParam(":subject",$subject);
        $stmnt->bindParam(":mdate",date("Y-m-d"));
        $stmnt->bindParam(":mtime",$date);
        $stmnt->bindParam(":status", $status);
        $stmnt->bindParam(":action", $action);
        $stmnt->bindParam(":threadattach",$filename);
        $stmnt->bindParam(":threadattachurl",$fileurl);
       
        try{
            if($stmnt->execute())
            {
                $msg = array("message"=>"Inserted","status"=>"200-Successfull");
                //var_dump($records);
              
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg; 
        }
    }
    public function GetProviderInformation($providerid)
    {
        $sql="Select * FROM providers WHERE providerid=:pid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$providerid);
        try{
             if($stmnt->execute())
             {
                //successful 
                $records = $stmnt->fetchAll();
               // var_dump($records);
                $msg = array("provider"=>$records);
                return $msg;
             }
        }
        catch(PDOException $e)
        {
            $msg = array("error"=>$e->__toString());
            return $msg;
        }
    }
    public function UpdateProviderInfo($providerid,$email,$pid,$ordnumber,$firstname,$lastname,$address1,$address2,$city,$state,$zip,$tel,$fax,$npinum,$status,$cell)
    {

         $sql="UPDATE providers SET npinumber=:npinum,patientid=:pid,addr1=:addr1,addr2=:addr2,city=:city,state=:state,postalcode=:zip,tel=:tel,fax=:fax,firstname=:fname,lastname=:lname,email=:mail,cell=:cell,status=:stat WHERE providerid=:provid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":npinum",$npinum);
       // $stmnt->bindParam(":ordernum",$ordnumber);
        $stmnt->bindParam(":pid",$pid);
        $stmnt->bindParam(":addr1",$address1);
        $stmnt->bindParam(":addr2",$address2);
        $stmnt->bindParam(":city",$city);
        $stmnt->bindParam(":state",$state);
        $stmnt->bindParam(":zip",$zip);
        $stmnt->bindParam(":tel",$tel);
        $stmnt->bindParam(":fax",$fax);
        $stmnt->bindParam(":fname",$firstname);
        $stmnt->bindParam(":lname",$lastname);
        $stmnt->bindParam(":stat",$status);
        $stmnt->bindParam(":mail",$email);
        $stmnt->bindParam(":provid",$providerid);
        $stmnt->bindParam(":stat",$status);
        $stmnt->bindParam(":cell", $cell);
        try{
            if($stmnt->execute())
            {
                $msg=array("msg"=>"Updated");
                return $msg;
           }
        }
        catch(PDOException $e)
        {
            $msg = array("error",$e->__toString());
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
    public function GetPrePourMeds($arr){
        $one = 1;
        $sql = "SELECT * FROM new_medications WHERE pid = :patientId AND accountId = :accountId AND prepour = :prepour";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":patientId", $arr["pid"]);
        $stmnt->bindParam(":accountId", $arr["accountId"]);
        $stmnt->bindParam(":prepour", $one);
        try {
            if ($stmnt->execute()) {
                $results = $stmnt->fetchAll(PDO::FETCH_ASSOC);
                return $results;
            }
        } catch (PDOException $e) {
            $msg = $e->__toString();
            return "Error: " . $msg;
        }
    }
    public function UpdatePrepourForActiveMedications($patientID, $accountID)
    {
        $sql = "
        SELECT * 
        FROM new_medications 
        WHERE pid = :patientID 
        AND accountId = :accountID 
        AND prepour = 0
        AND (endDate IS NULL OR STR_TO_DATE(endDate, '%Y-%m-%d') > NOW())
    ";


        $updateSql = "
            UPDATE new_medications 
            SET prepour = 1 
            WHERE medId = :medId
        ";

        // Prepare statements
        $stmnt = $this->con->prepare($sql);
        $updateStmnt = $this->con->prepare($updateSql);

        $stmnt->bindParam(":patientID", $patientID);
        $stmnt->bindParam(":accountID", $accountID);

        try {
            if ($stmnt->execute()) {
                $results = $stmnt->fetchAll(PDO::FETCH_ASSOC);

                if (count($results) > 0) {
                    foreach ($results as $medication) {
                        // Update the 'prepour' value to true for each medication
                        $updateStmnt->bindParam(":medId", $medication['medId']);
                        $updateStmnt->execute();
                    }
                    return "Prepour updated for active medications.";
                } else {
                    return "No active medications found for the given PatientID and AccountID.";
                }
            }
        } catch (PDOException $e) {
            $msg = $e->__toString();
            return "Error: " . $msg;
        }
    }
    public function GetOrders($orderArr){
      $pid = $orderArr["pid"];
      $accountId = $orderArr["accountId"];
      $query = "SELECT * from new_orders WHERE pid=:pid AND accountId=:accountId";
      $stmnt = $this->con->prepare($query);
      $stmnt->bindParam(":pid", $pid);
      $stmnt->bindparam(":accountId", $accountId);
      $stmnt->execute();
      $result = $stmnt->fetchAll();
      return $result;
  }
    public function GetPatientProcs($procArr){
        $query = "
            SELECT 
                a.*, 
                b.signerName as signerName,
                b.signerId as signerId,
                b.orderDate as orderDate,
                b.orderTime as orderTime,
                b.abnDelivery as abnDelivery,
                b.readOrderBack as readOrderBack,
                b.primaryPhysician as primaryPhysician,
                b.description as orderDescription,
                c.description as description
            FROM 
                new_procedures AS a
            LEFT JOIN 
                new_orders AS b
            ON 
                a.orderId = b.orderId
            LEFT JOIN 
                new_diagnoses AS c
            ON 
                a.diagId = c.diagId
            WHERE
                a.pid = :pid AND a.accountId = :accountId
        ";
        $stmnt = $this->con->prepare($query);
        $stmnt->bindParam(":pid", $procArr["pid"]);
        $stmnt->bindParam(":accountId", $procArr["accountId"]);
        $stmnt->execute();
        $result = $stmnt->fetchAll();
        return $result;
    }
    public function GetPatientMeds($medArr){
        $query = "
            SELECT 
                a.*, 
                b.signerName as signerName,
                b.signerId as signerId,
                b.orderDate as orderDate,
                b.orderTime as orderTime,
                b.abnDelivery as abnDelivery,
                b.readOrderBack as readOrderBack,
                b.primaryPhysician as primaryPhysician,
                b.description as orderDescription,
                c.description as diagDescription
            FROM 
                new_medications AS a
            LEFT JOIN 
                new_orders AS b
            ON 
                a.orderId = b.orderId
            LEFT JOIN 
                new_diagnoses AS c
            ON 
                a.diagId = c.diagId
            WHERE
                a.pid = :pid AND a.accountId = :accountId
        ";
        $stmnt = $this->con->prepare($query);
        $stmnt->bindParam(":pid", $medArr["pid"]);
        $stmnt->bindParam(":accountId", $medArr["accountId"]);
        $stmnt->execute();
        $result = $stmnt->fetchAll();
        return $result;
    }
    public function AddNewDiag($diag){
        $isExist = "SELECT * FROM new_diagnoses WHERE code=:code AND pid=:pid AND accountId=:accountId";
        $stmnt = $this->con->prepare($isExist);
        $stmnt->bindParam(":code", $diag["code"]);
        $stmnt->bindParam(":pid", $diag["pid"]);
        $stmnt->bindParam(":accountId", $diag["accountId"]);
        $stmnt->execute();
        $result = $stmnt->fetchAll();
        if (count($result) > 0){
            return;
        }

        $sql = "Insert into new_diagnoses (pid, accountId, code, description,
        shorthand, startDate, status, assignerName, assignerId) 
        VALUES (:pid, :accountId, :code, :description, :shorthand, :startDate,
        :status, :assignerName, :assignerId)";
        $stmnt = $this->con->prepare($sql);
        $status = "Active";
        $stmnt->bindParam(":pid", $diag["pid"]);
        $stmnt->bindParam(":accountId", $diag["accountId"]);
        $stmnt->bindParam(":code", $diag["code"]);
        $stmnt->bindParam(":description", $diag["description"]);
        $stmnt->bindParam(":shorthand", $diag["shorthand"]);
        $stmnt->bindParam(":startDate", $diag["startDate"]);
        $stmnt->bindParam(":status", $status);
        $stmnt->bindParam(":assignerName", $diag["assignerName"]);
        $stmnt->bindParam(":assignerId", $diag["assignerId"]);
        $stmnt->execute();
        return;

    }
    public function InsertNewOrder($orderArr){
        if ($orderArr["newDiags"]){
            foreach ($orderArr["newDiags"] as $diag){
                $this->AddNewDiag($diag);
            }
        }
        // every added med or proc must go with an order

        $orderSql = "Insert into new_orders (pid, accountId, creatorName, creatorId, procOrMed,
        procOrMedId, procOrMedName, diagId, orderDate, orderTime, orderType,
        abnDelivery, readOrderBack, primaryPhysician, primaryTaxonomy, npi, email, address, phone,
        fax, description, status, nurseSignature, nurseDateSig, providerSignature, providerDateSig) VALUES (:pid, :accountId,
        :creatorName, :creatorId, :procOrMed, :procOrMedId, :procOrMedName, (SELECT diagId from new_diagnoses WHERE code = :diagCode AND pid = :pidDiag AND accountId = :accountIdDiag),
        :orderDate, :orderTime, :orderType, :abnDelivery, :readOrderBack,
        :primaryPhysician, :primaryTaxonomy, :npi, :email, :address, :phone, :fax, :description,
        :status, :nurseSignature, :nurseDateSig, :providerSignature, :providerDateSig)"; // eventually include :history
        $stmnt = $this->con->prepare($orderSql);
        $stmnt->bindParam(":pid", $orderArr["pid"]);
        $stmnt->bindParam(":accountId", $orderArr["accountId"]);
        $stmnt->bindParam(":creatorName", $orderArr["creatorName"]);
        $stmnt->bindParam(":creatorId", $orderArr["creatorId"]);
        $stmnt->bindParam(":orderDate", $orderArr["orderDate"]);
        $stmnt->bindParam(":orderTime", $orderArr["orderTime"]);
        $stmnt->bindParam(":orderType", $orderArr["orderType"]);
        $stmnt->bindParam(":abnDelivery", $orderArr["abnDelivery"]);
        $stmnt->bindParam(":readOrderBack", $orderArr["readOrderBack"]);
        $stmnt->bindParam(":primaryPhysician", $orderArr["primaryPhysician"]);
        $stmnt->bindParam(":primaryTaxonomy", $orderArr["primaryTaxonomy"]);
        $stmnt->bindParam(":npi", $orderArr["npi"]);
        $stmnt->bindParam(":email", $orderArr["email"]);
        $stmnt->bindParam(":address", $orderArr["address"]);
        $stmnt->bindParam(":phone", $orderArr["phone"]);
        $stmnt->bindParam(":fax", $orderArr["fax"]);
        $stmnt->bindParam(":description", $orderArr["description"]);
        $stmnt->bindParam(":status", $orderArr["status"]);
        $stmnt->bindParam(":pidDiag", $orderArr["pid"]);
        $stmnt->bindParam(":accountIdDiag", $orderArr["accountId"]);
        $stmnt->bindParam(":nurseSignature", $orderArr["nurseSignature"]);
        $stmnt->bindParam(":nurseDateSig", $orderArr["nurseSigDate"]);
        $stmnt->bindParam(":providerSignature", $orderArr["provSignature"]);
        $stmnt->bindParam(":providerDateSig", $orderArr["provSigDate"]);
        // $stmnt->bindParam(":history", {});

        // attach this ^ sql to every proc and med here v
        if ($orderArr["newMeds"]){
            foreach ($orderArr["newMeds"] as $med){
                $this->AddNewMed($med, $stmnt);
            }
        }
        if ($orderArr["newProcs"]){
            foreach ($orderArr["newProcs"] as $proc){
                $this->AddNewProc($proc, $stmnt);
            }
        }
        return array("status" => "OK");

    }
    public function AddNewMed($med, $stmnt = null, $order = true){
        $medSql = "Insert into new_medications (pid, accountId, diagId, medName,
        medShorthand, quantity, frequency, duration, prn, route, altRoute, instructions,
        startDate, endDate, administration, prepour, nebulizer, status, assignerName, assignerId) 
        VALUES (:pid, :accountId, (SELECT diagId from new_diagnoses WHERE code = :diagCode AND pid = :pidDiag AND accountId = :accountIdDiag), :medName, :medShorthand,
        :quantity, :frequency, :duration, :prn, :route, :altRoute, :instructions,
        :startDate, :endDate, :administration, :prepour, :nebulizer, :status, :assignerName, :assignerId)";
        $medStmnt = $this->con->prepare($medSql);
        $medStmnt->bindParam(":pid", $med["pid"]);
        $medStmnt->bindParam(":pidDiag", $med["pid"]);
        $medStmnt->bindParam(":accountId", $med["accountId"]);
        $medStmnt->bindParam(":accountIdDiag", $med["accountId"]);
        $medStmnt->bindParam(":diagCode", $med["diagCode"]);
        $medStmnt->bindParam(":medName", $med["medName"]);
        $medStmnt->bindParam(":medShorthand", $med["medShorthand"]);
        $medStmnt->bindParam(":quantity", $med["quantity"]);
        $medStmnt->bindParam(":frequency", $med["frequency"]);
        $medStmnt->bindParam(":duration", $med["duration"]);
        $medStmnt->bindParam(":prn", $med["prn"]);
        $medStmnt->bindParam(":route", $med["route"]);
        $medStmnt->bindParam(":altRoute", $med["altRoute"]);
        $medStmnt->bindParam(":instructions", $med["instructions"]);
        $medStmnt->bindParam(":startDate", $med["startDate"]);
        $medStmnt->bindParam(":endDate", $med["endDate"]);
        $medStmnt->bindParam(":administration", $med["administration"]);
        $medStmnt->bindParam(":prepour", $med["prepour"]);
        $medStmnt->bindParam(":nebulizer", $med["nebulizer"]);
        $medStmnt->bindParam(":status", $med["status"]);
        $medStmnt->bindParam(":assignerName", $med["assignerName"]);
        $medStmnt->bindParam(":assignerId", $med["assignerId"]);
        // $this->con->lastInsertId()
        
        $medStmnt->execute();
        $medId = $this->con->lastInsertId();

        if ($order){
            $text = "medication";
            $stmnt->bindParam(":procOrMed", $text);
            $stmnt->bindParam(":procOrMedId", $medId);
            $stmnt->bindParam(":procOrMedName", $med["medName"]);
            $stmnt->bindParam(":diagCode", $med["diagCode"]);
            $stmnt->execute();
            $orderId = $this->con->lastInsertId();

            $finalSql = "UPDATE new_medications SET orderId=:orderId WHERE pid=:pid AND accountId=:accountId AND medId = :medId";
            $finalStmnt = $this->con->prepare($finalSql);
            $finalStmnt->bindParam(":orderId", $orderId);
            $finalStmnt->bindParam(":pid", $med["pid"]);
            $finalStmnt->bindParam(":accountId", $med["accountId"]);
            $finalStmnt->bindParam(":medId", $medId);
            $finalStmnt->execute();
        }
        else {
            $finalSql = "UPDATE new_medications SET orderId = 0 WHERE pid=:pid AND accountId=:accountId AND medId = :medId";
            $finalStmnt = $this->con->prepare($finalSql);
            $finalStmnt->bindParam(":pid", $med["pid"]);
            $finalStmnt->bindParam(":accountId", $med["accountId"]);
            $finalStmnt->bindParam(":medId", $medId);
            $finalStmnt->execute();
        }

    }
    public function AddNewProc($proc, $stmnt){
        $procSql = "Insert into new_procedures (pid, accountId, diagId, code, shorthand, instructions, startDate, endDate, status, assignerName, assignerId) 
    VALUES (:pid, :accountId, (SELECT diagId from new_diagnoses WHERE code = :diagCode AND pid = :pidDiag AND accountId = :accountIdDiag), :code, :shorthand, :instructions, :startDate, :endDate, :status, :assignerName, :assignerId)";
    
        $procStmnt = $this->con->prepare($procSql);
        $procStmnt->bindParam(":pid", $proc["pid"]);
        $procStmnt->bindParam(":pidDiag", $proc["pid"]);
        $procStmnt->bindParam(":accountId", $proc["accountId"]);
        $procStmnt->bindParam(":accountIdDiag", $proc["accountId"]);
        $procStmnt->bindParam(":diagCode", $proc["diagCode"]);
        $procStmnt->bindParam(":code", $proc["code"]);
        $procStmnt->bindParam(":shorthand", $proc["shorthand"]);
        $procStmnt->bindParam(":instructions", $proc["instructions"]);
        $procStmnt->bindParam(":startDate", $proc["startDate"]);
        $procStmnt->bindParam(":endDate", $proc["endDate"]);
        $procStmnt->bindParam(":status", $proc["status"]);
        $procStmnt->bindParam(":assignerName", $proc["assignerName"]);
        $procStmnt->bindParam(":assignerId", $proc["assignerId"]);
        
        $procStmnt->execute();
        $procId = $this->con->lastInsertId();
        $text = "procedure";
        $stmnt->bindParam(":procOrMed", $text);
        $stmnt->bindParam(":procOrMedId", $procId);
        $stmnt->bindParam(":procOrMedName", $proc["code"]);
        $stmnt->bindParam(":diagCode", $proc["diagCode"]);
        $stmnt->execute();
        $orderId = $this->con->lastInsertId();

        $finalSql = "UPDATE new_procedures SET orderId=:orderId WHERE pid=:pid AND accountId=:accountId AND procId=:procId";
        $finalStmnt = $this->con->prepare($finalSql);
        $finalStmnt->bindParam(":orderId", $orderId);
        $finalStmnt->bindParam(":pid", $proc["pid"]);
        $finalStmnt->bindParam(":accountId", $proc["accountId"]);
        $finalStmnt->bindParam(":procId", $procId);
        $finalStmnt->execute();
    }
    /*Get User Phone For SMS*/ 
    public function GetUserPhoneForSMS($accntnumber,$subaccnt)
    {
        //$sql="SELECT * FROM account_user WHERE accountnumber=:accnt AND unique_ID=:subaccnt AND user_phone=:phn LIMIT 1";
        $sql="SELECT * FROM account_user WHERE accountnumber=:accnt AND unique_ID=:subaccnt LIMIT 1";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accntnumber);
        $stmnt->bindParam(":subaccnt",$subaccnt);
        //$stmnt->bindParam(":phn",$phone);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
               
                $msg = array("status"=>"200","records"=>$records,"phone"=>$records[0]["user_phone"],"username"=>$records[0]["username"],
              "email"=>$records[0]["user_email"],"accountnumber"=>$records[0]["accountnumber"],"subaccountnumber"=>$records[0]["unique_ID"]);
               // var_dump($msg);
                return $msg;
            }
        }
        catch(PDOException $e){
        $msg = array("status"=>"701-sqlerror","error"=>$e->__toString());
           return $msg;
       }
    }
    /*GET SMS STORED CODE */
    public function GetUserSMSStoredCode($accntnumber,$subaccnt,$code)
    {
        $sql="SELECT * FROM account_user WHERE accountnumber=:accnt AND unique_ID=:subaccnt and loginCode=:code LIMIT 1";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accntnumber);
        $stmnt->bindParam(":subaccnt",$subaccnt);
        $stmnt->bindParam(":code",$code);
        try{
            if($stmnt->execute())
            {
                $records = $stmnt->fetchAll();
                $msg = array("status"=>"200","records"=>$records,"accountnumber"=>$records[0]["accountnumber"],"subaccountnumber"=>$records[0]["unique_ID"],"username"=>$records[0]["username"],"email"=>$records[0]["user_email"],"code"=>$records[0]["logincode"]);
               // var_dump($msg);
                return $msg;
            }
        }
        catch(PDOException $e){
        $msg = array("status"=>"701-sqlerror","error"=>$e->__toString());
           return $msg;
       }
    }
    public function UpdateUserLoginCodeByID($accntnumber,$subaccnt,$randcode)
    {
      //var_dump($accntnumber);
      //var_dump($subaccnt);
     // var_dump($randcode);
        $sql="UPDATE account_user SET logincode=:logcode WHERE accountnumber=:accnt AND unique_ID=:subaccnt";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accntnumber);
        $stmnt->bindParam(":subaccnt",$subaccnt);
        $stmnt->bindParam(":logcode",$randcode);
        
        try{
            if($stmnt->execute())
            {
                $update="Updated";
               // var_dump($update);
                $msg = array("status"=>"200","message"=>$update);
                return $msg;
            }
        }
        catch(PDOException $e){
        $msg = array("count"=>0,"error"=>$e->__toString());
           return $msg;
       }
   }
    public function getAccountType($acntnumber,$subaccntnumber,$npinumber)
    {
     $results="";
     $sql2="SELECT * FROM `account_user` WHERE accountnumber=:accnt AND unique_id=:subacnt";//328389345"
     $stmnt2 = $this->con->prepare($sql2);
     $stmnt2->bindParam(":accnt",$acntnumber);
     $stmnt2->bindParam(":subacnt",$subaccntnumber);
     try{
        if($stmnt2->execute())
        {

          $records2 = $stmnt2->fetchAll();
          $results = $records2[0]["account_type"];
        
        }
     }
    catch(PDOException $e)
    {
      $msg = array("error"=>"700-sql","message"=>$e->__toString());
      return $msg;
    }
     if($results=="Provider")
    {
      $sql="SELECT * FROM `patients` LEFT JOIN providerassignment ON patients.psersonalID = providerassignment.patient_id WHERE providerassignment.providerid=:npi AND providerassignment.assignment_date IS NOT NULL AND providerassignment.discharge_date IS NULL";
    }
    elseif($results=="Registered Nurse")
    {
     // var_dump("RN");
      //var_dump($npinumber);
      $sql="SELECT patients.accountnumber,patients.psersonalID AS patient_id,patients.first_name, patients.lastname AS patients_lname, patients.date_of_birth,patients.socnum,patients.gender,patients.contact_number,
      patients.email,patients.address,patients.city,patients.state,patients.zip,patients.emergency_contactname,patients.emergency_contact_number,
      patients.matial_status,patients.allegeries,patients.accntstatus,nurseassignment.assignment_date FROM `patients` LEFT JOIN nurseassignment ON patients.psersonalID = nurseassignment.patient_id WHERE nurseassignment.nurse_id=:npi";
    }
      //$sql="SELECT * FROM `patients` LEFT JOIN providerassignment ON patients.psersonalID = providerassignment.patient_id WHERE providerassignment.providerid=:npi";
      
      $stmnt = $this->con->prepare($sql);
      $stmnt->bindParam(":npi",$npinumber);
     // $stmnt->bindParam(":accnt",$acntnumber)
          try{
            if($stmnt->execute())
            {

              $records = $stmnt->fetchAll();
              //var_dump($records);
              $pendorders = array("status"=>"200 Successfull","records"=>$records,"accounttype"=>$results);
              return $pendorders;
            }
        }
        catch(PDOException $e)
        {
          $msg = array("error"=>"700-sql","message"=>$e->__toString());
          return $msg;
        }
    }
    public function GetAccountPendingOrdersByID($accountID,$orderid,$patientid)
    {
      //var_dump($patientid);
      $sql="SELECT * FROM `orders` INNER JOIN `patients` ON `orders`.`patientid` = `patients`.`psersonalID` WHERE 
      `orders`.accountnumber =:accntID AND `orders`.ordernumber=:ordnum AND `orders`.patientid=:patid AND `orders`.`status`='Pending' OR
      `orders`.`status`='Active-Pending'";
      $stmnt = $this->con->prepare($sql);
      $stmnt->bindParam(":accntID",$accountID);
      $stmnt->bindParam(":ordnum",$orderid);
      $stmnt->bindParam(":patid",$patientid);

      try{
          if($stmnt->execute())
          {

            $records = $stmnt->fetchAll();
            $pendorders = array("status"=>"200 Successfull","action"=>"Get Pending Order Data","orders"=>$records);
            return $pendorders;
          }
      }
      catch(PDOException $e)
      {
        $msg = array("error"=>"700-sql","message"=>$e->__toString());
        return $msg;
      }
    }
    public function GeOrderInfoByAccountNOrderID($accountID,$orderid)
    {
      $sql="SELECT * FROM `orders` INNER JOIN `patients` on `patients`.`psersonalID` = `orders`.`patientid` WHERE `orders`.`accountnumber` =:accntID AND `orders`.`ordernumber`=:ordid ";
      $stmnt = $this->con->prepare($sql);
      $stmnt->bindParam(":accntID",$accountID);
      $stmnt->bindParam(":ordid",$orderid);
      try{
          if($stmnt->execute())
          {

            $records = $stmnt->fetchAll();
            $numofpending = count($records);
            $pendorders = array("status"=>"200 Successfull","numofreords"=>$numofpending,"orders"=>$records);
            return $pendorders;
          }
      }
      catch(PDOException $e)
      {
        $msg = array("error"=>"700-sql","message"=>$e->__toString());
        return $msg;
      }

    }
    public function GetAllPendingOrdersByPatientID($accountID,$pid)
    {
      $sql="SELECT * FROM `orders` WHERE accountnumber =:accntID AND patientid=:pid AND `status`='Pending' OR `status`='Active-Pending'";
      $stmnt = $this->con->prepare($sql);
      $stmnt->bindParam(":accntID",$accountID);
      $stmnt->bindParam(":pid",$pid);
      try{
          if($stmnt->execute())
          {

            $records = $stmnt->fetchAll();
            $numofpending = count($records);
            $pendorders = array("status"=>"200 Successfull","numofpending"=>$numofpending,"orders"=>$records);
            return $pendorders;
          }
      }
      catch(PDOException $e)
      {
        $msg = array("error"=>"700-sql","message"=>$e->__toString());
        return $msg;
      }

    }
    public function GetAccountPendingOrders($accountID)
    {
      $sql="SELECT * FROM `orders` WHERE accountnumber =:accntID AND `status`='Pending'";
      $stmnt = $this->con->prepare($sql);
      $stmnt->bindParam(":accntID",$accountID);
      try{
          if($stmnt->execute())
          {

            $records = $stmnt->fetchAll();
            $numofpending = count($records);
            $pendorders = array("status"=>"200 Successfull","numofpending"=>$numofpending,"orders"=>$records);
            return $pendorders;
          }
      }
      catch(PDOException $e)
      {
        $msg = array("error"=>"700-sql","message"=>$e->__toString());
        return $msg;
      }

    }
    public function findProfLicense($accntnumber,$subaccntnumber)
    {
      $sql="SELECT * FROM account_user WHERE accountnumber=:accntnumber AND unique_ID=:unID";
      $stmnt = $this->con->prepare($sql);
      $stmnt->bindParam(":accntnumber",$accntnumber);
      $stmnt->bindParam(":unID",$subaccntnumber);
      try{
         if($stmnt->execute())
         {
          //sql statment successful
           $records = $stmnt->fetchAll();
           $proflicense = $records[0]["proffesionalLicense"];
           $licensear = array("status"=>"200 Successful","license"=>$proflicense,"records"=>$records);
           return $licensear;
         }
      }
      catch(PDOException $e)
      {
        $msg = array("error"=>"700-sql","message"=>$e->__toString());
        return $msg;
      }
    }
    public function OrderFilter($ordertype,$orderstatus,$accountType,$dtRange,$srchpatientname,$proflicense="",$accountnumber)
    {
       

      //var_dump($dtRange);
      if($dtRange =="")
      {
        //lets set the start date and last date variable to empty to avoid Exploding an empty string and that skews 
        //conditional logic
        $stdate="";
        $enddate="";
      }
      else{
          $xpdt = explode(" ",$dtRange);
          //now that the value wasn't empty lets assign values from the exploded array
          $stdate = $xpdt[0];
          $enddate = $xpdt[1];
      }
    
      
     // var_dump($stdate);
     // var_dump($enddate);
     // var_dump($srchpatientname);
      $expname = explode(" ",$srchpatientname);
      $fname = $expname[0];
      $lname = $expname[1];
     // var_dump($fname);
    //  var_dump($lname);
      $status=$orderstatus;
      $ordtype=$ordertype;
    //  var_dump($ordtype);
      $parmtype="";
      if($stdate !="" && $enddate !="" && $fname =="" && $lname=="" && $ordtype=="" && $status=="")
      {
       // var_dump("Should be the query to search by date");
        //var_dump($stdate." ".$enddate);
        //Lets use the ordtype variable to adjust the sql statement on which table should be used 
       
       switch($accountType)
       {
         case"Registered Nurse":
          {
            $sql="SELECT * FROM `orders` INNER JOIN  patients ON orders.patientid = patients.psersonalID
            INNER JOIN nurseassignments ON orders.patientid = nurseassignment.patient_id 
            WHERE orders.orderdate >=:startdate  AND orders.orderdate <=:enddate AND nurseassignment.nurse_id=:nrsid AND nurseassignment.accountnumber=:accntid ORDER BY orders.orderdate ASC";
            $parmtype="SearchByDateRange";
            break;
          }
          case"Provider":
            {
            
              $sql="SELECT * FROM `orders` INNER JOIN  patients ON orders.patientid = patients.psersonalID
              INNER JOIN providerassignment ON orders.patientid = providerassignment.patient_id 
              WHERE orders.orderdate >=:startdate  AND orders.orderdate <=:enddate AND providerassignment.providerid=:prvid AND providerassignment.accountnumber=:accntid ORDER BY orders.orderdate ASC";
              $parmtype="SearchByDateRange";
             
              break;
            }
       }
       
        
      }
      elseif($fname !='' && $lname!='' && $stdate=='' && $enddate=='' && $ordtype=='' && $status=='')
      {
       // var_dump("SearchByPatientName");
       switch($accountType)
       {
        case"Registered Nurse":
          {
            $sql="SELECT * FROM `orders` INNER JOIN  patients ON orders.patientid = patients.psersonalID
            INNER JOIN nurseassignment ON orders.patientid = nurseassignment.patient_id
            WHERE patients.first_name LIKE '%$fname%' OR patients.lastname LIKE '%$lname%' AND nurseassignment.nurse_id=:nrsid AND nurseassignment.acountnumber=:accnt ORDER BY orders.orderdate ASC";
           $parmtype="SearchByPatientName";
            break;
          }
          case"Provider":
            {
              $sql="SELECT * FROM `orders` INNER JOIN  patients ON orders.patientid = patients.psersonalID
              INNER JOIN providerassignment ON orders.patientid = providerassignment.patient_id
              WHERE patients.first_name LIKE '%$fname%' OR patients.lastname LIKE '%$lname%' AND providerassignment.providerid=:prvid AND providerassignment.accountnumber=:accnt ORDER BY orders.orderdate ASC";
             $parmtype="SearchByPatientName";
              break;
            }
       }
       
        //var_dump($nurseid);
        //var_dump($sql);
      }
      elseif($ordtype!='' && $fname =='' && $lname=='' && $stdate=='' && $enddate=='' && $status=='')
      {
        //var_dump("Search OrderTYpe");
        switch($accountType)
        {
          case"Registered Nurse":
            {
              $sql="SELECT * FROM `orders` INNER JOIN  patients ON orders.patientid = patients.psersonalID 
               INNER JOIN nurseassignment ON orders.patientid = nurseassignment.patient_id
              WHERE orders.ordertype=:ordtype AND nurseassignment.nurse_id=:nrsid AND nurseassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
              $parmtype="SearchByOrderType";
              break;
            }
            case"Provider":
              {
                $sql="SELECT * FROM `orders` INNER JOIN  patients ON orders.patientid = patients.psersonalID 
               INNER JOIN providerassignment ON orders.patientid = providerassignment.patient_id
              WHERE orders.ordertype=:ordtype AND providerassignment.providerid=:prvid AND providerassignment.accountnumber=:accnt ORDER BY `orderdate` ASC";
              $parmtype="SearchByOrderType";
                break;
              }
        }
        //$sql="SELECT * FROM `orders` INNER JOIN  patients ON orders.patientid = patients.psersonalID WHERE orders.ordertype=:ordtype";
       // $parmtype="SearchByOrderType";
      }
      elseif($status!='' && $fname =='' && $lname=='' && $stdate=='' && $enddate=='' && $ordtype=='')
      {
        //var_dump("Search Order Status");
        switch($accountType)
        {
          case"Registered Nurse":
          {
            $sql="SELECT * FROM `orders` INNER JOIN  patients ON orders.patientid = patients.psersonalID 
            INNER JOIN nurseassignment ON orders.patientid = nurseassignment.nurse_id
            WHERE orders.status=:stat AND nurseassignment.nurse_id=:nrsid AND nurseassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
        $parmtype="SearchByOrderStatus";
            break;
          }
          case"Provider":
          {
            $sql="SELECT * FROM `orders` INNER JOIN  patients ON orders.patientid = patients.psersonalID 
            INNER JOIN providerassignment ON orders.patientid = providerassignment.providerid
            WHERE orders.status=:stat AND providerassignment.providerid=:prvid AND providerassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
            $parmtype="SearchByOrderStatus";
           break;
          }
        }
        
      }
      elseif($status!='' && $fname !='' && $lname !='' && $stdate!='' && $enddate!='' && $ordtype!='')
      {
        switch($accountType)
        {
          case"Registered Nurse":
          {
            $sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID 
            INNER JOIN nurseassignment ON orders.patientid = nurseassignment.nurse_id 
            WHERE patients.first_name LIKE '%:fname%' AND lastname LIKE '%:lname%' AND orders.status=:stat 
        AND orders.ordertype=:ordtype AND  orders.orderdate >=:stdate AND orders.orderdate <=:enddate 
        AND nurseassignment.nurse_id=:nrsid AND nurseassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
        $parmtype ="SearchByAll";
            break;
          }
          case"Provider":
          {
            $sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID 
            INNER JOIN providerassignment ON orders.patientid = providerassignment.providerid 
            WHERE patients.first_name LIKE '%:fname%' AND lastname LIKE '%:lname%' AND orders.status=:stat 
        AND orders.ordertype=:ordtype AND  orders.orderdate >=:stdate AND orders.orderdate <=:enddate 
        AND  providerassignment.providerid=:prvid AND providerassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
        $parmtype ="SearchByAll";
           break;
          }
        }
        /*$sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID WHERE patients.first_name LIKE '%:fname%' AND lastname LIKE '%:lname%' AND orders.status=:stat 
        AND orders.ordertype=:ordtype AND  orders.orderdate >=:stdate AND orders.orderdate <=:enddate";
        $parmtype ="SearchByAll";*/ //the original query
      }
      elseif($ordtype!='' && $fname =='' && $lname=='' && $stdate=='' && $enddate=='' && $status!='')
      {
        switch($accountType)
        {
          case"Registered Nurse":
          {
            $sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID 
            INNER JOIN nurseassignment ON orders.patientid = nurseassignment.nurse_id 
            WHERE orders.status=:stat AND orders.orderdate >=:stdate AND orders.orderdate <=:enddate
            AND nurseassignment.nurse_id=:nrsid AND nurseassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
            $parmtype="SearchByOrderTypeAndStaus";

           
            break;
          }
          case"Provider":
          {
            $sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID 
            INNER JOIN providerassignment ON orders.patientid = providerassignment.providerid 
            WHERE orders.status=:stat AND orders.orderdate >=:stdate AND orders.orderdate <=:enddate
            AND providerassignment.providerid=:prvid AND providerassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
             $parmtype="SearchByOrderTypeAndStaus";
           break;
          }
        }
        //var_dump("Search OrderTYpe");
       /* $sql="SELECT * FROM `orders` INNER JOIN  patients ON orders.patientid = patients.psersonalID WHERE orders.ordertype=:ordtype AND orders.status=:stat";
        $parmtype="SearchByOrderTypeAndStaus"; */
      }
      elseif($status !="" && $stdate !="" && $enddate !="" && $ordtype=="")//run status and date range lokup
      {
        switch($accountType)
        {
          case"Registered Nurse":
          {
            $sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID
            INNER JOIN nurseassignment ON orders.patientid = nurseassignment.nurse_id
             WHERE orders.status=:stat AND orders.orderdate >=:stdate AND orders.orderdate <=:enddate
             AND nurseassignment.nurse_id=:nrsid AND nurseassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
            $parmtype ="ComboSearchStatusNDtRange";
            break;
          }
          case"Provider":
          {
            $sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID
            INNER JOIN providerassignment ON orders.patientid = providerassignment.providerid
             WHERE orders.status=:stat AND orders.orderdate BETWEEN :stdate AND :enddate
             AND providerassignment.providerid=:prvid AND providerassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
            $parmtype ="ComboSearchStatusNDtRange";
           break;
          }
        }
       // var_dump("ComboSearchStatusNDtRange");
        /*$sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID WHERE orders.status=:stat AND orders.orderdate >=:stdate AND orders.orderdate <=:enddate";
        $parmtype ="ComboSearchStatusNDtRange"; */
      }
      elseif($status !="" && $stdate !="" && $enddate !="" && $ordtype !="")
      {
        switch($accountType)
        {
          case"Registered Nurse":
          {
            $sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID
            INNER JOIN nurseassignment ON orders.patientid = nurseassignment.nurse_id
            WHERE orders.status=:stat AND orders.orderdate >=:stdate AND orders.orderdate <=:enddate AND orders.ordertype=:ordtype
            AND nurseassignment.nurse_id=:nrsid AND nurseassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
        $parmtype="ComboSearchStatusDtRangeNOrderType";
            break;
          }
          case"Provider":
          {
            $sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID
            INNER JOIN providerassignment ON orders.patientid = provierassignment.providerid
             WHERE orders.status=:stat AND orders.orderdate >=:stdate AND orders.orderdate <=:enddate AND orders.ordertype=:ordtype
            AND providerassignment.providerid=:prvid AND providerassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
        $parmtype="ComboSearchStatusDtRangeNOrderType";
           break;
          }
        }
        //var_dump("ComboSearchStatus,DtRange, Ordertyep");
       /* $sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID WHERE orders.status=:stat AND orders.orderdate >=:stdate AND orders.orderdate <=:enddate AND orders.ordertype=:ordtype";
        $parmtype="ComboSearchStatusDtRangeNOrderType";*/
      }
      elseif($fname !='' && $lname!='' && $stdate=='' && $enddate=='' && $ordtype!='' && $status=='')
      {
        switch($accountType)
        {
          case"Registered Nurse":
          {
            $sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID
            INNER JOIN nurseassignment ON orders.patientid = nurseassignment.nurse_id
            WHERE  patients.first_name LIKE '%$fname%' OR patients.lastname LIKE '%$lname%' AND orders.ordertype=:ordtype
            AND nurseassignment.nurse_id=:nrsid AND nurseassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
         $parmtype="SearchByPatientNameNOrdertype";
            break;
          }
          case"Provider":
          {
            $sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID
            INNER JOIN providerassignment ON orders.patientid = provierassignment.providerid
            WHERE  patients.first_name LIKE '%$fname%' OR patients.lastname LIKE '%$lname%' AND orders.ordertype=:ordtype
            AND providerassignment.providerid=:prvid AND providerassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
         $parmtype="SearchByPatientNameNOrdertype";
           break;
          }
        }
      //  var_dump("SearchByPatientName ANd Order Type");
       
       /* $sql="SELECT * FROM `orders` INNER JOIN  patients ON orders.patientid = patients.psersonalID WHERE patients.first_name LIKE '%$fname%' OR patients.lastname LIKE '%$lname%' AND orders.ordertype=:ordtype ORDER BY orders.orderdate ASC";
        $parmtype="SearchByPatientNameNOrdertype"; */
        //var_dump($sql);
      }
      elseif($fname !="" || $lname !="" && $ordtype !="" && $orderstatus!="" && $stdate=="" && $enddate =="")
      {
        switch($accountType)
        {
          case"Registered Nurse":
          {
            $sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID
            INNER JOIN nurseassignment ON orders.patientid = nurseassignment.nurse_id
            WHERE  patients.first_name LIKE '%$fname%' OR patients.lastname LIKE '%$lname%' AND orders.ordertype=:ordtype AND orders.status=:stat
            AND nurseassignment.nurse_id=:nrsid AND nurseassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
         $parmtype="ComboSearchNameOrderTypeStatus";
            break;
          }
          case"Provider":
          {
            $sql="SELECT * FROM `orders` INNER JOIN patients on orders.patientid = patients.psersonalID
            INNER JOIN providerassignment ON orders.patientid = providerassignment.providerid
            WHERE  patients.first_name LIKE '%$fname%' OR patients.lastname LIKE '%$lname%' AND orders.ordertype=:ordtype AND orders.status=:stat
            AND providerassignment.providerid=:prvid AND providerassignment.accountnumber=:accnt ORDER BY `orders`.`orderdate` ASC";
         $parmtype="ComboSearchNameOrderTypeStatus";
           break;
          }
        }
        //var_dump("SearchByPatientNameNOrderTypeNStatus");
       /* $sql="SELECT * FROM `orders` INNER JOIN  patients ON orders.patientid = patients.psersonalID WHERE patients.first_name LIKE '%$fname%' OR patients.lastname LIKE '%$lname%' AND orders.ordertype=:ordtype AND orders.status=:stat ORDER BY orders.orderdate ASC";
        $parmtype="ComboSearchNameOrderTypeStatus"; */
      }
      //var_dump($sql);
      //now run a switch case statement and evaluate the case result to then take the next option. 
      switch($parmtype)
      {
        case"SearchByDateRange":
          {
            //var_dump("Inside the SearchByDateRange");
            $stmnt = $this->con->prepare($sql);
            $stmnt->bindParam(":startdate",$stdate);
            $stmnt->bindParam(":enddate",$enddate);
            $stmnt->bindParam(":accntid",$accountnumber);
            if($accountType=="Registerd Nurse")
            {
              $stmnt->bindParam(":nrsid",$proflicense);
            }
            if($accountType=="Provider")
            {
              $stmnt->bindParam(":prvid",$proflicense);
              //var_dump($proflicense);
            }
           
            //lets try and run the statement 
            try{
               if($stmnt->execute())
               {
                //sql query was successfull
                $records = $stmnt->fetchAll();
                $msg = array("status"=>"200","records"=>$records);
              
                return $msg;
               }
            }
            catch(PDOException $e)
            {
              $msg =array("status"=>"700-sql","message"=>$e->__toString());
             // var_dump($msg);
              return $msg;
            }
            break;
          }
          case"SearchByPatientName":
            {
              $stmnt = $this->con->prepare($sql);
             // $stmnt->bindParam(":nrsid",$nurseid);
              $stmnt->bindParam(":accnt",$accountnumber);
             // $stmnt->bindParam(":fname",$firstname);
              //$stmnt->bindParam(":lname",$lastname);
              if($accountType=="Registered Nurse")
              {
                $stmnt->bindParam(":nrsid",$proflicense);
              }
              if($accountType=="Provider")
              {
                $stmnt->bindParam(":prvid",$proflicense);
              }
              try{
                if($stmnt->execute())
                {
                  $records = $stmnt->fetchAll();
                 
                  $msg = array("status"=>"200","records"=>$records);
                  return $msg;
                }
              }
              catch(PDOException $e)
              {
                $msg = array("status"=>"700-sql","error"=>$e->__toString());
                return $msg;
              }
              break;
            }
            case"SearchByOrderStatus":
              {
               // var_dump("Runing SearchByOrderStatus");
                $stmnt = $this->con->prepare($sql);
                $stmnt->bindParam(":stat",$orderstatus);
               $stmnt->bindParam(":accnt",$accountnumber);
               if($accountType=="Registered Nurse")
               {
                $stmnt->bindParam(":nrsid",$proflicense);
               }
               if($accountType=="Provider")
               {
                $stmnt->bindParam("prvid",$proflicense);
               }
                try{
                  if($stmnt->execute())
                  {
                    $records = $stmnt->fetchAll();
                   
                    $msg = array("status"=>"200","records"=>$records);
                   // var_dump($msg);
                    return $msg;
                  }
                }
                catch(PDOException $e)
                {
                  $msg = array("status"=>"700-sql","error"=>$e->__toString());
                 // var_dump($msg);
                  return $msg;
                }
                break; 
              }
              case"SearchByOrderType":
                {
                  $stmnt = $this->con->prepare($sql);
                $stmnt->bindParam(":ordtype",$ordertype);
                $stmnt->bindParam(":accnt",$accountnumber);
                if($accountType=="Registred Nurse")
                {
                  $stmnt->bindParam(":nrsid",$proflicense);
                }
                if($accountType=="Provider")
                {
                  $stmnt->bindParam(":prvid",$proflicense);
                }
                try{
                  if($stmnt->execute())
                  {
                   // var_dump("SearchbyOrderType Successfull");
                    $records = $stmnt->fetchAll();
                   
                    $msg = array("status"=>"200","records"=>$records);
                    return $msg;
                  }
                }
                catch(PDOException $e)
                {
                  $msg = array("status"=>"700-sql","error"=>$e->__toString());
                  return $msg;
                }
                  break;
                }
              case"ComboSearchStatusNDtRange":
                {
                  //var_dump("ComboSearch");
                  $stmnt = $this->con->prepare($sql);
                  $stmnt->bindParam(":stat",$orderstatus);
                  $stmnt->bindParam(":stdate",$stdate);
                  $stmnt->bindParam(":enddate",$enddate);
                  $stmnt->bindParam(":accnt",$accountnumber);
                  if($accountType=="Registered Nurse")
                  {
                    $stmnt->bindParam(":nrsid",$proflicense);
                  }
                  if($accountType=="Provider")
                  {
                    $stmnt->bindParam(":prvid",$proflicense);
                  }
                  try{
                    if($stmnt->execute())
                    {

                     // var_dump("ComboSearchStatusnDtRange Successfull");
                      $records = $stmnt->fetchAll();
                     // var_dump($records);
                      $msg = array("status"=>"200","records"=>$records);
                      return $msg;
                    }
                  }
                  catch(PDOException $e)
                  {
                    $msg = array("status"=>"700-sql","error"=>$e->__toString());
                   // var_dump($msg);
                    return $msg;
                  }
                  break;
                }
                case"SearchByOrderTypeAndStaus":
                {
                  $stmnt = $this->con->prepare($sql);
                  $stmnt->bindParam(":stat",$orderstatus);
                  $stmnt->bindParam(":ordtype",$ordertype);
                 $stmnt->bindParam(":accnt",$accountnumber);
                 if($accountType=="Registered Nurse")
                 {
                   $stmnt->bindParam(":nrsid",$proflicense);
                 }
                 if($accountType=="Provider")
                 {
                   $stmnt->bindParam(":prvid",$proflicense);
                 }
                  try{
                    if($stmnt->execute())
                    {

                     // var_dump("ComboSearchByOrderTypeAndStatus Successfull");
                      $records = $stmnt->fetchAll();
                    //  var_dump($records);
                      $msg = array("status"=>"200","records"=>$records);
                      return $msg;
                    }
                  }
                  catch(PDOException $e)
                  {
                    $msg = array("status"=>"700-sql","error"=>$e->__toString());
                    //var_dump($msg);
                    return $msg;
                  }
                  break;
                }
                case"ComboSearchNameOrderTypeStatus":
                {
                //var_dump("ComboSearchNameOrderTypeStatus");
                    $stmnt = $this->con->prepare($sql);
                    $stmnt->bindParam(":stat",$orderstatus);
                  //  $stmnt->bindParam(":fname",$fname);
                   // $stmnt->bindParam(":lname",$lname);
                   $stmnt->bindParam(":accnt",$accountnumber);
                    $stmnt->bindParam(":ordtype",$ordertype);
                    if($accountType=="Registered Nurse")
                    {
                      $stmnt->bindParam(":nrsid",$proflicense);
                    }
                    if($accountType=="Provider")
                    {
                      $stmnt->bindParam(":prvid",$proflicense);
                    }
                    try{
                      if($stmnt->execute())
                      {
  
                       // var_dump("ComboSearchStatusnDtRange &Ordertype Successfull");
                        $records = $stmnt->fetchAll();
                       // var_dump($records);
                        $msg = array("status"=>"200","records"=>$records);
                        return $msg;
                      }
                    }
                    catch(PDOException $e)
                    {
                      $msg = array("status"=>"700-sql","error"=>$e->__toString());
                      //var_dump($msg);
                      return $msg;
                    }
                    break;
                  }
                case"SearchByPatientNameNOrdertype":
                  {
                    //var_dump("ComboSearchPatientName_R_OrderType");
                    $stmnt = $this->con->prepare($sql);
                  //  $stmnt->bindParam(":fname",$fname);
                    $stmnt->bindParam(":accnt",$accountnumber);
                    $stmnt->bindParam(":ordtype",$ordertype);
                    if($accountType=="Registered Nurse")
                    {
                      $stmnt->bindParam(":nrsid",$proflicense);
                    }
                    if($accountType=="Provider")
                    {
                      $stmnt->bindParam(":prvid",$proflicense);
                    }
                    try{
                      if($stmnt->execute())
                      {
  
                       // var_dump("ComboSearchStatusnDtRange &Ordertype Successfull");
                        $records = $stmnt->fetchAll();
                       // var_dump($records);
                        $msg = array("status"=>"200","records"=>$records);
                        return $msg;
                      }
                    }
                    catch(PDOException $e)
                    {
                      $msg = array("status"=>"700-sql","error"=>$e->__toString());
                     // var_dump($msg);
                      return $msg;
                    }
                    break;
                  }
                case"ComboSearchStatusDtRangeNOrderType":
                  {
                    //var_dump("ComboSearch-DTRange ANd Type");
                    $stmnt = $this->con->prepare($sql);
                    $stmnt->bindParam(":stat",$orderstatus);
                    $stmnt->bindParam(":stdate",$stdate);
                    $stmnt->bindParam(":enddate",$enddate);
                    $stmnt->bindParam(":ordtype",$ordertype);
                    $stmnt->bindParam(":accnt",$accountnumber);
                    if($accountType=="Registered Nurse")
                    {
                      $stmnt->bindParam(":nrsid",$proflicense);
                    }
                    if($accountType=="Provider")
                    {
                      $stmnt->bindParam(":prvid",$proflicense);
                    }
                    try{
                      if($stmnt->execute())
                      {
  
                       // var_dump("ComboSearchStatusnDtRange &Ordertype Successfull");
                        $records = $stmnt->fetchAll();
                      
                        $msg = array("status"=>"200","records"=>$records);
                        return $msg;
                      }
                    }
                    catch(PDOException $e)
                    {
                      $msg = array("status"=>"700-sql","error"=>$e->__toString());
                     // var_dump($msg);
                      return $msg;
                    }
                    break;
                  }
              case"SearchByAll":
              {
                $stmnt = $this->con->prepare($sql);
                $stmnt->bindParam(":stat",$orderstatus);
                $stmnt->bindParam(":fname",$firstname);
                $stmnt->bindParam(":lname",$lastname);
                $stmnt->bindParam(":stat",$orderstatus);
                $stmnt->bindParam(":ordtype",$ordtype);
                $stmnt->bindParam(":stdate",$stdate);
                $stmnt->bindParam(":enddate",$enddate);
                $stmnt->bindParam(":accnttype",$accountType);
                $stmnt->bindParam(":accnt",$accountnumber);
                if($accountType=="Registered Nurse")
                {
                  $stmnt->bindParam(":nrsid",$proflicense);
                }
                if($accountType=="Provider")
                {
                  $stmnt->bindParam(":prvid",$proflicense);
                }
                try{
                  if($stmnt->execute())
                  {
                    $records = $stmnt->fetchAll();
                    $msg = array("status"=>"200","records"=>$records);
                    return $msg;
                  }
                }
                catch(PDOException $e)
                {
                  $msg = array("status"=>"700-sql","error"=>$e->__toString());
                  return $msg;
                }
              }
      }
     
    }
    public function InsertOrders($accnt,$ordnum,$orderdate,$patientid,$ordertime,$ordertype,$abndelivered,$ordReadback,$primphysician,$sndphysician,$email,$npi,$address,$ordphone,$ordfax,$sendtophyscians,$woundcare,$verbalorder,$verborderDate,$verbalorderTime,$ordMedication,$ordSupplies,$ordValue,$ordDiagnosis,$ordDescription,$ostatus,
    $writer,$nursesignature,$nursesigDate,$providersignature,$provsigDate)
    {
      //var_dump($providersignature);
      //var_dump($nursesignature);
        $sql="Insert Into `orders` (accountnumber,ordernumber,patientid,orderdate,ordertime,ordertype,abndelivery,readorderback,primary_physician,sec_physician,email,npinumber,address,phone,fax,sendtophys,woundcare,verbalorder,verborder_date,verborder_time,medication,supplies,valuesign,diagnosis,orderdescription,status,writer,nursesignature,nursedatesig,
        providersignature,providerdatesig)"
       ." Values(:accnt,:ordnumber,:patientid,:ordDate,:ordTime,:ordType,:abdeliv,:ordReadback,:primphysician,:secphysician,:email,:npinum,:ordAddress,:ordPhone,:ordFax,:sendphys,:woundcare,:verborder,:verborddate,:verbordtime,:medication,:supplies,:valsign,:orddiagnosis,:ordescrip,:ordstatus,:writer,:nrssig,:nrssigdt,:provsig,:provsigdt)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accnt);
        $stmnt->bindParam(":ordnumber",$ordnum);
        $stmnt->bindParam(":patientid",$patientid);
        $stmnt->bindParam(":ordDate",$orderdate);
        $stmnt->bindParam(":ordTime",$ordertime);
        $stmnt->bindParam(":ordType",$ordertype);
        $stmnt->bindParam(":abdeliv",$abndelivered);
        $stmnt->bindParam(":ordReadback",$ordReadback);
        $stmnt->bindParam(":primphysician",$primphysician);
        $stmnt->bindParam(":secphysician", $sndphysician);
        $stmnt->bindParam(":email", $email);
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
        $stmnt->bindParam(":writer",$writer);
        $stmnt->bindParam(":nrssig",$nursesignature);
        $stmnt->bindParam(":nrssigdt",$nursesigDate);
        $stmnt->bindParam(":provsig",$providersignature);
        $stmnt->bindParam(":provsigdt",$provsigDate);
       /* switch($ordertype)
        {
            case "Physicians Order":
            {
                $ostatus="Active";
                break;
            }
            case "Nurses Order":
            {
                $ostatus="Pending";
                break;
            }

        }*/
        $stmnt->bindParam(":ordstatus",$ostatus);
        try{
            if($stmnt->execute())
			{
                //lets Now lets update and Pending Med or Diagnosis 
                /*were going to make all med and diagnosis status active because if they are left in Pending due to Nurse Order, those records will be remove when the app loads and looks to remove
                any Meds or Diagnosis that were left in Pending status and not having Order with an assignd number submitted.*/
                $meddiagstatus ="Active";
                $updatePendingMed = $this->UpdatePendingMedications($ordnum,$meddiagstatus);
                $updatePendingDiag = $this->UpdatePendingDiagnosis($ordnum,$meddiagstatus);
                if($updatePendingMed["status"]=="Updated" && $updatePendingDiag["status"]=="Updated")
                {
                    $msg = "Inserted";
                    $result = array("result"=>$msg);
                    return json_encode($result);
                }
            }
        }
        catch(PDOException $e )
        {
            $result = $e->__toString();
            return $result;
        }
       
    }
    //Update Orders Function is going to go here 
    public function updateOrders($accnt,$ordnum,$orderdate,$patientid,$ordertime,$ordertype,$abndelivered,$ordReadback,$primphysician,$sndphysician,$email,$npi,$address,$ordphone,$ordfax,$sendtophyscians,$woundcare,$verbalorder,$verborderDate,$verbalorderTime,$ordMedication,$ordSupplies,$ordValue,$ordDiagnosis,$ordDescription,$ostatus,
    $writer,$nursesignature,$nursesigDate,$providersignature,$provsigDate)
    {
      //var_dump($providersignature);
      //var_dump($nursesignature);
        $sql="UPDATE `orders` SET accountnumber=:accnt,ordernumber=:ordnumber,patientid=:patientid,orderdate=:ordDate,
        ordertime=:ordTime,ordertype=:ordType,abndelivery=:abdeliv,readorderback=:ordReadback,primary_physician=:primphysician,
        sec_physician=:secphysician,email=:email,npinumber=:npinum,`address`=:ordAddress,phone=:ordPhone,fax=:ordFax,sendtophys=:sendphys,
        woundcare=:woundcare,verbalorder=:verborder,verborder_date=:verborddate,verborder_time=:verbordtime,medication=:medication,supplies=:supplies,
        valuesign=:valsign,diagnosis=:orddiagnosis,orderdescription=:ordescrip,`status`=:ordstatus,writer=:writer,nursesignature=:nrssig,nursedatesig=:nrssigdt,
        providersignature=:provsig,providerdatesig=:provsigdt WHERE ordernumber=:ordnumber AND accountnumber=:accnt";
       
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":accnt",$accnt);
        $stmnt->bindParam(":ordnumber",$ordnum);
        $stmnt->bindParam(":patientid",$patientid);
        $stmnt->bindParam(":ordDate",$orderdate);
        $stmnt->bindParam(":ordTime",$ordertime);
        $stmnt->bindParam(":ordType",$ordertype);
        $stmnt->bindParam(":abdeliv",$abndelivered);
        $stmnt->bindParam(":ordReadback",$ordReadback);
        $stmnt->bindParam(":primphysician",$primphysician);
        $stmnt->bindParam(":secphysician", $sndphysician);
        $stmnt->bindParam(":email", $email);
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
        $stmnt->bindParam(":writer",$writer);
        $stmnt->bindParam(":nrssig",$nursesignature);
        $stmnt->bindParam(":nrssigdt",$nursesigDate);
        $stmnt->bindParam(":provsig",$providersignature);
        $stmnt->bindParam(":provsigdt",$provsigDate);
        $stmnt->bindParam(":ordstatus",$ostatus);
        try{
            if($stmnt->execute())
			      {
              /* Jan 4 2025 We need to revaulate this section because if the order is pending we may neeed to be able to pull medication back in as 
              * as well and allow the provider of choice the ability to update medications and proceedures AND then once the over all Order is submitted
              * the Medication and Procedure tables should be updated if they were added to the order
              * Action for Now: We are going to send back an updated Message status that resides inside of array
              */
                //lets Now lets update and Pending Med or Diagnosis 
                /*were going to make all med and diagnosis status active because if they are left in Pending due to Nurse Order, those records will be remove when the app loads and looks to remove
                any Meds or Diagnosis that were left in Pending status and not having Order with an assignd number submitted.*/
                /*$meddiagstatus ="Active";
                $updatePendingMed = $this->UpdatePendingMedications($ordnum,$meddiagstatus);
                $updatePendingDiag = $this->UpdatePendingDiagnosis($ordnum,$meddiagstatus);
                if($updatePendingMed["status"]=="Updated" && $updatePendingDiag["status"]=="Updated")
                {
                    $msg = "Inserted";
                    $result = array("result"=>$msg);
                    return json_encode($result);
                } */
                $msg ="Updated";
                $result = array("result"=>$msg);
                return json_encode($result);
            }
        }
        catch(PDOException $e )
        {
            $result = $e->__toString();
            return $result;
        }
       
    }
    //end Update Orders Function 
    public function ProcessDiagnosisInfo($patientid,$ordnum,$diagtype,$diagcode,$proccode,$systemRating,$diagexacerbation,$diagdate,$endDate,$status,$instruction,$writer)
    {

        $process = $this->ProcessDiagnosis($patientid,$ordnum,$diagtype,$diagcode,$proccode,$procShorthand,$systemRating,$diagexacerbation,$diagdate,$endDate,$status, $instruction,$writer);
        return $process;

    }
    public function InsertMedication($ordernum,$patientid,$medname,$shorthand,$diagcode,$medamount,$meddoseuom,$medfreq,$prn,$route,$altroute,$instruction,$medstartdt,$medenddt,$medchangetype,$drugclass,$medunderstanding,$addsettings,$status,$writer)
    {
       
        $medresult= $this->ProcessMedication($ordernum,$patientid,$medname,$shorthand,$diagcode,$medamount,$meddoseuom,$medfreq,$prn,$route,$altroute,$instruction,$medstartdt,$medenddt,$medchangetype,$drugclass,$medunderstanding,$addsettings,$status,$writer);
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
    public function GetPatientMedsByOID($ppid,$ordnumber)
    {
        $patientID = $ppid;
        $t = $this->GetPatientMedicationsByOID($patientID,$ordnumber);
        $buildgrid = $this->buildMedGrid($t);
        $buildgridar = array("data"=>$t,"gridhtml"=>$buildgrid);
        return json_encode($buildgridar,JSON_PRETTY_PRINT);
    }
    public function GetPatientDiagsByOID($ppid,$ordnumber)
    {
        $patientID = $ppid;
        $t = $this->GetPatientDiagnosisByOID($patientID,$ordnumber);
        $buildgrid = $this->buildDiagnosisGrid($t);
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
    public function UpdateMedication($medID,$ordernumber, $patientID,$medicationname,$diagcode,$medamount,$doesoum,$medfrequency,$prn,$route,$instruction,$medstartdate,$medendate,$changetype,$drugClass,$understand,$addsettings,$status)
    {
        $t = $this->UpdateMedsByOrderID($medID,$ordernumber,$patientID,$medicationname,$diagcode,$medamount,$doesoum,$medfrequency,$prn,$route,$instruction,$medstartdate,$medendate,$changetype,$drugClass,$understand,$addsettings,$status);
        return $t;
    }
    public function GetMedicationByMedEntryID($medid)
    {
        $medentryID = $medid;
        $t = $this->GetMedicatinByMedEntry($medentryID);
        return $t;
    }
    public function UpdateDiagnosisInfo($pid,$ordnum,$diagId,$diagtype,$diagcode,$proccode,$onset,$diagDate,$controlrate)
    {
        $t = $this->UpdateDiagnosis($pid,$ordnum,$diagId,$diagtype,$diagcode,$proccode,$onset,$diagDate,$controlrate);
        return $t;
    }
    public function EditDiagnosisByOrdPID($did, $pid)
    {
        $dnoseID = $did;
        $patID = $pid; 
        $t = $this-> GetPDiagnosisByDiagIdAndPId($dnoseID,$patID);
        return $t;
    }
      public function DeleteMedicationByPidOrdnum($patientid,$medentryid)
    {
        $dresult = $this->DeleteMedsByIDOrdenum($patientid,$medentryid);
        return $dresult;
    }
    public function DeleteProviderById($pid)
    {
        $result = $this->DeleteProvById($pid);
        return $result;
    }
    public function DeleteBothMednDiagByOrdID($ordernumber,$status)
    {
        $deletboth = $this->DeleteMedDiagPendingByOnumber($ordernumber,$status);
        return $deletboth;
    }
    public function InsertProcedure($code,$description)
    {
        $sql="INSERT INTO procedurecodes (code,description) VALUES (:code,:descrip)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":code",$code);
        $stmnt->bindParam(":descrip",$description);
        try{
            if($stmnt->execute())
            {
                $msg="Inserted";
                return $msg;
            }
        }
        catch(PDOException $e)
        {
            $msg = $e->__toString();
            return $msg;
        }
    }
    public function SearchProcedureCodesByDescription($description)
    {
        $sdescrip = $description;
        $descripresult = $this->SearchProcedureDescription($sdescrip);
        if(is_array($descripresult) && count($descripresult) >0)
        {
            $procgrid = $this->buildDProcedureCodeGrid($descripresult);
            $msgar = array("count"=>count($descripresult), "result" => $descripresult, "html"=>$procgrid);
            return $msgar;
        }
        else{
            $msgar = array("result"=>"No Data Found","html"=>"");
            return $msgar;
        }
        
    }
    public function GetAllNotes($patientId)
    {
        $sqlTempTable = <<<EOD
CREATE TEMPORARY TABLE raw(
  bigid INT NOT NULL AUTO_INCREMENT,
  timeLength INT,
  startDt DATE,
  startTime TIME,
  endDt DATE,
  endTime TIME,
  name TEXT NOT NULL,
  type TEXT NOT NULL,
  postDate DATE NOT NULL,
  pnotes TEXT NOT NULL,
  patientId TEXT NOT NULL,
  accountType TEXT,
  year INT NOT NULL,
  month INT NOT NULL,
  yearmonth TEXT NOT NULL,
  pid INT NOT NULL,
  PRIMARY KEY(bigid)
)
EOD;
        $sqlInsert = <<<EOD
INSERT INTO raw(
  accountType,
  timeLength,
  startDt,
  startTime,
  endDt,
  endTime,
  name,
  type,
  postDate,
  pnotes,
  patientid,
  year,
  month,
  yearmonth,
  pid
)
SELECT 
  accountType,
  timeLength,
  startDt,
  startTime,
  endDt,
  endTime,
  name,
  type,
  postDate,
  pnotes,
  patientid,
  YEAR(startDt),
  MONTH(startDt),
  DATE_FORMAT(startDt,'%Y-%m'),
  pid
FROM
  patientNotes
WHERE
  patientid = :patientId
ORDER BY
  startDt DESC,startTime DESC,endDt DESC, endTime DESC
EOD;
        $sqlMeta = <<<EOD
SELECT yearmonth,min(bigid) - 1 AS base,count(*) AS size
FROM raw
GROUP BY yearmonth
ORDER BY yearmonth DESC;
EOD;
        $sqlData = <<<EOD
SELECT startDt,startTime,endDt,endTime,patientId,pnotes,postDate,pid,name,type,timeLength,accountType
FROM raw
ORDER BY bigid
EOD;
        $sqlCleanup = <<<EOD
DROP TABLE raw;
EOD;
        try {
            $stmnt = $this->con->prepare($sqlTempTable);
            $stmnt->execute();
            $stmnt = $this->con->prepare($sqlInsert);
            $stmnt->bindParam(":patientId",$patientId);
            $stmnt->execute();
            $stmnt = $this->con->prepare($sqlMeta);
            $stmnt->execute();
            $meta = $stmnt->fetchAll();
            $stmnt = $this->con->prepare($sqlData);
            $stmnt->execute();
            $data = $stmnt->fetchAll();
            $stmnt = $this->con->prepare($sqlCleanup);
            $stmnt->execute();
            return
            [
                'data'=>$data,
                'meta'=>$meta
            ];
        }
        catch(PDOException $e){
            return
            [
                'error'=>$e->__toString()
            ];
        }
        catch(Exception $e) {
            return
            [
                'error'=>$e->__toString()
            ];
        }
	}

	/*
        $sql="Select * FROM patientNotes WHERE patientId=:pid ORDER BY postDate ASC";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
       try{
            if($stmnt->execute())
            {
                $result =$stmnt->fetchAll();
                $notegrid = $this->buildNotesGrid($result);
                $msg =array("count"=>count($result), "result"=>$result, "html"=>$notegrid);
                return $msg;
            }
       }
       catch(PDOException $e){
        $msg = array("count"=>0,"error"=>$e->__toString());
           return $msg;
       }
	 */
     public function  GetNotesById($pid,$noteid)
    {
         //var_dump($pid, $startdate, $enddate);
         $sql="Select * FROM patientNotes WHERE patientId=:pid AND pid=:ntid ORDER BY postDate ASC";
         $stmnt = $this->con->prepare($sql);
         $stmnt->bindParam(":pid",$pid);
         $stmnt->bindParam(":ntid",$noteid);

        try{
             if($stmnt->execute())
             {
                 $result =$stmnt->fetchAll();
                 $msg =array("count"=>count($result),"results"=>$result);
                 return $msg;
             }
        }
        catch(PDOException $e){
         $msg = array("count"=>0,"error"=>$e->__toString());
            return $msg;
        }
    }
     public function GetPatientOrders_ByID($pid)
    {
        $patID = $pid;
        $ordresult = $this->GetAllOrdersByPatID($patID);
        if(is_array($ordresult) && count($ordresult) >0)
        {
            //now build the order grid 
            $ordgrid = $this->buildOrderGrid($ordresult);
            $msgar = array("Count"=>count($ordresult),"result"=>$ordresult, "html"=>$ordgrid);
            return $msgar;
        }
        else{
            $msgar = array("result"=>"No Data Found","html"=>"");
            return $msgar;
        }
    }
     public function GetOrdersByDates($pid,$ordstartdt, $ordenddt)
    {
      
        $patientID = $pid;
        $stdt = $ordstartdt;
        $enddt = $ordenddt;
        $getorders = $this->GetOrdersByOrdDates($patientID,$stdt,$enddt);
        $count = count($getorders);//count number of results 
       
        if($count >0)
        {
            $ordgrid = $this->buildOrderGrid($getorders);
            $msgar = array("count"=>$count ,"html"=>$ordgrid);
            return $msgar;
        }
        else{
            $msgar = array("count"=>0,"html"=>"");
            return $msgar;
        }
    }
     public function GetOrderByCombo($pid,$stdate,$enddate,$properties,$orderstatus)
    {
       
        $patientID = $pid;
        $stdt =$stdate;
        $enddt = $enddate;
        $prop  = $properties;
        $status = $orderstatus;
        $getorders = $this->GetOrdersByModifiyers($patientID,$stdt,$enddt,$prop,$status);
        $count = count($getorders);
        if($count >0)
        {
            $ordgrid = $this->buildOrderGrid($getorders);
            $msgar = array("count"=>$count ,"html"=>$ordgrid);
            return $msgar;
        }
        else{
            $msgar = array("count"=>0,"html"=>"");
            return $msgar;
        }
    }
     public function GetSingleOrderByID($oid)
    {
        $orderid = $oid;
        $getorders = $this->GetOrderBYOrderID($orderid);
        $count = count($getorders);
        if($count >0)
        {
            $msgar = array("count"=>$count,"result"=>$getorders);
            return $msgar;
        }
        else{
            $msgar = array("count"=>$count,"result"=>"No Data Found");
        }
    }
     public function UpdateMedEndDate_ByMedID($pid,$ordnum,$edate,$medId,$transtype)
    {
       //var_dump($pid." ".$ordnum." ".$edate." ".$medId." ".$transtype);
        $updatestatus = $this->UpdateMedDoseEnddate($pid,$ordnum,$edate,$medId,$transtype);
        return $updatestatus;
    }
    public function ChangeOrderStatus($ordnum, $status)
    {
        $sql = "UPDATE orders SET status=:st WHERE orderid=:ordid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":st", $status);
        $stmnt->bindParam(":ordid", $ordnum);
        try{
            if($stmnt->execute())
            {
                $result = "Updated";
                $msgar = array("result"=>$result);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msar = array("error"=>$e->__toString());
            return $msar;
        }
    }
     public function DiscontinueMedByDate($pid,$medentryid,$ordernum,$dcdate,$transtype)
    {
       // var_dump($pid." ".$medentryid." ".$ordernum." ".$dcdate." ".$transtype);
        $sql="UPDATE medications SET discontinue_date=:dcdate, med_enddate=:medenddate,medchangetype=:transtype WHERE medentryid=:medid and patient_id=:pid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":dcdate",$dcdate);
        $stmnt->bindParam(":medenddate",$dcdate);
        $stmnt->bindParam(":transtype",$transtype);
        $stmnt->bindParam(":medid",$medentryid);
        $stmnt->bindParam(":pid",$pid);

        try{
            if($stmnt->execute())
            {
                $result = "Updated";
                $msgar = array("count"=>count($result),"result"=>$result);
               //var_dump($msgar);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msar = array("error"=>$e->__toString());
            return $msar;
        }
    }
    public function GetNotes($pid,$startdate,$enddate)
    {
        //var_dump($pid, $startdate, $enddate);
        $sql="Select * FROM patientNotes WHERE patientId=:pid AND postDate BETWEEN '$startdate' AND '$enddate'  ORDER BY postDate ASC";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
       try{
            if($stmnt->execute())
            {
                $result =$stmnt->fetchAll();
                $notegrid = $this->buildNotesGrid($result);
                $msg =array("count"=>count($result),"html"=>$notegrid);
                return $msg;
            }
       }
       catch(PDOException $e){
        $msg = array("count"=>0,"error"=>$e->__toString());
           return $msg;
       }
    }
    public function SearchProcedureCodesByCode($code)
    {
        $srchcode = $code;
        $codresult = $this->SearchProcedureCode($srchcode);
       
        if(is_array($codresult) && count($codresult) >0)
        {
             $procgrid = $this->buildDProcedureCodeGrid($codresult);
            $msgar = array("result"=>$codresult,"html"=>$procgrid);
           return $msgar;
        }
        else{
            //no data found 
            $msgar =array("result"=>"No Data Found","html"=>"");
            return $msgar;
        }
       
      
    }
    
    private function SearchProcedureDescription($description)
    {
        $sql="Select * FROM procedurecodes WHERE description LIKE '%$description%' LIMIT 50";
        $stmnt = $this->con->prepare($sql);
       // $stmnt->bindParam(":sdescrip",$description);
        try{
            if($stmnt->execute())
            { 
                $result = $stmnt->fetchAll();
                //var_dump($result);
                return $result;
            }
        }
        catch(PDOException $e)
        {
            $msg = $e->__toString();
            $msgar =array("msg"=>$msg);
            return $msgar;
        }
    }
    private function SearchProcedureCode($code)
    {
        //var_dump($code);
        $sql="Select * FROM procedurecodes WHERE code LIKE '%$code%' LIMIT 50";
       // $sql="Select * FROM procedurecodes WHERE MATCH (code) AGAINST ('$code%')";
        $stmnt = $this->con->prepare($sql);
        //$stmnt->bindParam(":scode",$code);
        try{
            if($stmnt->execute())
            {
                $result = $stmnt->fetchAll();
                // var_dump($result);
                return $result;
            }
        }
        catch(PDOException $e)
        {
            $msg = $e->__toString();
            $msgar =array("msg"=>$msg);
            //var_dump($msg);
            return $msgar;
        }
    }
     private function buildNotesGrid($notedata)
    {
       
        //loop through the code and build out the table row
        if(is_array($notedata) && !empty($notedata))
        {
            //build grid
            $html="";
           foreach($notedata as $m)
           {

             
                    $html .="<div class='columnrow'>
                    <div class='col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row'>
                            <span id=\"medid\" class=\"coltext\" noteid=".$m["pid"].">".$m["pid"]."</span>
                    </div>
                    <div class='col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row'>
                            <span id=\"row\" class=\"coltext\">".$m["postDate"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["pnotes"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\"></span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\"></span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\"></span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\"></span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext msgiconbtn\"></span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\"></span>
                    </div>
                </div>";
             
           
           }
        return $html;

        }
    }
    private function buildMedGrid($meddata)
    {
       $html="";
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
      private function buildOrderGrid($meddata)
    {
       $html="";
        //loop through the code and build out the table row
        if(is_array($meddata) && !empty($meddata))
        {
            //build grid
           foreach($meddata as $m)
           {

            
                    $html .="<div class='columnrow' orderid=".$m["orderid"].">
                    <div class='col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row'>
                            <span id=\"medid\" class=\"coltext\" ordernumber=".$m["ordernumber"].">".$m["ordernumber"]."</span>
                    </div>
                    <div class='col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row'>
                            <span id=\"row\" class=\"coltext\">".$m["orderdate"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["ordertime"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["ordertype"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["medication"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["diagnosis"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["status"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext msgiconbtn\">".$m["orderdescription"]."</span>
                    </div>
                    <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                            <span id=\"row\" class=\"coltext\">".$m["alt_route"]."</span>
                    </div>
                </div>";
            
           
           }
        return $html;

        }
    }
    private function buildDiagnosisGrid($diagdata)
    {
       $html="";
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
                    <span id=\"row\" class=\"coltext\">".$m["procedurecode"]."</span>
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
            
        </div>";
           }
        return $html;

        }
    }
    private function buildDProcedureCodeGrid($diagdata)
    {
       $html="";
        //loop through the code and build out the table row
        if(is_array($diagdata) && !empty($diagdata))
        {
           // var_dump($diagdata);
            //build grid
           foreach($diagdata as $m)
           {

           $diagnosisID= $m["procid"];
            $html .="<div class='columnrow' data-procID='".$diagnosisID."' data-proccode='".$m["code"]."'>
            <div class='col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row'>
                    <span id=\"procid\" class=\"coltext\" procid='".$m["procid"]."'>".$m["procid"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"proccode\" class=\"coltext\">".$m["code"]."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\">".substr($m["description"],0,60)."</span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\"></span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\"></span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\"></span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext msgiconbtn\"></span>
            </div>
            <div class=\"col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row\">
                    <span id=\"row\" class=\"coltext\"></span>
            </div>
        </div>";
           }
        return $html;

        }
    }
     private function GetOrderBYOrderID($pordid)
    {
        $sql="SELECT * FROM orders WHERE orderid=:oid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":oid",$pordid);
        try{
            if($stmnt->execute())
            {
                $result = $stmnt->fetchAll();
                return $result; 
            }
        }catch(PDOException $e)
        {
            $result = $e->__toString();
            $msg = array("Error"=>$result);
            return $msg;
        }
    }
     private function GetAllOrdersByPatID($patientID) 
    {
        
        $sql="SELECT * FROM orders WHERE patientid=:pid Order BY orderdate DESC"; 
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$patientID);
       
        try{
            if($stmnt->execute())
            {
                $result=$stmnt->fetchAll();
                //var_dump($result);
                return $result; 
            }
        }
        catch(PDOException $e)
        {
            $result = $e->__toString();
            $msg = array("Error"=>$result);
            var_dump($msg);
            return $msg; 
        }

    }
     private function GetOrdersByPatID_AND_OrderID($pid,$ordid)
    {
        $sql="SELECT * FROM orders WHERE patientid=:pid AND ordid=:ordid Order BY orderdate DESC"; 
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$patientID);
        $stmnt->bindParam(":ordid",$ordid);
       
        try{
            if($stmnt->execute())
            {
                $result=$stmnt->fetchAll();
                //var_dump($result);
                return $result; 
            }
        }
        catch(PDOException $e)
        {
            $result = $e->__toString();
            $msg = array("Error"=>$result);
            var_dump($msg);
            return $msg; 
        }
    }
     private function GetOrdersByOrdDates($pid,$ordstartdt, $ordenddt)
    {
        $sql="SELECT * FROM orders WHERE orderdate BETWEEN '$ordstartdt' AND '$ordenddt' AND patientid=:pid"; 
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
       
        try{
            if($stmnt->execute())
            {
                $result=$stmnt->fetchAll();
               
                //var_dump($result);
                return $result; 
            }
        }
        catch(PDOException $e)
        {
            $result = $e->__toString();
            $msg = array("Error"=>$result);
            var_dump($msg);
            return $msg; 
        }
    }
     private function GetOrdersByModifiyers($pid, $strtdate,$enddate,$variables,$orderstatus)
    {
        
        switch($variables)
        {
            case"Medications":
            {
              
             $columnfield = "medication";
             if($orderstatus !="Select Order Status" && $orderstatus !="" && $strtdate !="" && $enddate !="")
             {
                //var_dump("1");
                $sql="SELECT * From orders WHERE $columnfield='true' AND patientid=:pid AND orderdate BETWEEN '$strtdate' AND '$enddate' AND status=:status ORDER BY orderdate ASC ";
                break;
             }
             elseif( $orderstatus !="Select Order Status" && $orderstatus!="" && $strtdate=="" && $enddate=="")
             {
                $sql="SELECT * From orders WHERE $columnfield='true' AND patientid=:pid AND status=:status ORDER BY orderdate ASC ";
                break;
             }
             elseif( $orderstatus =="Select Order Status" && $orderstatus=="" && $strtdate=="" && $enddate=="")
             {
                //var_dump("keyon");
                $sql="SELECT * FROM orders WHERE $columnfield='true' AND patientid=:pid ORDER BY orderdate ASC";
                break;
             }
             elseif( $orderstatus =="Select Order Status" || $orderstatus=="" && $strtdate !="" && $enddate !="")
             {
               
                $sql="SELECT * FROM orders WHERE $columnfield='true' AND orderdate BETWEEN '$strtdate' AND '$enddate' ORDER BY orderdate ASC";
                break;
             }
            
                
               // break;
            }
            case"Diagnosis":
            {
              
                $columnfield ="diagnosis";
                if($orderstatus !="Select Order Status" && $orderstatus !=="" && $strtdate !="" && $enddate !="")
                {
                    $sql="SELECT * From orders WHERE $columnfield='true' AND patientid=:pid AND orderdate BETWEEN '$strtdate' AND '$enddate' AND status=:status ORDER BY orderdate ASC ";
                    break;
                }
                elseif($orderstatus !="Selct Order Status" && $orderstatus !="" && $strtdate=="" && $enddate=="")
                {
                     $sql="SELECT * FROM orders WHERE $columnfield='true' AND patientid=:pid AND status=:status ORDER BY orderdate ASC";
                     break;
                }
                elseif($orderstatus=="Select Order Status" || $orderstatus=="" && $strtdate =="" && $enddate=="")
                {
                    $sql="SELECT * FROM orders WHERE $columnfield='true' AND patientid=:pid ORDER BY orderdate ASC";
                    break;
                }
                elseif($orderstatus =="Select Order Status" || $orderstatus =="" && $strtdate !="" && $enddate !="")
                {
                    $sql="SELECT * FROM orders WHERE $columnfield='true' AND patientid=:pid AND orderdate BETWEEN '$strtdate' AND '$enddate' ORDER BY orderdate ASC";
                    break;
                }
               
                break;
            }
            case"Procedure":
            {
               
                $columnfield ="procedure";
                if($orderstatus !="")
                {
                    $sql="SELECT * From orders WHERE $columnfield='true' AND patientid=:pid AND orderdate BETWEEN '$strtdate' AND '$enddate' AND status=:status ORDER BY orderdate ASC ";
                }
                else{
                     $sql ="SELECT * FROM orders WHERE $columnfield='true' AND patientid=:pid AND orderdate BETWEEN '$strtdate' AND '$enddate' ORDER BY orderdate ASC";
                }
               
                break;
            }
            case"Select Property Type":
                {
                   
                    $columnfield ="NA";
                    if($orderstatus !="Select Order Status" && $strtdate !="" && $enddate !="")
                    {
                        $sql="SELECT * From orders WHERE status=:status AND patientid=:pid AND orderdate BETWEEN '$strtdate' AND '$enddate' ORDER BY orderdate ASC ";
                        break;
                    }
                    elseif($orderstatus=="Select Order Status" && $strtdate !="" && $enddate !="")
                    {
                        $sql="SELECT * FROM orders WHERE patientid=:pid AND orderdate BETWEEN '$strtdate' AND '$enddate' ORDER BY orderdate ASC";
                        break;
                    }
                    else{
                        // $sql ="SELECT * FROM orders WHERE $columnfield='true' AND patientid=:pid AND orderdate BETWEEN '$strtdate' AND '$enddate' ORDER BY orderdate ASC";
                    }
                   
                    break;
                }
        }
       
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
        if($orderstatus !="Selct Order Status" && $orderstatus !="")
        {
            $stmnt->bindParam(":status",$orderstatus);
        }
        
       
        try{
            if($stmnt->execute())
            {
                $result=$stmnt->fetchAll();
                //var_dump($result);
                return $result; 
            }
        }
        catch(PDOException $e)
        {
            $result = $e->__toString();
            $msg = array("Error"=>$result);
            var_dump($msg);
            return $msg; 
        }
    } 
    private function GetPatientMedicationsByOID($patientID, $ordnumber)
    {
       
        $sql="Select * FROM medications WHERE patient_id=:pid AND order_number=:onumber";
        $stmnt =$this->con->prepare($sql);
        $stmnt->bindParam(":pid",$patientID);
        $stmnt->bindParam(":onumber",$ordnumber);
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
    private function GetPatientDiagnosisByOID($patientID, $ordnumber)
    {
       
        $sql="Select * FROM orders_diagnosis WHERE patientID=:pid AND order_id=:onumber";
        $stmnt =$this->con->prepare($sql);
        $stmnt->bindParam(":pid",$patientID);
        $stmnt->bindParam(":onumber",$ordnumber);
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
     private function UpdateMedDoseEnddate($pid,$ordnum,$edate,$medId,$transtype)
    {
       
        $sql="Update medications SET med_enddate=:medenddate, change_date=:changedate, medchangetype=:changetype WHERE medentryid=:medid AND order_number=:onumber";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":medid",$medId);
        $stmnt->bindParam(":medenddate",$edate);
        $stmnt->bindParam(":changetype",$transtype);
        $stmnt->bindParam(":changedate",$edate);
        $stmnt->bindParam(":onumber",$ordnum);
        try{
            if($stmnt->execute())
            {
                $result=array("result"=>"Updated","error"=>"");
               // var_dump($result);
                return $result; 
            }
        }
        catch(PDOException $e)
        {
            $result =array("result"=>"","error"=>$e->__toString());
            //var_dump($result);
            return $result; 
        }
    }
    private function UpdateMedsByOrderID($medID,$ordernumber,$patientID,$medicationname,$diagcode,$medamount,$doesoum,$medfrequency,$prn,$route,$instruction,$medstartdate,$medendate,$changetype,$drugClass,$understand,$addsettings,$status)
    {
        
        $sql="Update medications SET order_number=:ordnumber,patient_id=:pid,medname=:medName,diagnose_code=:diagcode,med_amount=:medamount,
        med_doseuom=:doseuom,med_frequency=:medfrequency,prn=:Prn,route=:route,instruction=:instruction,med_startdate=:medstrtdate,med_enddate=:medenddt,
        medchangetype=:changetype,drug_classification=:drugclass,med_understanding=:understanding,additional_settings=:addsettings,status=:status WHERE medentryid=:medid";
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
        $stmnt->bindParam(":status",$status);
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
    private function ProcessDiagnosis($pid,$ordid,$diagtype,$diagcode,$procCode,$procShorthand,$systemrating,$exacerbation,$diagDt,$endDate,$status,$instruction,$writer)
    {
        //$con = $this->con;
        $sql="Insert Into orders_diagnosis (patientID,order_id,diagnosis_type,diagnosis_code,procedure_code,procShorthand,system_controlrating,onset_exacerbation,diagnosis_date,end_date,status,instruction,writer) VALUES(:pid,:ordId,:diagtype,:diagcode,:proccode,:procShorthand,:syscontrolrating,:exacebration,:diagDate,:endDate,:status,:instruction,:writer)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
        $stmnt->bindParam(":ordId",$ordid);
        $stmnt->bindParam(":diagtype",$diagtype);
        $stmnt->bindParam(":diagcode",$diagcode);
        $stmnt->bindParam(":proccode",$procCode);
        $stmnt->bindParam(":procShorthand",$procShorthand);
        $stmnt->bindParam(":syscontrolrating",$systemrating);
        $stmnt->bindParam(":exacebration",$exacerbation);
        $stmnt->bindParam(":diagDate",$diagDt);
        $stmnt->bindParam(":endDate",$endDate);
        $stmnt->bindParam(":status",$status);
        $stmnt->bindParam(":instruction",$instruction);
        $stmnt->bindParam(":writer",$writer);
        try
        {
            if($stmnt->execute())
            {

                $result ="Inserted";
                return $result;
            }
        }
        catch(PDOException $e)
        {
            $result = $e->__toString();
            return $result;
        }
    }
    
    private function UpdateDiagnosis($pid,$ordid,$diagId,$diagtype,$diagcode,$proccode,$onset,$diagDate,$controlrate)
    {
        //$con = $this->con;
        
        $sql="UPDATE orders_diagnosis SET diagnosis_type=:diagtype,diagnosis_code=:diagcode,procedurecode=:proccode,system_controlrating=:syscontrolrating,onset_exacerbation=:exacebration,diagnosis_date=:diagDate WHERE patientID=:pid AND orddiagid=:diagID";
        $stmnt = $this->con->prepare($sql);
       
        $stmnt->bindParam(":diagtype",$diagtype);
        $stmnt->bindParam(":diagcode",$diagcode);
        $stmnt->bindParam(":proccode",$proccode);
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
        catch(PDOException $e)
        {
            $result = $e->__toString();
            return $result;
        }
    }
    public function DeleteDiagnosisByPID($patientid,$diagId)
    {
        $delete = $this->DeleteDiagnosis_ByPatDID($patientid,$diagId);
        return $delete;
    }
    private function DeleteDiagnosis_ByPatDID($patientid, $diagId) 
    {
        $pid = $patientid;
        $diagID = $diagId;
        $sql="DELETE FROM orders_diagnosis WHERE orddiagid=:ordid AND patientID=:pid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":ordid",$diagId);
        $stmnt->bindParam(":pid",$patientid);
        try{
            if($stmnt->execute())
            {
                $result ="Deleted";
                $rar = array("result"=>$result);
                return $rar;
            }
        }
        catch(PDOException $e)
        {
            $msg = $e->__toString();
            $msgar = array("error"=>$msg,"status"=>"Sql Error");
            return $msgar;
        }
    }
    private function GetPatientOrders($patientId)
    {
        $pid = $patientId;
        $sql = "Select * FROM orders WHERE patientid=:pid ORDER BY orderid ASC";
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

                        //Added on 2/21/2023 - Now Remove Any Medications or Diagnosis that's associated with the above $ordnum with a status of pending 
                    //     $mednordstatus="Pending";
                    //     $checkandremoveMeds = $this->RemoveMedByOrderID($ordnum ,$mednordstatus);
                    //    // var_dump($checkandremoveMeds);
                    //     $checkandremoveDiagnosis = $this->RemoveDiagnosisByOrdernum($ordnum,$mednordstatus);
                        //var_dump($checkandremoveDiagnosis);

                        return $ordnum;
                    
                }
                
            }
            else {
                return 1;
            }
        }
    }

    private function DeleteMedsByIDOrdenum($patientid,$medentryid)
    {
        $sql ="Delete FRom medications WHERE patient_id=:pid AND medentryid=:medid";
        $stmnt  = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$patientid);
        $stmnt->bindParam(":medid",$medentryid);
        try{

            if($stmnt->execute())
            {
                $status="Deleted";
                $msgar = array("status"=>$status,"recRemoved"=>$medentryid);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msg = $e->__toString();
            $msgar = array("msg"=>$msg,"error"=>"Sql Error");
            return $msgar;
        }
    }
    private function DeleteProvById($pid)
    {
        $sql = "Delete from providers WHERE providerid=:pid";
        $stmnt  = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
        try{

            if($stmnt->execute())
            {
                $status="Deleted";
                $msgar = array("status"=>$status);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msg = $e->__toString();
            $msgar = array("msg"=>$msg,"status"=>"Sql Error");
            return $msgar;
        }
    }
    private function DeleteMedDiagPendingByOnumber($ordernumber,$status)
    {
        //Added Feb 19 New

        $removeMed = $this->RemoveMedByOrderID($ordernumber,$status);

        if($removeMed["status"]=="Deleted")
        {
            //now lets remove Diagnosis

            $removeDiagnosis = $this->RemoveDiagnosisByOrdernum($ordernumber,$status);

            if($removeDiagnosis["status"]=="Deleted")
            {
                $msgar = array("status"=>"Deleted","recRemoved"=>"Pending Medication and Diagnosis Deleted");
                return $msgar;
            }
            else{
                return $removeDiagnosis;
            }
        }
        else{
            return $removeMed;
        }
    }
    private function UpdatePendingDiagnosis($ordernumber,$active)
    {
        $sql="UPDATE orders_diagnosis SET `status`=:active WHERE `order_id`=:oid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":active",$active);
        $stmnt->bindParam(":oid",$ordernumber);
        try{
            if($stmnt->execute())
            {
                $updatestatus="Updated";
                $msgar = array("status"=>$updatestatus);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msg = $e->__toString();
            $msgar = array("error"=>$msg,"errorMessage"=>"SQL Error");
            return $msgar;
        }
    }
    private function UpdatePendingMedications($ordernumber,$active)
    {
        //var_dump("I made it");
        $sql="UPDATE medications SET `status`=:active WHERE `order_number`=:oid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":active",$active);
        $stmnt->bindParam(":oid",$ordernumber);
        try{
            if($stmnt->execute())
            {
                $updatestatus="Updated";
                $msgar = array("status"=>$updatestatus);
                return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msg = $e->__toString();
            $msgar = array("error"=>$msg,"errorMessage"=>"SQL Error");
            return $msgar;
        }
    }
    private function RemoveMedByOrderID($ordernumber,$status)
    {
        $sql ="DELETE FROM medications WHERE order_number=:onum AND status=:status";
        $stmnt  = $this->con->prepare($sql);

        $stmnt->bindParam(":onum",$ordernumber);
        $stmnt->bindParam(":status",$status);
        try{
            
            if($stmnt->execute())
            {
                //Medication is Deleted Now lets delete Diagnosis 
                        $status="Deleted";
                        $msgar = array("status"=>$status,"recRemoved"=>"Medication Deleted");
                        return $msgar;
            }
        }
        catch(PDOException $e)
        {
            $msg = $e->__toString();
            $msgar = array("msg"=>$msg,"error"=>"SQL Error");
            return $msgar;
        }
    }
    private function RemoveDiagnosisByOrdernum($ordernumber,$status)
    {

         $onumber = $ordernumber;
         $sstat = $status;
                try{
                    $sql2="DELETE FROM orders_diagnosis WHERE order_id=:ordnumber AND `status`=:orderstatus ";

                    $stmnt2 = $this->con->prepare($sql2);
                    $stmnt2->bindParam(":ordnumber",$onumber);
                    $stmnt2->bindParam(":orderstatus",$status);
                    if($stmnt2->execute())
                    {
                       //var_dump($stmnt2->fetchAll());
                        $getmsg="Deleted";
                        $msgar = array("status"=>$getmsg,"recRemoved"=>"Diagnosis Deleted");
                        return $msgar;
                    }
                    else{
                        return $stmnt2->error_get_last();
                    }
                }
                catch(PDOException $m)
                {
                    $msg = $m->__toString();
                    $msgar = array("msg"=>$msg,"error"=>"Sql Error");
                    return $msgar;
                }
    }
    private function ProcessMedication($orderum,$patientid,$medname,$shorthand,$diagcode,$medamount,$meddoseuom,$medfreq,$prn,$route,$altroute,$instruction,$medstartdt,$medenddt,$medchangetype,$drugclass,$medunderstanding,$addsettings,$status,$writer)
    {
       // $con = $this->con;
         
        $sql ="Insert Into medications (order_number,patient_id,medname,shorthand,diagnose_code,med_amount,med_doseuom,med_frequency,prn,route,alt_route,instruction,med_startdate,med_enddate,medchangetype,drug_classification,med_understanding,additional_settings,status,writer) VALUES(:ordnum,:paitentId,:medname,:shorthand,:diagcode,:medamount,:medoseuom,:medfreq,:prn,:route,:altroute,:instruction,:medstdate,:medenddt,:medchngetype,:drugclassification,:medunderstanding,:addsettings,:status,:writer)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":ordnum",$orderum);
        $stmnt->bindParam(":paitentId",$patientid);
        $stmnt->bindParam(":medname",$medname);
        $stmnt->bindParam(":shorthand",$shorthand);
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
        $stmnt->bindParam(":status",$status);
        $stmnt->bindParam(":writer",$writer);
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
    public function GetAllergies($pid)
    {
        $sql = "SELECT * FROM allergies WHERE pid=:pid";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":pid",$pid);
        try{
            if($stmnt->execute())
            {
               //successful 
               $records = $stmnt->fetchAll();
              // var_dump($records);
               $msg = array("allergies"=>$records);
               return $msg;
            }
       }
       catch(PDOException $e)
       {
           $msg = array("error"=>$e->__toString());
           return $msg;
       }
    }
    public function AddAllergies($pid, $name)
    {
        $sql = "INSERT INTO allergies (name,pid) VALUES (:name,:pid)";
        $stmnt = $this->con->prepare($sql);
        $stmnt->bindParam(":name", $name);
        $stmnt->bindParam(":pid", $pid);
        try{

            if($stmnt->execute())
            {
                $result = "Inserted";
                $msg = array("status"=>$result);
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
