$(function()
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
          'bullist numlist checklist outdent indent | removeformat | a11ycheck code table help'
      });
   });

   $("#msgbtn").click(function(e){
      e.preventDefault();
      let note = tinyMCE.activeEditor.getContent({format:'raw'});
      let obj = {};
      obj.API_Meth="SavePatientNote";
      obj.PatientNote=note;
      obj.patientID="XX234534";
      obj.notify = $("#notifyptchkbx").prop('checked');
      console.log(obj.notify);
      let posturl="https://willowbuilt.it/fora/tswebook.php";
      $.post(posturl,obj)
        .done(function(ddata){
        console.log(ddata);
        var mdata = JSON.parse(ddata);
        if(mdata.msg=="Inserted")
        {
            
            //lets show the success msg
            $("#msglbl").attr("class","notemsglbl success");
            $("#msglbl").css("display","block");
            $("#msglbl").html("Message Save Successfully");
        }
        else{

            $("#msglbl").attr("class","notemsglbl error");
            $("#msglbl").css("display","block");
            $("#msglbl").html("Message Not Saved | Please Try Again or Notify System Admin");
        }
      });
   });

   $(".modalcancelbtn").click(function(e){
     e.preventDefault();
     //remove label if there are any
     $("#msglbl").attr("class","notemsglbl");
     $("#msglbl").css("display","none");
     $(".msboxwrapper").css("display","none");
     //clear the editor 
     tinyMCE.activeEditor.setContent('');
   });

   $(".providfield").on("keydown", function(e){
     e.preventDefault();
     $("provboxwrapper").css("display","flex");
   });
});