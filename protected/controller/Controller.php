<?php
class Controller
{
    public $view;
    public $conf;
    public $var;
    public $data;

    function __construct($view, $conf, $var, $acc)
    {
        $this->view = $view;
        $this->conf = $conf;
        //$this->var = $var;
        $this->accion = $acc;
        $this->data = $var;
        foreach ($_COOKIE as $key => $value) {
            $this->data[$key]=$value;
        }
    }
}
