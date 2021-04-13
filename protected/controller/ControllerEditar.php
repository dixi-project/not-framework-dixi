<?php
class ControllerEditar extends Controller {
    function __construct($view, $conf, $var, $acc) {
        parent::__construct($view, $conf, $var, $acc);
    } 
    public function main() {
        foreach ($this->var as $key => $value) {
            $this->data[$key] = $value;
        }
        $this->data["accion"] = "Editar";
        $dominio = $this->var["Dominio"];
        $this->data["nameTable"] = indexModel::bd($this->conf)->getEstructuraTable($dominio)["structure"]["nameTable"];
        $this->data["dominio"] = $dominio;
        $this->data["campos"] = indexModel::bd($this->conf)->getcamposAll($dominio);
        $this->data["datos"] = indexModel::bd($this->conf)->getDominio($dominio,$this->var["idReg"]);
        $this->data["isImg"] = indexModel::bd($this->conf)->getEstructuraTable($dominio)["structure"]["img"];
        $this->data["isPDF"] = indexModel::bd($this->conf)->getEstructuraTable($this->var["Dominio"])["structure"]["pdf"];
        $this->data["isFILE"] = indexModel::bd($this->conf)->getEstructuraTable($this->var["Dominio"])["structure"]["file"];
        $this->view->show("addCatalogo.html", $this->data, $this->accion); 
    }
}
?>