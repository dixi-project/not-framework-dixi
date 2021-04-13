<?php
class ControllerError404 extends Controller {
    function __construct($view, $conf, $var, $acc) {
        parent::__construct($view, $conf, $var, $acc);
    } 
    public function main() {
        $this->view->show("error404.html", $this->data, $this->accion); 
    }
}
?>