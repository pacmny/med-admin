$(function()
{
  $(document).ready(function()
  {
   $(".coltext.msgiconbtn").click(function(e){
    e.preventDefault();
    $(".msboxwrapper").css("display","flex");
    //now lets enqueue tinyMCE
    tinymce.init({
        selector: '#ptxtarea',
        plugins: [
          'a11ychecker','advlist','advcode','advtable','autolink','checklist','export',
          'lists','link','image','charmap','preview','anchor','searchreplace','visualblocks',
          'powerpaste','fullscreen','formatpainter','insertdatetime','media','table','help','wordcount'
        ],
        toolbar: 'undo redo | formatpainter casechange blocks | bold italic backcolor | ' +
          'alignleft aligncenter alignright alignjustify | ' +
          'bullist numlist checklist outdent indent | removeformat | |urldialog | a11ycheck code table help',
          
          setup: function (editor) {
            editor.ui.registry.addButton('urldialog', {
              icon: 'document-properties',
              onAction: function () {
                editor.windowManager.openUrl({
                  title: 'Pacmny Orders Template',
                  url: 'https://willowbuilt.it/fora/Order.html',
                  height: 940,
                  width: 1640,
                  content_css: 'css/style.css'
                });
              }
            })
          },
      });
   });
  
   $("#msgbtn").click(function(e){
      e.preventDefault();
      //lets do some testng for trishant here 
      let patientobj = {};
patientobj.AddPatientInfo = {};
patientobj.AddPatientInfo.API_Meth ="AddPatientInformation";
patientobj.AddPatientInfo.pid ="xX468249";
patientobj.AddPatientInfo.paccount="xX4554829";
patientobj.AddPatientInfo.ppassword ="CnfE2u4dg0";
patientobj.AddPatientInfo.psaid ="E01";
patientobj.AddPatientInfo.ptz ="T0002";
patientobj.AddPatientInfo.pname ="Gavin Geeters";
patientobj.AddPatientInfo.pbday= "";
patientobj.AddPatientInfo.psex = "1";
patientobj.AddPatientInfo.pheight = 159;
patientobj.AddPatientInfo.pweight =60;
patientobj.AddPatientInfo.ptel1 ="";
patientobj.AddPatientInfo.ptel2 = "";
patientobj.AddPatientInfo.pemail ="gavin@gmail.com";
patientobj.AddPatientInfo.pTgetup ="06:00";
patientobj.AddPatientInfo.ptbreakfast ="08:00";
patientobj.AddPatientInfo.ptlunch ="12:00";
patientobj.AddPatientInfo.ptdinner ="18:00";
patientobj.AddPatientInfo.ptsleep ="22:00";
patientobj.AddPatientInfo.paddr ="49 Sea Eagle Ct";
patientobj.AddPatientInfo.prace = "";
patientobj.AddPatientInfo.metertype ="TD3261";
patientobj.AddPatientInfo.meterexid ="1";
patientobj.AddPatientInfo.meterid = "";
patientobj.AddPatientInfo.gatewayid ="";

let nursobj = {};
nursobj.NursyInfo ={};
nursobj.NursyInfo.API_Meth="LookupNursesInfo"
nursobj.NursyInfo.ncsid="21237245";
nursobj.NursyInfo.jurisdiction="KY";
nursobj.NursyInfo.rnlicense="6093812";
nursobj.NursyInfo.ltype="RN";

let manursobj ={};
manursobj.ManageNurseList ={};
manursobj.ManageNurseList.API_Meth="Add_Nurse";
manursobj.ManageNurseList.ncsid="3223099";
manursobj.ManageNurseList.subactioncode="A";
manursobj.ManageNurseList.jurisdiction="IN";
manursobj.ManageNurseList.licensenum="28146329";
manursobj.ManageNurseList.ltype="RN";
manursobj.ManageNurseList.address1="4308 Lake Field Ct";
manursobj.ManageNurseList.address2="";
manursobj.ManageNurseList.city="Indianapolis";
manursobj.ManageNurseList.state="IN";
manursobj.ManageNurseList.zip="46254";
manursobj.ManageNurseList.lastfourssn="1787";
manursobj.ManageNurseList.bdayear="1970";
manursobj.ManageNurseList.hospitalpracsetting="8";
manursobj.ManageNurseList.hostpracsettingother="";
manursobj.ManageNurseList.notficationenabled="Y";
manursobj.ManageNurseList.reminderenabled="Y";
manursobj.ManageNurseList.locationlist="";
manursobj.ManageNurseList.recid="";
console.log(nursobj);
      let note = tinyMCE.activeEditor.getContent({format:'raw'});
      let obj = {};
      obj.API_Meth="SavePatientNote";
      obj.PatientNote=note;
      obj.patientID="XX234534";
      obj.notify = $("#notifyptchkbx").prop('checked');
      console.log(obj.notify);
      //let posturl="http://localhost:8888/fora/tswebook.php";
      let posturl="https://willowbuilt.it/fora/tswebook.php";
      $.post(posturl, obj)
        .done(function(ddata){
        console.log(ddata);
        var mdata = JSON.parse(ddata);
        if(mdata.msg=="Inserted")
        {
            alert(mdata.msg);
            //lets show the success msg
            $("#msglbl").attr("class","notemsglbl success");
            $("#msglbl").css("display","block");
            $("#msglbl").html("Message Save Successfully");
        }
      });
   });

   $(".modalcancelbtn").click(function(e){
     e.preventDefault();
     //remove label if there are any
     $("#msglbl").attr("class","notemsglbl");
     $("#msglbl").css("display","none");
     $(".msboxwrapper").css("display","none");
   });

   $(".providfield").on("keydown", function(e){
     e.preventDefault();
     $("provboxwrapper").css("display","flex");
   });
  });
  
});