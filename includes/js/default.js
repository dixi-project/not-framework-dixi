function mandarCornestone(){
  var r = confirm(unescape("¿Desea mandar los datos a Cornerstone?"));
  if (r == false) {
      return false;
  }
  alert("Datos enviados!!! :)");
}

$(".cmdDesplegarChange").click(function () {
    var dataTableID = $(this).attr("data-tableid");
    var path = $("#path").val();
    $.ajax({
      url: path + "includes/ajax/desplegarChange.php",
      data: {
        dataTableID: dataTableID,
      },
      type: "POST",
      dataType: "json",
    });
  });
  


  $(".imgPrincipal").click(function () {
    var dataTableID = $(this).attr("data-tableid");
    var path = $("#path").val();
    activar = 0;
    if ($(this).is(":checked")) {
      activar = 1;
    }

    $.ajax({
      url: path + "includes/ajax/updateIMGTable.php",
      data: {
        dataTableID: dataTableID,
        activar: activar,
      },
      type: "POST",
      dataType: "json",
    });
  });

  $(".pdfPrincipal").click(function () {
    var dataTableID = $(this).attr("data-tableid");
    var path = $("#path").val();
    activar = 0;
    if ($(this).is(":checked")) {
      activar = 1;
    }

    $.ajax({
      url: path + "includes/ajax/updatePDFTable.php",
      data: {
        dataTableID: dataTableID,
        activar: activar,
      },
      type: "POST",
      dataType: "json",
    });
  });

  $(".viewTable").click(function () {
    var dataType = $(this).attr("data-type");
    var dataFieldID = $(this).attr("data-fieldid");
    var dataTableID = $(this).attr("data-tableid");
    var path = $("#path").val();
    activar = 0;
    if ($(this).is(":checked")) {
      activar = 1;
    }

    $.ajax({
      url: path + "includes/ajax/updateView.php",
      data: {
        dataFieldID: dataFieldID,
        dataTableID: dataTableID,
        activar: activar,
        dataType: dataType,
      },
      type: "POST",
      dataType: "json",
    });
  });

  $(".txtNameFiell").blur(function () {
    var dataFieldID = $(this).attr("data-fieldid");
    var namFiled = $(this).val();
    var path = $("#path").val();
    $.ajax({
      url: path + "includes/ajax/updateNamFille.php",
      data: {
        dataFieldID: dataFieldID,
        namFiled: namFiled,
      },
      type: "POST",
      dataType: "json",
    });
  });
  $(".txtNumFile").blur(function () {
    var dataTableID = $(this).attr("data-tableid");
    var numFile = $(this).val();
    var path = $("#path").val();
    $.ajax({
      url: path + "includes/ajax/updateNumFiles.php",
      data: {
        dataTableID: dataTableID,
        numFile: numFile,
      },
      type: "POST",
      dataType: "json",
    });
  });
  $(".txtNameEntity").blur(function () {
    var dataTableID = $(this).attr("data-tableid");
    var newName = $(this).val();
    var path = $("#path").val();
    $.ajax({
      url: path + "includes/ajax/updateNameEntity.php",
      data: {
        dataTableID: dataTableID,
        newName: newName,
      },
      type: "POST",
      dataType: "json",
    });
  });




  
$("#cmdRegresar").click(function () {
    var action = $(this).attr('data-action');
    var path = $("#path").val();
    $("#frm1").attr('action', path + action);
    document.getElementById("frm1").submit();
});

function verInfo(idCandd,path){
    var data = "idCAnd=" + idCandd;
    //$("#idCandidat").val(idCandd);
    var html = $.ajax({
        url: path+"includes/ajax/infoCandidato.php",
        type: "POST",
        data: data,
        async: false
    }).responseText;
    $('#capaInfo').html(html);
    $('#myModal66').modal('toggle');
}

function verDeatell(idVacante,path){
    var data = "idVac=" + idVacante;
    $("#idVacante").val(idVacante);
    var html = $.ajax({
        url: path+"includes/ajax/vacante.php",
        type: "POST",
        data: data,
        async: false
    }).responseText;
    $('#capaVAcante').html(html);
    $('#myModal5').modal('toggle');
}
 


function tablaSalidas(path,$id_empresa){
  $.ajax({
    url: path+"/includes/ajax/tablaProductos.php",
    success: function(response){

    }

  });



}

function ModeloGPS(id_registro,id_empresa,telefono,id_Modelo,path){
    $('#myModal').modal('toggle');

    var mensaje1;
    var mensaje2;
    var mensaje3;
    var mensaje4;
    var mensaje5;
   

    const boton = document.querySelector("#enviar");

      $.ajax({
        url: path+"/includes/ajax/modelos.php",
        type: "POST",
        data: "idModelo="+id_Modelo,
        cache: false,
        processData: false,
        dataType: "json",
      success:function(response){

        //console.log(response);
        
      $("#modelo").html("MESAJES DE CONFIGURACION DE GPS "+response[0]["modelo"]);

      /*=========================AJAX PARA CAPTURAR LOS PARAMETROS GPS=================================*/
 
       var dat = {idEmpresa : id_empresa, idModelo : id_Modelo};

        $.ajax({
        url: path+"/includes/ajax/parametros_gps.php",
        type: "POST",
        data: dat,
        cache: false,
        dataType: "json",
      success:function(response2){

        var apn = response2[0]["APN"];
        var usuario = response2[0]["nombre_usuario"];
        var contrasena = response2[0]["contrasena"];
        var ip = response2[0]["ip"];
        var puerto = response2[0]["puerto"];
        var timer = response2[0]["timer"];
        var ip_backup = response2[0]["ip_backup"];
        var puerto_backup = response2[0]["puerto_backup"];

    
        /*=======MENSAJE 1======*/

        if(response[0]["cadena1"] !="" && response[0]["cadena1"] != null ){
            txt1 = response[0]["cadena1"];
            txt1 =txt1.replace("[apn]",apn);
            txt1 =txt1.replace("[contrasena]",contrasena);
            txt1=txt1.replace("[usuario]",usuario);
            txt1= txt1.replace("[ip]",ip);
            txt1=txt1.replace("[puerto]",puerto);
            txt1=txt1.replace("[timer]",timer);
            txt1=txt1.replace("[ip_backup]",ip_backup);
            txt1= txt1.replace("[puerto_backup]",puerto_backup);

            mensaje1 = txt1;
        }
        /*=======MENSAJE 2======*/
           if(response[0]["cadena2"] !="" && response[0]["cadena2"] != null ){
            txt2 = response[0]["cadena2"];
            txt2 =txt2.replace("[apn]",apn);
            txt2 =txt2.replace("[contrasena]",contrasena);
            txt2= txt2.replace("[ip]",ip);
            txt2=txt2.replace("[puerto]",puerto);
            txt2=txt2.replace("[timer]",timer);
            txt2=txt2.replace("[ip_backup]",ip_backup);
            txt2= txt2.replace("[puerto_backup]",puerto_backup);

            mensaje2 = txt2;

           }
        /*=======MENSAJE 3======*/
            if(response[0]["cadena3"] !="" && response[0]["cadena3"] != null ){
            txt3 = response[0]["cadena3"];
            txt3 =txt3.replace("[apn]",apn);
            txt3 =txt3.replace("[contrasena]",contrasena);
            txt3= txt3.replace("[ip]",ip);
            txt3=txt3.replace("[puerto]",puerto);
            txt3=txt3.replace("[timer]",timer);
            txt3=txt3.replace("[ip_backup]",ip_backup);
            txt3= txt3.replace("[puerto_backup]",puerto_backup);

            mensaje3 = txt3;

           }
                   /*=======MENSAJE 4======*/
              if(response[0]["cadena4"] !="" && response[0]["cadena4"] != null ){
            txt4 = response[0]["cadena4"];
            txt4 =txt4.replace("[apn]",apn);
            txt4 =txt4.replace("[contrasena]",contrasena);
            txt4 = txt4.replace("[ip]",ip);
            txt4 =txt4.replace("[puerto]",puerto);
            txt4 =txt4.replace("[timer]",timer);
            txt4 =txt4.replace("[ip_backup]",ip_backup);
            txt4 = txt4.replace("[puerto_backup]",puerto_backup);

            mensaje4 = txt4;

           }
        /*=======MENSAJE 5======*/
            if(response[0]["cadena5"] !="" && response[0]["cadena5"] != null ){
           
            txt5 =response[0]["cadena5"];
            txt5 =txt5.replace("[apn]",apn);
            txt5 =txt5.replace("[contrasena]",contrasena);
            txt5 =txt5.replace("[ip]",ip);
            txt5 =txt5.replace("[puerto]",puerto);
            txt5 =txt5.replace("[timer]",timer);
            txt5 =txt5.replace("[ip_backup]",ip_backup);
            txt5 =txt5.replace("[puerto_backup]",puerto_backup);

            mensaje5 = txt5;

           }
         $("#cadena1").val(mensaje1);
         $("#cadena2").val(mensaje2);
         $("#cadena3").val(mensaje3);
         $("#cadena4").val(mensaje4);
         $("#cadena5").val(mensaje5);
             

          boton.addEventListener("click", function(evento){
       

            var datos = { m1 : mensaje1, m2 : mensaje2, m3 : mensaje3, m4 : mensaje4, m5 : mensaje5 , telefono : telefono, id : id_registro};


            $.ajax({
              url:path+"/includes/ajax/twilio.php",
              type: "POST",
              data: datos,
              dataType:"json",
              success: function(response3){
             // console.log(response3);
              if(response3["mensaje"] =="enviado"){
                  alert("Mensaje Enviado");
                  location.reload();
                  // $('#myModal').modal('hide');
        
              }


              } 

            });
        });
      

      



    
       //console.log(response2[0]["APN"]);

    }
  });
 

    }
  });


    


}

function conocimientos(idVacante,path){
    var data = "idVac=" + idVacante;
    $("#idVacante2").val(idVacante);
    var html = $.ajax({
        url: path+"includes/ajax/conocimientos.php",
        type: "POST",
        data: data,
        async: false
    }).responseText;
    $('#capaConocimiento').html(html);
    $('#myModal3').modal('toggle');
    $("#txtConocimiento").focus();
}

function requerimeintos(idVacante,path){
    var data = "idVac=" + idVacante;
    $("#idVacante1").val(idVacante);
    var html = $.ajax({
        url: path+"includes/ajax/requerimientos.php",
        type: "POST",
        data: data,
        async: false
    }).responseText;
    $('#capaRequerimiento').html(html);
    $('#myModal4').modal('toggle');
    $("#capaRequerimiento").focus();
}

function verURLS(idVacante,path){
    var data = "idVac=" + idVacante;
    var html = $.ajax({
        url: path+"includes/ajax/urls.php",
        type: "POST",
        data: data,
        async: false
    }).responseText;
    $('#capaObjCDC').html(html);
    $('#myModal1').modal('toggle');
}

function addDatos(idVacante,path){

   $('#txtIDVAc').val(idVacante);
    var data = "idVac=" + idVacante;
    var html = $.ajax({
        url: path+"includes/ajax/addDatos.php",
        type: "POST",
        data: data,
        async: false
    }).responseText;
    $('#capaObjAdd').html(html);
    $('#myModal2').modal('toggle');
    $('#txtNamCon').val("");
    $('#txtNamCon').focus();
}

function clickAddCo(path){
  var idVAc = $("#txtIDVAc").val();
  var nam = $("#txtNamCon").val();
  var data = "idVac=" + idVAc+"&nam="+nam;
  var html = $.ajax({
      url: path+"includes/ajax/addDatos.php",
      type: "POST",
      data: data,
      async: false
  }).responseText;
  $("#capaObjAdd").html(html);
  $("#txtNamCon").val("");
  $("#txtNamCon").focus();
}


/*
  1.-Boton submit
  2.-formulacio action ->adonde va ir
  3.-Controles guarda y genera el pdf
*/

function clickBtnAJax2(id, action, ruta, frm) {
  var action="delete";
  var ruta ="Salidas";
  var frm ="frm2";
  try{

    var path = $("#path").val();
    if (action == "delete") {
        var r = confirm(unescape("¿Desea eliminar el registro?"));
        if (r == false) {
            return false;
        }
    }
    $("#idReg").val(id);
    $("#Action").val(action);
    $("#" + frm).attr('action', path + ruta);
    $("#" + frm).submit();
  }catch(e){alert(e);}

}

function clickBtnAJax(id, action, ruta, frm) {
  var action="editar";
  var ruta ="agregarSalidas";
  var frm ="frm2";
  try{

    var path = $("#path").val();
    if (action == "delete") {
        var r = confirm(unescape("¿Desea eliminar el registro?"));
        if (r == false) {
            return false;
        }
    }
    $("#idReg").val(id);
    $("#Action").val(action);
    $("#" + frm).attr('action', path + ruta);
    $("#" + frm).submit();
  }catch(e){alert(e);}

}


function clickBtnAJax3(id_entrada,id_salida) {
  var action="ticket";
  var ruta ="comprobante";
  var frm ="frm2";
  try{

    var path = $("#path").val();
    if (action == "delete") {
        var r = confirm(unescape("¿Desea eliminar el registro?"));
        if (r == false) {
            return false;
        }
    }
    $("#id_entrada").val(id_entrada);
    $("#id_salida").val(id_salida);
    $("#Action").val(action);
    $("#" + frm).attr('action', path + ruta);
    $("#" + frm).submit();
  }catch(e){alert(e);}

}


function clickBtn(id, action, ruta, frm) {
  try{
      
    var path = $("#path").val();
    if (action == "delete") {
        var r = confirm(unescape("¿Desea eliminar el registro?"));
        if (r == false) {
            return false;
        }
    }
    $("#idReg").val(id);
    $("#Action").val(action);
    $("#" + frm).attr('action', path + ruta);
    $("#" + frm).submit();
  }catch(e){alert(e);}

}

function clickBtn2(id, action, ruta, frm) {
    var path = $("#path").val();
    if (action == "delete") {
        var r = confirm(unescape("¿Desea eliminar el registro?"));
        if (r == false) {
            return false;
        }
    }

    if (action == "subir") {
        var r = confirm(unescape("Si decide programar este envío quedara fijo?"));
        if (r == false) {
            return false;
        }
    }

    $("#idReg2").val(id);
    $("#Action").val(action);
    $("#" + frm).attr('action', path + ruta);
    $("#" + frm).submit();
}

function clickBtn45(id, action, ruta, frm) {
    var path = $("#path").val();
    if (action == "activar") {
        var r = confirm(unescape("¿Esta seguro de querer aceptar a este candidato?"));
        if (r == false) {
           alert("NO");
            $(this).prop('checked', false)
            return false;
        }
    }
      $("#idReg").val(id);
      $("#Action").val(action);
      $("#" + frm).attr('action', path + ruta);
      $("#" + frm).submit();

}

function iraa(id, action, ruta, frm) {

    var path = $("#path").val();
    //if ($(this).is(':checked')) {
    if(action=="aceptar"){
        var r = confirm(unescape("¿Esta seguro de aceptar a este candidato?"));
        if (r == false) {
            //$(this).prop('checked', false)
            return false;
        }
        var action = "activar";
    }
    //if (!$(this).is(':checked')) {
    if(action=="rechazar"){
        var r = confirm(unescape("¿Esta seguro de rechazar a este candidato?"));
        if (r == false) {
            //$(this).prop('checked', true)
            return false;
        }
        var action = "desactivar";
    }


      $("#idReg").val(id);
      $("#Action").val(action);
      $("#" + frm).attr('action', path + ruta);
      $("#" + frm).submit();
}


$(document).ready(function () {



  $('.btnEdit').click(function() {

      var dataId = $(this).attr("dataId");
      var dataAccion = $(this).attr("dataAccion");
      var dataDestino = $(this).attr("dataDestino");
      var dataForm = $(this).attr("dataForm");

    try{

    var path = $("#path").val();
    if (dataAccion == "delete") {
        var r = confirm(unescape("¿Desea eliminar el registro?"));
        if (r == false) {
            return false;
        }
    }
    $("#idReg").val(dataId);
    $("#Action").val(dataAccion);
    $("#" + dataForm).attr('action', path + dataDestino);
    $("#" + dataForm).submit();


  }catch(e){alert(e);}
      
  });











  $('.chePer1').click(function() {
      var path = $(this).attr("path-id");
      var actionid = $(this).attr("action-id");
      var rolid = $(this).attr("rol-id");
      var moduloid = $(this).attr("modulo-id");
      var activar = 2;
      if ($(this).is(':checked')) {
            activar = 1;
      }
      var data = "r="+rolid+"&m="+moduloid+"&ac="+actionid+"&a="+activar;
      var html = $.ajax({
          url: path+"includes/ajax/updatePermisos.php",
          type: "POST",
          data: data,
          async: false
      }).responseText;
  });

  $('.chePer2').click(function() {
      var path = $(this).attr("path-id");
      var actionid = $(this).attr("action-id");
      var rolid = $(this).attr("rol-id");
      var moduloid = $(this).attr("modulo-id");
      var activar = 2;
      if ($(this).is(':checked')) {
            activar = 1;
      }
      var data = "r="+rolid+"&m="+moduloid+"&ac="+actionid+"&a="+activar;
      var html = $.ajax({
          url: path+"includes/ajax/updatePermisos.php",
          type: "POST",
          data: data,
          async: false
      }).responseText;
  });

  $('.chePer3').click(function() {
      var path = $(this).attr("path-id");
      var actionid = $(this).attr("action-id");
      var rolid = $(this).attr("rol-id");
      var moduloid = $(this).attr("modulo-id");
      var activar = 2;
      if ($(this).is(':checked')) {
            activar = 1;
      }
      var data = "r="+rolid+"&m="+moduloid+"&ac="+actionid+"&a="+activar;
      var html = $.ajax({
          url: path+"includes/ajax/updatePermisos.php",
          type: "POST",
          data: data,
          async: false
      }).responseText;
  });

  $('.chePer4').click(function() {
      var path = $(this).attr("path-id");
      var actionid = $(this).attr("action-id");
      var rolid = $(this).attr("rol-id");
      var moduloid = $(this).attr("modulo-id");
      var activar = 2;
      if ($(this).is(':checked')) {
            activar = 1;
      }
      var data = "r="+rolid+"&m="+moduloid+"&ac="+actionid+"&a="+activar;
      var html = $.ajax({
          url: path+"includes/ajax/updatePermisos.php",
          type: "POST",
          data: data,
          async: false
      }).responseText;
  });

  $('.chePer1All').click(function() {

      var path = $(this).attr("path-id");
      var actionid = $(this).attr("action-id");
      var rolid = $(this).attr("rol-id");
      var activar = 2;
      if ($(this).is(':checked')) {
            activar = 1;
            $(".chePer1").prop("checked", true);
      }else{
            $(".chePer1").prop("checked", false);
      }
      var data = "r="+rolid+"&ac="+actionid+"&a="+activar;

      var html = $.ajax({
          url: path+"includes/ajax/updatePermisos.php",
          type: "POST",
          data: data,
          async: false
      }).responseText;

  });

  $('.chePer2All').click(function() {
      var path = $(this).attr("path-id");
      var actionid = $(this).attr("action-id");
      var rolid = $(this).attr("rol-id");
      var activar = 2;
      if ($(this).is(':checked')) {
            activar = 1;
            $(".chePer2").prop("checked", true);
      }else{
            $(".chePer2").prop("checked", false);
      }
      var data = "r="+rolid+"&ac="+actionid+"&a="+activar;
      var html = $.ajax({
          url: path+"includes/ajax/updatePermisos.php",
          type: "POST",
          data: data,
          async: false
      }).responseText;
  });

  $('.chePer3All').click(function() {
      var path = $(this).attr("path-id");
      var actionid = $(this).attr("action-id");
      var rolid = $(this).attr("rol-id");
      var activar = 2;
      if ($(this).is(':checked')) {
            activar = 1;
            $(".chePer3").prop("checked", true);
      }else{
            $(".chePer3").prop("checked", false);
      }
      var data = "r="+rolid+"&ac="+actionid+"&a="+activar;
      var html = $.ajax({
          url: path+"includes/ajax/updatePermisos.php",
          type: "POST",
          data: data,
          async: false
      }).responseText;
  });

  $('.chePer4All').click(function() {
      var path = $(this).attr("path-id");
      var actionid = $(this).attr("action-id");
      var rolid = $(this).attr("rol-id");
      var activar = 2;
      if ($(this).is(':checked')) {
            activar = 1;
            $(".chePer4").prop("checked", true);
      }else{
            $(".chePer4").prop("checked", false);
      }
      var data = "r="+rolid+"&ac="+actionid+"&a="+activar;
      var html = $.ajax({
          url: path+"includes/ajax/updatePermisos.php",
          type: "POST",
          data: data,
          async: false
      }).responseText;
  });


  $("#TUnidadN").change(function () {
      $("#frm2").submit();
  });

  $("#Tvacante").change(function () {
      $("#frm2").submit();
  });
/*
  $('.iraa').change(function(event){
      var id = $(this).attr("data-id");
      var accion = $(this).attr("data-action");
      var ruta = $(this).attr("data-ira");
      var frm = "frm1";
      var path = $("#path").val();
      //if ($(this).is(':checked')) {
      if(accion=="aceptar"){
          var r = confirm(unescape("¿Esta seguro de aceptar a este candidato?"));
          if (r == false) {
              //$(this).prop('checked', false)
              return false;
          }
          var action = "activar";
      }
      //if (!$(this).is(':checked')) {
      if(accion=="rechazar"){
          var r = confirm(unescape("¿Esta seguro de desactivar a este candidato?"));
          if (r == false) {
              //$(this).prop('checked', true)
              return false;
          }
          var action = "desactivar";
      }


        $("#idReg").val(id);
        $("#Action").val(action);
        $("#" + frm).attr('action', path + ruta);
        $("#" + frm).submit();
  });
*/
$('#txtempresa_id').change(function(event){
  var idempresa =$(this).val();
  var path = $(this).attr("data-path");
var data = "idEmp=" + idempresa;
        var html = $.ajax({
            url: path+"includes/ajax/empleados.php",
            type: "POST",
            data: data,
            async: false
        }).responseText;
        $("#capaEmpleados").html(html);
});

  $('.btnpCan').change(function(event){
      var id = $(this).attr("data-id");
      if($(this).is(':checked')) {
          $("#idReg").val(id);
          $("#Action").val("activar");
          $("#frm1").attr('action', "//catalogo/candidatos");
          $("#frm1").submit();
      }
  });

    $("#addCuali").click(function () {
        var path = $(this).attr("data-path");
        var idVacante = $("#idVacante").val();
        var txtCualidad = $("#txtCualidad").val();
        var data = "idVac=" + idVacante + "&cuali=" + txtCualidad;
        var html = $.ajax({
            url: path+"includes/ajax/cualidades.php",
            type: "POST",
            data: data,
            async: false
        }).responseText;
        $("#capaCualidades").html(html);
        // --> Limpiar campo
        $("#txtCualidad").val("");
    });

    $("#addConoci").click(function () {
        var path = $(this).attr("data-path");
        var idVacante = $("#idVacante2").val();
        var txtConocimiento = $("#txtConocimiento").val();
        var data = "idVac=" + idVacante + "&conoci=" + txtConocimiento;
        var html = $.ajax({
            url: path+"includes/ajax/conocimientos.php",
            type: "POST",
            data: data,
            async: false
        }).responseText;
        $("#capaConocimiento").html(html);
        // --> Limpiar campo
        $("#txtConocimiento").val("");
    });

    $("#addReque").click(function () {
        var path = $(this).attr("data-path");
        var idVacante = $("#idVacante1").val();
        var txtRequerimiento = $("#txtRequerimiento").val();
        var data = "idVac=" + idVacante + "&requeri=" + txtRequerimiento;
        var html = $.ajax({
            url: path+"includes/ajax/requerimientos.php",
            type: "POST",
            data: data,
            async: false
        }).responseText;
        $("#capaRequerimiento").html(html);
        // --> Limpiar campo
        $("#txtRequerimiento").val("");
    });


/*
    tinymce.init({
        selector: "textarea.mceEditor",
        theme: "modern",
        file_browser_callback: RoxyFileBrowser,
        plugins: [
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons template paste textcolor colorpicker textpattern imagetools"
        ],
        toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
        toolbar2: "print preview media | forecolor backcolor emoticons",
        image_advtab: true,
        templates: [
            {title: 'Test template 1', content: 'Test 1'},
            {title: 'Test template 2', content: 'Test 2'}
        ]
    });
    function RoxyFileBrowser(field_name, url, type, win) {
        var roxyFileman = '{/literal}{$pathSite}{literal}fileman/index.html';
        if (roxyFileman.indexOf("?") < 0) {
            roxyFileman += "?type=" + type;
        } else {
            roxyFileman += "&type=" + type;
        }
        roxyFileman += '&input=' + field_name + '&value=' + win.document.getElementById(field_name).value;
        if (tinyMCE.activeEditor.settings.language) {
            roxyFileman += '&langCode=' + tinyMCE.activeEditor.settings.language;
        }
        tinyMCE.activeEditor.windowManager.open(
                {
                    file: roxyFileman,
                    title: 'Roxy Fileman',
                    width: 850,
                    height: 650,
                    resizable: "yes",
                    plugins: "media",
                    inline: "yes",
                    close_previous: "no"
                },
                {
                    window: win,
                    input: field_name
                });
        return false;
    }
    */
    $(".cmdSubmit").click(function () {
        var id = $(this).attr("data-id");
        var action = $(this).attr("data-action");
        var frmm = $(this).attr("data-frm");
        var field1 = $(this).attr("data-field");
        $("#" + field1).val(id);
        $("#" + frmm).attr('action', action);
        $("#" + frmm).submit();
    });

    $("#txtCondicion_id").change(function () {
        var id = $(this).val();
        if(id == 7){
            $("#capaValor2").show();
        }else{
            $("#capaValor2").hide();
        }
    });

    $("#ch1").click(function () {

        if($(this).is(':checked')) {
            $("#capaValor3").show();
            $("#capaValor1").hide();
            $("#capaValor2").hide();
        }else{
            $("#capaValor3").hide();
            $("#capaValor1").show();
            var idCon = $("#txtCondicion_id").val();
            if(idCon == 7){
                $("#capaValor2").show();
            }
        }
    });




});
/*
$(function() { "use strict";
       $('.bootstrap-datepicker').bsdatepicker({
           format: 'yyyy-mm-dd'
       });
   });
*/
/* Timepicker */
/*
    $(function() { "use strict";
        $('.timepicker-example').timepicker();


        $('#datetimepicker1').datetimepicker({
          format:'YYYY-MM-DD HH:mm:ss',
          icons: {
              time: 'fa fa-clock-o',
              date: 'fa fa-calendar',
              up: 'fa fa-chevron-up',
              down: 'fa fa-chevron-down',
              previous: 'fa fa-chevron-left',
              next: 'fa fa-chevron-right',
              today: 'fa fa-crosshairs',
              clear: 'fa fa-trash'
            }
        });
    // only time
    $('#datetimepicker2').datetimepicker({
        format: 'LT'
    });
    });
*/
$(window).load(function () {
    setTimeout(function () {
        $('#loading').fadeOut(400, "linear");
    }, 300);
});

$('#alert').fadeIn();     
              setTimeout(function() {
              $("#alert").fadeOut();           
              },5000);



/*=========================REPORTE===================*/
function Imprimir(){
  
}


$(document).ready(function() {
  $('#datatable-example').dataTable();
});


