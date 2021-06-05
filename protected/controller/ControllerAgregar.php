<?php
class controllerAgregar extends Controller
{
    function __construct($view, $conf, $var, $acc)
    {
        parent::__construct($view, $conf, $var, $acc);
    }
    public function main()
    {
        $dominio = $this->data["Dominio"];
        $this->data["accion"] = "Agregar";
        $this->data["nameTable"] = indexModel::bd($this->conf)->getEstructuraTable($dominio)["structure"]["nameTable"];
        $this->data["dominio"] = $this->data["Dominio"];
        $this->data["campos"] = indexModel::bd($this->conf)->getcamposAllAjax($this->data["Dominio"], "ADD");
        $this->data["isImg"] = indexModel::bd($this->conf)->getEstructuraTable($this->data["Dominio"])[0]["structure"]["img"];
        $this->data["isPDF"] = indexModel::bd($this->conf)->getEstructuraTable($this->data["Dominio"])[0]["structure"]["pdf"];
        $this->data["isFILE"] = indexModel::bd($this->conf)->getEstructuraTable($this->data["Dominio"])[0]["structure"]["files"];
        $this->view->show("addCatalogo.twig", $this->data, $this->accion);
    }
}
