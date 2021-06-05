<?php
class controllerLogin extends Controller
{
    function __construct($view, $conf, $var, $acc)
    {
        parent::__construct($view, $conf, $var, $acc);
    }
    public function main()
    {
        $data = null;
        $usu = $this->data["username"];
        $pass = $this->data["pwd"];
        $res = indexModel::bd($this->conf)->validarAcceso($usu, $pass);
        $dd = explode("|", $res);
        if ($dd[0] > 0) {
            $data["isCorrect"] = TRUE;
            $data["tituloMensaje"] = "Acceso correcto.";
            $data["Mensaje"] = "El usuario es valido.";
            //if($dd[1]==1){// --> Administrador
            $data["return"] = $this->conf["pathSite"] . "home";
            //}
            $data["tiempo"] = "0";
        } else {
            $data["isCorrect"] = FALSE;
            $data["tituloMensaje"] = "Error en el login.";
            $data["Mensaje"] = "El usuario o contaseña son incorectos o el usuario aun no es validado.";
            $data["return"] = $this->conf["pathSite"];
            $data["tiempo"] = "0";
        }
        $data["return"] = indexModel::bd($this->conf)->getMensaje($data);
        $templa  = "mensajeBackEnd.html";
        $this->view->show($templa, $data, $this->accion);
    }
}
