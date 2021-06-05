<?php
class ControllerCatalogo extends Controller
{
    function __construct($view, $conf, $var, $acc)
    {
        parent::__construct($view, $conf, $var, $acc);
    }
    public function main()
    {
        //indexModel::bd($this->conf)->controlAcceso(["1"]);
        $dat = explode("/", $this->data["con"]);
        $dominio = $dat[1];
        $this->data["activeRol"] = "sfActive";
        $structure = indexModel::bd($this->conf)->getEstructuraTable($dominio)["structure"];
        $this->data["isImg"] = $structure["img"];
        $this->data["dominio"] = $dominio;
        $this->data["campos"] = indexModel::bd($this->conf)->getcamposAjax($dominio, "REPORT");
        $this->data["datos"] = indexModel::bd($this->conf)->getDominio($dominio);
        asort($this->data["datos"]);
        $this->view->show("adminCatalogo.twig", $this->data, $this->accion);
    }
}
