<?php
$offset =10;
$numofrec =100;
?>

<!DOCTYPE html>
    <head>
        <title>BootstrapGrid</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="js/script.js"></script>
        <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
        
</head>
<body>
    <div class="wrapper">
        <div class="toptitle">
            <div class="container">
                <h1>Patient Management</h1>
            </div>
        </div>
        <div class="searchfindarea">
            <div class="container">
                <form name="searchform" action="">
                    <div class="iconfield col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
                        <img src="https://cdn3.iconfinder.com/data/icons/doctor-icons/100/01-1Doctor-512.png" width="40" height="40" alt="image"/>
                        <label name="findprovlbl">Find Provider</label>
                        <input type="text" name="findprov" class="providfield" placeholder="Search for Provider"/>
                    </div>
                    <div class="iconfield col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
                        <img src="images/document.png" width="40" height="40" alt="image"/>
                        <label name="finddoclbl">Find Document</label>
                        <input type="text" name="findDoc" class="docfield" placeholder="Search for Documents"/>
                    </div>
                    <div class="iconfield col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4">
                        <img src="images/addpatient.png" width="40" height="40" alt="image"/>
                        <label name="findprovlbl">Register</label>
                        <input type="button" name="addPateint" class="addPatientBtn" value="Add Patients"/>
                    </div>
                </form>
            </div>
        </div>
        <div class="gridarea">
            <div class="container">
                    <div class="columntop">
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-col">
                                <span id="col1" class="coltext">PID</span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-col">
                                <span id="col2" class="coltext">Name</span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-col">
                                <span id="col3" class="coltext">Date of Birth</span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-col">
                                <span id="col4" class="coltext">Login ID</span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-col">
                                <span id="col4" class="coltext">Last Upload/DateTime</span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-col">
                                <span id="col6" class="coltext">Remote</span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-col">
                                <span id="col7" class="coltext">Message</span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-col">
                                <span id="col8" class="coltext">Status</span>
                            </div>
                    </div>
                   
                     <?php 
                     for($i=0; $i<=10; $i++)
                     {
                        ?>
                         <div class="columnrow">
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row">
                                    <span id="row<?php echo $i;?>" class="coltext">455344</span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row">
                                    <span id="row<?php echo $i;?>" class="coltext">Keon White</span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row">
                                    <span id="row<?php echo $i;?>" class="coltext">1/31/1980</span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row">
                                    <span id="row<?php echo $i;?>" class="coltext">XiD4</span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row">
                                    <span id="row<?php echo $i;?>" class="coltext">01-21-23:09:23</span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row">
                                    <span id="row<?php echo $i;?>" class="coltext"><img src="https://cdn1.iconfinder.com/data/icons/ionicons-fill-vol-1/512/checkmark-128.png" width="40" height="40"/></span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row">
                                    <span id="row<?php echo $i;?>" class="coltext msgiconbtn"><img src="https://cdn3.iconfinder.com/data/icons/essential-42/32/Out_Callessential_ui_android-512.png" width="40" height="40"/></span>
                            </div>
                            <div class="col-xs-2 col-sm-4 col-md-2 col-lg-2 col-xl-2 top-row">
                                    <span id="row<?php echo $i;?>" class="coltext">Pending</span>
                            </div>
                        </div>
                        <?php 
                        
                     }  ?>
                     <div class="columnbottom">
                        <div class="pagnav">
                            <a href="#1pagnav" title="1st page">1</a>
                            <a href="#2pagnav" title="2nd page">2</a>
                            <a href="#3pagnav" title="3nd page">3</a>
                            <a href="#4pagnav" title="4nd page">4</a>
                            <a href="#5pagnav" title="5th page">5</a>
                            <a href="#6pagnav" title="6th page">6</a>
                            <a href="#7pagnav" title="7th page">7</a>
                            <a href="#8pagnav" title="8nd page">8</a>
                            <a href="#9pagnav" title="9th page">9</a>
                            <a href="#10pagnav" title="10th page">10</a>
                        </div>
                     </div>
                    <div class="msboxwrapper">
                        <div class="container">
                            <div class="mboxtitle col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <h2>Patient | Provider Communication</h2>
                                <div id="msglbl" class="notemsglbl"></div>
                            </div>
                                <div class="msgbox col-xs-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                    <form action="" method="POST">
                                        <input type="textarea" placeholder="Insert Patient Message" id="ptxtarea" class="ptxtarea"/>
                                        <label>Notify Patient<input type="checkbox" id="notifyptchkbx" value="true"/></label>
                                        <input type="button" value="Cancel" id="cnclbtn" class="modalcancelbtn"/>
                                        <input type="button" value="Save" id="msgbtn" class="modalbtn"/>
                                    </form>
                                </div> 
                        </div>

                    </div>
                   
            </div>
        </div>
    </div>
</body>
    </html>
