<?php
class ControllerIndex extends Controller {
    function __construct($view, $conf, $var, $acc) {
        parent::__construct($view, $conf, $var, $acc);
    }
    public function main() {
        //$dominio = indexModel::bd($this->conf)->getDominio("role");
        //$this->view->show("index.html", $this->data, $this->accion);
        $this->view->show("indexLogin.html", $this->data, $this->accion);
    }
}
?>
