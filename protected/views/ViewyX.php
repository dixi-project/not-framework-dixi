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

class Viewy {

    /**
     * If debugging is enabled, a debug console window will display
     * when the page loads (make sure your browser allows unrequested
     * popup windows)
     *
     * @var boolean
     */
    private $debugging = false;

    /**
     * pathSitio
     * @name $pathSitio
     * @access private
     * @var string
     */
    private $pathSitio;

    /**
     * pathEinfluss
     * @name $pathEinfluss
     * @access private
     * @var string
     */
    private $pathEinfluss;

    /**
     * pathStats
     * @name $pathStats
     * @access private
     * @var string
     */
    private $pathStats;

    /**
     * pathAbsoluteSite
     * @name pathAbsoluteSite
     * @access private
     * @var string
     */
    private $pathAbsoluteSite;

    //private $viewsFolder;
    function __construct($dir, $configuracion) {
        $this->model = "protected/modelos/";
        $this->conf = $configuracion;
        $this->viewsFolder = $dir;
        $this->tpl_file = "";
        $this->pathSite = $configuracion['pathSite'];
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
        /*
        $divTem = explode(".", $name);
        $name = "";
        foreach ($divTem as $key => $value) {
            $name .= $value."/";
        }
        $name= substr($name, 0,-1);
        */
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

// --> Armamos la ruta a la plantilla de smarty
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
// --> Creamos una variable con el nombre del metodo y ahi guardamos lo que resulte del metodo
                $$key = $value;
// --> Creamos una variable de smarty con el nombre del metodo y ahi guardamos lo que resulte del metodo.
                $mandarData[$key]=$value;
            }
        }
        // --> Controller
        $dat = explode("/", $vars["con"]);
        $mandarData["controller"] = $dat[0];

//var_dump($_COOKIE);
        foreach ($_COOKIE as $key => $value) {
            //$sm->assign($key, $value);
            //echo $key."-".$value."<br>";
            $mandarData[$key]=$value;
            //echo $key."-".$value."<br>";
        }

        // --> Time unique
         $mandarData['timeUnique']= uniqid();


// --> Valores del config como variables disponibles para vista
        foreach ($this->conf as $key => $value) {
            //$sm->assign($key, $value);
            $mandarData[$key]=$value;
            $$key = $value;
        }
// --> Si no hay un titulo colocamos uno
        if ($titulo == "" or $titulo == "index") {
            //$titulo = "Not Framework DIXI";
            $mandarData['titulo']=$title;
        } else {
            //$sm->assign('titulo', $titulo);
            $mandarData['titulo']= $title." | ".ucfirst(strtolower($titulo));
        }
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
        $mandarData['favicon']='<link rel="icon" href="includes/img/favicon.ico" />';
// --> Sacamos la fecha actual del sitio
        $mandarData['now'] = strtotime(date("d-m-Y H:i:s", time()));
        $mandarData['year'] = date("Y");
        $mandarData['now_calendar'] = date("d/m/Y H:i A", time());





// --> CSS varios
        $mandarData['css_files'] = array('includes/css/bootstrap.min.css','includes/css/bootstrap-theme.min.css','includes/css/default.css');

        $mandarData['css_files_HELPERS'] = array('assets/helpers/animate.css','assets/helpers/backgrounds.css','assets/helpers/boilerplate.css','assets/helpers/border-radius.css','assets/helpers/grid.css','assets/helpers/page-transitions.css','assets/helpers/spacing.css','assets/helpers/typography.css','assets/helpers/utils.css','assets/helpers/colors.css');

        $mandarData['css_files_ELEMENTS'] = array('assets/elements/badges.css','assets/elements/buttons.css','assets/elements/content-box.css','assets/elements/dashboard-box.css','assets/elements/forms.css','assets/elements/images.css','assets/elements/info-box.css','assets/elements/invoice.css','assets/elements/loading-indicators.css','assets/elements/menus.css','assets/elements/panel-box.css','assets/elements/response-messages.css','assets/elements/responsive-tables.css','assets/elements/ribbon.css','assets/elements/social-box.css','assets/elements/tables.css','assets/elements/tile-box.css','assets/elements/timeline.css');

        $mandarData['css_files_ICONS'] = array('assets/icons/fontawesome/fontawesome.css','assets/icons/linecons/linecons.css','assets/icons/spinnericon/spinnericon.css');

        $mandarData['css_files_WIDGETS'] = array('assets/widgets/accordion-ui/accordion.css','assets/widgets/calendar/calendar.css','assets/widgets/carousel/carousel.css','assets/widgets/charts/justgage/justgage.css','assets/widgets/charts/morris/morris.css','assets/widgets/charts/piegage/piegage.css','assets/widgets/charts/xcharts/xcharts.css','assets/widgets/chosen/chosen.css',
'assets/widgets/colorpicker/colorpicker.css','assets/widgets/datatable/datatable.css','assets/widgets/datepicker/datepicker.css','assets/widgets/datepicker-ui/datepicker.css','assets/widgets/daterangepicker/daterangepicker.css','assets/widgets/dialog/dialog.css','assets/widgets/dropdown/dropdown.css','assets/widgets/dropzone/dropzone.css','assets/widgets/file-input/fileinput.css','assets/widgets/input-switch/inputswitch.css','assets/widgets/input-switch/inputswitch-alt.css','assets/widgets/ionrangeslider/ionrangeslider.css','assets/widgets/jcrop/jcrop.css','assets/widgets/jgrowl-notifications/jgrowl.css','assets/widgets/loading-bar/loadingbar.css','assets/widgets/maps/vector-maps/vectormaps.css','assets/widgets/markdown/markdown.css','assets/widgets/modal/modal.css','assets/widgets/multi-select/multiselect.css','assets/widgets/multi-upload/fileupload.css','assets/widgets/nestable/nestable.css','assets/widgets/noty-notifications/noty.css','assets/widgets/popover/popover.css','assets/widgets/pretty-photo/prettyphoto.css','assets/widgets/progressbar/progressbar.css','assets/widgets/range-slider/rangeslider.css','assets/widgets/slidebars/slidebars.css','assets/widgets/slider-ui/slider.css','assets/widgets/summernote-wysiwyg/summernote-wysiwyg.css','assets/widgets/tabs-ui/tabs.css','assets/widgets/theme-switcher/themeswitcher.css','assets/widgets/timepicker/timepicker.css','assets/widgets/tocify/tocify.css','assets/widgets/tooltip/tooltip.css','assets/widgets/touchspin/touchspin.css','assets/widgets/uniform/uniform.css','assets/widgets/wizard/wizard.css','assets/widgets/xeditable/xeditable.css');

        $mandarData['css_files_SNIPPETS'] = array('assets/snippets/chat.css','assets/snippets/files-box.css','assets/snippets/login-box.css','assets/snippets/notification-box.css','assets/snippets/progress-box.css','assets/snippets/todo.css','assets/snippets/user-profile.css','assets/snippets/mobile-navigation.css');

        $mandarData['css_files_APPLICATIONS'] = array('assets/applications/mailbox.css');

        $mandarData['css_files_Admin_theme'] = array('assets/themes/admin/layout.css','assets/themes/admin/color-schemes/default.css');

        $mandarData['css_files_Components_theme'] = array('assets/themes/components/default.css','assets/themes/components/border-radius.css');

        $mandarData['css_files_Admin_responsive'] = array('assets/helpers/responsive-elements.css','assets/helpers/admin-responsive.css');



        // --> Cargamos todos los archivos js usados pra la aplicación
        $mandarData['js_files'] = array('includes/js/bootstrap.min.js','includes/js/default.js');
        $mandarData['js_core'] = array('assets/js-core/jquery-core.js','assets/js-core/jquery-ui-core.js','assets/js-core/jquery-ui-widget.js','assets/js-core/jquery-ui-mouse.js','assets/js-core/jquery-ui-position.js','assets/js-core/modernizr.js','assets/js-core/jquery-cookie.js');
        // --> JS para el footer
        $mandarData['js_files_footer'] = array('assets/widgets/dropdown/dropdown.js','assets/widgets/tooltip/tooltip.js','assets/widgets/popover/popover.js','assets/widgets/progressbar/progressbar.js','assets/widgets/button/button.js','assets/widgets/collapse/collapse.js','assets/widgets/superclick/superclick.js','assets/widgets/input-switch/inputswitch-alt.js','assets/widgets/slimscroll/slimscroll.js','assets/widgets/slidebars/slidebars.js','assets/widgets/slidebars/slidebars-demo.js','assets/widgets/charts/piegage/piegage.js','assets/widgets/charts/piegage/piegage-demo.js','assets/widgets/screenfull/screenfull.js','assets/widgets/content-box/contentbox.js','assets/widgets/overlay/overlay.js','assets/js-init/widgets-init.js','assets/themes/admin/layout.js','assets/widgets/theme-switcher/themeswitcher.js','includes/js/default.js');//'assets/bootstrap/js/bootstrap.js',
        $mandarData['js_files_dataTable'] = array('assets/widgets/datatable/datatable.js','assets/widgets/datatable/datatable-bootstrap.js','assets/widgets/datatable/datatable-tabletools.js','assets/widgets/datatable/datatable-reorder.js');



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
            $newPath=$this->viewsFolder.'twig/templates/'.$this->conf["design"]."/";
            //echo "X:",$newPath;
            $loader = new Twig_Loader_Filesystem($newPath);
            $twig = new Twig_Environment($loader, array('debug' => true));
            $twig->addExtension(new Twig_Extension_Debug());

            echo $twig->render($name, $mandarData);
        } catch (Exception $e) {
            echo 'E:' . $e->getMessage();
        }
    }

    private function getImagen($idImagen) {
        $types = array("jpg", "png", "gif");
        foreach ($types as $key => $value) {
            $img = $this->pathSite . "/includes/images/user/" . $idImagen . "." . $value;
            //echo $img."<br>";
            if ($this->url_exists($img)) {
                return $img;
            }
        }
        return $this->pathSite . "/includes/images/profile/profile.png";
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
