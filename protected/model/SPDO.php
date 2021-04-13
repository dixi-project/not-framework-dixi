<?php
/**
   * SPDO  
	* <br>Constrola el acceso a la libreria PDO que sirve para trabajar con conexiones a bases de datos 
	* @package protected   
	* @subpackage modelos
 	* @author Castillejos Sánchez José Alfredo <acastillejos@phpmexico.com>
 	* @copyright Copyright (c) 2016, Dixi Project.
 	* @link http://dixi-project.com
	* @category Modelo
	* @version 1.0 2017-06-21 12:55:00   
	* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*/
	class SPDO extends PDO
	{
		private static $instance = null;
		public $host;
		public $bd;
		public $user;
		public $clave;

		function __construct($host1,$bd1,$user1,$clave1) 
		{
			try {

				parent::__construct('mysql:host=' . $host1 . ';dbname=' . $bd1, $user1, $clave1);
			} catch (PDOException $e) {
                                print "<div style='text-align:center;'>"
                            . "<img src='includes/img/logo.png' width='200'>"
                                        . "<h3>Not Framework Dixi requiere tener creada una base de datos y configurada.</h3>";
				print "en /protected/config/data.php";
                                print "<h4>Error: " . $e->getMessage() . "</h4><h5>" . $host1."--".$bd1."--".$user1."--".$clave1."<br>\n"."</h5></div>"; 
				die();
			}

		} 
/**
* Metodo singleton() que modela las funciones singleton para conexion a PDO
* Controla la pantalla de lista de ultimos comentarios
* @return Object self::$instance Consulta SQL  
*/ 
public static function singleton($host1,$bd1,$user1,$clave1)
{
	$host = $host1;  
	$bd = $bd1;
	$user = $user1;
	$clave = $clave1;
	if( self::$instance == null )
	{
		self::$instance = new self($host,$bd,$user,$clave);
	}
	return self::$instance;
}
}
?>