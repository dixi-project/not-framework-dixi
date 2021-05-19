<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
class ControllerCatalogo extends Controller
{
    function __construct($view, $conf, $var, $acc)
    {
        parent::__construct($view, $conf, $var, $acc);
    }
    public function main()
    {
        //indexModel::bd($this->conf)->controlAcceso(["1"]);
        foreach ($this->var as $key => $value) {
            $this->data[$key] = $value;
            $$key = $value;
            //echo $key . "--". $value;
        }
        $dat = explode("/", $this->var["con"]);
        //$this->data["controller"] = $dat[0];
        $dominio = $dat[1];
        // --> Programar

        if ($Action == "subir") {
            $sqlValidate1 = "SELECT * FROM campana WHERE id = {$idReg2}";
            $campana = indexModel::bd($this->conf)->getSQL($sqlValidate1)[0];
            //var_dump($campana);
            // --> Actualizar Campaña
            $ss = "UPDATE campana SET status_envio_id = 1 WHERE id = {$idReg2}";
            //echo $ss;
            indexModel::bd($this->conf)->getSQL($ss);
            $file_handle = fopen(dirname(__FILE__) . "/../../includes/files/campana/" . $idReg2 . ".csv", "r");
            //echo $file_handle;
            $conn = 0;
            while (!feof($file_handle)) {
                $line_of_text = fgetcsv($file_handle, 2048);
                $conn++;
                if ($conn > 1) {
                    $sss = "INSERT INTO send (nombre,telefono,mensaje,fecha,campana_id,fecha_limite,cantidad) VALUES ('" . $line_of_text[1] . "','" . $line_of_text[0] . "','" . $campana->mensaje . "', '" . $campana->fecha_envio . " " . $campana->hora_envio . "', {$idReg2},'" . $line_of_text[2] . "','" . $line_of_text[3] . "')";
                    //echo $sss . " <br>\n";
                    indexModel::bd($this->conf)->getSQL($sss);
                }
            }
        }

        $this->data["activeRol"] = "sfActive";
        $structure = indexModel::bd($this->conf)->getEstructuraTable($dominio)["structure"];
        //var_dump($structure);
        //$this->data["nameTable"] = $structure["nameTable"];
        $this->data["isImg"] = $structure["img"];
        $this->data["dominio"] = $dominio;
        $this->data["campos"] = indexModel::bd($this->conf)->getcamposAjax($dominio,"REPORT");
        //var_dump( $this->data["campos"]);
        if ($dominio == "campana") {
            $iid = $_COOKIE["idUser"];
            $this->data["datos"] = indexModel::bd($this->conf)->getSQL("SELECT * FROM campana WHERE user_id = {$iid}");
        } else {
            $this->data["datos"] = indexModel::bd($this->conf)->getDominio($dominio);
        }

        asort($this->data["datos"]);
        $this->view->show("adminCatalogo.html", $this->data, $this->accion);
    }


    public function check_in_range($fecha_inicio, $fecha_fin, $fecha)
    {

        $fecha_inicio = strtotime($fecha_inicio);
        $fecha_fin = strtotime($fecha_fin);
        $fecha = strtotime($fecha);

        if (($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {

            return true;
        } else {

            return false;
        }
    }
}
