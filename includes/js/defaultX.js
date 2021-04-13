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


$(document).ready(function () {
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