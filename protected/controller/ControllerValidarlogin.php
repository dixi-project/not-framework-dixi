<?php
class controllerValidarLogin {
    function __construct($view, $conf, $var, $acc) {
        $this->view = $view;
        $this->conf = $conf;
        $this->var = $var;
        $this->accion = $acc;
    } 
    public function main() {
        $data=null;
        $usu = $this->var["TXTemail"];
        $pass = $this->var["TXTpassword"];
        $res = indexModel::bd($this->conf)->validarAcceso($usu,$pass);
        if($res==1){
            $data["isCorrect"] = TRUE;
            $data["tituloMensaje"] = "Acceso correcto.";
            $data["Mensaje"] = "El usuario es valido.";
            switch ($_COOKIE["idRol"]){
                case 1:
                    $data["return"] = "home-admin";
                break;
                case 2:
                    $data["return"] = "home-organizador";
                break;
                case 3:
                    $data["return"] = "home-proveedor";
                break;
                default:
                    $data["return"] = "home-admin";
                break;
            }
            $data["tiempo"] = "0";
        }else{
            $data["isCorrect"] = FALSE;
            $data["tituloMensaje"] = "Error en el login.";
            $data["Mensaje"] = "El usuario o contaseña son incorectos o el usuario aun no es validado.";
            $data["return"] = $this->conf["pathSite"];
            $data["tiempo"] = "3";
        }
        $data["return"]=indexModel::bd($this->conf)->getMensaje($data);
        $templa  = "mensajeBackEnd.tpl";
        $this->view->show($templa, $data, $this->accion); 
    }
}
?>