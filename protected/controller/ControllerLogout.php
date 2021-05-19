<?php
class ControllerLogout {
    function __construct($view, $conf, $var, $acc) {
        $this->view = $view;
        $this->conf = $conf;
        $this->var = $var;
        $this->accion = $acc;
    }
    public function main() {
        $data=null;


        setcookie('idUser', null, -1, '/', $_SERVER["SERVER_NAME"], isset($_SERVER["HTTPS"]), true);
        setcookie('idRol', null, -1, '/', $_SERVER["SERVER_NAME"], isset($_SERVER["HTTPS"]), true);
        setcookie('Rol', null, -1, '/', $_SERVER["SERVER_NAME"], isset($_SERVER["HTTPS"]), true);
        setcookie('Nombre', null, -1, '/', $_SERVER["SERVER_NAME"], isset($_SERVER["HTTPS"]), true);
        /*
        unset($_COOKIE['idUser']);
        unset($_COOKIE['idRol']);
        unset($_COOKIE['Rol']);
        unset($_COOKIE['Nombre']);
        */
        $data["isCorrect"] = TRUE;
        $data["tituloMensaje"] = "Exito!";
        $data["Mensaje"] = "Saliendo del sistema.";
        $data["return"] = $this->conf["pathSite"];
        $data["tiempo"] = "3";
        $data["return"]=indexModel::bd($this->conf)->getMensaje($data);
        $templa  = "mensajeBackEnd.html";
        $this->view->show($templa, $data, $this->accion);

    }
}
?>
