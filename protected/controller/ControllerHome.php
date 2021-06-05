<?php
class ControllerHome extends Controller
{
    function __construct($view, $conf, $var, $acc)
    {
        parent::__construct($view, $conf, $var, $acc);
    }
    public function main()
    {
        //indexModel::bd($this->conf)->controlAcceso(["1","2"]);
        $this->view->show("home.html", $this->data, $this->accion);
    }
}
