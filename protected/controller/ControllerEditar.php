<?php
class ControllerEditar extends Controller
{
    function __construct($view, $conf, $var, $acc)
    {
        parent::__construct($view, $conf, $var, $acc);
    }
    public function main()
    {
    
        $this->data["accion"] = "Editar";
        $dominio = $this->data["Dominio"];
        $r = indexModel::bd($this->conf)->getEstructuraTable($dominio);
        $this->data["nameTable"] = indexModel::bd($this->conf)->getEstructuraTable($dominio)[0]["structure"]["name"];
        $this->data["dominio"] = $dominio;
        $this->data["campos"] = indexModel::bd($this->conf)->getcamposAll($dominio);
        $this->data["datos"] = indexModel::bd($this->conf)->getDominio($dominio, $this->data["idReg"]);
        $this->data["isImg"] = indexModel::bd($this->conf)->getEstructuraTable($dominio)[0]["structure"]["img"];
        $this->data["isPDF"] = indexModel::bd($this->conf)->getEstructuraTable($dominio)[0]["structure"]["pdf"];
        $this->data["isFILE"] = indexModel::bd($this->conf)->getEstructuraTable($dominio)[0]["structure"]["files"];
        $this->view->show("addCatalogo.twig", $this->data, $this->accion);
    }
}
