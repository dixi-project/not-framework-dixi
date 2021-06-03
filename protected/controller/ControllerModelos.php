<?php
class ControllerModelos extends Controller
{
    function __construct($view, $conf, $var, $acc)
    {
        parent::__construct($view, $conf, $var, $acc);
    }

    public function main()
    {
        $dd = null;
        foreach ($this->var as $key => $value) {
            $this->data[$key] = $value;
        }
        $nameFielBD = "Tables_in_" . $this->conf["dbname"];
        // --> 1. Cargar datos si no hay datos en las tablas de config 
        $tables = indexModel::bd($this->conf)->getSQL("SHOW TABLES");
        foreach ($tables as $key => $value) {
            if (substr($value->$nameFielBD, 0, 5) == "conf_") {
                continue;
            }
            // --> Buscar si existe en conf_tables si no crear el registro 
            $campo = $value->$nameFielBD;
            $ss1 = "SELECT COUNT(*) AS nr FROM conf_table WHERE conf_table = '$campo'";
            $sql = (object) indexModel::bd($this->conf)->getSQL($ss1)[0];
            if ($sql->nr == 0) {
                $nameNew = ucfirst(str_replace("_", " ", str_replace("_id", "", $campo)));

                $aar = array(
                    "Dominio" => 'conf_table',
                    "txtconf_table" => $campo,
                    "txtname" => $nameNew

                );
                $idTable = indexModel::bd($this->conf)->updateDominio($aar);
            } else {
                $ss2 = "SELECT id FROM  conf_table WHERE conf_table = '$campo'";
                $idTable = (object) indexModel::bd($this->conf)->getSQL($ss2)[0];
                $idTable = $idTable->id;
            }
            // --> Buscar los campos y llenarlos 
            $ss = "SHOW FULL COLUMNS FROM " . $campo;
            $tables2 = indexModel::bd($this->conf)->getSQL($ss);
            foreach ($tables2 as $key1 => $value1) {
                $ss1 = "SELECT COUNT(*) AS nr FROM conf_field where conf_table_id = {$idTable} AND conf_field='{$value1->Field}'";
                $sql = (object) indexModel::bd($this->conf)->getSQL($ss1)[0];
                if ($sql->nr == 0) {
                    $campo = ucfirst(str_replace("_", " ", str_replace("_id", "", $value1->Field)));
                    $ss3 = "INSERT INTO conf_field VALUES (0,'{$value1->Field}','{$campo}',{$idTable})";
                    indexModel::bd($this->conf)->getSQL($ss3);
                }
            }
        }
        $tables = indexModel::bd($this->conf)->getDominio("conf_table");
        asort($tables);
        $this->data["tables"] = $tables;
        foreach ($this->data["tables"] as $k => $v) {
            $aa = array("conf_table_id" => $v["id"]);
            $campos = indexModel::bd($this->conf)->getDominioID("conf_field", $aa);
            asort($campos);
            $this->data["tables"][$k]["fields"] = $campos;
        }
        $confView = indexModel::bd($this->conf)->getDominio("conf_view");
        foreach ($confView as $kk => $vv) {
            $nn = $vv["conf_table_id"] . $vv["conf_type_view_id"] . $vv["conf_field_id"];
            $ddd[$nn] = $vv;
        }
        $this->data["confView"] = $ddd;
        $this->view->show("modelos.html", $this->data, $this->accion);
    }
}
