<?php
ini_set('display_errors',0);
error_reporting(E_ALL);
header('content-type: application/json; charset=utf-8');
header("access-control-allow-origin: *");
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
$config=dirname(__FILE__).'/../../protected/config/data.php';
$conf = require($config);
require dirname(__FILE__).'/../../'.$conf['folderModelos'].'SPDO.php';
$db = SPDO::singleton($conf['host'],$conf['dbname'],$conf['username'],$conf['password']);
$pathSitioCMS = $conf['pathCMSSite'];
function Security($_Cadena) {
    $_Cadena = htmlspecialchars(trim(addslashes(stripslashes(strip_tags($_Cadena)))));
    $_Cadena = str_replace(chr(160),'',$_Cadena);
    return $_Cadena;
    //return mysql_real_escape_string($_Cadena);
}
?>
