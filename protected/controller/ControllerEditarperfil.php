<?php
class ControllerEditarperfil extends Controller {
    function __construct($view, $conf, $var, $acc) {
        parent::__construct($view, $conf, $var, $acc);
    }
    public function main() {

   
    	foreach ($this->var as $key => $value) {
            $this->data[$key] = $value;
            $$key = $value;
        } 


        //Extraer id del usuario activo
        $usuario = $_COOKIE["idUser"];


        if (isset($cmdGuardar3) && $cmdGuardar3==1)
        {
            $dat = array(
                'Dominio' => "usuarios",
                'txtname' => $txtnombre,
                'txtemail' => $txtemail,
                'txtuser' => $txtdireccion,
                'txtrol_id' => $txtcurp,
                'txtstatus_id' => $txtcurp
            );

            $idReg = indexModel::bd($this->conf)->updateDominio($dat, $usuario);
           
        }


        if(isset($cmdGuardar4) && $cmdGuardar4==1)
        {
            
            //Llamado de contraseña
            
            $passwordo = $_POST['passwordo']; 
            $newpswd = $_POST['newpswd'];
            $repeatpswd = $_POST['repeatpswd'];

            //Encriptar contraseña.
            $passwordo = md5('passwordo');
            $newpswd = md5('newpswd');
            $repeatpswd =md5('repeatpswd');

            $pas = "SELECT password FROM usuarios WHEN id = '".$_COOKIE[$usuario]."'";

            if ($pas == $passwordo)
            {
                echo "La contraseña es correcta";

                

            }
            else
            {
                echo "Tu contraseña es incorrecta";
            }

        }
        

        /*Obtener contraseña*/
        $pass = "SELECT count(*) as nr FROM usuarios WHERE id = 1 AND password = md5(123)";
        $contra = indexModel::bd($this->conf)->getSQL($pass);
        $this->data["contrasena"]= $contra;


        

   
        $tab = indexModel::bd($this->conf)->getDominio("user",$usuario)[0];
        //var_dump($tab);
        $this->data["usuarioss"] = $tab;


        $this->view->show("editarperfil.html", $this->data, $this->accion);
    }
}
?>
