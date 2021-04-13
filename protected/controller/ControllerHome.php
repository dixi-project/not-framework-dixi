<?php
class ControllerHome extends Controller {
    function __construct($view, $conf, $var, $acc) {
        parent::__construct($view, $conf, $var, $acc);
    }
     public function main() {
    	    foreach ($this->var as $key => $value) {
            $this->data[$key] = $value;
        }
        //indexModel::bd($this->conf)->controlAcceso(["1","2"]);
        $this->view->show("home.html", $this->data, $this->accion);
    }
}
?>
