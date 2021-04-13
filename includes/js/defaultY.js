function validar(e) {
            tecla = (document.all) ? e.keyCode : e.which;
            if (tecla == 8 || tecla == 0){
                return true; //Tecla de retroceso (para poder borrar)  
            }
            // dejar la línea de patron que se necesite y borrar el resto  
            //patron =/[A-Za-z]/; // Solo acepta letras  
            patron = /\d/; // Solo acepta números  
            //patron = /\w/; // Acepta números y letras  
            //patron = /\D/; // No acepta números  
            //  
            te = String.fromCharCode(tecla);
            return patron.test(te);
        } 
function clickBtnCiclo(id, action,ruta) {
    var path = $("#path").val();
    if (action == "pasarCiclo") {
        //id = $(this).attr("idD");
        var r = confirm(unescape("¿Desea crear un Ciclo Escolar con los datos de este ciclo?"));
        if (r == false) {
            return false;
        }
        do {
            var nombreCiclo = prompt("Escriba el nombre del siguiente ciclo escolar:", "Nuevo Ciclo Escolar");
        }
        while (nombreCiclo == ""); 

    }
    $("#idReg").val(id);
    $("#idRegA").val(nombreCiclo);
    $("#Action").val(action);
    $("#frm1").attr('action', path + ruta);
    document.getElementById("frm1").submit();
}


function clickBtn3(id, action,ruta) {
    var path = $("#path").val();
    if (action == "delete") {
        //id = $(this).attr("idD");
        var r = confirm(unescape("¿Desea eliminar el registro?"));
        if (r == false) {
            return false;
        }
    }
    $("#idRegQ").val(id);
    $("#Action").val(action);
    $("#frm1").attr('action', path + ruta);
    document.getElementById("frm1").submit();
}

function clickBtn2(id, action,ruta) {
    var path = $("#path").val();
    if (action == "delete") {
        //id = $(this).attr("idD");
        var r = confirm(unescape("¿Desea eliminar el registro?"));
        if (r == false) {
            return false;
        }
    }
    $("#idRegR").val(id);
    $("#Action").val(action);
    $("#frm1").attr('action', path + ruta);
    document.getElementById("frm1").submit();
}


function clickBtn1(id, action,ruta) {
    var path = $("#path").val();
    if (action == "delete") {
        //id = $(this).attr("idD");
        var r = confirm(unescape("¿Desea eliminar el registro?"));
        if (r == false) {
            return false;
        }
    }
    $("#idRegA").val(id);
    $("#Action").val(action);
    $("#frm1").attr('action', path + ruta);
    document.getElementById("frm1").submit();
}


function clickBtn0(id, action,ruta) {
    var path = $("#path").val();
    if (action == "delete") {
        //id = $(this).attr("idD");
        var r = confirm(unescape("¿Desea eliminar el registro?"));
        if (r == false) {
            return false;
        }
    }
    $("#idRegA").val(id);
    $("#Action").val(action);
    $("#frm0").attr('action', path + ruta);
    document.getElementById("frm0").submit();
}

function clickBtn(id, action,ruta) {
    var path = $("#path").val();
    if (action == "delete") {
        //id = $(this).attr("idD");
        var r = confirm(unescape("¿Desea eliminar el registro?"));
        if (r == false) {
            return false;
        }
    }
    $("#idReg").val(id);
    $("#Action").val(action);
    $("#frm1").attr('action', path + ruta);
    document.getElementById("frm1").submit();
}

$(document).ready(function () {
    
    $(".Btnevalua4").click(function () {
        var id = $(this).attr('data-id');
        var action = $(this).attr('data-action');
        var valor = $(this).attr('data-val');
        //alert(action);
        var color="";
        if(action=="0"){
            color="label-danger";
        }else if(action=="1"){
            color="label-yellow";
        }else if(action=="2"){
            color="label-blue-alt";
        }else if(action=="3"){
            color="label-purple";
        }
        //alert(color);
        $( "#txtE"+id).val(valor);
        $( "#colorEvalua"+id ).removeClass("label-danger label-yellow label-blue-alt label-purple").addClass(color);
        $( "#colorEvalua"+id ).css('font-weight', 'bold');
    });
    
    $(".Btnevalua2").click(function () { 
        var id = $(this).attr('data-id');
        var action = $(this).attr('data-action');
        var valor = $(this).attr('data-val');
        //alert(action);
        var color="";
        if(action=="0"){
            color="label-danger"; 
        }else if(action=="1"){
            color="label-success";
        }
        //alert(color);
        $( "#txtE"+id).val(valor);
        $( "#colorEvalua"+id ).removeClass("label-danger label-success").addClass(color);
        $( "#colorEvalua"+id ).css('font-weight', 'bold');
    });
    
    $(".Btnevalua").click(function () {
        var id = $(this).attr('data-id');
        var action = $(this).attr('data-action');
        var valor = $(this).attr('data-val');
        var color="";
        if(action=="0"){
            color="label-danger";
        }else if(action=="1"){
            color="label-yellow";
        }else if(action=="2"){
            color="label-success";
        }
        //alert(color);
        $( "#txtE"+id).val(valor);
        $( "#colorEvalua"+id ).removeClass("label-danger label-yellow label-success").addClass(color);
        $( "#colorEvalua"+id ).css('font-weight', 'bold');
    });
    /*
    $("#txtCiclo1").click(function () {
        var idn = $(this).val();
        var data = "idN="+idn;
        var html = $.ajax({
                url: "includes/js/serchNiveles_1.php",
                type: "POST",
                data: data,
                async: false
        }).responseText;
        $('#capaGrado').html(html); 
        $('#capaGrupo').html('<select id="txtGrupos" name="txtGrupos" class="form-control form-control"><option value="0" selected>----- Selecciona -----</option></select>'); 
        $('#capaAlumnos').html(''); 
    });
    */
    
    $("#txtCondominio").change(function () {
        var idn = $(this).val();
        var data = "idN="+idn;
        var html = $.ajax({
                url: "includes/js/serchEdificios.php",
                type: "POST",
                data: data,
                async: false
        }).responseText;
        $('#capaEdificios').html(html); 
        
    });


    $("#txtNivel1").change(function () {
        var idn = $(this).val();
        var data = "idN="+idn;
        var html = $.ajax({
                url: "includes/js/serchGrados_1.php",
                type: "POST",
                data: data,
                async: false
        }).responseText;
        $('#capaGrado').html(html); 
        $('#capaGrupo').html('<select id="txtGrupos" name="txtGrupos" class="form-control form-control"><option value="0" selected>----- Selecciona -----</option></select>'); 
        $('#capaAlumnos').html(''); 
    });
    
    $("#txtNivel").change(function () {
        var idn = $(this).val();
        var data = "idN="+idn;
        var html = $.ajax({
                url: "includes/js/serchGrados.php",
                type: "POST",
                data: data,
                async: false
        }).responseText;
        $('#capaGrado').html(html); 
        
    });
    
    $("#cmdRegresar").click(function () {
        var action = $(this).attr('data-action');
        var path = $("#path").val();
        $("#frm1").attr('action', path + action);
        document.getElementById("frm1").submit();
    });
    
    $('#datatable-example').dataTable();
    
    //history.back();
    if(history.forward(1)){
        //history.go(-1);
        //return false;
        location.replace(history.forward(1))
        //document.refresh();
    }
    
});