<?php
class controllerAgregar extends Controller {
    function __construct($view, $conf, $var, $acc) {
        parent::__construct($view, $conf, $var, $acc);
    } 
    public function main() {
        foreach ($this->var as $key => $value) {
            $$key = $value;
        }
        $dominio = $Dominio;
        $this->data["accion"] = "Agregar";
        $this->data["nameTable"] = indexModel::bd($this->conf)->getEstructuraTable($dominio)["structure"]["nameTable"];
        $this->data["dominio"] = $this->var["Dominio"];
        $this->data["campos"] = indexModel::bd($this->conf)->getcamposAllAjax($this->var["Dominio"],"ADD");
        $this->data["isImg"] = indexModel::bd($this->conf)->getEstructuraTable($this->var["Dominio"])[0]["structure"]["img"];
        $this->data["isPDF"] = indexModel::bd($this->conf)->getEstructuraTable($this->var["Dominio"])[0]["structure"]["pdf"];
        $this->data["isFILE"] = indexModel::bd($this->conf)->getEstructuraTable($this->var["Dominio"])[0]["structure"]["file"];
        $this->view->show("addCatalogo.html", $this->data, $this->accion); 
    }
}
?>