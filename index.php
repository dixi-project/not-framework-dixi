<?php
/**
 * ••• NOT Framework DIXI •••
 * » Acceso principal al sistema ---------------------
 * @package Principal
 * @subpackage admin
 * @author Castillejos Sánchez José Alfredo <acastillejos@dixi-project.com>
 * @copyright Copyright (c) 2020, Dixi Project.
 * @link http://dixi-project.com
 * @category Class Access
 * @version 1.0 2020-07-10 12:35:00
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @access public
 */
ini_set('display_errors',0);
error_reporting(E_ALL);
/**
 * ••• Descripción •••
 * » Link de Acceso al framework
 * @var string Link de acceso al framework
 * @name $dixi
 * @access public
 * @see dixi
 */
$dixi=dirname(__FILE__).'/framework/DIXI.php';
/**
 * ••• Descripción •••
 * » Link de Acceso a los datos de configuración
 * @var string Link de Acceso a los datos de configuración
 * @name $config
 * @access private
 * @see dixi
 */
$config=dirname(__FILE__).'/protected/config/data.php';
// --> LLama el archivo del framework
require_once($dixi);
// --> Creación de la aplicación Web
DIXI::crearAplicacionWeb($config)->run();
?>
