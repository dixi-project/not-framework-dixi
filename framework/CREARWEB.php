<?php
/**
 * ••• Descripción •••
 * » Link de Acceso a los datos de configuración
 * @var string Link de Acceso a los datos de configuración
 * @name $config
 * @access private
 * @see dixi
 */
class CREARWEB {
    private $configure;
    public function __construct($config = null) {
        if (is_string($config)){
            $config = require($config);
        }
        $this->configure = $config;
        $this->pathSitio = $config["pathSite"];
    }
    private function getFolderControlador($conf = null) {
        return $conf['folderControladores'];
    }

    private function getFolderModelo($conf = null) {
        return $conf['folderModelos'];
    }

    private function getFolderVista($conf = null) {
        return $conf['folderVistas'];
    }

    private function getTimezone($conf = null) {
        return $conf['timezone'];
    }
    private function Security($_Cadena) {
        //$_Cadena = htmlspecialchars(trim(addslashes(stripslashes(strip_tags($_Cadena)))));
        $_Cadena = trim(stripslashes($_Cadena));
        $_Cadena = str_replace(chr(160),'',$_Cadena);
        return $_Cadena;
    }
    public function run() {
        $con=null;
        $variables=null;
        $accion=null;
        date_default_timezone_set($this->getTimezone($this->configure));
        foreach ($_REQUEST as $key => $value) {
            if(is_array($value)){
                $variables[$key]=$value;
            }else{
                $$key = $this->Security($value);
                $variables[$key]=$this->Security($value);
            }
        }
       if($con==""){
           $controllerName = "ControllerIndex";
       }else{
            $accion = str_replace("-", "", $con);
            $ex = explode("/", $accion);
            if(count($ex)>1){
                $accion =   $ex[0];
            }
            $controllerName = "Controller". ucfirst($accion);
       }
// -- >Lo mismo sucede con las acciones, si no hay accion, tomamos index como accion
      $actionName = "main";
// --> Carga la direccion de las vistas
      $vistaPath = $this->getFolderVista($this->configure);
// --> Carga la direccion del modelo
      $modelosPath = $this->getFolderModelo($this->configure); 
// --> Si no hay cookie de login
      //if($controllerName != 'ControllerLogin' && !isset($_COOKIE['idUser'])){
      //    $controllerName = "ControllerIndex";
      //}
// -- >Incluimos el fichero que contiene nuestra clase controladora solicitada
      $controllerPath = $this->getFolderControlador($this->configure) .$controllerName.'.php';
      if(!is_file($controllerPath)){
            $controllerName = "ControllerError404";
            // --> Carga la direccion del controlado
            $controllerPath = $this->getFolderControlador($this->configure) .$controllerName.'.php';
      }
      require_once $this->getFolderControlador($this->configure).'Controller.php';
      require_once ($controllerPath);
      require_once $this->getFolderModelo($this->configure).'indexModel.php';
      require_once $this->getFolderVista($this->configure) . 'Viewy.php';
      $view = new Viewy($this->getFolderVista($this->configure), $this->configure);
// --> Si hay que sacar una ubicacion de tema
      $controller = new $controllerName($view,$this->configure,$variables,$accion);
// --> (Vista, modelo, configuración , Controlador,Path sitio, Path einfluss, Path Stats)
// --> Ejecuta la accion por la con la cual se esta operando
      $controller->$actionName();
    }
}
?>