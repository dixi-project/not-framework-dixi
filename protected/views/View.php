<?php
/**
 * ••• Framework DIXI •••
 * » Clase principal de todas las Vistas del portal<br>
 * » Constrola principal de las vistas
 * @package protected
 * @subpackage vistas
 * @author Castillejos Sánchez José Alfredo <acastillejos@phpmexico.com>
 * @copyright Copyright (c) 2010, Dixi Project.
 * @link http://dixi-project.com
 * @category Class Access
 * @version 0.1 2017-06-21 10:54:00
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @access public
 */

class View {

    /**
     * If debugging is enabled, a debug console window will display
     * when the page loads (make sure your browser allows unrequested
     * popup windows)
     *
     * @var boolean
     * private $debugging = false;
     */


    /**
     * pathSitio
     * @name $pathSitio
     * @access private
     * @var string
     * private $pathSitio;
     */


    /**
     * pathEinfluss
     * @name $pathEinfluss
     * @access private
     * @var string
     * private $pathEinfluss;
     */


    /**
     * pathStats
     * @name $pathStats
     * @access private
     * @var string
     * private $pathStats;
     */




    //private $viewsFolder;
    function __construct($dir, $configuracion) {
        $this->model = "protected/modelos/";
        $this->conf = $configuracion;
        $this->viewsFolder = $dir;
        $this->tpl_file = "";
        $this->pathSite = $configuracion['pathSite'];
        $this->title = $configuracion['title'];
        $this->pathCMSSite = $configuracion['pathCMSSite'];
    }

    /**
     * ••• Descripción •••
     * » Class show<br>
     * » Es el acceso a la impresión de la información a la pantalla
     * @access private
     * @name show
     * @param string $name nombre del templete a utilizar para mostrar la aplicación
     * @param string $vars Parametros pasados en forma de array para trabajar dentro de la aplicación
     * @param string $vars1 Otra lista de parametros pasados en forma de array para trabajar dentro de la aplicación
     * @see Viewy
     * @uses Viewy::$tpl_file
     * @uses Viewy::$vars
     * @uses Viewy::$vars1
     * @uses Viewy::$name
     * @return void
     */
    public function show($name = null, $vars = array(), $titulo = null) {
        $idRol= null;
        $dominio = null;

        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

// --> Armamos la ruta a la plantilla de Twig
        $path = $this->viewsFolder . 'twig/templates/'.$this->conf["design"] .'/'. $name;
        $this->tpl_file = $path;
        $this->vars = $vars;
        $this->name = $name;
// --> Si no existe el fichero en cuestion, tiramos un 404
        if (file_exists($path) == false) {
            trigger_error('Template `' . $path . '` does not exist.', E_USER_NOTICE);
            return false;
        }
// --> Si hay variables para asignar, las pasamos una a una.
        if (is_array($vars)) {
            foreach ($vars as $key => $value) {
              //echo $key."-".$value."<br>";
// --> Creamos una variable con el nombre del metodo y ahi guardamos lo que resulte del metodo
                $$key = $value;
// --> Creamos una variable de smarty con el nombre del metodo y ahi guardamos lo que resulte del metodo.
                $mandarData[$key]=$value;
            }
        }
//var_dump($_COOKIE);
        foreach ($_COOKIE as $key => $value) {
            //$sm->assign($key, $value);
            //echo $key."-".$value."<br>";
            $mandarData[$key]=$value;
            $$key=$value;
        }

        // --> Time unique
         $mandarData['timeUnique']= uniqid();


// --> Valores del config como variables disponibles para vista
        foreach ($this->conf as $key => $value) {
            //$sm->assign($key, $value);
            $mandarData[$key]=$value;
        }
// --> Si no hay un titulo colocamos uno
        if ($titulo == "" or $titulo == "index") {
            $titulo = $this->title;
            $mandarData['titulo']=$titulo;
        } else {
            //$sm->assign('titulo', $titulo);
            $mandarData['titulo']= $this->title." | ".ucfirst(strtolower($titulo));
        }
        
        // --> Todos los permisos del rol
        $mandarData['permisosRol']=indexModel::bd($this->conf)->getSQL("SELECT * FROM permisos WHERE rol_id=".$idRol);
        //var_dump($mandarData['permisosRol']);
        // --> obtener modulo actual
        $sslq1="SELECT id FROM modulo WHERE tabla = '{$dominio}'";
        //echo $sslq1."<br>";
        $modulo=(object) indexModel::bd($this->conf)->getSQL($sslq1)[0];
        // --> obtener permisos
        $sslq="SELECT * FROM permisos WHERE rol_id=".$idRol." AND modulo_id = ".$modulo->id;
        //echo $sslq;
        $permisos= (object) indexModel::bd($this->conf)->getSQL($sslq)[0];
        $mandarData['permissionCreate'] = $permisos->permiso_crear_id;
        $mandarData['permissionRead'] = $permisos->permiso_leer_id;
        $mandarData['permissionUpdate'] = $permisos->permiso_actualizar_id;
        $mandarData['permissionDelete'] = $permisos->permiso_borrar_id;

        //var_dump($mandarData['permisosRol']);
        $bus=array(
            "tipo_menu_id"=>2
        );
        $mandarData['menu']=indexModel::bd($this->conf)->getDominioID("menu",$bus);
        asort($mandarData['menu']);
        $bus=array(
            "tipo_menu_id"=>1
        );
        $mandarData['menu2']=indexModel::bd($this->conf)->getDominioID("menu",$bus);
        asort($mandarData['menu2']);


// --> Cuentas
        /*
        if(isset($selectempresa)){
            $myfile = fopen("includes/files/empresa.str", "w+");
            fwrite($myfile, $selectempresa);
            fclose($myfile);
        }
        */
        $mandarData['empresas']=indexModel::bd($this->conf)->getDominio("empresa");
        $sqlCuentas ="SELECT * FROM empresa WHERE empresa_id = 0";
        $mandarData['cuentas'] = indexModel::bd($this->conf)->getSQL($sqlCuentas);
        
        
        $sqlCuentas ="SELECT * FROM empresa WHERE id =".$empresaID;
        $ssrr = (object) indexModel::bd($this->conf)->getSQL($sqlCuentas)[0];
        // --> Cuenta actualen minuscula
        $mandarData['eem'] = str_replace(" ","",strtolower($ssrr->empresa));
        //echo $mandarData['eem'];
        //var_dump($mandarData['cuentas']); 
       // -- Empresa activa
       /*
       $myfile = fopen("includes/files/empresa.str", "r");

       while(!feof($myfile)){
        $traer = fgets($myfile);
       }
       fclose($myfile);
       */

// --> Imagen del Logo
        $mandarData['imagenLogo']=indexModel::bd($this->conf)->getImgProfile($this->pathSite);
// --> Asignamos el meta a usar
        $mandarData['codeUTF8']='<meta charset="utf-8" />';
// --> Asignamos la base a usar
        $mandarData['base']='<base href="' . $this->pathSite . '">';
// --> Cargamos datos del config en smarty
        foreach ($this->conf as $key => $value) {
            $mandarData[$key]=$value;
        }
// --> Carga los datos de la url
        $dddt = explode('/', $_SERVER['REQUEST_URI']);
// --> Cargamos la url de nuestro favicon
        $mandarData['favicon']='<link rel="icon" href="img/favicon.ico" />';
// --> Sacamos la fecha actual del sitio
        $mandarData['now'] = strtotime(date("d-m-Y H:i:s", time()));
        $mandarData['year'] = date("Y");
        $mandarData['now_calendar'] = date("d/m/Y H:i A", time());
        $mandarData['hoy'] = date("Y-m-d", time());

// --> Lenguaje
        $fichero = file_get_contents('includes/language/es.len', FILE_USE_INCLUDE_PATH);
        $filearray = explode("\n", $fichero);
        foreach ($filearray as $key => $value) {
            $dd = explode("|", $value);
            if ($dd[0] != "" && count($dd) == 2) {
                $key1 = $dd[0];
                $mandarData[$key1]=$dd[1];
            }
        }
        try {
            require_once('framework/Twig/Autoloader.php');
            Twig_Autoloader::register();
            $loader = new Twig_Loader_Filesystem($this->viewsFolder.'twig/templates/'.$this->conf["design"]."/");
            $twig = new Twig_Environment($loader, array('debug' => true));
            $twig->addExtension(new Twig_Extension_Debug());
            $function = new Twig_SimpleFunction('getIMG', function ($path,$id) {
                return $this->getImagen($path,$id);
            });
            $twig->addFunction($function);
            echo $twig->render($name, $mandarData);
        } catch (Exception $e) {
            echo 'E:' . $e->getMessage();
        }
    }

    private function getImagen($path,$idImagen) {
        $types = array("jpg", "png", "gif");
        foreach ($types as $key => $value) {
            $img = $this->pathSite . "includes/images/".$path."/" . $idImagen . "." . $value;
            //echo $img."<br>";
            if ($this->url_exists($img)) {
                return $img;
            }
        }
        return $this->pathSite . "includes/images/profile/profile.png";
    }

    private function url_exists($url) {
        $ch = @curl_init($url);
        @curl_setopt($ch, CURLOPT_HEADER, TRUE);
        @curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        @curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
        @curl_setopt($ch, CURLOPT_USERPWD, "desarrollo:1q2w3e4r");
        @curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $status = array();
        $d = @curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        preg_match('/HTTP\/.* ([0-9]+) .*/', $d, $status);
        return ($status[1] == 200);
    }
}
?>













