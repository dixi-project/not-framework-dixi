<?php
class Catalogos
{
    public function __construct()
    {
    }

    public static function getEstructuraBD($db, $base)
    {
        $comment = NULL;
        $sd = dirname(__FILE__) . "/../../includes/structure/structureBD.str";
        $handle = fopen($sd, "r");
        if ($handle) {
            $buffer = fgets($handle, 16392);
            $table_comment = $buffer;
            $comment = json_decode($table_comment, true);
            fclose($handle);
        }
        return $comment;
    }

    public static function getStructureTable($nameBase, $db, $table)
    {
        $comment = NULL;
        $sd = dirname(__FILE__) . "/../../includes/structure/structure_{$table}.str";
        //echo $sd."<br>";
        $handle = fopen($sd, "r");
        if ($handle) {
            $buffer = fgets($handle, 16392);
            $comment = json_decode($buffer, true);
        }
        fclose($handle);
        //var_dump($comment);
        return $comment;
    }

    public static function getRelacionTable($db, $nameBase, $table, $id = null)
    {
        $cad = "";
        $ss = "SHOW TABLES";
        $recordset = $db->prepare($ss);
        $recordset->execute();
        $o = 0;
        while ($item1 = $recordset->fetch(PDO::FETCH_OBJ)) {
            $campo = "Tables_in_" . $nameBase;
            $nombreTabla = $item1->$campo;
            $numCarac = strlen($table) + 4;
            if ($table . "_has" == substr($nombreTabla, 0, $numCarac)) {
                $nom = explode("_has_", $nombreTabla);
                if (is_null($id)) {
                    $registrosActuales = null;
                } else {
                    $campos = array($nom[0] . "_id" => $id);
                    $registrosActuales = Catalogos::getDataArray($db, $nombreTabla, $nameBase, $campos);
                }
                $dat = array(
                    "nombre" => ucfirst($nom[1]),
                    "name" => $nombreTabla,
                    "registros" => Catalogos::getData($db, $nom[1], $nameBase),
                    "registrosActuales" => $registrosActuales,
                );
                $cad[] = $dat;
            }
        }
        return $cad;
    }

    public static function getFields($nameBase, $db, $table)
    {
        $dat = null;
        $cad = array(
            "password" => 0,
        );
        $estructuraTable = Catalogos::getStructureTable($nameBase, $db, $table);
        try {
            // --> Obtener datos de los Campos
            $ss = "SHOW FULL COLUMNS FROM " . $table;
            $recordset1 = $db->prepare($ss);
            $recordset1->execute();
            foreach ($recordset1->fetchAll(PDO::FETCH_OBJ) as $key => $row1) {
                $cad = null;
                $Field = $row1->Field;
                if ($row1->Comment != "") {
                    $arr1 = str_split($row1->Comment);
                    foreach ($arr1 as $key => $value) {
                        $num = ord($value);
                        if ($num == 147 || $num == 148) {
                            $letra = '"';
                        } else {
                            $letra = $value;
                        }
                        $cad .= $letra;
                    }
                    $commentField = json_decode($cad, true);
                    $Field = $commentField["name"];
                }

                $nameCamp = str_replace("_id", " ", $Field);
                //$nameCamp = str_replace("id_tra", " ", $nameCamp);
                //$nameCamp = str_replace("id_conf", " ", $nameCamp);
                //$nameCamp = str_replace("id_", " ", $nameCamp);
                //$nameCamp = str_replace("_", " ", $nameCamp);

                if (@array_key_exists($row1->Field, $cad)) {

                } else {
                    $entrar = 0;
                    //var_dump($estructuraTable[0]["structure"]["views"]["REPORT"]);
                    if (isset($estructuraTable["structure"]["skip"])) {
                        foreach ($estructuraTable["structure"]["skip"] as $key => $value) {

                            if ($row1->Field == $value) {
                                $entrar = 1;
                            }
                        }
                        if ($entrar == 0) {
                            $dat[$row1->Field] = ucfirst($nameCamp);
                        }
                    }else{
                        $dat[$row1->Field] = ucfirst($nameCamp);
                    }
                    
                }
            }
            return $dat;
        } catch (PDOException $e) {
            echo $e->getMessage() . "--" . $e->getCode();
        }
    }

    public static function getFieldsAll($db, $table,  $base)
    {

        //var_dump($db);
        $relaciones = Catalogos::getEstructuraBD($db, $base);
        //--> Estructura
        $estructuraTable = Catalogos::getStructureTable($base, $db, $table);
        $dat = null;
        try {
            // --> Obtener datos de los Campos
            $ss = "SHOW FULL COLUMNS FROM " . $table;
            $recordset1 = $db->prepare($ss);
            $recordset1->execute();
            foreach ($recordset1->fetchAll(PDO::FETCH_OBJ) as $key => $row1) {
                $cad = null;
                $Field = $row1->Field;
                if ($row1->Comment != "") {
                    $arr1 = str_split($row1->Comment);
                    foreach ($arr1 as $key => $value) {
                        $num = ord($value);
                        if ($num == 147 || $num == 148) {
                            $letra = '"';
                        } else {
                            $letra = $value;
                        }
                        $cad .= $letra;
                    }
                    $commentField = json_decode($cad, true);
                    $Field = $commentField["name"];
                }

                $nameCamp = str_replace("_id", " ", $row1->Field);
                //$nameCamp = str_replace("id_cat", " ", $row1->Field);
                //$nameCamp = str_replace("id_tra", " ", $nameCamp);
                //$nameCamp = str_replace("id_conf", " ", $nameCamp);
                //$nameCamp = str_replace("id_", " ", $nameCamp);
                $nameCamp = str_replace("_", " ", $nameCamp);
                $ttipo = explode("(", $row1->Type);
                $tam = str_replace(")", "", $ttipo[1]);
                $paso = true;

                if (isset($estructuraTable["structure"]["skip"])) {
                    foreach ($estructuraTable["structure"]["skip"] as $key => $value) {
                        if ($value == $row1->Field) {
                            $paso = false;
                        }
                    }
                }

                if ($paso) {
                    if ($row1->Field != "user_id") {
                        $relaD = NULL;
                        if (isset($relaciones[$table][$row1->Field])) {
                            $relaD = array($relaciones[$table][$row1->Field], Catalogos::getData($db, $relaciones[$table][$row1->Field]["tabla"], $base));
                        }
                        $dat[$row1->Field] = array(
                            "nombre" => ucfirst($nameCamp),
                            "nombreSalida" => ucfirst($Field),
                            "tipo" => $ttipo[0],
                            "size" => $tam,
                            "relaciones" => $relaD
                        );
                    }
                }
            }
            //$relaciones = Catalogos::getEstructuraBD($db,$base);
            return $dat;
        } catch (PDOException $e) {
            echo $e->getMessage() . "--" . $e->getCode();
        }
    }



    public static function getSql($base, $db, $ss)
    {
        $dat = null;
        $relaciones = Catalogos::getEstructuraBD($db, $base);
        try {
            $recordset = $db->prepare($ss);
            $recordset->execute();
            $item1 = $recordset->fetchAll(PDO::FETCH_OBJ);
            return $item1;
        } catch (PDOException $e) {
            echo $e->getMessage() . "--" . $e->getCode();
        }
    }

    public static function getDataWhere($db, $table, $base, $where, $id = null, $limite = null)
    {
        $dat = null;
        $relaciones = Catalogos::getEstructuraBD($db, $base);
        try {
            $ss = "SHOW FULL COLUMNS FROM " . $table;
            $recordset1 = $db->prepare($ss);
            $recordset1->execute();
            $me = 0;
            foreach ($recordset1->fetchAll(PDO::FETCH_OBJ) as $key => $row1) {
                if ($row1->Field == "empresa_id" && $table != "empresa") {
                    $me = 1;
                }
            } 

            if ($me == 1) {
                $idEmpresa = $_COOKIE["empresaID"];
                if($table=="empresa"){
                    $ss = "SELECT * FROM $table WHERE id = $idEmpresa or id 
                            IN (SELECT id FROM empresa WHERE empresa_id='$idEmpresa' )  ORDER BY id DESC";
                }else{
                    if (is_null($limite)) {
                        if (is_null($id)) {
                            $ss = "SELECT * FROM $table WHERE (empresa_id = $idEmpresa or empresa_id 
                            IN (SELECT id FROM empresa WHERE empresa_id='$idEmpresa') ) AND $where ORDER BY id DESC ";
                        } else {
                            $ss = "SELECT * FROM $table WHERE id = " . $id . " AND (empresa_id = $idEmpresa or empresa_id 
                            IN (SELECT id FROM empresa WHERE empresa_id='$idEmpresa')) ORDER BY id DESC";
                        }
                    } else {
                        if (is_null($id)) {
                            $ss = "SELECT * FROM $table WHERE empresa_id = $idEmpresa or empresa_id 
                            IN (SELECT id FROM empresa WHERE empresa_id='$idEmpresa') ORDER BY " . $limite["campo"] . " " . $limite["orden"] . " LIMIT " . $limite["limite"] . "";
                        } else {
                            $ss = "SELECT * FROM $table WHERE id = " . $id . " AND (empresa_id = $idEmpresa or empresa_id 
                            IN (SELECT id FROM empresa WHERE empresa_id='$idEmpresa')) ORDER BY " . $limite["campo"] . " " . $limite["orden"] . " LIMIT " . $limite["limite"] . "";
                        }
                    }
                }
                
            } else {
                if (is_null($limite)) {
                    if (is_null($id)) {
                        $ss = "SELECT * FROM $table WHERE {$where} ORDER BY id DESC";
                    } else {
                        $ss = "SELECT * FROM $table WHERE id = " . $id . "  ORDER BY id DESC";
                    }
                } else {
                    if (is_null($id)) {
                        $ss = "SELECT * FROM $table WHERE {$where} ORDER BY " . $limite["campo"] . " " . $limite["orden"] . " LIMIT " . $limite["limite"] . "";
                    } else {
                        $ss = "SELECT * FROM $table WHERE id = " . $id . "  ORDER BY " . $limite["campo"] . " " . $limite["orden"] . " LIMIT " . $limite["limite"] . "";
                    }
                }
            }
            $recordset = $db->prepare($ss);
            $recordset->execute();
            $o = 0;
            while ($item1 = $recordset->fetch()) {
                $dat[$o] = $item1;
                $datt = null;
                if (isset($relaciones[$table])) {
                    $datt = null;
                    foreach ($relaciones[$table] as $key => $value) {
                        if ($key != "municipio_id" && $key != "areas_ocupacion_id" && $key != "ocupacion_id") {
                            $resul = Catalogos::getCampoExterno($base, $db, $relaciones[$table][$key]["tabla"], $value["campoReturn"], $item1[$key]);
                            $datt[$key] = $resul;
                        }
                    }
                    $dat[$o]["relaciones"] = $datt;
                }
                $o++;
            }
            return $dat;
        } catch (PDOException $e) {
            echo $e->getMessage() . "--" . $e->getCode();
        }
    }

    public static function getData($db, $table, $base, $id = null, $limite = null)
    {
        $dat = null;
        $relaciones = Catalogos::getEstructuraBD($db, $base);
        try {
            $ss = "SHOW FULL COLUMNS FROM " . $table;
            $recordset1 = $db->prepare($ss);
            $recordset1->execute();
            $me = 0;
            foreach ($recordset1->fetchAll(PDO::FETCH_OBJ) as $key => $row1) {
                if ($row1->Field == "empresa_id" && $table != "empresa") {
                    $me = 1;
                }
            } 

            if ($me == 1) {
                $idEmpresa = $_COOKIE["empresaID"];
                if($table=="empresa"){
                    $ss = "SELECT * FROM $table WHERE id = $idEmpresa or id 
                            IN (SELECT id FROM empresa WHERE empresa_id='$idEmpresa') ORDER BY id DESC";
                }else{
                    if (is_null($limite)) {
                        if (is_null($id)) {
                            $ss = "SELECT * FROM $table WHERE empresa_id = $idEmpresa or empresa_id 
                            IN (SELECT id FROM empresa WHERE empresa_id='$idEmpresa') ORDER BY id DESC ";
                        } else {
                            $ss = "SELECT * FROM $table WHERE id = " . $id . " AND (empresa_id = $idEmpresa or empresa_id 
                            IN (SELECT id FROM empresa WHERE empresa_id='$idEmpresa')) ORDER BY id DESC";
                        }
                    } else {
                        if (is_null($id)) {
                            $ss = "SELECT * FROM $table WHERE empresa_id = $idEmpresa or empresa_id 
                            IN (SELECT id FROM empresa WHERE empresa_id='$idEmpresa') ORDER BY " . $limite["campo"] . " " . $limite["orden"] . " LIMIT " . $limite["limite"] . "";
                        } else {
                            $ss = "SELECT * FROM $table WHERE id = " . $id . " AND (empresa_id = $idEmpresa or empresa_id 
                            IN (SELECT id FROM empresa WHERE empresa_id='$idEmpresa')) ORDER BY " . $limite["campo"] . " " . $limite["orden"] . " LIMIT " . $limite["limite"] . "";
                        }
                    }
                }
                
            } else {
                if (is_null($limite)) {
                    if (is_null($id)) {
                        $ss = "SELECT * FROM $table ORDER BY id DESC";
                    } else {
                        $ss = "SELECT * FROM $table WHERE id = " . $id . "  ORDER BY id DESC";
                    }
                } else {
                    if (is_null($id)) {
                        $ss = "SELECT * FROM $table ORDER BY " . $limite["campo"] . " " . $limite["orden"] . " LIMIT " . $limite["limite"] . "";
                    } else {
                        $ss = "SELECT * FROM $table WHERE id = " . $id . "  ORDER BY " . $limite["campo"] . " " . $limite["orden"] . " LIMIT " . $limite["limite"] . "";
                    }
                }
            }

            $recordset = $db->prepare($ss);
            $recordset->execute();
            $o = 0;
            while ($item1 = $recordset->fetch()) {
                $dat[$o] = $item1;
                $datt = null;
                if (isset($relaciones[$table])) {
                    $datt = null;
                    foreach ($relaciones[$table] as $key => $value) {
                        if ($key != "municipio_id" && $key != "areas_ocupacion_id" && $key != "ocupacion_id") {
                            $resul = Catalogos::getCampoExterno($base, $db, $relaciones[$table][$key]["tabla"], $value["campoReturn"], $item1[$key]);
                            $datt[$key] = $resul;
                        }
                    }
                    $dat[$o]["relaciones"] = $datt;
                }
                $o++;
            }
            return $dat;
        } catch (PDOException $e) {
            echo $e->getMessage() . "--" . $e->getCode();
        }
    }

    public static function getDataArray($db, $table, $base, $valores = null)
    {
        $dat = null;
        $relaciones = Catalogos::getEstructuraBD($db, $base);
        try {
            if (is_null($valores)) {
                $ss = "SELECT * FROM $table ORDER BY id DESC";
            } else {
                $where = "";
                foreach ($valores as $key => $value) {
                    $where .= "{$key}='{$value}' AND ";
                }
                $where = substr($where, 0, -4);
                $ss = "SELECT * FROM {$table} WHERE {$where}  ORDER BY id DESC";
            }
            $recordset = $db->prepare($ss);
            $recordset->execute();
            $o = 0;
            while ($item1 = $recordset->fetch()) {
                $dat[$o] = $item1;
                $datt = null;
                if (isset($relaciones[$table])) {
                    $datt = null;
                    foreach ($relaciones[$table] as $key => $value) {
                        $resul = Catalogos::getCampoExterno($base, $db, $relaciones[$table][$key]["tabla"], $value["campoReturn"], $item1[$key]);
                        $datt[$key] = $resul;
                    }
                    $dat[$o]["relaciones"] = $datt;
                }
                $o++;
            }
            return $dat;
        } catch (PDOException $e) {
            echo $e->getMessage() . "--" . $e->getCode();
        }
    }

    public static function getDataID($db, $table, $id, $campo = null)
    {
        $dat = null;
        try {
            if (is_null($campo)) {
                $ss = "SELECT * FROM $table WHERE id = " . $id;
            } else {
                $ss = "SELECT * FROM {$table} WHERE {$campo} = " . $id;
            }
            $recordset = $db->prepare($ss);
            $recordset->execute();
            $item1 = $recordset->fetch(PDO::FETCH_OBJ);
            return $item1;
        } catch (PDOException $e) {
            echo $e->getMessage() . "--" . $e->getCode();
        }
    }

    public static function getDataForField($db, $table, $campo, $id = null)
    {
        $dat = null;
        $relaciones = Catalogos::getEstructuraBD($db, $db);
        $estructuraTable = Catalogos::getStructureTable($db, $table);
        try {
            $ss = "SELECT id FROM {$table} WHERE REPLACE(LOWER({$campo}),' ','_') = '{$id}'";
            $recordset = $db->prepare($ss);
            $recordset->execute();
            $o = 0;
            $item1 = $recordset->fetch(PDO::FETCH_OBJ);
            $dat = $item1->id;
            return $dat;
        } catch (PDOException $e) {
            echo $e->getMessage() . "--" . $e->getCode();
        }
    }

    public static function relaciones()
    {
        $relaciones = array(
            "user" => array(
                "rol_id" => array(
                    "tabla" => "role",
                    "campoReturn" => array("role"),
                )
            )
        );
        return $relaciones;
    }

    public static function getCampoExterno($nameBase, $db, $table, $campoReturn, $valor = null)
    {
        $dat = null;
        try {
            $ss = "SELECT * FROM $table";
            $recordset = $db->prepare($ss);
            $recordset->execute();
            while ($item1 = $recordset->fetch(PDO::FETCH_OBJ)) {
                $retorno = "";
                foreach ($campoReturn as $key => $value) {
                    $retorno .= $item1->$value . " ";
                }
                if (trim($retorno) == "") {
                    $sss = "SELECT table_comment FROM INFORMATION_SCHEMA.TABLES WHERE table_schema='{$nameBase}' AND table_name='{$table}' ";
                    $recordsets = $db->prepare($sss);
                    $recordsets->execute();
                    $item2 = $recordsets->fetch(PDO::FETCH_OBJ);
                    $comment = json_decode($item2->table_comment, true);
                    foreach ($comment["structure"]["return"] as $key => $value) {
                        $retorno .= $item1->$value . " ";
                    }
                }
                $dat[$item1->id] = $retorno;
            }
            return $dat;
        } catch (PDOException $e) {
            echo $e->getMessage() . "--" . $e->getCode();
        }
    }


    public static function guardarRegistro($conf, $nameBase, $path, $bd, $tabla, $campos, $camposRelacionados)
    {
        $camposD = null;
        $valoresD = null;
        // --> Inserta campos de fecha y fecha registro de forma automatica
        $cam = Catalogos::getFields($nameBase, $bd, $tabla);
        foreach ($cam as $key => $value) {
            if (($key == "fecha" && !isset($campos["fecha"])) or ($key == "fecha_registro" && !isset($campos["fecha_registro"])) or ($key == "date_update" && !isset($campos["date_update"])) or ($key == "registration_date" && !isset($campos["registration_date"]))) {
                $camposD .= $key . ",";
                $valoresD .= "'".date("Y-m-d H:i:s")."',";
            } elseif ($key == "user_id") {
                if (isset($_COOKIE["idUser"]) && $_COOKIE["idUser"] > 0) {
                    $camposD .= $key . ",";
                    $valoresD .= $_COOKIE["idUser"] . ",";
                }
            } elseif ($key == "usuarios_id") {
                if (isset($_COOKIE["idUser"]) && $_COOKIE["idUser"] > 0) {
                    $camposD .= $key . ",";
                    $valoresD .= $_COOKIE["idUser"] . ",";
                }
            }
        }

        foreach ($campos as $key => $value) {
            if ($key == "Cpassword") {
                continue;
            }
            if ($key == "password") {
                if (trim($value) != "") {
                    $camposD .= $key . ",";
                    $valoresD .= "MD5('" . $value . "'),";
                }
            } elseif ($key == "password1") {
                if (trim($value) != "") {
                    $camposD .= "password,";
                    $valoresD .= "MD5('" . $value . "'),";
                }
            } elseif ($key == "birthdate") {
                $y =  substr($value, 6, 4);
                $m =  substr($value, 3, 2);
                $d =  substr($value, 0, 2);
                $h =  substr($value, 11, 2);
                $n =  substr($value, 14, 2);
                $nv = $y . "-" . $m . "-" . $d . " " . $h . ":" . $n;
                $camposD .= $key . ",";
                $valoresD .= "'" . $value . "',";
            } else {
                if ($tabla == "blog") {
                    $value = str_replace("../fileman/Uploads/", $path . "/fileman/Uploads/", $value);
                }
                $camposD .= $key . ",";
                if (is_numeric($value)) {
                    $valoresD .= "" . $value . ",";
                } else {
                    $valoresD .= "'" . $value . "',";
                }
            }
        }
        $camposD = substr($camposD, 0, -1);
        $valoresD = substr($valoresD, 0, -1);
        $ss = "INSERT INTO $tabla ($camposD) VALUES ($valoresD)";
        $recordset = $bd->prepare($ss);
        $recordset->execute();
        $idReg = $bd->lastInsertId();

        if ($conf["islogs"] == 1) {

            /*--------------------CONEXION A MONGODB --------------------*/
            $conexion = Catalogos::ConexionMongoDB("Logs_Sistema", "logs");

            ini_set('date.timezone', 'America/Mexico_City');
            $time2 = date('Y/m/d, H:i:s', time());

            $nuevoRegistro = array(
                "id_usuario" => $_COOKIE["idUser"],
                "nombre" => $_COOKIE["Nombre"],
                "accion" => "Insertar",
                "sql" => $ss,
                "tabla" => $tabla,
                "fecha_hora" => $time2
            );

            $res = Catalogos::InsertarMongoDB($conexion, $nuevoRegistro);
            /*--------------------Fin de la conexion a mongo----------------*/
        }


        // --> Guardar imagen
        if (isset($_FILES["txFile"])) {
            $estructura = dirname(__FILE__) . "/../../includes/images/{$tabla}/";
            if (!file_exists($estructura)) {
                if (!mkdir($estructura, 0777, true)) {
                    die('Fallo al crear las carpetas...');
                }
            }
            $file_name = $_FILES["txFile"]["name"];
            $ext = explode(".", $file_name);
            if ($tabla == "profile") {
                $idReg = $campos["url"];
            }
            $add = $estructura . "/" . $idReg . "." . strtolower($ext[1]);
            if (move_uploaded_file($_FILES["txFile"]["tmp_name"], $add)) {
                $ex =  strtolower($ext[1]);
                if ($ex != "jpg") @unlink($estructura . "$idReg.jpg");
                if ($ex != "png") @unlink($estructura . "$idReg.png");
                if ($ex != "gif") @unlink($estructura . "$idReg.gif");
            } else {
            }
        }
        // --> Guardar PDF
        if (isset($_FILES["txFilePDF"])) {
            $estructura = dirname(__FILE__) . "/../../includes/file/zona/";
            if (!file_exists($estructura)) {
                if (!mkdir($estructura, 0777, true)) {
                    die('Fallo al crear la carpeta de PDF...');
                }
            }
            $file_name = $_FILES["txFilePDF"]["name"];
            $ext = explode(".", $file_name);
            if ($tabla == "profile") {
                $idReg = $campos["url"];
            }
            $add = $estructura . "/" . $idReg . ".pdf";
            if (move_uploaded_file($_FILES["txFilePDF"]["tmp_name"], $add)) {
            } else {
            }
        }
        // --> Guardar File
        if (isset($_FILES["txOnlyFile"])) {
            $estructura = dirname(__FILE__) . "/../../includes/files/{$tabla}/";
            if (!file_exists($estructura)) {
                if (!mkdir($estructura, 0777, true)) {
                    die('Fallo al crear la carpeta de FILE...');
                }
            }
            $file_name = $_FILES["txOnlyFile"]["name"];
            $ext = explode(".", $file_name);
            if ($tabla == "profile") {
                $idReg = $campos["url"];
            }
            $add = $estructura . "/" . $idReg . "." . $ext[1];
            if (move_uploaded_file($_FILES["txOnlyFile"]["tmp_name"], $add)) {
            } else {
            }
        }
        // --> Si campos relacionados
        if (!is_null($camposRelacionados)) {
            foreach ($camposRelacionados as $key => $value) {
                $ss = "DELETE FROM {$key} WHERE {$tabla}_id = " . $id;
                $recordset = $bd->prepare($ss);
                $recordset->execute();
                // --> Borrar todoas las relaciones agregar nuevas
                foreach ($value as $k => $v) {
                    $ssw = "INSERT INTO {$key} VALUES (0,{$id},{$v})";
                    $recordsetw = $bd->prepare($ssw);
                    $recordsetw->execute();
                }
            }
        }
        return $idReg;
    }

    public static function borrarRegistro($conf, $bd, $tabla, $id)
    {
        $ss = "DELETE FROM $tabla WHERE id = " . $id;
        $recordset = $bd->prepare($ss);
        $x = $recordset->execute();
          if (!$x) {
            print_r($bd->errorInfo());
        }

        if ($conf["islogs"] == 1) {

            /*--------------------CONEXION A MONGODB --------------------*/
            $conexion = Catalogos::ConexionMongoDB("Logs_Sistema", "logs");

            ini_set('date.timezone', 'America/Mexico_City');
            $time2 = date('Y/m/d, H:i:s', time());

            $nuevoRegistro = array(
                "id_usuario" => $_COOKIE["idUser"],
                "nombre" => $_COOKIE["Nombre"],
                "accion" => "Borrar",
                "sql" => $ss,
                "tabla" => $tabla,
                "fecha_hora" => $time2
            );

            $res = Catalogos::InsertarMongoDB($conexion, $nuevoRegistro);
            /*--------------------Fin de la conexion a mongo----------------*/
        }



        return $id;
    }

    public static function editarRegistro($conf, $nameBase, $path, $bd, $tabla, $campos, $camposRelacionados, $id)
    {
        $camposD = null;
        $valoresD = null;
        // --> Inserta campos de fecha y fecha registro de forma automatica
        $cam = Catalogos::getFields($nameBase, $bd, $tabla);
        foreach ($cam as $key => $value) {
            if (($key == "fecha" && !isset($campos["fecha"])) or ($key == "fecha_modificacion" && !isset($campos["fecha_modificacion"])) or ($key == "date_update" && !isset($campos["date_update"]))) {
                $camposD .= $key . "='".date("Y-m-d H:i:s")."',";
            } elseif ($key == "user_id" && ($tabla != "user_has_servicio")) {
                $camposD .= $key . "=" . $_COOKIE["idUser"] . ",";
            } elseif ($key == "usuarios_id" && ($tabla != "user_has_servicio")) {
                $camposD .= $key . "=" . $_COOKIE["idUser"] . ",";
            }
        }
        foreach ($campos as $key => $value) {
            if ($key == "password") {
                if (trim($value) != "") {
                    $camposD .= $key . "=MD5('" . $value . "'),";
                }
            } else {
                if ($key == "birthdate") {
                    $y =  substr($value, 6, 4);
                    $m =  substr($value, 3, 2);
                    $d =  substr($value, 0, 2);
                    $h =  substr($value, 11, 2);
                    $n =  substr($value, 14, 2);
                    $nv = $y . "-" . $m . "-" . $d . " " . $h . ":" . $n;
                    $camposD .= $key . "='" . $value . "',";
                } else {
                    if ($tabla == "blog") {
                        $value = str_replace("../fileman/Uploads/", $path . "/fileman/Uploads/", $value);
                    }
                    if (is_numeric($value)) {
                        $camposD .= $key . "=" . $value . ",";
                    } else {
                        $camposD .= $key . "='" . $value . "',";
                    }
                }
            }
        }
        $camposD = substr($camposD, 0, -1);
        $ss = "UPDATE $tabla SET $camposD WHERE id = " . $id;
        $recordset = $bd->prepare($ss);
        $recordset->execute();
        $idReg = $id;

        if ($conf["islogs"] == 1) {

            /*------------CONEXION A MONGO DB PARA ACTUALIZAR DATOS------------*/
            $conexion = Catalogos::ConexionMongoDB("Logs_Sistema", "logs");

            ini_set('date.timezone', 'America/Mexico_City');
            $time2 = date('Y/m/d, H:i:s', time());

            $nuevoRegistro = array(
                "id_usuario" => $_COOKIE["idUser"],
                "nombre" => $_COOKIE["Nombre"],
                "accion" => "Editar",
                "sql" => $ss,
                "tabla" => $tabla,
                "fecha_hora" => $time2
            );

            $res = Catalogos::InsertarMongoDB($conexion, $nuevoRegistro);
            /*--------------------Fin de la conexion a mongo----------------*/
        }







        // --> Guardar imagen
        if (isset($_FILES["txFile"])) {
            $estructura = dirname(__FILE__) . "/../../includes/images/{$tabla}/";
            if (!file_exists($estructura)) {
                if (!mkdir($estructura, 0777, true)) {
                    die('Fallo al crear las carpetas...');
                }
            }
            $file_name = $_FILES["txFile"]["name"];
            $ext = explode(".", $file_name);
            if ($tabla == "profile") {
                $idReg = $campos["url"];
            }
            $add = $estructura . "" . $idReg . "." . strtolower($ext[1]);
            if (move_uploaded_file($_FILES["txFile"]["tmp_name"], $add)) {
                $ex =  strtolower($ext[1]);
                if ($ex != "jpg") @unlink($estructura . "$idReg.jpg");
                if ($ex != "png") @unlink($estructura . "$idReg.png");
                if ($ex != "gif") @unlink($estructura . "$idReg.gif");
            } else {
            }
        }
        // --> Guardar PDF
        if (isset($_FILES["txFilePDF"])) {
            $estructura = dirname(__FILE__) . "/../../includes/pdfs/{$tabla}/";
            if (!file_exists($estructura)) {
                if (!mkdir($estructura, 0777, true)) {
                    die('Fallo al crear la carpeta de PDF...');
                }
            }
            $file_name = $_FILES["txFilePDF"]["name"];
            $ext = explode(".", $file_name);
            if ($tabla == "profile") {
                $idReg = $campos["url"];
            }
            $add = $estructura . "/" . $idReg . ".pdf";
            if (move_uploaded_file($_FILES["txFilePDF"]["tmp_name"], $add)) {
            } else {
            }
        }
        // --> Guardar File
        if (isset($_FILES["txOnlyFile"])) {
            $estructura = dirname(__FILE__) . "/../../includes/files/{$tabla}/";
            if (!file_exists($estructura)) {
                if (!mkdir($estructura, 0777, true)) {
                    die('Fallo al crear la carpeta de FILES...');
                }
            }
            $file_name = $_FILES["txOnlyFile"]["name"];
            $ext = explode(".", $file_name);
            if ($tabla == "profile") {
                $idReg = $campos["url"];
            }
            $add = $estructura . "/" . $idReg . "." . $ext[1];
            if (move_uploaded_file($_FILES["txOnlyFile"]["tmp_name"], $add)) {
            } else {
            }
        }
        // --> Si campos relacionados
        if (!is_null($camposRelacionados)) {
            foreach ($camposRelacionados as $key => $value) {
                $ss = "DELETE FROM {$key} WHERE {$tabla}_id = " . $id;
                $recordset = $bd->prepare($ss);
                $recordset->execute();
                // --> Borrar todoas las relaciones agregar nuevas
                foreach ($value as $k => $v) {
                    $ssw = "INSERT INTO {$key} VALUES (0,{$id},{$v})";
                    $recordsetw = $bd->prepare($ssw);
                    $recordsetw->execute();
                }
            }
        }
        return $id;
    }

    public function IsImgTable($db, $tabla)
    {
        try {
            $ss = "SELECT * FROM conf_tabla WHERE tabla = '$tabla'";
            $recordset = $db->prepare($ss);
            $recordset->execute();
            $item1 = $recordset->fetch(PDO::FETCH_OBJ);
            return $item1->img;
        } catch (PDOException $e) {
            echo $e->getMessage() . "--" . $e->getCode();
        }
    }
    /*---------------METODOS DE MONGODB----------------- */

    public static function ConexionMongoDB($bd, $colection)
    {
        $collection = (new MongoDB\Client)->$bd->$colection;

        return $collection;
    }

    public static function ConsultarMongoDB($collection)
    {
        $rs = $collection->find();

        return $rs;
    }

    public static function InsertarMongoDB($collection, $array)
    {

        $insertOneResult =  $collection->insertOne($array);

        return $insertOneResult->getInsertedCount();
    }

    public static function EditarMongoDB($collection, $item, $array)
    {

        $updateResult = $collection->updateOne(array("id" => $item), $array);

        return $updateResult->getModifiedCount();
    }

    public static function EliminarMongoDB($collection, $item)
    {

        $deleteResult = $collection->deleteOne(array('id' => $item));

        return $deleteResult->getDeletedCount();
    }

/*
Metodos para manejo de estructuras editables
*/

    public static function getFieldsAjax($nameBase, $db, $table,$origin)
    {
        $dat = null;
        $cad = array(
            "password" => 0,
        );
        $estructuraTable = Catalogos::getStructureTable($nameBase, $db, $table);
        try {
            // --> Obtener datos de los Campos
            $ss = "SHOW FULL COLUMNS FROM " . $table;
            $recordset1 = $db->prepare($ss);
            $recordset1->execute();
            foreach ($recordset1->fetchAll(PDO::FETCH_OBJ) as $key => $row1) {
                $cad = null;
                $Field = $row1->Field;
                if ($row1->Comment != "") {
                    $arr1 = str_split($row1->Comment);
                    foreach ($arr1 as $key => $value) {
                        $num = ord($value);
                        if ($num == 147 || $num == 148) {
                            $letra = '"';
                        } else {
                            $letra = $value;
                        }
                        $cad .= $letra;
                    }
                    $commentField = json_decode($cad, true);
                    $Field = $commentField["name"];
                }

                $nameCamp = str_replace("_id", " ", $Field);
                //$nameCamp = str_replace("id_tra", " ", $nameCamp);
                //$nameCamp = str_replace("id_conf", " ", $nameCamp);
                //$nameCamp = str_replace("id_", " ", $nameCamp);
                //$nameCamp = str_replace("_", " ", $nameCamp);

                if (@array_key_exists($row1->Field, $cad)) {

                } else {
                    $entrar = 0;
                    if (isset($estructuraTable[0]["structure"]["views"][$origin]) && !empty($estructuraTable[0]["structure"]["views"][$origin])) {
                        foreach ($estructuraTable[0]["structure"]["views"][$origin] as $key => $value) {
                            if ($row1->Field == $key) {
                                $entrar = 1;
                            }
                        }
                        if ($entrar == 1) {
                            $dat[$row1->Field] = ucfirst($nameCamp);
                        }
                    }else{
                        $dat[$row1->Field] = ucfirst($nameCamp);
                    }
                    
                }
            }
            return $dat;
        } catch (PDOException $e) {
            echo $e->getMessage() . "--" . $e->getCode();
        }
    }

    public static function getFieldsAllAjax($db, $table,  $base,$origin)
    {

        //var_dump($db);
        $relaciones = Catalogos::getEstructuraBD($db, $base);
        //--> Estructura
        $estructuraTable = Catalogos::getStructureTable($base, $db, $table);
        $dat = null;
        try {
            // --> Obtener datos de los Campos
            $ss = "SHOW FULL COLUMNS FROM " . $table;
            $recordset1 = $db->prepare($ss);
            $recordset1->execute();
            foreach ($recordset1->fetchAll(PDO::FETCH_OBJ) as $key => $row1) {
                $cad = null;
                $Field = $row1->Field;
                if ($row1->Comment != "") {
                    $arr1 = str_split($row1->Comment);
                    foreach ($arr1 as $key => $value) {
                        $num = ord($value);
                        if ($num == 147 || $num == 148) {
                            $letra = '"';
                        } else {
                            $letra = $value;
                        }
                        $cad .= $letra;
                    }
                    $commentField = json_decode($cad, true);
                    $Field = $commentField["name"];
                }

                $nameCamp = str_replace("_id", " ", $row1->Field);
                //$nameCamp = str_replace("id_cat", " ", $row1->Field);
                //$nameCamp = str_replace("id_tra", " ", $nameCamp);
                //$nameCamp = str_replace("id_conf", " ", $nameCamp);
                //$nameCamp = str_replace("id_", " ", $nameCamp);
                $nameCamp = str_replace("_", " ", $nameCamp);
                $ttipo = explode("(", $row1->Type);
                $tam = str_replace(")", "", $ttipo[1]);
                $paso = false;
               
                if (isset($estructuraTable[0]["structure"]["views"][$origin])) {
                    foreach ($estructuraTable[0]["structure"]["views"][$origin] as $key => $value) {
                        if ($row1->Field == $key) {
                            $paso = true;
                        }
                    }
                }
                if ($paso) {
                    if ($row1->Field != "user_id") {
                        $relaD = NULL;
                        if (isset($relaciones[$table][$row1->Field])) {
                            $relaD = array($relaciones[$table][$row1->Field], Catalogos::getData($db, $relaciones[$table][$row1->Field]["tabla"], $base));
                        }
                        $dat[$row1->Field] = array(
                            "nombre" => ucfirst($nameCamp),
                            "nombreSalida" => ucfirst($Field),
                            "tipo" => $ttipo[0],
                            "size" => $tam,
                            "relaciones" => $relaD
                        );
                    }
                }
            }
            //$relaciones = Catalogos::getEstructuraBD($db,$base);
            return $dat;
        } catch (PDOException $e) {
            echo $e->getMessage() . "--" . $e->getCode();
        }
    }


    public static function guardarRegistroAjax($origin,$conf, $nameBase, $path, $bd, $tabla, $campos, $camposRelacionados)
    {
        $camposD = null;
        $valoresD = null;
        // --> Inserta campos de fecha y fecha registro de forma automatica
        $cam = Catalogos::getFieldsAjax($nameBase, $bd, $tabla,$origin);
        foreach ($cam as $key => $value) {
            if (($key == "fecha" && !isset($campos["fecha"])) or ($key == "fecha_registro" && !isset($campos["fecha_registro"])) or ($key == "date_update" && !isset($campos["date_update"])) or ($key == "registration_date" && !isset($campos["registration_date"]))) {
                $camposD .= $key . ",";
                $valoresD .= "'".date("Y-m-d H:i:s")."',";
            } elseif ($key == "user_id") {
                if (isset($_COOKIE["idUser"]) && $_COOKIE["idUser"] > 0) {
                    $camposD .= $key . ",";
                    $valoresD .= $_COOKIE["idUser"] . ",";
                }
            } elseif ($key == "usuarios_id") {
                if (isset($_COOKIE["idUser"]) && $_COOKIE["idUser"] > 0) {
                    $camposD .= $key . ",";
                    $valoresD .= $_COOKIE["idUser"] . ",";
                }
            }
        }

        foreach ($campos as $key => $value) {
            if ($key == "Cpassword") {
                continue;
            }
            if ($key == "password") {
                if (trim($value) != "") {
                    $camposD .= $key . ",";
                    $valoresD .= "MD5('" . $value . "'),";
                }
            } elseif ($key == "password1") {
                if (trim($value) != "") {
                    $camposD .= "password,";
                    $valoresD .= "MD5('" . $value . "'),";
                }
            } elseif ($key == "birthdate") {
                $y =  substr($value, 6, 4);
                $m =  substr($value, 3, 2);
                $d =  substr($value, 0, 2);
                $h =  substr($value, 11, 2);
                $n =  substr($value, 14, 2);
                $nv = $y . "-" . $m . "-" . $d . " " . $h . ":" . $n;
                $camposD .= $key . ",";
                $valoresD .= "'" . $value . "',";
            } else {
                if ($tabla == "blog") {
                    $value = str_replace("../fileman/Uploads/", $path . "/fileman/Uploads/", $value);
                }
                $camposD .= $key . ",";
                if (is_numeric($value)) {
                    $valoresD .= "" . $value . ",";
                } else {
                    $valoresD .= "'" . $value . "',";
                }
            }
        }
        $camposD = substr($camposD, 0, -1);
        $valoresD = substr($valoresD, 0, -1);
        $ss = "INSERT INTO $tabla ($camposD) VALUES ($valoresD)";
        $recordset = $bd->prepare($ss);
        $recordset->execute();
        $idReg = $bd->lastInsertId();

        if ($conf["islogs"] == 1) {

            /*--------------------CONEXION A MONGODB --------------------*/
            $conexion = Catalogos::ConexionMongoDB("Logs_Sistema", "logs");

            ini_set('date.timezone', 'America/Mexico_City');
            $time2 = date('Y/m/d, H:i:s', time());

            $nuevoRegistro = array(
                "id_usuario" => $_COOKIE["idUser"],
                "nombre" => $_COOKIE["Nombre"],
                "accion" => "Insertar",
                "sql" => $ss,
                "tabla" => $tabla,
                "fecha_hora" => $time2
            );

            $res = Catalogos::InsertarMongoDB($conexion, $nuevoRegistro);
            /*--------------------Fin de la conexion a mongo----------------*/
        }


        // --> Guardar imagen
        if (isset($_FILES["txFile"])) {
            $estructura = dirname(__FILE__) . "/../../includes/images/{$tabla}/";
            if (!file_exists($estructura)) {
                if (!mkdir($estructura, 0777, true)) {
                    die('Fallo al crear las carpetas...');
                }
            }
            $file_name = $_FILES["txFile"]["name"];
            $ext = explode(".", $file_name);
            if ($tabla == "profile") {
                $idReg = $campos["url"];
            }
            $add = $estructura . "/" . $idReg . "." . strtolower($ext[1]);
            if (move_uploaded_file($_FILES["txFile"]["tmp_name"], $add)) {
                $ex =  strtolower($ext[1]);
                if ($ex != "jpg") @unlink($estructura . "$idReg.jpg");
                if ($ex != "png") @unlink($estructura . "$idReg.png");
                if ($ex != "gif") @unlink($estructura . "$idReg.gif");
            } else {
            }
        }
        // --> Guardar PDF
        if (isset($_FILES["txFilePDF"])) {
            $estructura = dirname(__FILE__) . "/../../includes/file/zona/";
            if (!file_exists($estructura)) {
                if (!mkdir($estructura, 0777, true)) {
                    die('Fallo al crear la carpeta de PDF...');
                }
            }
            $file_name = $_FILES["txFilePDF"]["name"];
            $ext = explode(".", $file_name);
            if ($tabla == "profile") {
                $idReg = $campos["url"];
            }
            $add = $estructura . "/" . $idReg . ".pdf";
            if (move_uploaded_file($_FILES["txFilePDF"]["tmp_name"], $add)) {
            } else {
            }
        }
        // --> Guardar File
        if (isset($_FILES["txOnlyFile"])) {
            $estructura = dirname(__FILE__) . "/../../includes/files/{$tabla}/";
            if (!file_exists($estructura)) {
                if (!mkdir($estructura, 0777, true)) {
                    die('Fallo al crear la carpeta de FILE...');
                }
            }
            $file_name = $_FILES["txOnlyFile"]["name"];
            $ext = explode(".", $file_name);
            if ($tabla == "profile") {
                $idReg = $campos["url"];
            }
            $add = $estructura . "/" . $idReg . "." . $ext[1];
            if (move_uploaded_file($_FILES["txOnlyFile"]["tmp_name"], $add)) {
            } else {
            }
        }
        // --> Si campos relacionados
        if (!is_null($camposRelacionados)) {
            foreach ($camposRelacionados as $key => $value) {
                $ss = "DELETE FROM {$key} WHERE {$tabla}_id = " . $id;
                $recordset = $bd->prepare($ss);
                $recordset->execute();
                // --> Borrar todoas las relaciones agregar nuevas
                foreach ($value as $k => $v) {
                    $ssw = "INSERT INTO {$key} VALUES (0,{$id},{$v})";
                    $recordsetw = $bd->prepare($ssw);
                    $recordsetw->execute();
                }
            }
        }
        return $idReg;
    }

  

    public static function editarRegistroAjax($origin, $conf, $nameBase, $path, $bd, $tabla, $campos, $camposRelacionados, $id)
    {
        $camposD = null;
        $valoresD = null;
        // --> Inserta campos de fecha y fecha registro de forma automatica
        $cam = Catalogos::getFieldsAjax($nameBase, $bd, $tabla,$origin);
        foreach ($cam as $key => $value) {
            if (($key == "fecha" && !isset($campos["fecha"])) or ($key == "fecha_modificacion" && !isset($campos["fecha_modificacion"])) or ($key == "date_update" && !isset($campos["date_update"]))) {
                $camposD .= $key . "='".date("Y-m-d H:i:s")."',";
            } elseif ($key == "user_id" && ($tabla != "user_has_servicio")) {
                $camposD .= $key . "=" . $_COOKIE["idUser"] . ",";
            } elseif ($key == "usuarios_id" && ($tabla != "user_has_servicio")) {
                $camposD .= $key . "=" . $_COOKIE["idUser"] . ",";
            }
        }
        foreach ($campos as $key => $value) {
            if ($key == "password") {
                if (trim($value) != "") {
                    $camposD .= $key . "=MD5('" . $value . "'),";
                }
            } else {
                if ($key == "birthdate") {
                    $y =  substr($value, 6, 4);
                    $m =  substr($value, 3, 2);
                    $d =  substr($value, 0, 2);
                    $h =  substr($value, 11, 2);
                    $n =  substr($value, 14, 2);
                    $nv = $y . "-" . $m . "-" . $d . " " . $h . ":" . $n;
                    $camposD .= $key . "='" . $value . "',";
                } else {
                    if ($tabla == "blog") {
                        $value = str_replace("../fileman/Uploads/", $path . "/fileman/Uploads/", $value);
                    }
                    if (is_numeric($value)) {
                        $camposD .= $key . "=" . $value . ",";
                    } else {
                        $camposD .= $key . "='" . $value . "',";
                    }
                }
            }
        }
        $camposD = substr($camposD, 0, -1);
        $ss = "UPDATE $tabla SET $camposD WHERE id = " . $id;
        $recordset = $bd->prepare($ss);
        $recordset->execute();
        $idReg = $id;

        if ($conf["islogs"] == 1) {

            /*------------CONEXION A MONGO DB PARA ACTUALIZAR DATOS------------*/
            $conexion = Catalogos::ConexionMongoDB("Logs_Sistema", "logs");

            ini_set('date.timezone', 'America/Mexico_City');
            $time2 = date('Y/m/d, H:i:s', time());

            $nuevoRegistro = array(
                "id_usuario" => $_COOKIE["idUser"],
                "nombre" => $_COOKIE["Nombre"],
                "accion" => "Editar",
                "sql" => $ss,
                "tabla" => $tabla,
                "fecha_hora" => $time2
            );

            $res = Catalogos::InsertarMongoDB($conexion, $nuevoRegistro);
            /*--------------------Fin de la conexion a mongo----------------*/
        }







        // --> Guardar imagen
        if (isset($_FILES["txFile"])) {
            $estructura = dirname(__FILE__) . "/../../includes/images/{$tabla}/";
            if (!file_exists($estructura)) {
                if (!mkdir($estructura, 0777, true)) {
                    die('Fallo al crear las carpetas...');
                }
            }
            $file_name = $_FILES["txFile"]["name"];
            $ext = explode(".", $file_name);
            if ($tabla == "profile") {
                $idReg = $campos["url"];
            }
            $add = $estructura . "" . $idReg . "." . strtolower($ext[1]);
            if (move_uploaded_file($_FILES["txFile"]["tmp_name"], $add)) {
                $ex =  strtolower($ext[1]);
                if ($ex != "jpg") @unlink($estructura . "$idReg.jpg");
                if ($ex != "png") @unlink($estructura . "$idReg.png");
                if ($ex != "gif") @unlink($estructura . "$idReg.gif");
            } else {
            }
        }
        // --> Guardar PDF
        if (isset($_FILES["txFilePDF"])) {
            $estructura = dirname(__FILE__) . "/../../includes/pdfs/{$tabla}/";
            if (!file_exists($estructura)) {
                if (!mkdir($estructura, 0777, true)) {
                    die('Fallo al crear la carpeta de PDF...');
                }
            }
            $file_name = $_FILES["txFilePDF"]["name"];
            $ext = explode(".", $file_name);
            if ($tabla == "profile") {
                $idReg = $campos["url"];
            }
            $add = $estructura . "/" . $idReg . ".pdf";
            if (move_uploaded_file($_FILES["txFilePDF"]["tmp_name"], $add)) {
            } else {
            }
        }
        // --> Guardar File
        if (isset($_FILES["txOnlyFile"])) {
            $estructura = dirname(__FILE__) . "/../../includes/files/{$tabla}/";
            if (!file_exists($estructura)) {
                if (!mkdir($estructura, 0777, true)) {
                    die('Fallo al crear la carpeta de FILES...');
                }
            }
            $file_name = $_FILES["txOnlyFile"]["name"];
            $ext = explode(".", $file_name);
            if ($tabla == "profile") {
                $idReg = $campos["url"];
            }
            $add = $estructura . "/" . $idReg . "." . $ext[1];
            if (move_uploaded_file($_FILES["txOnlyFile"]["tmp_name"], $add)) {
            } else {
            }
        }
        // --> Si campos relacionados
        if (!is_null($camposRelacionados)) {
            foreach ($camposRelacionados as $key => $value) {
                $ss = "DELETE FROM {$key} WHERE {$tabla}_id = " . $id;
                $recordset = $bd->prepare($ss);
                $recordset->execute();
                // --> Borrar todoas las relaciones agregar nuevas
                foreach ($value as $k => $v) {
                    $ssw = "INSERT INTO {$key} VALUES (0,{$id},{$v})";
                    $recordsetw = $bd->prepare($ssw);
                    $recordsetw->execute();
                }
            }
        }
        return $id;
    }


}
