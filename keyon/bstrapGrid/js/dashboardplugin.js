$(function()
{

    //declare object to post to enpoing
    let obj = {};
    let medobj = {};
    let diagobj = {};
    let validar = [];
    let glbltimer =3000;
    let glblordnum ="";
    let objordnumber ="";
    let medEntryID="";
    let postweburl ="https://willowbuilt.it/fora/bstrapGrid/tswebook.php";
    let postresultobj ={};
    let patientID="45064367";
    //custom dialog component 
    
    //get All orders for patient ID 
    let startdate, enddate="";
    obj.Orders ={};
    obj.Orders.patientID=patientID;
    obj.Orders.API_Meth="GetALLPatientOrders";
    $.post(postweburl,JSON.stringify(obj))
    .done(function(data){
    // StopPreloader();
       console.log(data);
       let jdata = JSON.parse(data);
       console.log(jdata);
       if(jdata.Count >0 && jdata.html !="")
       {
       
        $(".ordGrid ").find(".columbottom").html('');
      
        $(".ordGrid").find(".columbottom").append(jdata.html);
        //lets add the event listener here because the grid was just added to the DOM
        $(".ordGrid").find(".columnrow").click(function(e){
            e.preventDefault();
            ClearObjects("GetPatientOrders");
            $(".columbottom").find(".columnrow").each(function(){
                $(".columnrow").css("border-style","none");
            });
            $(this).css("border-style","dotted");
            //now find the dat-attribute so we can go get the individual record 
            let dataid = $(this).attr("orderid");
            let ordernumber = $(this).find("#medid").attr("ordernumber");
            glblordnum = ordernumber;//using the globalnum variable to find medications or diagnosis  that's associated 
            //set the Orders orderid object property
            obj.Orders = {};
            obj.Orders.orderid = dataid;
            obj.Orders.ordernumber = ordernumber;
            obj.Orders.API_Meth="ViewOrderByID";
            obj.Orders.patientID=patientID
            let mdata = PostData(postweburl,obj);

            
        }); 
 
       }
       else{
         alert("No Orders Found for Patient"+" "+obj.Orders.patientID);
       }
      
 
    });
     //now lets enqueue tinyMCE
     tinymce.init({
        selector: '#ordertextarea',
        plugins: [
          'autolink',
          'lists','link','image','charmap','preview','anchor','searchreplace','visualblocks',
          'fullscreen','insertdatetime','media','table','help','wordcount'
        ],
        toolbar: 'undo redo | formatpainter casechange blocks | bold italic backcolor | ' +
          'alignleft aligncenter alignright alignjustify | ' +
          'bullist numlist checklist outdent indent | removeformat | a11ycheck code table help'
      });
      //lets set the notes text area as well
      tinymce.init({
        selector: '#notetextarea',
        plugins: [
          'autolink',
          'lists','link','image','charmap','preview','anchor','searchreplace','visualblocks',
          'fullscreen','insertdatetime','media','table','help','wordcount'
        ],
        toolbar: 'undo redo | formatpainter casechange blocks | bold italic backcolor | ' +
          'alignleft aligncenter alignright alignjustify | ' +
          'bullist numlist checklist outdent indent | removeformat | a11ycheck code table help'
      });
    //dates filter 

    $("#srchbystrtdate").change(function(e){
        e.preventDefault();
        ClearObjects("OrderFilterByDate");
          startdate =  $("#srchbystrtdate").val();
          if($("#orderproperties").val() =="Select Property Type")
         {
            obj.Orders.API_Meth="GetOrderByDates";
         }
         else{
            obj.Orders.API_Meth="GetOrdersByCombination";
         }
          obj.Orders.ordstartdate = startdate;
    });
   
    $("#srchbyenddate").change(function(e){
        e.preventDefault();
         enddate =$("#srchbyenddate").val();
         if($("#orderproperties").val() =="Select Property Type")
         {

         
            obj.Orders.API_Meth="GetOrderByDates";
         }
         else{
            obj.Orders.API_Meth="GetOrdersByCombination";
         }
          obj.Orders.ordsenddate = enddate;
    });
    $("#orderproperties").change(function(e){
        e.preventDefault();
        let ordattributetype,ordstatus;
        ordattributetype = $("#orderproperties").val();
        ordstatus =$("#orderstatus").val();
        console.log(ordstatus);
        if($("#srchbystrtdate").val() =="" && $("#srchbyenddate").val()=="")
        {
            obj.Orders.API_Meth="GetOrderByProperties";
        }
        else{
             obj.Orders.API_Meth="GetOrdersByCombination";
        }
       
        obj.Orders.ordertype=ordattributetype;
        if(ordstatus !="Select Order Status")
        {
            obj.Orders.orderstatus = ordstatus;
        }
        else{
            obj.Orders.orderstatus="";
        }

    });
    $("#orderstatus").change(function(e){
        e.preventDefault();
        //check to see if dates are empty if so just do a a direct filter, if not its a filter comboy
        if($("#srchbystrtdate").val() =="" && $("#srchbyenddate").val()=="" && $("#orderproperties").val()=="Select Property Type")
        {
            obj.Orders.API_Meth="GetOrderByStatus";
            obj.Orders.orderstatus=$("#orderstatus").val();
        }
        else{
            //check Property Type 
            if($("#orderproperties").val()=="Select Prooperty Type")
            {
                obj.Orders.ordertype="";
            }
            else{
                obj.Orders.ordertype=$("#orderproperties").val();
            }
             obj.Orders.API_Meth="GetOrdersByCombination";
             obj.Orders.orderstatus =$("#orderstatus").val();
        }
    });
   //grid click
   $("#acrdaccordtop").click(function(e){
     if($(".filtercontainer").css("display")=="block")
     {
        //close it
        $(".filtercontainer").slideUp("slow")
        $(".accordtop .caret").find("img").attr("src","/fora/bstrapGrid/images/chevron-down-arrow.svg");
        
     }
     else if($(".filtercontainer").css("display")=="none")
     {
        $(".filtercontainer").slideDown("slow");
        $(".accordtop .caret").find("img").attr("src","/fora/bstrapGrid/images/chevron-right-navigation-svgrepo-com.png");

        //make sure button according isn't open
        if($(".notefiltercontainer").css("display")=="block"){
            $(".notefiltercontainer").slideUp("slow");
            //change the caret 
            $(".noteaccordtop .caret").find("img").attr("src","/fora/bstrapGrid/images/chevron-down-arrow.svg");
            //slie canvas up 
            //check to ensure the Order canvas is up and Notes is down
           
                $(".NoteCanvas").slideUp("slow");
                //lets clear objects here 
                ClearObjects("FilterOrders");//get rid of notes 
              //Pull Fresh Records
              PullOrderData();
                $(".OrderCanvas").slideDown("slow");
            
             
          } 
       
     }
   });
   $("#noteacrdaccordtop").click(function(e){
    if($(".notefiltercontainer").css("display")=="block")
    {
       //close it
       $(".notefiltercontainer").slideUp("slow")
       $(".noteaccordtop .caret").find("img").attr("src","/fora/bstrapGrid/images/chevron-down-arrow.svg");
    }
    else if($(".notefiltercontainer").css("display")=="none")
    {
       $(".notefiltercontainer").slideDown("slow");
       $(".OrderCanvas").slideUp("slow");
       $(".NoteCanvas").slideDown("slow",function(){
         //after the animatin Go and Pull All Notes 
         ClearObjects("FilterNotes");
            obj.Note = {};
            obj.Note.patientID="45064367";
            obj.Note.API_Meth="GetAllNotes";
            GetFilterNotes(postweburl,obj);

    
       });
       $(".noteaccordtop .caret").find("img").attr("src","/fora/bstrapGrid/images/chevron-right-navigation-svgrepo-com.png");
       //make sure the top accordion is closed as well
      if($(".filtercontainer").css("display")=="block"){
        $(".filtercontainer").slideUp("slow");
        //make sure the caret is down 
        $(".accordtop .caret").find("img").attr("src","/fora/bstrapGrid/images/chevron-down-arrow.svg");
        if($(".OrderCanvas").css("display")=="block")
        {
            $(".OrderCanvas").slideUp("slow");
        }
      } 
    }
  });
  $("#noteloadmorebtn").click(function(e){
    ClearObjects("FilterNotes");
     if($(".notefiltercontainer").find("#notesrchbystrtdate").val() !="" && $(".notefiltercontainer").find("#notesrchbyenddate").val() !="")   
     {
        let strtdt =$("#notesrchbystrtdate").val();
        let enddt = $("#notesrchbyenddate").val();
        obj.Note = {};
        obj.Note.poststartdate=strtdt;
        obj.Note.postenddate=enddt;
        obj.Note.API_Meth="FilterNotes";
        GetFilterNotes(postweburl,obj);

     }
  });
  function PullOrderData()
  {
        obj.Orders ={};
        obj.Orders.patientID=patientID;
        obj.Orders.API_Meth="GetALLPatientOrders";
        $.post(postweburl,JSON.stringify(obj))
        .done(function(data){
        // StopPreloader();
        console.log(data);
        let jdata = JSON.parse(data);
        console.log(jdata);
        if(jdata.Count >0 && jdata.html !="")
        {
        
            $(".ordGrid ").find(".columbottom").html('');
        
            $(".ordGrid").find(".columbottom").append(jdata.html);
            //lets add the event listener here because the grid was just added to the DOM
            $(".ordGrid").find(".columnrow").click(function(e){
                e.preventDefault();
                ClearObjects("GetPatientOrders");
                $(".columbottom").find(".columnrow").each(function(){
                    $(".columnrow").css("border-style","none");
                });
                $(this).css("border-style","dotted");
                //now find the dat-attribute so we can go get the individual record 
                let dataid = $(this).attr("orderid");
                let ordernumber = $(this).find("#medid").attr("ordernumber");
                //alert(ordernumber);
                glblordnum = ordernumber;//using the globalnum variable to find medications or diagnosis  that's associated 
                //set the Orders orderid object property
                obj.Orders = {};
                obj.Orders.orderid = dataid;
                obj.Orders.ordernumber = ordernumber;
                obj.Orders.API_Meth="ViewOrderByID";
                obj.Orders.patientID=patientID
                let mdata = PostData(postweburl,obj);

                
            }); 
    
        }
        else{
            alert("No Orders Found for Patient"+" "+obj.Orders.patientID);
        }
    });
  }
  function GetFilterNotes(postweburl,obj)
  {
    $.post(postweburl,JSON.stringify(obj))
    .done(function(data){
        let jdata = JSON.parse(data);
        console.log(jdata);
        if(jdata.count >0 && jdata.html !="")
        {
            $(".note-grid").find(".columbottom").html('');
            $(".note-grid").find(".columbottom").append(jdata.html);
            $(".note-grid").find(".columnrow").click(function(e){
                //alert("click");
                $(".note-grid .columbottom").find(".columnrow").each(function(){
                    $(".columnrow").css("border-style","none");
                });
                $(this).css("border-style","dotted");
                //now find the dat-attribute so we can go get the individual record 
                let noteid = $(this).find("#medid").attr("noteid");
                console.log("Our Note id is"+noteid);
                obj.Note.API_Meth="GetNoteByID";
                obj.Note.noteid=noteid;
                obj.Note.patientID ="45064367";
                GetNoteData(obj);
            });
        }
        else{
            //lets empty grid
            $(".note-grid").find(".columbottom").html('');
            alert("No Results Found. Please Search Again");
        }
    });
  }
  function GetNoteData(obj)
  {
    $.post(postweburl,JSON.stringify(obj))
    .done(function(data){
        let jdata = JSON.parse(data);
        if(jdata.count >0)
        {
            //we have data now lets parse it
            $(".note-grid").slideUp("slow");
            $(".noteState").slideDown("slow",function(){
                //now parse data and show Grid Button to get back to the Grid
                $(".notesearchcontianer").find(".gridntemplatesec").css("display","flex");

                $(".notepostdt").html(jdata.results[0]["postdate"]);
                tinymce.get("notetextarea").setContent(jdata.results[0]["pnotes"]);
            });
        }
    })
  }
  $("#notegridbtn").click(function(e){
    e.preventDefault();
    ClearObjects("FilterNotes");
    obj.Note = {};
    obj.Note.patientID="45064367";
    obj.Note.API_Meth="GetAllNotes";
    GetFilterNotes(postweburl,obj);
    $(".noteState").slideUp("slow",function(){
        $(".note-grid").slideDown("slow",function(){
            //hide the button 
            $(".gridntemplatesec").css("display","none");
        });
    });

  });
    $("#loadmorebtn").click(function(e){
        e.preventDefault();
        switch(obj.Orders.API_Meth)
        {
            case"GetOrderByDates":
            {
                //look for start and endate field validation before trying to send to server side 
                if($("#srchbystrtdate").val() !="" && $("#srchbyenddate").val() !="")
                {
                    ShowProgress();
                    $.post(postweburl,JSON.stringify(obj))
                    .done(function(data){
                        StopProgress();
                        let jdata = JSON.parse(data);
                        console.log(jdata);
                        if(jdata.count >0 && jdata.html !="")
                        {
                        
                            $(".ordGrid ").find(".columbottom").html('');
                        
                            $(".ordGrid").find(".columbottom").append(jdata.html);
                            //lets set the click event for the grid here 
                            $(".ordGrid").find(".columnrow").click(function(e){
                                e.preventDefault();
                                
                                $(".columbottom").find(".columnrow").each(function(){
                                    $(".columnrow").css("border-style","none");
                                });
                                $(this).css("border-style","dotted");
                                //now find the dat-attribute so we can go get the individual record 
                                let dataid = $(this).attr("orderid");
                                let ordernumber = $(this).find("#medid").attr("ordernumber");
                                glblordnum = ordernumber;//using the globalnum variable to find medications or diagnosis  that's associated 
                               // alert(ordernumber);
                                //set the Orders orderid object property
                                obj.Orders.orderid = dataid;
                                obj.Orders.ordernumber = ordernumber;
                                obj.Orders.API_Meth="ViewOrderByID";
                                let mdata = PostData(postweburl,obj);
                            });
                    
                        }
                        else{
                            alert("No Orders Found for Patient"+" "+obj.Orders.patientID +" between the dates of"+" "+$("#srchbystrtdate").val()+" and"+" "+$("#srchbyenddate").val());
                        }
                    });
                }
                break;
            }
            case"GetOrdersByCombination":
            {
                //look for start and endate field validation before trying to send to server side 
                if($("#srchbystrtdate").val() !="" && $("#srchbyenddate").val() !="" || $("#properties").val() !="" || $("#orderstatus").val() !="")
                {
                    $.post(postweburl,JSON.stringify(obj))
                    .done(function(data){
                        let jdata = JSON.parse(data);
                        console.log(jdata);
                        if(jdata.count >0 && jdata.html !="")
                        {
                        
                            $(".ordGrid ").find(".columbottom").html('');
                        
                            $(".ordGrid").find(".columbottom").append(jdata.html);
                            
                    
                        }
                        else{
                            alert("No Orders Found for Patient"+" "+obj.Orders.patientID +" between the dates of"+" "+$("#srchbystrtdate").val()+" and"+" "+$("#srchbyenddate").val());
                            //lets update the grid because there are records being returned
                            $(".ordGrid").find(".columbottom").html('');
                        }
                    });
                }
                break;
            }
        }
    });
    $("#clrfilterbtn").click(function(e){
        e.preventDefault();
        ClearAllOrderFilters();
    });
    $(".tabs #diagnosisbtn").click(function(e){
        e.preventDefault();
        //lets change the attr
        OrderTabOn("diagnosis");
        console.log("here");
        $(".ordescriptab").attr("class","ordescriptab");
        $(".medicationtab").attr("class","medicationtab");
        $(".diagnosistab").attr("class","diagnosistab active");
        //close canvases
        $("#ordercanvas").css("display","none");
        $("#medicationcanvas").css("display","none");
        $("#diagnosiscanvas").css("display","block");
        //now go and get the information for the diagnosis Grid
        ClearObjects("PopulateDiagnosisGrid")
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
    $(".tabs #medicationbtn").click(function(e){
        e.preventDefault();
        //clear bout the obj object 
        OrderTabOn("medication");
        obj ={};
      
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
        ClearObjects("PopulateMedGrid");
        //now get grid for medication if they exist
        //let medobj = {};
        medobj.MedGrid = {};
        medobj.MedGrid.pid="45064367";
        medobj.MedGrid.ordernumber = glblordnum;
        medobj.MedGrid.API_Meth="PopulateMedGridByOrderID";
        //let url="http://localhost:8888/fora/tswebook.php";
        ShowPreloader();
        $.post(postweburl,JSON.stringify(medobj))
        .done(function(data){
            console.log(data);
            StopPreloader();
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
           
        });
      });
    $("#ordescripbtn").click(function(e){
    $(".ordescriptab").attr("class","ordescriptab active");
    $(".medicationtab").attr("class","medicationtab");
    $(".diagnosistab").attr("class","diagnosistab");
    //close canvases
    $("#medicationcanvas").css("display","none");
    $("#ordercanvas").css("display","block");
    
    });
    $("#viewcurrentmedsbtn").click(function(e){
       
        for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
        for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
        $(".mediationmodal .order-grid").find(".columnrow").remove();
        //lets get current Mediations for the client
        medobj.GetCurrentMed ={};
        medobj.GetCurrentMed.API_Meth="ViewCurrentMedication";
        medobj.GetCurrentMed.patientid="45064367";
        //let url="http://localhost:8888/fora/tswebook.php";
        ShowPreloader();
        $.post(postweburl,JSON.stringify(medobj))
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
      $(".sortgrid").find("#toggleSearch").change(function(data){
       
        if($(".sortgrid #toggleSearch").is(":checked"))
        {
           let viewallmedrecords ="true";
           $(".mediationmodal .sortgrid").find("#switchlbl").html('View All Medications');
           medobj.GetCurrentMed.API_Meth="ViewAllMedication";
           //let url="http://localhost:8888/fora/tswebook.php";
           ShowPreloader();
            $.post(postweburl,JSON.stringify(medobj))
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
            //let url="http://localhost:8888/fora/tswebook.php";
            ShowPreloader();
            $.post(postweburl,JSON.stringify(medobj))
            .done(function(data){
                StopPreloader();
                let jdata = JSON.parse(data);
                console.log(jdata);
                $(".mediationmodal .order-grid").find(".columnrow").remove();
                $(".mediationmodal .order-grid").find(".columntop").after(jdata.gridhtml);
            });
        }

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
        $(".mediationmodal").find(".changedt").css("display","none");
        $(".mediationmodal").find(".savemodifiedvalbtn").css("display","none");
      });

      $("#addtransbtn").change(function(e){
        //2.5.2023 
        /*Added dropdown with modifiers and need to update the logic to account for modifires
        @change - Change Doses - Need to update/close last med transaction date /Then Add new does amount under transaction type 
        @Void - Void Medication or Order /Need to Confirm with Dr
        @New - New Medication - Need to discountinue old order 
        @discountinue - Discontinue Medications and Then Create New Order to Add New Medication 
        */
        switch( $("#addtransbtn").val() )
        {
            case"New":
            {
                $(".mediationmodal").css("display","flex");
                $("#medsearchbox").css("display","block");
                $(".searchresult").css("display","block");
                $("#nextbtn").css("display","none");
                $("#medsubmitbtn").css("display","none");
                //now lets set the medobj Meth API
                medobj.MedicationInsert ={};
                medobj.MedicationInsert.API_Meth="InsertMedication";
                //lets grab the order number to send over 
                break;
            }
            case"Change":
            {
                ClearObjects("ViewCurrentMed");
                $(".mediationmodal .order-grid").find(".columnrow").remove();
                //lets get current Mediations for the client
                medobj.GetCurrentMed ={};
                medobj.GetCurrentMed.API_Meth="ViewCurrentMedication";
                medobj.GetCurrentMed.patientid="45064367";
                //let url="http://localhost:8888/fora/tswebook.php";
                ShowPreloader();
                $.post(postweburl,JSON.stringify(medobj))
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
                    //now show the change date field
                   
                    //now lets handle the double click functionality 
                    $(".mediationmodal .order-grid").find(".columnrow").dblclick(function(e){
                        e.preventDefault();
                        console.log("double click working");
                        let ask = confirm("Are you sure you want to change the Medication Dose Amount on an active Order? Please not that Orders can't be modified"+
                        "and will need a new order to 'Add a New Medication'. All changes will be counted as a transaction type update or an addtional transaction that corresponds to this Order");
                        if(ask==true)
                        {
                            //get the information and then prefill the values before showing the Modal again
                            let medid = $(this).find("#medid").attr("medid");
                            medEntryID = medid;
                            let ordnumber = $(this).find("#medid").html();
                            PreFillMediationField(medid);
                            //new code starts here 
                            $(".mediationmodal").find(".changedt").css("display","block");
                            $(".mediationmodal").find(".savemodifiedvalbtn").css("display","block");
                            //lets get the medid before we attach it to the MedicationModifer Object
                          
                            alert(ordnumber);
                            alert(medid);
                            objordnumber = ordnumber;
                            //lets add a sub_action to the MedicationModifer Object
                             ClearObjects("MedicationModifer");
                            medobj.MedicationModifer ={};
                            medobj.MedicationModifer.API_Meth="Transaction_ChangeMedicationvariables";
                            medobj.MedicationModifer.medid= medid;
                            medobj.MedicationModifer.ordernumber = ordnumber;
                            console.log(medobj);
                          
                            /*ok lets pull up the old medication modal (hide this current Medication Modal) and the change in medication will be a new Transcation
                            *thats associated with the existing Med Order. Then after the new Insert Transaction, we will come back and show the Current Medication Modal
                            * In order to Update the existng Med Transaction by updating the meds end medication date and udating the medchangetype field in the db
                            * Note: The Transaction_ChangeMdicationvariables method needs to carry a sub action that will essentially replicate the InsertMedication method */
                            $(".mediationmodal").css("display","flex");
                            $(".mediationmodal").find(".modaltop").css("display","none");
                            $(".mediationmodal").find(".order-grid").css("display","none");
                            $(".mediationmodal").find(".modalbottom").css("display","block");
                            $("#medsearchbox").css("display","block");
                            $(".searchresult").css("display","block");
                            $("#nextbtn").css("display","block");
                            $("#medsubmitbtn").css("display","none");
                            //now lets set the medobj Meth API
                           
                            
                            
                            //old code ends here

                        }
                    });
        
        
                });
                break;
            }
            case"DC":
            {
                ClearObjects("ViewCurrentMed");
                $(".mediationmodal .order-grid").find(".columnrow").remove();
                //lets get current Mediations for the client
                medobj.GetCurrentMed ={};
                medobj.GetCurrentMed.API_Meth="ViewCurrentMedication";
                medobj.GetCurrentMed.patientid="45064367";
               // let url="http://localhost:8888/fora/tswebook.php";
                ShowPreloader();
                $.post(postweburl,JSON.stringify(medobj))
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
                     //now lets handle the double click functionality 
                     $(".mediationmodal .order-grid").find(".columnrow").dblclick(function(e){
                        e.preventDefault();
                        console.log("double click working");
                        let ask = confirm("Are you sure you want to Discontinue this Medication? Please not that Orders can't be modified"+
                        "and will need a new order to 'Add a New Medication'. All changes will be counted as a transaction type update or an addtional transaction that corresponds to this Order");
                        if(ask==true)
                        {
                           
                             //get the information and then prefill the values before showing the Modal again
                             let medid = $(this).find("#medid").attr("medid");
                             medEntryID = medid;
                             let ordnumber = $(this).find("#medid").html();
                             alert(medid +" "+ordnumber);
                             $(".med-modifers").css("display","block");
                             $(".med-modifers").find(".dcontinuedt").css("display","block");
                             $(".savemodifiedvalbtn").css("display","block");
                             //lets the medobj attributes 
                             ClearObjects("MedicationModifer");
                             medobj.MedicationModifer ={};
                             medobj.MedicationModifer.API_Meth="Transaction_DCMedication";
                             medobj.MedicationModifer.medid= medid;
                             medobj.MedicationModifer.ordernumber = ordnumber;
                             console.log(medobj);
                            
                        }
                    });

                });
            
                break;
            }
            case"Void":
            {

                break;
            }
        }
       

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
                alert("medobj exist");
                //lets grab data and send it back to be updated 
                let medentry = medobj.MedicationEdit.medentryid;
                let ordnumber = medobj.MedicationEdit.ordernumber;
                alert(ordnumber);
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
            else if( medobj.MedicationModifer.API_Meth=="Transaction_ChangeMedicationvariables")
            {
               // var_dump("Transaction");exit();
                ClearObjects("MedicationModifer");
               // medobj.MedicationModifer.ordernumber = glblordnum;
                medobj.MedicationModifer.API_Meth="Transaction_ChangeMedicationvariables";
                medobj.MedicationModifer.ordernumber=  objordnumber;
                medobj.MedicationModifer.medname = $("#medsearchbox").val();
                medobj.MedicationModifer.doseamount = $("#doseamount").val();
                medobj.MedicationModifer.doseUOM= $("#doesUOM").val();
                medobj.MedicationModifer.frequency = $("#freqselect").val();
                medobj.MedicationModifer.diagcode = $("#medReason").val();
                medobj.MedicationModifer.medRoute = $("#medRoute").val();
                medobj.MedicationModifer.altroute = $("#altrouteselect").val();
                medobj.MedicationModifer.instruction = $("#medInstructions").val();
                medobj.MedicationModifer.medstrtdt =$("#medStartdate").val();
                medobj.MedicationModifer.medenddt = $("#Medenddate").val();
                medobj.MedicationModifer.changetype = $("#neworchange").val();
                medobj.MedicationModifer.durgclassification =$("#drugcategory").val();
                if($("#prncheckbox").prop("checked"))
                {
                    medobj.MedicationModifer.prn="true";
                }
                else{
                    medobj.MedicationModifer.prn="false";
                }
                
            
                if($("#meddirectionuse").prop("checked"))
                {
                medobj.MedicationModifer.directionuse="Direction for Use";
                
                }
                if($("#Medpurpose").prop("checked"))
                {
                medobj.MedicationModifer.directionuse="Purpose";
                
                }
                if($("#MedSideffects").prop("checked"))
                {
                medobj.MedicationModifer.directionuse="Side Effects/Interactions";
                
                }
           
                if($("#agencyadmin").prop("checked"))
                {
                    medobj.MedicationModifer.additionalsettings ="Agency Administered";
                    
                }
                if($("#highrisk").prop("checked"))
                {
                    medobj.MedicationModifer.additionalsettings="High Risk";
                    
                }
                alert("Insert MedicationModifer");
                console.log(medobj.MedicationModifer);
                //console.log(medobj +" "+obj.systemData);
                ShowPreloader();
                $.post(postweburl,JSON.stringify(medobj))
                .done(function(data){
                    StopPreloader();
                    let jdata  = JSON.parse(data);
                    console.log(jdata);
                    if(jdata.sqlresult=="Inserted")
                    {
                        alert("Medication Change Entered Successfully!");
                        //now close modal
                        $(".mediationmodal").find(".modalbottom").css("display","none");
                        $(".mediationmodal").find(".modaltop").css("display","none");
                        $(".mediationmodal").find(".order-grid").css("display","block");
                    }
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
                alert("Insert Medication");
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
     $(".savemodifiedvalbtn").click(function(e){
       let updateaction = medobj.MedicationModifer.API_Meth;
       console.log("zzz"+" "+updateaction);
       if(updateaction=="Transaction_ChangeMedicationvariables" || updateaction=="UpdatePastMedInfo_PullNew" && $("#changemeddt").val() !="")
       {
          ClearObjects("MedicationModifer");
          medobj.MedicationModifer.API_Meth="UpdatePastMedInfo_PullNew";
          medobj.MedicationModifer.ordernumber = objordnumber;
          medobj.MedicationModifer.enddate = $("#changemeddt").val();
          medobj.MedicationModifer.medentry = medEntryID;
          ShowPreloader();
          $.post(postweburl,JSON.stringify(medobj))
          .done(function(data){
              StopPreloader();
              let jdata  = JSON.parse(data);
              console.log(jdata);
              if(jdata.result=="Updated")
              {
                  alert("Medication Changed Date updated Successfully");
                  //lets run the function to Repopulate the Medication Grid 
                  PopulateMedGrid();
              }
              else
              {
                 alert("Please contact a system Administrator — Error (SQL-1023)");
              }
          });
       }
       else if(updateaction=="Transaction_DCMedication")
       {
            alert("SEnd DC Modifier");
            if($("#discontdt").val() !="" && medobj)
            {
                let dcdate = $("#discontdt").val();
                medobj.MedicationModifer.dcdate = dcdate;
                $.post(postweburl,JSON.stringify(medobj))
                .done(function(data){
                    let jdata = JSON.parse(data);
                    if(jdata.result=="Updated")
                    {
                        alert(medobj.MedicationModifer.medid +" "+"has been discountinued.");
                        PopulateMedGrid();
                    }
                });
            }
       }
       else{
          alert("Medication Change Date field is a requirement for updating Medication Change Date. Please enter a valid date.");
       }
       
     });
     $("#viewgridbtn").click(function(e){
        closeGridBtn();
    });
     function PopulateMedGrid()
     {
        ClearObjects("PopulateMedGrid");
        //now get grid for medication if they exist
        //let medobj = {};
        medobj.MedGrid = {};
        medobj.MedGrid.pid="45064367";
        medobj.MedGrid.ordernumber = glblordnum;
        medobj.MedGrid.API_Meth="PopulateMedGridByOrderID";
       // let url="http://localhost:8888/fora/tswebook.php";
        ShowPreloader();
        $.post(postweburl,JSON.stringify(medobj))
        .done(function(data){
            console.log(data);
            StopPreloader();
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
           
        });
     }
    
     function PreFillMediationField(medid)
     {
        for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
        for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
       //let medobj = {};
       console.log("made it to the prefill function");
       medobj.MedicationEdit ={};
        medobj.MedicationEdit.API_Meth="EditMedication";
        medobj.MedicationEdit.medentryid = medid;
         ShowPreloader();
        $.post(postweburl,JSON.stringify(medobj))
        .done(function(data){
         let jdata = JSON.parse(data);
         console.log(jdata);
         StopPreloader();
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
     }
    function PostData(postweburl,obj)
    {
        $.post(postweburl,JSON.stringify(obj))
        .done(function(data){
            let jdata = JSON.parse(data);
            if(jdata.count >0 && jdata.result.length >0)
            {
                //lets slide the grid up and OrderTemplate down
                SlideGridUp();
                SlideDownOrderTemplate();
                showGridBtn();
                //pull data into template 
                let dataar = jdata.result;
                console.log(dataar);
               // alert(dataar[0]["orderdate"]);
                FillData(dataar);
            }
            else{
                alert("No Results Found");
            }
        });
    }
    function SlideGridUp()
    {
        $(".ordGrid").slideUp("slow",function(){
            //nothing yet 
        });
    }
    function SlideDownOrderTemplate()
    {
        $(".orderState").slideDown("slow",function(){
            //
        });
    }
    function FillData(dataresults)
    {
        $("#orderdate").val(dataresults[0]["orderdate"]);
        $("#orderdate").attr("disabled","true");
        $("#ordertime").val(dataresults[0]["ordertime"]);
        $("#ordertime").attr("disabled","true");
        $("#ordertype").val(dataresults[0]["ordertype"]);
        $("#ordertype").attr("disabled","true");
        $("#abntopatient").val(dataresults[0]["abndelivery"]);
        $("#abntopatient").attr("disabled","true");
        $("#phyAgentphy").val(dataresults[0]["readorderback"]);
        $("#phyAgentphy").attr("disabled","true");
        $("#primphysician").val(dataresults[0]["primary_physician"]);
        $("#primphysician").attr("disabled","true");
        $("#secphysician").val(dataresults[0]["sec_physician"]);
        $("#secphysician").attr("disabled","true");
        $("#ordtrackinggrp").val(dataresults[0]["ord_trackgroup"]);
        $("#ordtrackinggrp").attr("disabled","true");
        $("#npinumber").val(dataresults[0]["npinumber"]);
        $("#npinumber").attr("disabled","true");
        $("#physaddress").val(dataresults[0]["address"]);
        $("#physaddress").attr("disabled","true");
        $("#orderphonenumber").val(dataresults[0]["phone"]);
        $("#orderphonenumber").attr("disabled","true");
        $("#orderfaxnumber").val(dataresults[0]["fax"]);
        $("#orderfaxnumber").attr("disabled","true");
        if(dataresults[0]["sendtophys"]=="true")
        {
            $("#sendtophysician").attr("checked",true);
            $("#sendtophysician").attr("disabled","true");
        }
        if(dataresults[0]["woundcare"]=="true")
        {
            $("#wondcare").attr("checked",true);
            $("#wondcare").attr("disabled","true");
        }
        if(dataresults[0]["verbalorder"]=="true")
        {
            $("#verbalorder").attr("checked",true);
            $("#verbalorder").attr("disabled","true");
        }
        if(dataresults[0]["verborder_date"] !="0000-00-00" || dataresults[0]["verborder_date"] !="")
        {
            $(".verbdatefield").val(dataresults[0]["verborder_date"]);
            $(".verbdatefield").attr("disabled","true");
        }
        if(dataresults[0]["verborder_time"] !="00:00:00")
        {
            $(".verbtimefield").val(dataresults[0]["verborder_time"]);
            $(".verbtimefield").attr("disabled","true");
        }
        if(dataresults[0]["medication"]=="true")
        {
            $("#medication").attr("checked",true);
            OrderTabOn("medication");
        }
        if(dataresults[0]["diagnosis"]=="true")
        {
            $("#diagnosis").attr("checked",true);
            OrderTabOn("diagnosis");
        }
        tinymce.get("ordertextarea").setContent(dataresults[0]["orderdescription"]);
       // alert(dataresults[0]["status"]);
        DisableMedDiagnosisBtns(dataresults[0]["status"]);
        $(".ordertextarea").attr("disabled","true");
        $(".ordclosebtn").css("display","none");
        

    }
    function showGridBtn()
    {
        if($(".orderState").css("display")=="block")
        {
              $(".gridntemplatesec").css("display","flex");
        }
      
        
    }
    function closeGridBtn()
    {
        if($(".orderState").css("display")=="block")
        {
            //slide Order up 
            $(".orderState").slideUp("slow",function(){
                //then slide down the grd
                $(".ordGrid").slideDown("slow",function(){
                     $(".gridntemplatesec").css("display","none");
                });
            });
        }
       
    }
    function ClearAllOrderFilters()
    {
        ClearObjects("ClearFilter");
        obj.Orders ={};
        obj.Orders.patientID=patientID;
        obj.Orders.API_Meth="GetALLPatientOrders";
        $.post(postweburl,JSON.stringify(obj))
        .done(function(data){
        // StopPreloader();
        console.log(data);
        let jdata = JSON.parse(data);
        console.log(jdata);
        if(jdata.Count >0 && jdata.html !="")
        {
        
            $(".ordGrid ").find(".columbottom").html('');
        
            $(".ordGrid").find(".columbottom").append(jdata.html);
            //lets add the event listener here because the grid was just added to the DOM
            $(".ordGrid").find(".columnrow").click(function(e){
                e.preventDefault();
                
                $(".columbottom").find(".columnrow").each(function(){
                    $(".columnrow").css("border-style","none");
                });
                $(this).css("border-style","dotted");
                //now find the dat-attribute so we can go get the individual record 
                let dataid = $(this).attr("orderid");
                let ordernumber = $(this).find("#medid").attr("ordernumber");
                glblordnum = ordernumber;//using the globalnum variable to find medications or diagnosis  that's associated 
                alert(ordernumber);
                //set the Orders orderid object property
                obj.Orders.orderid = dataid;
                obj.Orders.ordernumber = ordernumber;
                obj.Orders.API_Meth="ViewOrderByID";
                let mdata = PostData(postweburl,obj);

                
            }); 
 
       }
       else{
         alert("No Orders Found for Patient"+" "+obj.Orders.patientID);
       }
    });
    }
    function DisableMedDiagnosisBtns(status)
    {
        let ordstatus = status;
        //alert(ordstatus);
        if(status=="active")
        {
            //turn off buttons 
            $(".medbtn_nav").find("#delettransbtn").attr("disabled",true);
            $(".medbtn_nav").find("#delettransbtn").css("background-color","#cccccc");
            $(".medbtn_nav").find("#delettransbtn").removeAttr("href",false);

        }
    }
    function OrderTabOn(tab)
    {
        switch(tab)
        {
            case"medication":
            {
                $(".medicationtab").css("display","block");
                //disable sub buttons
                $(".medbtn_nav").find("#addtransbtn").attr("disabled","disabled");
                $(".medbtn_nav").find("#addtransbtn").css("background-color","#d3d0d0");
                $(".medbtn_nav").find("#edittransbtn").attr("disabled","disabled");
                $(".medbtn_nav").find("#edittransbtn").css("background-color","#d3d0d0");
                $(".medbtn_nav").find("#delettransbtn").attr("disabled","disabled");
                $(".medbtn_nav").find("#delettransbtn").css("background-color","#d3d0d0");
                break;
            }
            case"diagnosis":
            {
                $(".diagnosistab").css("display","block");
                $(".diagnosisbtn_nav").find("#adddiagbtn").attr("disabled","disabled");
                $(".diagnosisbtn_nav").find("#adddiagbtn").css("background-color","#d3d0d0");
                $(".diagnosisbtn_nav").find("#editdiagnosisbtn").attr("disabled","disabled");
                $(".diagnosisbtn_nav").find("#editdiagnosisbtn").css("background-color","#d3d0d0");
                $(".diagnosisbtn_nav").find("#delettransbtn").attr("disabled","disabled");
                $(".diagnosisbtn_nav").find("#delettransbtn").css("background-color","#d3d0d0");
                break;
            }
            case"ValueSign":
            {
                
                break;
            }
        }
    }
    function OrderTabOff(tab)
    {
        switch(tab)
        {
            case"medication":
            {
                $(".medicationtab").css("display","none");
                break;
            }
            case"diagnosis":
            {
                $(".diagnosistab").css("display","none");
                break;
            }
            case"ValueSign":
            {
                
                break;
            }
        }
    }
    function ShowProgress()
    {
        $(".preloader").css("display","block");
    }
    function StopProgress()
    {
        $(".preloader").css("display","none");
    }
    function ShowPreloader()
   {
        $("#orderform").find(".preloader").css("display","block");
   }
   function StopPreloader()
   {
        $("#orderform").find(".preloader").css("display","none");
   }
   function ClearObjects(currentobj)
   {
     switch(currentobj)
     {
        case"FilterNotes":
        {
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
           for (var member in medobj.MedicationModifer) delete medobj.MedicationModifer[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in obj.Orders) delete obj.Orders[member];
            for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
            break;
        }
        case"FilterOrders":
        {
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
           for (var member in medobj.MedicationModifer) delete medobj.MedicationModifer[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in obj.Note) delete obj.Note[member];
            for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
            break;
        }
        case"GetPatientOrders":
        {
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
           for (var member in medobj.MedicationModifer) delete medobj.MedicationModifer[member];
           // for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
            break;
        }
        case"OrderFilterByDate":
        {

             break;
        }
        case"ClearFilter":
        {
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
           for (var member in medobj.MedicationModifer) delete medobj.MedicationModifer[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
           break;
        }
        case"PopulateMedGrid":
        {
             //clear all but Medication Edit 
            // for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
             for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
             for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
             for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in medobj.MedicationModifer) delete medobj.MedicationModifer[member];
             for (var member in obj.systemData) delete obj.systemData[member];
             for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
            break;
        }
        case"MedicationEdit":
        {
            //clear all but Medication Edit 
            for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
           // for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
           for (var member in medobj.MedicationModifer) delete medobj.MedicationModifer[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
            break;
        }
        case"MedicationInsert":
        {
            //clear all but MedInsert
            for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            //for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in medobj.MedicationModifer) delete medobj.MedicationModifer[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
            break;
        }
        case"ViewCurrentMed":
        {
            //clear all but MedInsert
            for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            //for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in medobj.MedicationModifer) delete medobj.MedicationModifer[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
        }
        case"MedicationModifer":
        {
            for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
           // for (var member in medobj.MedicationModifer) delete medobj.MedicationModifer[member];
        }
        case"System":
        {
            //clear All but system 
            for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in medobj.MedicationModifer) delete medobj.MedicationModifer[member];
            for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
           // for (var member in obj.systemData) delete obj.systemData[member];
            break; 
        }
        case"PopulateDiagnosisGrid":
        {
            for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in medobj.MedicationModifer) delete medobj.MedicationModifer[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            for (var member in obj) delete obj[member];
            //for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
        }
        case"UpdateDiagnosis":
        {
            for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in medobj.MedicationModifer) delete medobj.MedicationModifer[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            //for (var member in diagobj.Diagnosis) delete diagobj.Diagnosis[member];
            break;
        }
        case"InsertDiagnosis":
        {
            for (var member in medobj.MedGrid) delete medobj.MedGrid[member];
            for (var member in medobj.GetCurrentMed) delete medobj.GetCurrentMed[member];
            for (var member in medobj.MedicationInsert) delete medobj.MedicationInsert[member];
            for (var member in medobj.MedicationEdit) delete medobj.MedicationEdit[member];
            for (var member in medobj.MedicationModifer) delete medobj.MedicationModifer[member];
            for (var member in obj.systemData) delete obj.systemData[member];
            //for (var member in diagobj.EditDiagnosis.E) delete diagobj.Diagnosis[member];
            break;
        }
     }
   }

});