<?php
class controllerUpdatecatalogo {
    function __construct($view, $conf, $var, $acc) {
        $this->view = $view;
        $this->conf = $conf;
        $this->var = $var;
        $this->accion = $acc;
    }

    public function main() {
        //indexModel::bd($this->conf)->controlAcceso(["1","3"]);
        $data=null;
        foreach ($this->var as $key => $value) {
            //echo $key."--".$value."<br>";
            $$key = $value;
        }
        //exit();
        $directos = array("evento"=>0);
        if($this->var["idReg"]>0 && $this->var["Action"]=="delete"){
            $res =  indexModel::bd($this->conf)->deleteDominio($this->var["Dominio"],$this->var["idReg"]);
        }elseif(!isset($cmdRegresar)){
            foreach ($this->var as $key => $value) {
               $datoss[$key] = str_replace("'", '"', $value);
            }
            $res = indexModel::bd($this->conf)->updateDominio($datoss,$this->var["idReg"]);
        }

        if($res > 0){
            $data["isCorrect"] = TRUE;
            $data["tituloMensaje"] = "Exito!";
            if($this->var["idReg"]>0 && $this->var["Action"]=="delete"){
                $data["Mensaje"] = "Registo eliminado de forma correcta.";
            }else{
                $data["Mensaje"] = "Registo guardado de forma correcta.";
            }
            if(key_exists($this->var["Dominio"], $directos)){
                $data["return"] = $this->var["path"]."".$this->var["Dominio"];
            }else{
                $data["return"] = $this->var["path"]."catalogo/".$this->var["Dominio"];
            }
            $data["tiempo"] = "3";
        }elseif(isset($cmdRegresar)){
            $data["isCorrect"] = TRUE;
            $data["tituloMensaje"] = "Regresando!";
            $data["Mensaje"] = "Regresando de forma correcta.";
            $data["return"] = $this->var["path"]."catalogo/".$this->var["Dominio"];
            $data["tiempo"] = "3";
        }else{
            $data["isCorrect"] = FALSE;
            $data["tituloMensaje"] = "Error!!!";
            if($this->var["idReg"]>0 && $this->var["Action"]=="delete"){
                $data["Mensaje"] = "El registo no pudo ser eliminado consulte al administrador.";
            }else{
                $data["Mensaje"] = "El registo no pudo ser guardado consulte al administrador.";
            }
            if(key_exists($this->var["Dominio"], $directos)){
                $data["return"] = $this->var["path"]."".$this->var["Dominio"];
            }else{
                $data["return"] = $this->var["path"]."catalogo/".$this->var["Dominio"];
            }
            $data["tiempo"] = "3";
        }
        if($_COOKIE["idRol"]==2 && $this->var["Dominio"]=="user"){
            $data["return"] = $this->var["path"]."editar-perfil";
        }

        $data["return"]=indexModel::bd($this->conf)->getMensaje($data);
        $templa  = "mensajeBackEnd.html";
        $this->view->show($templa, $data, $this->accion);

    }
}
?>
