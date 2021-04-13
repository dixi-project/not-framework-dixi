
/*==================================================================
				CARGAMOS LA TABLA DE REGISTRO VEHICULO
===================================================================*/

var empresa_id = $("#empresa_id").val();
var path = $("#path").val();
var url= path+"includes/js/ajax/tablaRegistroVehiculo.php?empresa_id="+empresa_id;

console.log(url);

 $('#tablaVehiculos').DataTable( {
        "ajax": url
  });



	