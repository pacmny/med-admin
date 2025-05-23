$(function()
{

    //declare object to post to enpoing
    let obj = {};
    let medobj = {};
    let diagobj = {};
    let ordobj = {};
    let validar = [];
    let glblordnum ="";
    let posturl="https://willowbuilt.it/fora/NPILookup.php";
    let pacmnyurl="http://10.10.3.6/pacmny-be/web/index.php/npi/query";
    let postweburl ="https://willowbuilt.it/fora/tswebook.php";
    let icdurl ="https://willowbuilt.it/fora/icd_calls.php";
    let postresultobj ={};
    //custom dialog component 

    //now lets enqueue tinyMCE
    tinymce.init({
        selector: '#ordertextarea',
        plugins: [
          'a11ychecker','advlist','advcode','advtable','autolink','checklist','export',
          'lists','link','image','charmap','preview','anchor','searchreplace','visualblocks',
          'powerpaste','fullscreen','formatpainter','insertdatetime','media','table','help','wordcount'
        ],
        toolbar: 'undo redo | formatpainter casechange blocks | bold italic backcolor | ' +
          'alignleft aligncenter alignright alignjustify | ' +
          'bullist numlist checklist outdent indent | removeformat | a11ycheck code table help'
      });
   
   obj.systemData ={};
   obj.systemData.API_Meth="GetOrderNumber";
   //get global order number 
   ShowPreloader();
   $.post(postweburl,JSON.stringify(obj))
   .done(function(data){
    StopPreloader();
      console.log(data);
      let jdata = JSON.parse(data);
      console.log(jdata);
      if(jdata.ordernumber && jdata.ordernumber !="" || jdata.order_number !=null)
      {
         glblordnum = jdata.ordernumber;
         console.log(glblordnum);
         //now lets clear the systemData 
        // for (var member in obj) delete obj[member];
        // obj = undefined;
        // obj = {};
        // alert(obj.systemData);
        
        // console.log(obj.systemData);

      }
      else{
        alert("The Sytem Couldn't produce a global Order Number - Please contact the system Administrator to reset");
      }
     

   });

   $("#ordersavebtn").click(function(e){
     e.preventDefault();
    let formvalid =  ValidateForm();
    if(formvalid =="Validated")
    {
        //The NPI Data should already be there and if so we can now save the data 
        if($("#sendtophysician").prop('checked'))
        {
           
           //now check to see if the Verbal order box is check if not lets ask to confirm they are a nurse
           if($("#verbalorder").attr("prop") !="checked")
           {
              
               let nurseconfirm = confirm("Are You Trying to Send a Nurses Order? If So, The Verbal Order Check Box Must Be Checked");
               if(nurseconfirm==true)
               {
                   $("#verbalorder").attr("prop","checked");
                   //now we know we don't need to check for the NPI, but just validate and save the order with a notification
   
               }
               else{
                   /*lets uncheck the box and then check to see if physicians field isn't empty. If Phy field not empty, 
                   *lets go see if we can find the npi and fill in the address, phone flelds and then send if the info exist
                   */
                  $("#sendtophysician").prop('checked',false);
                  //now we should be able to process nurses order
               }
           }
        }
        else{
            /* Assuming its a physician looking to Process the Order Lets checked the aditional requirements
            *@requirement 1. NPI must be associated with the Order 
            @requirement 2. Order Description must exist
            @#requirement 3. Diagnosis Must exist 
            @requirement 4. MEdication must be associated with the Order */
            let validobj = {};
            if($("#npinumber").val() !="" && $("#npinumber").val().length >=4)
            {
                //must be valid 
                validobj.hasnpi="true";
            }
            else{
                validobj.hasnpi="false";
            }
            //check tinymcs to see if content is in the text box 
            let ordtext =  tinyMCE.EditorManager.get('ordertextarea').getContent();
            
            if(ordtext !="" && ordtext.length >0)
            {
                validobj.hasordertext="true";
                //alert("Have text");
            }
            else{
               // alert("no text");
                validobj.hasordertext="false";
            }
            if($("#srchnlookupterms").val()!="" && $("#srchnlookupterms").val().length >0)
            {
                validobj.hasICD10="true";
            }
            else{
                validobj.hasICD10="fasle";
            }
            if($("#medsearchbox").val() !=="" && $("#medsearchbox").val().length >0 && $("#freqselect").val()!="" && $("#medReason").val() !="" && $("#doesUOM").val()!="")
            {
                validobj.hasMedication="true";
            }
            else
            {
                validobj.hasMedication="false";
            }

            if(validobj.hasnpi =="true" && validobj.hasordertext =="true")
            {
               
                let orderobj = {};
                orderobj.OrderTemp = {};
                orderobj.OrderTemp.API_Meth="InsertOrder";
                orderobj.OrderTemp.orderdate= $("#orderdate").val();
                orderobj.OrderTemp.ordertime = $("#ordertime").val();
                orderobj.OrderTemp.ordertype = $("#ordertype").val();
                orderobj.OrderTemp.abndelivery = $("#abntopatient").val();
                orderobj.OrderTemp.ordReadback = $("#phyAgentphy").val();
                orderobj.OrderTemp.primphysician = $("#primphysician").val();
                orderobj.OrderTemp.secphysician = $("#secphysician").val();
                orderobj.OrderTemp.ordtrackinggrp = $("#ordtrackinggrp").val(); 
                orderobj.OrderTemp.npinumber = $("#npinumber").val();
                orderobj.OrderTemp.address = $("#physaddress").val();
                orderobj.OrderTemp.phone = $("#orderphonenumber").val();
                orderobj.OrderTemp.fax = $("#orderfaxnumber").val();
                if($("#sendtophysician").prop("checked"))
                {
                    orderobj.OrderTemp.sendtophysicans="true";
                }
                else{
                    orderobj.OrderTemp.sendtophysicans="false";
                }
               if($("#woundcare").prop("checked"))
               {
                orderobj.OrderTemp.woundcare="true";
               }
               else{
                orderobj.OrderTemp.woundcare="false";
               }
               if($("#verbalorder").prop("checked"))
               {
                orderobj.OrderTemp.verbalorder="true";
               }
               else{
                orderobj.OrderTemp.verbalorder="false";
               }
               
                orderobj.OrderTemp.verbalStartdt = $(".verbdatefield").val();
               
                orderobj.OrderTemp.verbalEnddt = $(".verbtimefield").val();
                orderobj.OrderTemp.description = ordtext;
                /*
                orderobj.OrderTemp.medname = $("#medsearchbox").val();
                orderobj.OrderTemp.doseamount = $("#doseamount").val();
                orderobj.OrderTemp.doesUOM = $("#doesUOM").val();
                orderobj.OrderTemp.frequency = $("#freqselect").val();
                if($("#prncheckbox").prop("checked"))
                {
                    orderobj.OrderTemp.prn="true";
                }
                else{
                    orderobj.OrderTemp.prn="false";
                }
                orderobj.OrderTemp.reason = $("#medReason").val();
                orderobj.OrderTemp.route = $("#medRoute").val();
                orderobj.OrderTemp.alroute = $("#altrouteselect").val();
                orderobj.OrderTemp.medinstruction =$("#medInstructions").val();
                orderobj.OrderTemp.medStartDate = $("#medStartdate").val();
                orderobj.OrderTemp.medEndDate = $("#Medenddate").val();
                orderobj.OrderTemp.neworchange = $("#neworchange").val();
                orderobj.OrderTemp.category = $("#drugcategory").val();
                orderobj.OrderTemp.medunderstanding ="";
                orderobj.OrderTemp.additionalSettings="";
                /*if($("#diagchkbox").prop("checked"))
                {
                    orderobj.OrderTemp.diagnosis="true";
                }
                else{
                    orderobj.OrderTemp.diagnosis="false";
                }
                if($("#procedurechkbox").prop("checked"))
                {
                    orderobj.OrderTemp.procedure="true";
                }
                else{
                    orderobj.OrderTemp.procedure="false"
                }
                orderobj.OrderTemp.diagIC10 = $("#srchnlookupterms").val();
                orderobj.OrderTemp.diagnosisDate = $("#diagnosisDate").val();
                orderobj.OrderTemp.diagControlRating = $("#controlnumber").val();
                if($("#onsetkbox").prop("checked"))
                {
                    orderobj.OrderTemp.Onsetexacerbation="true";
                }else{  orderobj.OrderTemp.Onsetexacerbation ="false"; }
                
                if($("#excaberationchkbox").prop("checked"))
                {
                    orderobj.OrderTemp.Exacerbationonset="true";
                }
                else{
                    orderobj.OrderTemp.Exacerbationonset="false";
                }*/
                //checked values 
                if($("#medication").prop("checked"))
                {
                    orderobj.OrderTemp.hasmed ="true";
                }
                if($("#diagnosis").prop("checked"))
                {
                    orderobj.OrderTemp.hasdiag="true";
                }
                orderobj.OrderTemp.status="pending";
                ShowPreloader();
                $.post(postweburl,JSON.stringify(orderobj))
                .done(function(data){
                  StopPreloader();
                    let jdata = JSON.parse(data);
                    console.log(jdata);
                    if(jdata.result=="Inserted")
                    {
                      alert("Order Number"+" "+glblordnum+" "+"has been successfully submitted.");
                    }
                });
                
              

            }
            else{
                alert("Please Make sure Order has a Description, Medication, NPI Number, ICD10 Code");
                console.log(validobj);
            }

            
            
        }
    }
    else{
       // alert("Not Valid");
        //now clear out the validar array for fresh results during next validation 
        validar = [];
    }
     //remove label if there are any
   
     
     
   });
  //cancel button 
  $("#cancelbtn").click(function(e){
    let confirmclose =  confirm("Are you sure you want to cancel Order"+" "+glblordnum+"? If the answer is yes, all order information will be removed from the Order template.");
    if(confirmclose==true)
    {
      //clear all order template fields values 
         $("#npinumber").val('') 
            //check tinymcs to see if content is in the text box 
             tinyMCE.activeEditor.setContent('');
            
           $("#srchnlookupterms").val('');
           $("#medsearchbox").val('') ;
           $("#freqselect").val('');
           $("#medReason").val('') ;
           $("#doesUOM").val('');
            $("#orderdate").val('');
           $("#ordertime").val('');
            $("#ordertype").val('');
            $("#abntopatient").val('');
            $("#phyAgentphy").val('');
            $("#primphysician").val('');
            $("#secphysician").val('');
            $("#ordtrackinggrp").val(''); 
            $("#npinumber").val('');
            $("#physaddress").val('');
            $("#orderphonenumber").val('');
            $("#orderfaxnumber").val('');
            $("#sendtophysician").attr("checked",false);
           $("#woundcare").attr("checked",false);
           $("#verbalorder").attr("checked",false);
            $(".verbdatefield").val('');
            $(".verbtimefield").val('');
            $("#medication").attr("checked",false);
            $("#diagnosis").attr("checked",false);
                

    }
  });
   $("#primphysician").keydown(function(e){
    //trying to remove namesearch div before next results
    $(".secondrow #npinamesearch").remove();
   });
   $("#primphysician").keyup(function(e){
     e.preventDefault();
     
        //remove it
      // $(".secondrow #npinamesearch").remove();
       $("#npiadditionaladdr").remove();   
       $("#addimgprovider").css("display","none");
      // $("#npinamesearch").remove();
      $("#npinamesearch").each(function(){
          $("#npinamesearch").remove();
      });
     
     obj.methname="GetAllInfo";
     obj.primphysician=$.trim($("#primphysician").val());
     ShowPreloader();
     $.post(posturl,obj)
     .done(function(data){
      StopPreloader();
        //console.log(data);
        let jdata = JSON.parse(data);
       // postresultobj.nameresult = jdata;//go get info when the user selects from the addiitonal fields
        console.log(jdata);
        if(jdata.results=="No Results Found" && jdata.count==0 && $("#primphysician").val().length >=10)
        {
            $(".addimgprovider").css("display","flex");

        }
        else{
            let resultcount = jdata.length;
            console.log("count"+" "+resultcount);
        // let addrhtml="<div id='npiadditionaladdr' class='npiadditionmodal'>";
            if(jdata.count ==0 || resultcount==undefined)
            {
                //do nothing and show the add provider button 
                $(".addimgprovider").css("display","flex");
            }
            else{
                let namehtml="<div id='npinamesearch' class='npinamemodal'>";
                for(var i=0; i < resultcount;i++)
                {
                    console.log("Numberofloops"+" "+i);
                    namehtml +="<div class='additionalnameli'>"+jdata[i].name+"</div>";
                }
                //Because the list is suggestive, lets go ahead and put the acutal spelling of the name in the box item last
                namehtml +="<div class='additionalnameli current'>"+$.trim($("#primphysician").val())+"</div>";
                namehtml +="</div>";
                
                $("#primphysician").after(namehtml);
                $(".additionalnameli").click(function(e){
                    $("#primphysician").val($(this).html());
                    
                    //now go get the rest of the information from the api  
                    obj.methname="GetNPIAddr";
                    obj.primphysician=$.trim($("#primphysician").val());
                   obj.npinumber= "";
                   ShowPreloader();
                   $.post(posturl,obj)
                   .done(function(data){
                    StopPreloader();
                        let mdata = JSON.parse(data);
                        console.log(mdata);
                        console.log("address"+" "+mdata[0].address);
                        let mdatacount = mdata.length;
                        let npinumber = mdata[0].npinumber;
                        let npiar = [];
                        let addrhtml="<div id='npiadditionaladdr' class='npiadditionmodal'>";
                        for(var m=0; m < mdatacount; m++)
                        {
                              let addressar = mdata[m].address;
                             // alert(addressar);
                             let addrphobj = {}; 
                             addrphobj.address =mdata[m].address;
                             addrphobj.tel=mdata[m].tel;
                             addrphobj.fax=mdata[m].fax;
                             npiar.push(addrphobj);
                             addrhtml +="<div class='additonaladdrli'>"+mdata[m].address+"</div>";
                             
                           // addrhtml +="<div class='additonaladdrli'>"+addressar[m].address+"</div>";
                        }
                       
                        console.log(npiar);
                        addrhtml +="</div>";
                        $("#physaddress").after(addrhtml);
                        $(".additonaladdrli").click(function(e){
                            $("#physaddress").val($(this).html());
                            //$("#physaddress").css("text-transform","capitalize");
                            //insert npinumber
                            $("#npinumber").val(npinumber);
                            for(var x=0; x < npiar.length;x++)
                            {
                                if($("#physaddress").val()==npiar[x].address)
                                {
                                    //get tel and fex and set it in the field 
                                    $("#orderphonenumber").val(npiar[x].tel);
                                    $("#orderfaxnumber").val(npiar[x].fax);
                                    continue;
                                }
                            }
                           
                            $(".npiadditionmodal").css("display","none");
                            //now remove it 
                            $(".npinamemodal").remove();
                        });
                   });
                    
                    //now lets close the modal
                    $(".npinamemodal .additonaladdrli").each(function(e){
                        $(".secondrow").next(".npinamemodal").remove();
                    });
                    $(".npinamemodal").css("display","none");
                });
            }
            

        }
        
        
       
       
    });
    
     
    
   });
   //event for Adding Provider
   
    $("#closebtnel").click(function(e){
        $(".providerformsec").css("display","none");
        if($("#npispan").html() !="")
        {
            //grab fields and send them to the appropriate Order fields 
            $("#npinumber").val($("#npispan").html());
            $("#physaddress").val($("#addr1field").val()+","+$("#cityfield").val()+" "+$("#statefield").val()+" "+$("#zipfield").val());
            $("#orderphonenumber").val($("#telfield").val());
            $("#orderfaxnumber").val($("#faxfield").val());
            $("#primphysician").val($("#fnamefield").val()+" "+$("#lnamefield").val());

            //now lets take off the Add Provider H3 css styling
            $("#providform h3").css("border-color","inherit");
            $("#providform h3").css("background-color","transparent");
            $("#providform h3").css("font-weight","400");

        }
    });
    $(".addimgprovider img").click(function(e){
        $(".providerformsec").css("display","flex");
        let whname = $("#primphysician").val();
        let whar = whname.split(" ");
        console.log(whar);
        let fname = whar[0];
        let lname = whar[1];
        let newname = fname+" "+lname;
        console.log(newname);
       // $("#fnamefield").val(newname);
        $("#fnamefield").val(fname);
        $("#lnamefield").val(lname);
        //now call the NPIService
        let callservice = NpiService("GetNPIAddr",obj,posturl);
       // console.log("CallSevice Data"+" "+callservice.length);
       
    });
    $("#savebtnel").click(function(e){
        e.preventDefault();
        //lets form values and store in object so that we can send over to the server
        //alert(postweburl);
        ClearObjects("AddProvider");
       obj.methname="AddProvider";
       obj.provFname= $("#fnamefield").val();
       obj.provLname =$("#lnamefield").val();
       obj.provAddr1 = $("#addr1field").val();
       obj.provAddr2 =$("#addr2field").val();
       obj.provCity = $("#cityfield").val();
       obj.provState = $("#statefield").val();
       obj.provZip = $("#zipfield").val();
       obj.provTel = $("#telfield").val();
       obj.provFax = $("#faxfield").val();
       obj.npinumber = $("#npispan").html();
       obj.ordernumber = glblordnum;
       obj.status ="pending";
       ShowPreloader();
       $.post(postweburl,obj)
       .done(function(ddata){
        StopPreloader();
        console.log("Raw"+" "+ddata);
         //alert("Done ready to process");
        let response = JSON.parse(ddata);
         if(response.message=="Inserted")
         {
            $("#providform h3").html($("#fnamefield").val() + " "+$("#lnamefield").val()+" has been added!");
            $("#providform h3").css("border-style","none");
            $("#providform h3").css("border-color","#bef0d9");
            $("#providform h3").css("background-color","#bef0d9");
            $("#providform h3").css("font-weight","600");
            $("#providform h3").css("padding","10px");
         }
         else{
            $("#providform h3").html(response.message);
         }
       });
    });

    $(".tabs #medicationbtn").click(function(e){
        e.preventDefault();
        //lets clear the grid each time to ensure the newest data point is available 

        $("#medicationcanvas").find(".columnrow").remove();
        //lets change the attr
        console.log("here");
        $(".ordescriptab").attr("class","ordescriptab");
        $(".medicationtab").attr("class","medicationtab active");
        $(".diagnosistab").attr("class","diagnosistab");
        //close canvases
        $("#ordercanvas").css("display","none");
        $("#medicationcanvas").css("display","block");
        $("#diagnosiscanvas").css("display","none");
        
        //now get grid for medication if they exist
        //let medobj = {};
        medobj.MedGrid = {};
        medobj.MedGrid.pid="45064367";
        medobj.MedGrid.API_Meth="PopulateMedGrid";
        let url="https://willowbuilt.it/fora/tswebook.php";
        ShowPreloader();
        $.post(url,JSON.stringify(medobj))
        .done(function(data){
          StopPreloader();
            console.log(data);
            let jdata = JSON.parse(data);
            console.log(jdata.gridhtml);
              $("#medicationcanvas").find(".order-grid .columnrow").remove();//css("display","none");
            $("#medicationcanvas").append(jdata.gridhtml);// .columntop").append(jdata.gridhtml);
            /*now lets sets the event listeners here since the buttons are apart of the medcanvas
        * Grab ID and send to server to get info and then display in Med UI*/
        $("#medicationcanvas .columnrow").click(function(e){
            e.preventDefault();
            console.log("grid instance clicked");
            //removed all dotted lines
            $("#medicationcanvas .columnrow").each(function(){
                $("#medicationcanvas .columnrow").css("border-style","none");
            });
            //changed the border to dotted so there is a visual indicator that the grid item was selected 
            $(this).css("border-style","dotted");
            let medid = $(this).find("#medid").attr("medid");
            let ordnumber = $(this).find("#medid").html();
            medobj.medentryid=medid;
            medobj.ordernumber = ordnumber;
            console.log(medobj.medentryid);
            //alert(medid);

        });
           //$("#medicationcanvas").find(".order-grid .columnrow").remove();
            
           
            //$("#medicationcanvas").css("display","none");
        });
      });

      //subButtons inside the Medication Canvas 
      $(".medbtn_nav").find("#edittransbtn").click(function(e){
       console.log("Edit Medication Button has been clicked");
       for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
       for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
      //let medobj = {};
      medobj.MedicationEdit ={};
       medobj.MedicationEdit.API_Meth="EditMedication";
       medobj.MedicationEdit.medentryid = medobj.medentryid;
       ShowPreloader();
       $.post(postweburl,JSON.stringify(medobj))
       .done(function(data){
        StopPreloader();
        let jdata = JSON.parse(data);
        console.log(jdata);
        //alert(jdata.data[0].medentryid);
        if(jdata.status=="No Data Found")
        {
            alert("Please check the patient Medication Chart to ensure that a medication has been assigned. 100-No Data Found to Edit");

        }
        else{
            //assuming there is data lets extrapolate it 
            let medentryid = jdata.data[0].medentryid;
            let ordnum =jdata.data[0].order_number;
            let paitientID = jdata.data[0].patient_id;
            let medname = jdata.data[0].medname;
            let diagcode = jdata.data[0].diagnose_code;
            let medamount = jdata.data[0].med_amount;
            let meddose = jdata.data[0].med_doseuom;
            let medfreq = jdata.data[0].med_frequency;
            let prn = jdata.data[0].prn;
            let route = jdata.data[0].route;
            let altroute = jdata.data[0].alt_route;
            let instruction = jdata.data[0].instruction;
            let medstrtdt = jdata.data[0].med_startdate;
            let medenddt = jdata.data[0].med_enddate;
            let medchangetype = jdata.data[0].medchangetype;
            let durgclassification = jdata.data[0].drug_classification;
            let medunderstanding = jdata.data[0].med_understanding;
            let additionalsettings = jdata.data[0].additional_settings;
            //now assign them to the UI and then show 
            $("#medsearchbox").val(medname);
            $("#doseamount").val(medamount);
            $("#doesUOM").val(meddose);
            $("#freqselect").val(medfreq);
            $("#medReason").val(diagcode);
            $("#medRoute").val(route);
            if(prn=="true")
            {
                $("#prncheckbox").attr("checked",true);
            }
            $("#altrouteselect").val(altroute);
            $("#neworchange").val(medchangetype);
            $("#medInstructions").val(instruction);
            $("#mediationmodal").css("display","flex");
            $("#medStartdate").val(medstrtdt);
            $("#Medenddate").val(medenddt);
            $("#drugcategory").val(durgclassification);
            switch(medunderstanding)
            {
               case"Direction for Use":
               {
                 $("#meddirectionuse").attr("checked",true);
                 break;
               }
               case"Purpose":
               {
                $("#Medpurpose").attr("checked",true);
                break;
               }
               case"Side Effects/Interactions":
               {
                $("#MedSideffects").attr("checked",true);
                break;
               }
            }
            switch(additionalsettings)
            {
                case"Agency Administered":
                {
                    $("#agencyadmin").attr("checked",true);
                    break;
                }
                case"High Risk":
                {
                    $("#highrisk").attr("checked",true);
                    break;
                }
            }
            //show UI for medicationmodal
            $("#medicationmodal").css("display","block");
            //show the next button 
            $("#nextbtn").css("display","block");
            $("#medsubmitbtn").css("display","none");
            $(".mediationmodal").css("display","flex");
            $("#medsearchbox").css("display","block");
            $(".searchresult").css("display","block");
            $("#mednamesec").css("dispplay","none");
            $("#doseform").css("display","none");
            
            
        }
        //pull data throug to the Medication UIs and then show them so users can update fields

       });
      });
      $(".sortgrid").find("#toggleSearch").change(function(data){
       
        if($(".sortgrid #toggleSearch").is(":checked"))
        {
           let viewallmedrecords ="true";
           $(".mediationmodal .sortgrid").find("#switchlbl").html('View All Medications');
           medobj.GetCurrentMed.API_Meth="ViewAllMedication";
           let url="https://willowbuilt.it/fora/tswebook.php";
           ShowPreloader();
            $.post(url,JSON.stringify(medobj))
            .done(function(data){
              StopPreloader();
                let jdata = JSON.parse(data);
                console.log(jdata);
                $(".mediationmodal .order-grid").find(".columnrow").remove();
                $(".mediationmodal .order-grid").find(".columntop").after(jdata.gridhtml);
            });
        }
        else{
            let viewallmedrecords="false";
            $(".mediationmodal .sortgrid").find("#switchlbl").html('View Current Medications');
            medobj.GetCurrentMed.API_Meth="ViewCurrentMedication";
            medobj.GetCurrentMed.patientid="45064367";
            let url="https://willowbuilt.it/fora/tswebook.php";
            ShowPreloader();
            $.post(url,JSON.stringify(medobj))
            .done(function(data){
              StopPreloader();
                let jdata = JSON.parse(data);
                console.log(jdata);
                $(".mediationmodal .order-grid").find(".columnrow").remove();
                $(".mediationmodal .order-grid").find(".columntop").after(jdata.gridhtml);
            });
        }

      });
      $("#viewcurrentmedsbtn").click(function(e){
       
        for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
        for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
        $(".mediationmodal .order-grid").find(".columnrow").remove();
        //lets get current Mediations for the client
        medobj.GetCurrentMed ={};
        medobj.GetCurrentMed.API_Meth="ViewCurrentMedication";
        medobj.GetCurrentMed.patientid="45064367";
        let url="https://willowbuilt.it/fora/tswebook.php";
        ShowPreloader();
        $.post(url,JSON.stringify(medobj))
        .done(function(data){
          StopPreloader();
            let jdata = JSON.parse(data);
            console.log(jdata);
            $(".mediationmodal .order-grid").find(".columntop").after(jdata.gridhtml);
            //lets close top and bttom element
            $(".mediationmodal .modaltop").css("display","none");
            $(".mediationmodal .modalbottom").css("display","none");
            $(".mediationmodal").css("height","645");
            $(".mediationmodal").css("overflow","scroll");
            $(".mediationmodal").css("display","flex");
            $(".mediationmodal .order-grid").css("display","block");


        });
      });
      $("#nextbtn").click(function(e){
        e.preventDefault();
        $(".srchbtncontainer").css("display","none");
         $("#medicationName").html($("#medsearchbox").val());
        $("#medsearchbox").css("display","none");
        $(".searchresult").css("display","none");
        //now open the the medication form
        $(".modalbottom form").css("display","flex");
        $(".mednamesec").css("display","block");
        //cary the name through 
       

        });
        /*Save Meds Button Event Listener Here */
        $("#savemed").click(function(e){
            console.log("the save button is working");
            e.preventDefault();
            //check object for existence and if the Method is EditMedication Exist 
            if(medobj.MedicationEdit && medobj.MedicationEdit.API_Meth=="EditMedication")
            {
                
                //lets grab data and send it back to be updated 
                let medentry = medobj.MedicationEdit.medentryid;
                let ordnumber = medobj.MedicationEdit.ordernumber;
               
                //now lets clear out the past objects so that we can update the MedObj
                ClearObjects("MedicationEdit");
                console.log(obj + " "+ obj.systemData);
                /*for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
                for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
                for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
                for (var member in obj.systemData) delete obj.systemData[member];*/
                
                medobj.MedicationEdit.medentryid = medentry;//reset Rec ID that needs to be update 
                medobj.MedicationEdit.ordernumber = ordnumber;
                console.log(medobj.MedicationEdit.ordernumber);
                medobj.MedicationEdit.API_Meth="UpdateMedication";
                medobj.MedicationEdit.medname = $("#medsearchbox").val();
                medobj.MedicationEdit.doseamount = $("#doseamount").val();
                medobj.MedicationEdit.doseUOM= $("#doesUOM").val();
                medobj.MedicationEdit.frequency = $("#freqselect").val();
                medobj.MedicationEdit.diagcode = $("#medReason").val();
                medobj.MedicationEdit.medRoute = $("#medRoute").val();
                medobj.MedicationEdit.altroute = $("#altrouteselect").val();
                medobj.MedicationEdit.instruction = $("#medInstructions").val();
                medobj.MedicationEdit.medstrtdt =$("#medStartdate").val();
                medobj.MedicationEdit.medenddt = $("#Medenddate").val();
                medobj.MedicationEdit.changetype = $("#neworchange").val();
                medobj.MedicationEdit.durgclassification =$("#drugcategory").val();
                if($("#prncheckbox").prop("checked"))
                {
                    medobj.MedicationEdit.prn="true";
                }
                else{
                    medobj.MedicationEdit.prn="false";
                }
                
            
                if($("#meddirectionuse").prop("checked"))
                {
                medobj.MedicationEdit.directionuse="Direction for Use";
                
                }
                if($("#Medpurpose").prop("checked"))
                {
                medobj.MedicationEdit.directionuse="Purpose";
                
                }
                if($("#MedSideffects").prop("checked"))
                {
                medobj.MedicationEdit.directionuse="Side Effects/Interactions";
                
                }
           
                if($("#agencyadmin").prop("checked"))
                {
                    medobj.MedicationEdit.additionalsettings ="Agency Administered";
                    
                }
                if($("#highrisk").prop("checked"))
                {
                    medobj.MedicationEdit.additionalsettings="High Risk";
                    
                }
                //lets send over to the server
                ShowPreloader();
                $.post(postweburl,JSON.stringify(medobj))
                .done(function(data){
                  StopPreloader();
                    console.log(data);
                    let jdata = JSON.parse(data);
                    console.log(jdata);
                    alert(jdata.msg);

                });
           
            }
            else if(medobj.MedicationInsert && medobj.MedicationInsert.API_Meth=="InsertMedication")
            {
                ClearObjects("MedicationInsert");
               
                medobj.MedicationInsert.ordernumber = glblordnum;
                medobj.MedicationInsert.medname = $("#medsearchbox").val();
                medobj.MedicationInsert.doseamount = $("#doseamount").val();
                medobj.MedicationInsert.doseUOM= $("#doesUOM").val();
                medobj.MedicationInsert.frequency = $("#freqselect").val();
                medobj.MedicationInsert.diagcode = $("#medReason").val();
                medobj.MedicationInsert.medRoute = $("#medRoute").val();
                medobj.MedicationInsert.altroute = $("#altrouteselect").val();
                medobj.MedicationInsert.instruction = $("#medInstructions").val();
                medobj.MedicationInsert.medstrtdt =$("#medStartdate").val();
                medobj.MedicationInsert.medenddt = $("#Medenddate").val();
                medobj.MedicationInsert.changetype = $("#neworchange").val();
                medobj.MedicationInsert.durgclassification =$("#drugcategory").val();
                if($("#prncheckbox").prop("checked"))
                {
                    medobj.MedicationInsert.prn="true";
                }
                else{
                    medobj.MedicationInsert.prn="false";
                }
                
            
                if($("#meddirectionuse").prop("checked"))
                {
                medobj.MedicationInsert.directionuse="Direction for Use";
                
                }
                if($("#Medpurpose").prop("checked"))
                {
                medobj.MedicationInsert.directionuse="Purpose";
                
                }
                if($("#MedSideffects").prop("checked"))
                {
                medobj.MedicationInsert.directionuse="Side Effects/Interactions";
                
                }
           
                if($("#agencyadmin").prop("checked"))
                {
                    medobj.MedicationInsert.additionalsettings ="Agency Administered";
                    
                }
                if($("#highrisk").prop("checked"))
                {
                    medobj.MedicationInsert.additionalsettings="High Risk";
                    
                }
                
                console.log(medobj.MedicationInsert);
                //console.log(medobj +" "+obj.systemData);
                ShowPreloader();
                $.post(postweburl,JSON.stringify(medobj))
                .done(function(data){
                  StopPreloader();
                    let jdata  = JSON.parse(data);
                    console.log(jdata);
                    if(jdata.sqlresult=="Inserted")
                    {
                        alert("Medication Data Saved Successfully");
                    }
                });
            }
            else{
                //lets send over to the server to be inserted for the first time
            }
        });
    
       $("#deletemed").click(function(e){
         $ask = confirm("Are you sure you want to close and not save Medication Information?");
         if($ask==true)
         {
            //clear the fields and then close the modal
            $("#medsearchbox").val('');
            $("#doseamount").val('');
             $("#doesUOM").val('');
            $("#freqselect").val('');
           $("#medReason").val('');
           $("#medRoute").val('');
            $("#altrouteselect").val('');
            $("#medInstructions").val('');
            $("#medStartdate").val('');
             $("#Medenddate").val('');
            $("#neworchange").val('');
           $("#drugcategory").val('');
            $("#prncheckbox").attr("checked",false)
            $("#meddirectionuse").attr("checked",false);
            $("#Medpurpose").attr("checked",false);
            $("#MedSideffects").attr("checked",false);
           $("#agencyadmin").attr("checked",false);
           $("#highrisk").attr("checked",false);
            
         }
       });
      $(".tabs #diagnosisbtn").click(function(e){
        e.preventDefault();
        //lets change the attr
        console.log("here");
        $(".ordescriptab").attr("class","ordescriptab");
        $(".medicationtab").attr("class","medicationtab");
        $(".diagnosistab").attr("class","diagnosistab active");
        //close canvases
        $("#ordercanvas").css("display","none");
        $("#medicationcanvas").css("display","none");
        $("#diagnosiscanvas").css("display","block");
        //now go and get the information for the diagnosis Grid
        
        diagobj.Diagnosis ={};
        diagobj.Diagnosis.API_Meth="PopulateDiagGrid";
        diagobj.Diagnosis.patientID="45064367";
        ShowPreloader();
        $.post(postweburl,JSON.stringify(diagobj))
        .done(function(data){
           StopPreloader();
            let jdata = JSON.parse(data);
             console.log(jdata);
            //remove the previous column
            $("#diagnosiscanvas .order-grid").find(".columnrow").remove();
            $("#diagnosiscanvas .order-grid").find(".columntop").after(jdata.gridhtml);
        });
      });
      
      $("#diagnosiscanvas .order-grid").click(function(e){
         //get the Id number of the column 
         let diagId = $("#diagnosiscanvas .order-grid").find(".columnrow").attr("data-diagID");
         diagobj.Diagnosis = {};
         diagobj.Diagnosis.API_Meth="EditDiagnosis";
         diagobj.Diagnosis.patientID="45064367";
         diagobj.Diagnosis.diagID = diagId;
         ShowPreloader();
         $.post(postweburl,JSON.stringify(diagobj))
         .done(function(data){
          StopPreloader();
            let jdata = JSON.parse(data);
            console.log(jdata);
            //now start to pull the data through and then show the diagnosis Modal
            $("#srchnlookupterms").val(jdata.data[0].diagnosis_code);
            if(jdata.data[0].diagnosis_type=="Diagnosis")
            {
                $("#diagchkbox").attr("checked",true);
            }
            switch(jdata.data[0].onset_exacerbation)
            {
                case"onset":
                {
                    $("#onsetkbox").attr("checked",true);
                    break;
                }
                case"exacerbation":
                {
                    $("#excaberationchkbox").attr("checked",true);
                    break;
                }
            }
            $("#diagnosisDate").val(jdata.data[0].diagnosis_date);
            $("#controlnumber").val(jdata.data[0].system_controlrating);
            //ensure change the state and hide the grid 
            $(".diagnosismodal").find(".order-grid").css("display","none");
            
            
         });
      });
      $("#canceldiagbtn").click(function(e){
        let ask = confirm("Are you sure you want to close and cancel?");
        if(ask==true)
        {
            //clear form fields 
            $("#srchnlookupterms").val('');
            $("#diagchkbox").attr("checked",false);
            $("#onsetkbox").attr("checked",false);
            $("#excaberationchkbox").attr("checked",false);
            $("#diagnosisDate").val('');
            $("#controlnumber").val('');
        }
        else{
            //dont do anything
        }
      });
      $("#icdsfrthopt").find("#savediagnbtn").click(function(e){
         console.log("Save Diaglog Button");
         if(diagobj.Diagnosis.API_Meth=="EditDiagnosis")
         {
            //grab data and send it over to the server 
            ClearObjects("UpdateDiagnosis");
            diagobj.Diagnosis.icd10code = $("#srchnlookupterms").val();
            if($("#diagchkbox").prop("checked"))
            {
                diagobj.Diagnosis.diagnosis_type="Diagnosis"
            }
            if($("#procedurechkbox").prop("checked"))
            {
                diagobj.Diagnosis.diagnosis_type="Procedure";
            }
            if($("#onsetkbox").prop("checked"))
            {
                diagobj.Diagnosis.onset_exacerbation="onset";
            }
            else{
                if($("#excaberationchkbox").prop("checked"))
                {
                    diagobj.Diagnosis.onset_exacerbation="exacerbation";
                }
            }
           
            diagobj.Diagnosis.diagnosisDate = $("#diagnosisDate").val();
            diagobj.Diagnosis.diagControlRating = $("#controlnumber").val();
            //lets update 
            diagobj.Diagnosis.API_Meth="UpdateDiagnosis";
            diagobj.Diagnosis.ordernumber = glblordnum;
            ShowPreloader();
            $.post(postweburl,JSON.stringify(diagobj))
            .done(function(data){
              StopPreloader();
                let jdata = JSON.parse(data);
                console.log(jdata);
                if(jdata.result=="Updated")
                {
                    alert("Diagnosis for Orderid has been updated!");
                }

            });
            
         }
         else {
            console.log(diagobj);
            if(diagobj.Diagnosis.API_Meth=="InsertDiagnosis")
            {
                
                ClearObjects("InsertDiagnosis");
                if($("#diagchkbox").prop("checked"))
                {
                    diagobj.Diagnosis.diagnosis_type="Diagnosis"
                }
                if($("#procedurechkbox").prop("checked"))
                {
                    diagobj.Diagnosis.diagnosis_type="Procedure";
                }
                if($("#onsetkbox").prop("checked"))
                {
                    diagobj.Diagnosis.onset_exacerbation="onset";
                }
                else{
                    if($("#excaberationchkbox").prop("checked"))
                    {
                        diagobj.Diagnosis.onset_exacerbation="exacerbation";
                    }
                }
           
            diagobj.Diagnosis.diagnosisDate = $("#diagnosisDate").val();
            diagobj.Diagnosis.diagControlRating = $("#controlnumber").val();
            diagobj.Diagnosis.diagcode = $("#srchnlookupterms").val();
            ShowPreloader();
            $.post(postweburl,JSON.stringify(diagobj))
            .done(function(data){
              StopPreloader();
                let jdata = JSON.parse(data);
                if(jdata.result =="Inserted")
                {
                    alert("Diagnosis Information Inerted Succesfully");
                }
            });

            }
        }
      });
      $(".diagnosisbtn_nav").find("#editdiagnosisbtn").click(function(e){
         //close Grid 
         if(diagobj.Diagnosis.diagID)
         {
             $(".diagnosismodal").css("display","block");
         }
         else{
            alert("Please select a Diagnosis Record From the Grid to View. Err:102 -No Data Selected");
         }
        
      });
      $("#ordescripbtn").click(function(e){
       $(".ordescriptab").attr("class","ordescriptab active");
       $(".medicationtab").attr("class","medicationtab");
       $(".diagnosistab").attr("class","diagnosistab");
       //close canvases
       $("#medicationcanvas").css("display","none");
       $("#ordercanvas").css("display","block");
       //now populate the Order Grid
       
       
      });
      $("#addtransbtn").click(function(e){
         $(".mediationmodal").css("display","flex");
         $("#medsearchbox").css("display","block");
         $(".searchresult").css("display","block");
         $("#nextbtn").css("display","none");
         $("#medsubmitbtn").css("display","none");
         //now lets set the medobj Meth API
         medobj.MedicationInsert ={};
         medobj.MedicationInsert.API_Meth="InsertMedication";
         //lets grab the order number to send over 

      });
      $("#amodalclose").click(function(e){
        e.preventDefault();
        //lets close the bottom half of the doseform
        $(".mednamesec").css("display","block");
       
       
        $(".srchbtncontainer").css("display","flex");
        $("#medsubmitbtn").css("display","block");
        $("#nextbtn").css("display","none");
        $(".mednamesec").css("display","none");
         $(".modalbottom form").css("display","none");
        $(".mediationmodal").css("display","none");
        //remove medlist 
        $(".medlist").each(function(index){
            $(this).remove();
        });
        $("medsearchbox").val('');
        //now set the box back to its original height
        $(".searchresult").css("height","324px");
        //lets close the Medication View Current Med Grid area
        $(".mediationmodal .modaltop").css("display","flex");
        $(".mediationmodal .modalbottom").css("display","block");
        $(".mediationmodal").css("height","auto");
        $(".mediationmodal").css("overflow","inherit");
        $(".mediationmodal .order-grid").css("display","none");
      });
      
      $("#medsearchbox").keyup(function(e){
         //clear results from first box if there are any 
         if($(".medlist"))
         {
            $(".medlist").each(function(){
                $(this).remove();
            });
         }
        let medname = $("#medsearchbox").val();
           GetMedsFromAPI(medname);
      });

      $("#medReason").keyup(function(e){
        
        if($("#medReason").val() !="" && $("#medReason").length >0)
        {
            //lets make a call 
            let obj = {};
            obj.action="CodeLookUp";
            obj.ICDCode = $("#medReason").val();
            obj.searchTerm="";
            obj.ICDReleaseUrl="";
              let codehtml="";
              ShowPreloader();
            $.post(icdurl,obj)
            .done(function(data){
              StopPreloader();
                let mdata =data;// JSON.parse(data);
                console.log(mdata);
                if( mdata.founddata !=null)
                {

                    let icdbrowser="";
                    icdbrowser="<div class='icdbroswer'>"+
                    "<div class='closeiframe'><a href='#close' title='Close IC10 Browser'>X</a></div>"+
                    "<iframe src="+mdata.browserURL+" id='iframebrowser'/>"+
                    "</div";
                    $("#medReason").after(mdata.html);
                    $(".icd10srchlist").append(icdbrowser);
                    $(".icd10srchlist").click(function(e){
                        e.preventDefault();
                        $("#medReason").val($(this).find("#icdcode").html());
                       // alert($(this).find("#ic10ui").html());
                        $(".icd10listcontainer").remove();
                        // $("#medReason").val(jdata.data.title["@value"]);
                        
                    });
                    $(".closeiframe a").click(function(e){
                        $(".icdbroswer").css("display","none");
                    });
                    $("#icdbrowserlink").hover(function(){
                       
                        console.log($(this).parent());
                        $(this).parent().next(".icdbroswer").css("display","block");
                    });
                    
                   
                }
                else{
                    //no codes found 
                }

            });
        }
        else{
            $("#medReason").attr("placeholder"," *Please Insert ICD Code");
        }
      });

      $(".diagnosismodal #toggleSearch").change(function(e){
         e.preventDefault();
        
         if($(".diagnosismodal #toggleSearch").is(":checked"))
         {
            
            //build html 
           
            $("#icdfirstopt #srchbylbl").html('Search ICD-10 Description');
             $(".diagnosismodal #toggleSearch").attr("checked",true);
            
             obj.action="Search";
             $("#icdfirstopt input[type='text']").attr("placeholder","Search ICD-10 Description");
             console.log(obj);
             console.log(obj.action);
            
         }
         else{
           // $("#switch").html('Search by ICD-10 Codes');
           if(!$(".diagnosismodal #toggleSearch").is(":checked"))
           {
               
            
                $("#srchbylbl").html('Search By ICD-10 Codes');
                $("#toggleSearch").attr("checked",false);
                obj.action="CodeLookUp";
                $("#icdfirstopt input[type='text']").attr("placeholder","Search ICD-10 Codes");
                console.log(obj);
                console.log(obj.action);
           }
            
           
            
         }
      });
     
      $("#srchnlookupterms").keydown(function(e){
        // $(".icd10srchlist").remove();
         $(".icd10listcontainer").remove();
      });

      $("#icdfirstopt input[type='text']").keyup(function(e){
        //lets removethe lookuplist
       // $(".icd10srchlist").remove();
       if(!$(".diagnosismodal #toggleSearch").is(":checked"))
       {
        obj.action="CodeLookUp";
       }
       if($(".ic10listcontainer"))
       {
        $(".icd10listcontainer").remove();
       }
       
        
        //check to make sure the value is not empty
           if( $(".diagnosismodal #icdfirstopt input[type='text']").val() !="" &&  $(".diagnosismodal #icdfirstopt input[type='text']").length >0)
           {
                if(obj.action=="Search")
                {
                  
                    obj.searchTerm=  $(".diagnosismodal #icdfirstopt input[type='text']").val();
                    ShowPreloader();
                    $.post(icdurl,obj)
                    .done(function(data){
                      StopPreloader();
                        let jdata = JSON.parse(data);
                        if(jdata.length !=0)
                        {
                            //not null 
                            console.log(jdata);
                            console.log(jdata.html);
                           // $(".icd10srchlist").remove();
                            $(".icd10listcontainer").remove();
                            $("#srchnlookupterms").after(jdata.html);
                            //set event listener for icd10srchlist element
                            $(".icd10srchlist").click(function(e){
                                e.preventDefault();
                                //close element and parse out code
                                $("#srchnlookupterms").val($(this).find("#icdcode").html());
                                $(".icd10listcontainer").remove();
                            });
                        }
                    });

                }
                else if(obj.action=="CodeLookUp")
                {
                    obj.ICDCode = $("#icdfirstopt input[type='text']").val();
                    ShowPreloader();
                    $.post(icdurl,obj)
                    .done(function(data){
                      StopPreloader();
                        let jdata = JSON.parse(data);
                        if(jdata.length !=0)
                        {
                            console.log(jdata);
                            console.log(jdata.html);
                            console.log(jdata.founddata);
                            //not null 
                            $(".icd10listcontainer").remove();
                            $("#srchnlookupterms").after(jdata.html);
                            //set event listener for icd10srchlist element
                            $(".icd10srchlist").click(function(e){
                                e.preventDefault();
                                //close element and parse out code
                                $("#srchnlookupterms").val($(this).find("#icdcode").html());
                                $(".icd10listcontainer").remove();
                            });
                        }
                    });
                }
              
           }
      });
       $(".diagnosisbtn_nav #adddiagbtn").click(function(e){
        //ensure that bottom of the Diagnosis Grid is turned off 
        $(".diagnosismodal .order-grid").css("display","none");
        $(".diagnosismodal").css("display","block");
        ClearObjects("InsertDiagnosis");
        diagobj.Diagnosis= {};
        diagobj.Diagnosis.API_Meth="InsertDiagnosis";
        diagobj.Diagnosis.ordernumber = glblordnum;
      });

      $(".diagnosisbtn_nav #viewdiagnosisbtn").click(function(e){
            e.preventDefault();
            $(".diagnosismodal").css("display","block");
            $(".diagnosismodal .modalbottom").css("display","none");
            $(".diagnosismodal .diagtitlesec h3").html("View Diagnosis" +"("+diagobj.Diagnosis.patientID+")");
            $(".diagnosismodal .order-grid").css("display","block");
            //go get the grid Element
            diagobj.Diagnosis.API_Meth="PopulateDiagGrid";
            ShowPreloader();
            $.post(postweburl,JSON.stringify(diagobj))
            .done(function(data){
               StopPreloader();
                let jdata = JSON.parse(data);
                 console.log(jdata);
                //remove the previous column
                $(".diagnosismodal .order-grid").find(".columnrow").remove();
                $(".diagnosismodal .order-grid").find(".columntop").after(jdata.gridhtml);
            });
      });

      $(".diagnosismodal #amodalclose").click(function(e){
        e.preventDefault();
        $(".diagnosismodal .order-grid").css("display","none");
        $(".diagnosismodal .modalbottom").css("display","block");
        $(".diagnosismodal .diagtitlesec h3").html("Diagnosis/Procedure");
        $(".diagnosismodal").css("display","none");
      })
      $("#medication").click(function(e){
        
        if($("#medication").is(":checked"))
        {
           
            //now turn on the medication tab
            $(".medicationtab").css("display","block");
            //call PopulateMEdication Tab
           
            
          
          
        }else{
            $(".medicationtab").css("display","none");
            $("#medicationcanvas").css("display","none");
            //check to see if all of the canvas are off and if so, show the order canvas css 
            checkClosedTabs();
        }
      });

      $("#diagnosis").click(function(e){
        if($("#diagnosis").is(":checked"))
        {
            $(".diagnosistab").css("display","block");
            
        }
        else{
            $(".diagnosistab").css("display","none");
            //let close the canvas as well 
            $("#diagnosiscanvas").css("display","none");
            checkClosedTabs();
        }
      });

      
      function ShowPreloader()
      {
        $("#orderform").find(".preloader").css("display","block");
       }
      function StopPreloader()
      {
        $("#orderform").find(".preloader").css("display","none");
      }
    function checkClosedTabs()
    {
        if(!$("#medication").is(":checked") && !$("#diagnosis").is(":checked"))
            {
                $("#ordercanvas").css("display","block");

            }
    }
    function GetMedsFromAPI(medname)
    {
        let posturl="https://rxnav.nlm.nih.gov/REST/drugs.json?name="+medname;
        $.get(posturl)
        .done(function(data){
            console.log(data);
            if(data.drugGroup.name==null && jQuery.isEmptyObject(data.drugGroup.conceptGroup))
            {
                //the search came back empty
            }
            else{
                //should have data so lets grab the length
                let medar = data.drugGroup.conceptGroup;
                //let medar = data.drugGroup.conceptGroup[1]["conceptProperties"];
                let htmlresult="";
                /*for(var i=0; i < medar.length;i++)
                {
                    htmlresult +="<div class='medlist'><span id='rxcui'>"+medar[i].rxcui+"</span><p>"+  medar[i].name+"</p></div>";
                }*/
                for(var i=0; i < medar.length;i++)
                {
                    if(medar[i]["conceptProperties"])
                    {
                        let newlength = medar[i]["conceptProperties"];
                        console.log("newlength"+newlength.length);
                        for(x=0; x < newlength.length; x++)
                        {
                            console.log(x);
                             htmlresult +="<div class='medlist'><span id='rxcui'>"+medar[i]["conceptProperties"][x].rxcui+"</span><p>"+  medar[i]["conceptProperties"][x].name+"</p></div>";
                             console.log(htmlresult);
                        }
                    }
                   
                }
                console.log(htmlresult);
                $(".searchresult").append(htmlresult);
                $(".medlist").click(function(e){
                  
                    $("#medsearchbox").val($(this).find("p").text());
                    removeSearchResults();
                    $(".searchresult").css("height","324px");
                    //now pull in the drug name into the doeses setion
                    $("#medicationName").html($("#medsearchbox").val());
                    //close searchbox and associated fields
                    $(".srchbtncontainer").css("display","none");
                    $("#medsearchbox").css("display","none");
                    $(".searchresult").css("display","none");
                    //now open the the medication form
                    $(".modalbottom form").css("display","flex");
                 });
                $(".searchresult").css("height","auto");

            }
            
            //console.log(medar +", Length"+" "+medar.length);
        });
    }
    function removeSearchResults()
    {
        $(".searchresult").each(function(){
            $(".medlist").remove();
        });
    }
    function NpiService(methname,obj,posturl)
    {
        console.log("TestObj"+" "+obj.primphysician);
        obj.methname=methname; 
        console.log("Meth"+" "+methname);
        obj.primphysician = $("#fnamefield").val() +" "+$("#lnamefield").val();
        ShowPreloader();
        $.post(posturl,obj)
        .done(function(ddata){
          StopPreloader();
            console.log(ddata);
           var bbdata= JSON.parse(ddata);
           console.log("Bdata"+" "+bbdata[0].address);
           GetProviderDetails(bbdata);
        });
    }
    function GetProviderDetails(bdata)
    {
        console.log(bdata);
        let nobj ={};
        let dataAr = [];
         let offset = bdata.length; 

            //lets break down the address 
           
            let html="<div id='npiadditionaladdr' class='npiadditionmodal'>";
            for(var i=0; i< offset;i++)
            {
                nobj.address = bdata[i].address;
                nobj.npinumber = bdata[i].npinumber;
                nobj.tel = bdata[i].tel;
                nobj.fax = bdata[i].fax;
                dataAr.push(nobj);
                html +="<div class='additonaladdrli'>"+bdata[i].address+"</div>";
               
            }
            html +="</div>";
            $("#addr1field").after(html);
            $(".additonaladdrli").click(function(e){
                $("#addr1field").val($(this).html());
                //now close the additonaladdr modal
                $(".npiadditionmodal").css("display","none");
               let selectAddress = $("#addr1field").val();
               console.log("SelectedAddr"+selectAddress);
               //lets scroll up while the match is going 
               $("html, body").animate({
                scrollTop:($("#providform").offset().top - 300)
             }, 1000);
               FindAddrMatch(dataAr,selectAddress);
            });
          
    }

    function FindAddrMatch(findAddr,addrSelected)
    {
       
        console.log("Find Address"+" "+findAddr);
        if(findAddr.length >=0)
        {
           
            for(var i=0; i < findAddr.length; i++)
            {
                if(findAddr[i].address==addrSelected)
                {
                    //fill in the fields now
                   
                    let findnewAddr = addrSelected.split(",");
                    let provaddr =findnewAddr[0];
                    let provRestofAddr = findnewAddr[1];
                    let splitaddr = provRestofAddr.split(" ");
                    let provcity =splitaddr[0];
                    let provstate =splitaddr[1];
                    let provzip  = splitaddr[2];
                    let provtel = findAddr[i].tel;
                    let provfax = findAddr[i].fax;
                    let provnpi = findAddr[i].npinumber;
                    //okay now put them into fields
                    $("#addr1field").val(provaddr);
                    $("#cityfield").val(provcity);
                    $("#statefield").val(provstate);
                    $("#zipfield").val(provzip);
                    $("#telfield").val(provtel);
                    $("#faxfield").val(provfax);
                    $("#npispan").html(provnpi); 
                    break;

                }
            }
        }
    }
   function ClearObjects(currentobj)
   {
     switch(currentobj)
     {
        case"MedicationEdit":
        {
            //clear all but Medication Edit 
            for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
           // for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
            for (var member in ordobj.Order) delete ordobj.Order[member];
            break;
        }
        case"MedicationInsert":
        {
            //clear all but MedInsert
            for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            //for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
            for (var member in ordobj.Order) delete ordobj.Order[member];
            break;
        }
        case"System":
        {
            //clear All but system 
            for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
            for (var member in ordobj.Order) delete ordobj.Order[member];
           // for (var member in obj.systemData) delete obj.systemData[member];
            break; 
        }
        case"UpdateDiagnosis":
        {
            for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in ordobj.Order) delete ordobj.Order[member];
            //for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
          break;
        }
        case"InsertDiagnosis":
        {
            for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in ordobj.Order) delete ordobj.Order[member];
            //for (var member in diagobj.EditDiagnosis.E) delete diagobj.Diagnosis[member];
          break;
        }
        case"AddProvider":
        {
          for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in ordobj.Order) delete ordobj.Order[member];
            //for (var member in diagobj.EditDiagnosis.E) delete diagobj.Diagnosis[member];
          break;
        }
        case"Order":
        {
          for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            //for (var member in ordobj.Order) delete ordobj.Order[member];
            for (var member in diagobj.EditDiagnosis.E) delete diagobj.Diagnosis[member];
          break;
        }
     }
   }
   function ValidateForm()
   {
    
    if($("#orderdate").val()==="" || $("#orderdate").val().trim().length ===0)
    {
        validar.push("orderdate");
        //$("#moddelivaddr").css("border-width","thin");
        console.log(validar);
        $("#orderdate").css("border-color","#ff0000");
        
    }
    else{
        $("#orderdate").css("border-color","#000000");
        //now set the objec value
       
        obj.orderdate=$("#orderdate").val();
        console.log(obj);
    }
    if($("#ordertime").val()==="" || $("#ordertime").val().trim().length==0)
    {
        validar.push("OrderTime");
        console.log(validar);
        $("#ordertime").css("border-color","#ff0000");
        
    }
    else{
        $("#ordertime").css("border-color","#000000");
        obj.ordertime=$("#ordertime").val();
        console.log(obj);
    }
    if($("#ordertype").val()==="" || $("#ordertype").val().trim().length==0)
    {
        validar.push("OrderType");
        console.log(validar);
        $("#ordertype").css("border-color","#ff0000");
        
    }
    else{
        $("#ordertype").css("border-color","#000000");
        obj.ordertype=$("#ordertype").val();
        console.log(obj);
    }
    if($("#primphysician").val()==="" || $("#primphysician").val().trim().length==0)
    {
        validar.push("Prim Physician");
        console.log(validar);
        $("#primphysician").css("border-color","#ff0000");
        
    }
    else{
        $("#primphysician").css("border-color","#000000");
        obj.primphysician=$("#primphysician").val();
        console.log(obj);
    }
   /* if($("#secphysician").val()==="" || $("#secphysician").val().trim().length==0)
    {
        validar.push("Secondary Physician");
        console.log(validar);
        $("#secphysician").css("border-color","#ff0000");
        
    }
    else{
        $("#secphysician").css("border-color","#000000");
        obj.secphysician=$("#secphysiciann").val();
        console.log(obj);
    }*/
    if($("#npinumber").val()==="" || $("#npinumber").val().trim().length==0)
    {
        validar.push("Npi Number");
        console.log(validar);
        $("#npinumber").css("border-color","#ff0000");
        
    }
    else{
        $("#npinumber").css("border-color","#000000");
        obj.npinumber=$("#npinumber").val();
        console.log(obj);
    }
    if($("#physaddress").val()==="" || $("#physaddress").val().trim().length==0)
    {
        validar.push("Physician Address");
        console.log(validar);
        $("#physaddress").css("border-color","#ff0000");
        
    }
    else{
        $("#physaddress").css("border-color","#000000");
        obj.physaddress=$("#physaddress").val();
        console.log(obj);
    }
    if(validar.length !=0)
    {
        //there are errors inthe form
        $(".opclosesec").css("background-color","#F00000");
        $(".opclosesec").css("color","#FFFFFF");
        $(".opclosesec").slideDown("slow");
        $(".opclosesec").css("display","flex");
        $(".opclosesec h4").html("Required Fields Must Be Filled Out, Please address accordingly.");
        $(".opclosesec h4").css("text-align","center");
        $(".opclosesec h4").css("font-size","18px");
        let msg="Forn Errors";
        console.log(msg);
        return msg;
    }
    else{
        $(".opclosesec").slideUp("slow");
        let msg="Validated";
        return msg;
    }

   }
});