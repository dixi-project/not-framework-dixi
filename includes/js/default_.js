function mandarCornestone(){
  var r = confirm(unescape("¿Desea mandar los datos a Cornerstone?"));
  if (r == false) {
      return false;
  }
  alert("Datos enviados!!! :)");
}

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

function cualidades(idVacante,path){
    var data = "idVac=" + idVacante;
    $("#idVacante").val(idVacante);
    var html = $.ajax({
        url: path+"includes/ajax/cualidades.php",
        type: "POST",
        data: data,
        async: false
    }).responseText;
    $('#capaCualidades').html(html);
    $('#myModal2').modal('toggle');
    $("#txtCualidad").focus();
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





function clickBtn(id, action, ruta, frm) {
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
$(window).load(function () {
    setTimeout(function () {
        $('#loading').fadeOut(400, "linear");
    }, 300);
});
