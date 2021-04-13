

/*==================================================================
				CARGAMOS LA TABLA PRODUCTOS
===================================================================*/

var empresa_id = $("#empresa_id").val();
var path = $("#path").val();
var url= path+"includes/js/ajax/tablaSalidas.php?empresa_id="+empresa_id;

 $('#tablaSalidas').DataTable( {
        "ajax": url
  });



	

 
