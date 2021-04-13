<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Framework Dixi</title>
</head>
<body >
<!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
<div class="contenedor">
    <h2>Framework Dixi</h2>
    <hr>
   

<?php
ini_set('display_errors',0);
error_reporting(E_ALL);
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
$sqlValidate = "SHOW TABLES";
$recordset = $db->prepare($sqlValidate);
$recordset->execute();
while($row = $recordset->fetch(PDO::FETCH_OBJ)){
   $nameField = 'Tables_in_'.$conf["dbname"];
   //echo $conf["dbname"] . "--".$row->$nameField."<br>";
   // --> Create Structure
   $data = getStructureTable($conf["dbname"], $db, $row->$nameField);
   $dir1=dirname(__FILE__)."/../structure/structure_{$row->$nameField}.str";
   $myfile = fopen($dir1, "w");
   //print_r(error_get_last());

   
   fwrite($myfile, $data);
   fclose($myfile);
   //echo $data;
   // --> Create Estructura 2
   $data2 = getEstructuraBD($db,$conf["dbname"]);

   $myfile2 = fopen(dirname(__FILE__)."/../structure/structureBD.str", "w");
   $data2 = json_encode($data2,true);
   //echo $data2;
   fwrite($myfile2, $data2);
   fclose($myfile2);
}
echo '<h3>Estructura creada...</h3>';
function getStructureTable($nameBase, $db, $table){
    $sss="SELECT table_comment FROM INFORMATION_SCHEMA.TABLES WHERE table_schema='{$nameBase}' AND table_name='{$table}' ";
    //echo $sss."<br>";
    $recordsets = $db->prepare($sss);
    $recordsets->execute();
    $item2 = $recordsets->fetch(PDO::FETCH_OBJ);
    $cad = null;
    if($item2){
        $arr1 = str_split($item2->table_comment);
        foreach ($arr1 as $key => $value) {
            $num = ord($value);
            if($num == 147 || $num == 148){
                $letra ='"';
            }else{
                $letra = $value;
            }
            $cad .= $letra;
        }
    }
    // --> Extrarer estructura por campo
    $recordset = $db->prepare("SHOW FULL COLUMNS FROM {$table}");
    $recordset->execute();
    while ($row = $recordset->fetch(PDO::FETCH_OBJ)) {
        $campos[$row->Field] = json_decode($row->Comment);
    }
    $retu[] = json_decode($cad); 
    $retu[] = $campos; 
    $valoraRegresar= json_encode($retu);
    //echo "<pre>$table<hr>";
    //var_dump($valoraRegresar);
    //echo "</pre>";
    //exit();
    return $valoraRegresar;
}

function getEstructuraBD($db,$base) {
    $recordset1 = null;
    try {
        // --> Limpiar tablas
        $db->prepare("SET FOREIGN_KEY_CHECKS = 0")->execute();
        //$db->prepare("TRUNCATE conf_tabla")->execute();
        //$db->prepare("TRUNCATE conf_campo")->execute();
        $db->prepare("SET FOREIGN_KEY_CHECKS = 1")->execute();

        $ss = "SHOW TABLES";
        $recordset = $db->prepare($ss);
        //--> validaciones
        //$recordset->bindParam("v_correo", $txt_correo, PDO::PARAM_STR);
        $recordset->execute();
        $nameTaB = "Tables_in_".$base;
        //echo $nameTaB;
        while ($row = $recordset->fetch(PDO::FETCH_OBJ)) {
            //var_dump($row);
            if(isset($row->$nameTaB)){
                if (substr($row->$nameTaB, 0, 5) != "conf_"){
                    //
                    $ffil=dirname(__FILE__)."/../structure/structure_{$row->$nameTaB}.str";
                    $myfile3 = fopen($ffil, "r");
                    if ($myfile3) {
                        while (($buffer = fgets($myfile3, 4096)) !== false) {
                            $esT = json_decode($buffer,true);
                        }
                        fclose($myfile3);
                    }
                    
                    
                    //var_dump($esT);
                    //echo "<hr>";
                    //if($row->$nameTaB=="user") echo "<h2>TABLA:".$row->$nameTaB."</h2>";
                    // --> Obtener datos de los Campos
                    /*
                    $ss = "SHOW FULL COLUMNS FROM " . $row->$nameTaB;

                    $recordset1 = $db->prepare($ss);
                    $recordset1->execute();
                    if($row->$nameTaB=="empresa") echo '<table border="1"><tr><th>Campo</th><th>Tipo</th><th>Commentario</th></tr>';
                    while($campos = $recordset1->fetch(PDO::FETCH_OBJ)){
                        if($row->$nameTaB=="empresa") echo '<tr><td>'.$campos->Field.'</td><td>'.$campos->Type.'</td><td>'.$campos->Comment.'</td></tr>' ;
                    }
                    if($row->$nameTaB=="empresa") echo '</table>';
                    */
                    // --> Relaciones
                    $ss = "SHOW CREATE TABLE " . $row->$nameTaB;
                    //echo $ss."<br>";
                    $recordset1 = $db->prepare($ss);
                    $recordset1->execute();
                    while($campos = $recordset1->fetch(PDO::FETCH_OBJ)){
                        foreach ($campos as $key => $value) {
                            //if($row->$nameTaB=="user")  echo $key ."--". $value."<br>";
                            if($key=="Create Table"){
                                $structure = $value;
                            }
                        }
                        //if($row->$nameTaB=="empresa") var_dump($structure);
                        $dat = explode("ENGINE",$structure);
                        //if($row->$nameTaB=="user") var_dump($dat);
                        $dat1 = explode("CONSTRAINT", $dat[0]);
                        if(count($dat1) > 1){
                            foreach ($dat1 as $key => $value) {
                                if($key==0){
                                    continue;
                                }
                                $limpiar = str_replace("ON DELETE NO ACTION ON UPDATE NO ACTION,", "", $value);
                                $limpiar = str_replace("ON DELETE CASCADE ON UPDATE CASCADE", "", $limpiar);
                                $limpiar = str_replace("ON DELETE CASCADE ON UPDATE NO ACTION", "", $limpiar);
                                $limpiar = str_replace("ON DELETE NO ACTION ON UPDATE CASCADE", "", $limpiar);
                                $limpiar = str_replace("`", "", $limpiar);
                                $limpiar = str_replace(")", "", $limpiar);
                                $limpiar = str_replace("(", "", $limpiar);
                                $dat3 = explode("FOREIGN KEY",$limpiar);
                                $nameRelacion = trim($dat3[0]);
                                $dat4 = explode("REFERENCES",$dat3[1]);
                                $campoRelacion = trim($dat4[0]);
                                $dat5 = explode(" ",$dat4[1]);
                                $tabla = trim($dat5[1]);
                                $campo = trim($dat5[2]);

                                $nuevocampo = str_replace("_id", "", $campoRelacion);
                                $nuevocampo2 = str_replace("_id1", "", $campoRelacion);
                                //echo $campoRelacion."-".$nuevocampo."<br>";
                                if(strpos($campoRelacion, "_id") !== false){
                                    $ffil=dirname(__FILE__)."/../structure/structure_{$nuevocampo}.str";
                                    $myfile3 = fopen($ffil, "r");
                                    if ($myfile3) {
                                        while (($buffer = fgets($myfile3, 4096)) !== false) {
                                            $esT = json_decode($buffer,true);
                                        }
                                        fclose($myfile3);
                                    }

                                  //$esTablaa = getStructureTable($base, $db, $nuevocampo);
                                  $esTablaa = $esT ;
                                }
                                if(strpos($campoRelacion, "_id1") !== false){
                                    $ffil=dirname(__FILE__)."/../structure/structure_{$nuevocampo2}.str";
                                    $myfile3 = fopen($ffil, "r");
                                    if ($myfile3) {
                                        while (($buffer = fgets($myfile3, 4096)) !== false) {
                                            $esT = json_decode($buffer,true);
                                        }
                                        fclose($myfile3);
                                    }

                                  //echo $campoRelacion."-".$nuevocampo2."<br>";
                                  //$esTablaa2 = getStructureTable($base, $db, $nuevocampo2);

                                  $esTablaa2 = $esT ;
                                }

                                if((isset($esT["structure"]["return"]) && is_array($esT["structure"]["return"])) or (isset($esTablaa["structure"]["return"]) && is_array($esTablaa["structure"]["return"])) or (isset($esTablaa2["structure"]["return"]) && is_array($esTablaa2["structure"]["return"]))){

                                    if(is_array($esTablaa["structure"]["return"])){
                                        $camposReturn = $esTablaa["structure"]["return"];
                                    }
                                    if(is_array($esTablaa2["structure"]["return"])){
                                        $camposReturn = $esTablaa2["structure"]["return"];
                                    }
                                    if(is_array($esT["structure"]["return"])){
                                        $camposReturn = $esT["structure"]["return"];
                                    }
                                }else{
                                    $camposReturn=array($tabla);
                                }
                                $dd[$row->$nameTaB][$campoRelacion]=array("tabla" => $tabla,"campoReturn" => $camposReturn);

                            }
                        }
                    }
                }
            }
        }
        return $dd;
    } catch (PDOException $e) {
        echo $e->getMessage() . "--" . $e->getCode();
    }
}
?>
 
</div>
</body>
</html>

