<?php
class indexModel {

    public $db;
    private $host;
    private $bd;
    private $user;
    private $clave;
    public $pathSite;
    private $conf;
    private $estructura;
    private static $tituloAlternox;

    function __construct($conf) {
        //Traemos la unica instancia de PDO
        $PDOPath=dirname(__FILE__).'/../../'.$conf['folderModelos'] . 'SPDO.php';
        //echo $PDOPath;
        require_once $PDOPath;
        $host = $conf['host'];
        $bd = $conf['dbname'];
        $this->bd = $conf['dbname'];
        $user = $conf['username'];
        $clave = $conf['password'];
        $this->conf = $conf;
        $this->pathSite = $conf['pathSite'];
        $this->db = SPDO::singleton($host, $bd, $user, $clave);
    }

    public function setJsonV1($error,$array){
        /*
        $result = "error";
        if($status){
            $result = "success";
        }
        */
        $data = array(
            "error"=>$error,
            "version"=>"1",
            "response"=>$array
        );
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    public function desbloquearUsuario($id) {
        $sql = "UPDATE user SET status_id=1 WHERE id = " . $id;
        $reg = indexModel::bd($this->conf)->getSQL($sql);
        return 1;
    }

    public function bloquearUsuario($id) {
        $sql = "UPDATE user SET status_id=0 WHERE id = " . $id;
        $reg = indexModel::bd($this->conf)->getSQL($sql);
        return 1;
    }

    public function cambiarClave($datos) {
        foreach ($datos as $key => $value) {
            $$key = $value;
        }
        $camposRelacionados = null;
        // --> Buscar registro
        $sql = "SELECT * FROM user WHERE correo='{$TXTemail}'";
        //echo $sql;
        $reg = indexModel::bd($this->conf)->getSQL($sql);
        $id = $reg[0]->id;
        // --> Llenar campos
        $campos = array(
            "password" => $TXTpassword1
        );
        $cad = Catalogos::editarRegistro($this->conf, $this->bd, $this->pathSite, $this->db, "user", $campos, $camposRelacionados, $id);
        return $id;
    }

    public function crearUsuario($datos) {
        $cad = null;
        foreach ($datos as $key => $value) {
            if (substr($key, 0, 4) == "Xrel") {
                $camposRelacionados[substr($key, 4)] = $value;
            }
            if (substr($key, 0, 3) == "txt") {
                $campos[substr($key, 3)] = $value;
            }
        }
        $campos["titulo_id"] = 1;
        $campos["sexo_id"] = 1;
        $campos["ocupacion_id"] = 1;
        $campos["estado_id"] = 1;
        $campos["servicio_de_interes_id"] = 1;
        $campos["municipio_id"] = 1;
        $campos["pais_id"] = 1;
        $cad = Catalogos::guardarRegistro($this->conf, $this->bd, $this->pathSite, $this->db, "user", $campos, $camposRelacionados);
        return $cad;
    }

    public function getHascamposAll($table, $id = null) {
        return Catalogos::getRelacionTable($this->db, $this->bd, $table, $id);
    }

    public function getEstructuraTable($table) {
        return Catalogos::getStructureTable($this->bd,$this->db, $table);
    }

    public static function getNameDominio($var) {
        $dat = explode("/", $var["con"]);
        return $dat[1];
    }

    public static function bd($config) {
        return new indexModel($config);
    }

    public function getSQL($sql) {
        return Catalogos::getSql($this->bd,$this->db, $sql);
    }

    
    public function getDominioWhere($table, $where, $id = null, $limit = null) {
        return Catalogos::getDataWhere($this->db, $table, $this->bd, $where, $id, $limit);
    }

    public function getDominio($table, $id = null, $limit = null) {
        return Catalogos::getData($this->db, $table, $this->bd, $id, $limit);
    }

    public function getDominioID($table, $valores = null) {
        return Catalogos::getDataArray($this->db, $table, $this->bd, $valores);
    }

    public function htmlPOST($table, $valores = null) {
        $respo = "";
        if (isset($_COOKIE["idUser"]) && $_COOKIE["idUser"] > 0) {
            $respo = "responder";
        }
        $cad = "";
        $primerOrden = Catalogos::getDataArray($this->db, $table, $this->bd, $valores);
        foreach ($primerOrden as $key => $value) {
            //var_dump($value);
            $valores = array("id_padre" => $value["id"]);
            $hijos = $this->htmlPOST2($table, $valores);

            $cad .= '<li class="media media-comment">
                                        <div class="box-round box-mini pull-left">
                                            <div class="box-dummy"></div>
                                            <a class="box-inner" href="#">
                                                <img alt="" class="media-objects img-circle" src="includes/images/user/' . $value["user_id"] . '.jpg">
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <div class="media-inner">
                                                <h5 class="media-heading clearfix">
              ' . $value["relaciones"]["user_id"][$value["user_id"]] . ', ' . $value["fecha"] . '
              <a class="comment-reply pull-right cmdRes" dataTitle="' . $value["relaciones"]["user_id"][$value["user_id"]] . '"  dataid="' . $value["id"] . '" href="javascript: void(0)">

                ' . $respo . '
              </a>
            </h5>
                                                <p>
                                                    ' . $value["post"] . '
                                                </p>
                                            </div> ';
            $cad .= $hijos;
            $cad .= '</div></li>';
        }
        return $cad;
    }

    public function htmlPOST2($table, $valores = null) {
        $respo = "";
        if (isset($_COOKIE["idUser"]) && $_COOKIE["idUser"] > 0) {
            $respo = "responder";
        }
        $cad = "";
        $primerOrden = Catalogos::getDataArray($this->db, $table, $this->bd, $valores);
        foreach ($primerOrden as $key => $value) {
            $valores = array("id_padre" => $value["id"]);
            $hijos = $this->htmlPOST2($table, $valores);

            $cad .= '<div class="media media-comment">
                                        <div class="box-round box-mini pull-left">
                                            <div class="box-dummy"></div>
                                            <a class="box-inner" href="#">
                                                <img alt="" class="media-objects img-circle" src="includes/images/user/' . $value["user_id"] . '.jpg">
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <div class="media-inner">
                                                <h5 class="media-heading clearfix">
              ' . $value["relaciones"]["user_id"][$value["user_id"]] . ', ' . $value["fecha"] . '
              <a class="comment-reply pull-right cmdRes" dataTitle="' . $value["relaciones"]["user_id"][$value["user_id"]] . '" dataid="' . $value["id"] . '" href="javascript: void(0)">
                ' . $respo . '
              </a>
            </h5>
                                                <p>
                                                    ' . $value["post"] . '
                                                </p>
                                            </div> ';
            $cad .= $hijos;
            $cad .= '</div></div>';
        }
        return $cad;
    }

    public function getIDField($table, $campo, $valor) {
        return Catalogos::getDataForField($this->db, $table, $campo, $valor);
    }

    public function getcampos($table) {
        return Catalogos::getFields($this->bd,$this->db, $table);
    }

    public function getcamposAll($table) {
        return Catalogos::getFieldsAll($this->db, $table, $this->bd);
    }


    public function getcamposAjax($table,$origin) {
        return Catalogos::getFieldsAjax($this->bd,$this->db, $table,$origin);
    }

    public function getcamposAllAjax($table,$origin) {
        return Catalogos::getFieldsAllAjax($this->db, $table, $this->bd,$origin);
    }


    public function updateDominio($datos, $id = null) {
        //var_dump($datos);
        $camposRelacionados = null;
        foreach ($datos as $key => $value) {
            if (substr($key, 0, 4) == "Xrel") {
                $camposRelacionados[substr($key, 4)] = $value;
            }
            if (substr($key, 0, 3) == "txt") {
                $campos[substr($key, 3)] = $value;
            }
        }
        if ($id == 0 || $id == "") {
            //echo "INSERT";
            $cad = Catalogos::guardarRegistro($this->conf, $this->bd, $this->pathSite, $this->db, $datos["Dominio"], $campos, $camposRelacionados);
        } else {
            //echo "UPDATE";
            $cad = Catalogos::editarRegistro($this->conf, $this->bd, $this->pathSite, $this->db, $datos["Dominio"], $campos, $camposRelacionados, $id);
        }
        return $cad;
    }

    public function deleteDominio($table, $id) {
        return Catalogos::borrarRegistro($this->conf, $this->db, $table, $id);
    }

    public function getMensaje($data) {
        $color = "danger";
        $colorx = "red";
        if ($data["isCorrect"]) {
            $color = "success";
            $colorx = "green";
        }

        $campos = "";
        if(isset($data["txt"])){
          foreach ($data["txt"] as $key => $value) {
              if ($key != "con") {
                  $campos.='<input type="hidden" name="' . $key . '" value="' . $value . '">'.PHP_EOL ;
              }
          }
        }

        $res = '
        <br><br><br><br><br>
        <form action="' . $data["return"] . '" method="post" name="fmReturn" id="fmReturn">
          ' . $campos . '
        <div class="col-md-3"></div>
        <div class="col-md-6">
        <div class="content-box border-top border-' . $colorx . '">
                                <h3 class="content-box-header clearfix">
                                    ' . $data["tituloMensaje"] . '
                                    <small></small>

                                </h3>
                                <div class="content-box-wrapper">
                                    <p><div class="alert alert-' . $color . '">' . $data["Mensaje"] . '</div></p>
                                    <div class="divider"></div>
                                    <div class="loading-spinner">
                                        <i class="bg-' . $colorx . '"></i>
                                        <i class="bg-' . $colorx . '"></i>
                                        <i class="bg-' . $colorx . '"></i>
                                        <i class="bg-' . $colorx . '"></i>
                                        <i class="bg-' . $colorx . '"></i>
                                        <i class="bg-' . $colorx . '"></i>
                                    </div>
                                </div>
                            </div>




        <!--
        <div class="alert alert-' . $color . '">
            ' . $campos . '
            <strong>' . $data["tituloMensaje"] . ' </strong>
            ' . $data["Mensaje"] . '
            </div>
            -->
            </div>
            </form>
                <script>

                    function iraFormulario(){
                        document.getElementById("fmReturn").submit();
                    }
                    setTimeout(function(){ iraFormulario(); }, ' . $data["tiempo"] . '000);

                </script>';
        return $res;
    }

    public function validarAcceso($usuario, $pass, $id = null) {
        // --> Validar curso para el usuario


        if (!is_null($id)) {
            $ss = "UPDATE user SET status_id = 1 WHERE id = {$id}";
            $ultimas = $this->db->prepare($ss);
            $ultimas->execute();
        }

        if (is_null($id)) {
            $ss = "SELECT a.*, count(*)as nr, b.rol FROM user as a INNER JOIN rol as b ON a.rol_id=b.id WHERE a.email = '" . $usuario . "' AND a.password=MD5('" . $pass . "') AND status_id = 1 GROUP BY id";
        } else {
            $ss = "SELECT a.*, count(*)as nr, b.rol FROM user as a INNER JOIN rol as b ON a.rol_id=b.id WHERE a.id={$id} GROUP BY id";
        }
        //echo $ss."<hr>";
        //exit();
        $ultimas = $this->db->prepare($ss);
        $ultimas->execute();
        $res = $ultimas->fetch(PDO::FETCH_OBJ);
        //var_dump($res);
        // --> Entonces generar relacion de curso modulos y paginas
        if ($res->nr == 1) {
          /*
            $_COOKIE["idUser"] = $res->id;
            $_COOKIE["idRol"] = $res->rol_id;
            $_COOKIE["Rol"] = $res->rol;
            $_COOKIE["Nombre"] = $res->nombre;
           */
            setcookie('idUser', $res->id, time ()+(86400 * 30), '/',$_SERVER["SERVER_NAME"]);
            setcookie('empresaID', $res->empresa_id, time ()+(86400 * 30), '/',$_SERVER["SERVER_NAME"]);
            setcookie('idRol', $res->rol_id, time ()+(86400 * 30), '/',$_SERVER["SERVER_NAME"]);
            setcookie('Rol', $res->rol, time ()+(86400 * 30), '/',$_SERVER["SERVER_NAME"]);
            setcookie('Nombre', $res->name, time ()+(86400 * 30), '/',$_SERVER["SERVER_NAME"]);
            //var_dump($_COOKIE);
            //exit();

             $rr="{$res->id}|{$res->rol_id}|{$res->rol}|{$res->nombre}|{$res->empresa_id}";
        } else {
          /*
            session_destroy();
            unset($_COOKIE['idUser']);
            unset($_COOKIE['idRol']);
            unset($_COOKIE['Rol']);
            unset($_COOKIE['Nombre']);
            */
            setcookie('idUser', null, time()-100, '/',$_SERVER["SERVER_NAME"]);
            setcookie('empresaID', null, time()-100, '/',$_SERVER["SERVER_NAME"]);
            setcookie('idRol', null, time()-100, '/',$_SERVER["SERVER_NAME"]);
            setcookie('Rol', null, time()-100, '/',$_SERVER["SERVER_NAME"]);
            setcookie('Nombre', null, time()-100, '/',$_SERVER["SERVER_NAME"]);
            $rr="0|0|0|0|0";
        }
        return $rr;
    }

    public function getMenu($type = 1) {
        $cad = array(
            1 => array(
                "Generales" => array(
                    "icon" => "fa fa-gear",
                    array(
                        "ruta" => "catalogo/rol",
                        "name" => "Roles",
                        "icon" => "icon-grid"
                    ),
                ),
                "Servicios" => array(
                    "icon" => "icon-note",
                    array(
                        "ruta" => "prospectos",
                        "name" => "Alta de Prospectos",
                        "icon" => "icon-grid"
                    ),
                    array(
                ),

            ),
            2 => array(
                "Servicios" => array(
                    "icon" => "icon-note",
                    array(
                        "ruta" => "prospectos",
                        "name" => "Alta de Prospectos",
                        "icon" => "icon-grid"
                    ),
                ),
            )
        )
            );
        return $cad[$type];
    }

    public function sendMailGetResponse($correo, $name, $asunto, $mensaje, $opc = 0) {

    }

    public static function sendMail($correo, $name, $asunto, $mensaje, $opc = 0) {
      if ($opc == 1) {
          include_once('../../framework/phpMailer/class.phpmailer.php');
      } else {
          include_once('framework/phpMailer/class.phpmailer.php');
      }
      //include("framework/phpMailer/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
      //$fs = fsockopen("ssl://smtp.gmail.com", 465);
      //echo 1;
      $mail = new PHPMailer();
      //$mensaje="<img  alt=\"Notify MX\" src=\"http://admin.notify.com.mx/assets/image-resources/logo.png\"><br><br>".$mensaje;

      //$body = eregi_replace("[\]", '', $mensaje);

      $body = $mensaje;
      if ($opc == 1) {
          $mail->SetLanguage("en", '../../framework/phpMailer/language/');
      } else {
          $mail->SetLanguage("en", 'framework/phpMailer/language/');
      }
      $mail->IsSMTP();
      $mail->SMTPAuth = true;                  // enable SMTP authentication
      //$mail->SMTPSecure = "ssl";                  // sets the prefix to the servier
      $mail->Host = "mail.notify.com.mx";//"hv3svg038.neubox.net"; //"ssl://smtp.gmail.com";      // sets GMAIL as the SMTP server
      $mail->Port       = 587;                   // set the SMTP port for the GMAIL server
      $mail->Username = "info@notify.com.mx";  // GMAIL username
      $mail->Password = "1G_}Vv3mw^rJ";            // GMAIL password
      //$mail->AddReplyTo("contacto@deporteorganizado.com","First Last");
      $mail->From = "info@notify.com.mx";
      $mail->FromName = "Residuos";
        $mail->Subject = $asunto;
        //$mail->Body       = "Hi,<br>This is the HTML BODY<br>";                      //HTML Body
        //$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        $mail->WordWrap = 50; // set word wrap
        $mail->MsgHTML($mensaje);
        $mail->AddAddress($correo, $name);
        /*
          if($opc==1){
          $mail->AddAttachment("../../cms/includes/images/cat_general/1.png");             // attachment
          }else{
          $mail->AddAttachment("cms/includes/images/cat_general/1.png");             // attachment
          }
         */
        $mail->IsHTML(true); // send as HTML

        if (!$mail->Send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;
            return "2";
        } else {
            return "1";
        }
    }

    protected function getIDPublico($plaintext) {
        $key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a7");
        $key_size = strlen($key);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CBC, $iv);
        $ciphertext = $iv . $ciphertext;
        $ciphertext_base64 = base64_encode(urlencode($ciphertext));
        return $ciphertext_base64;
    }

    public function generarURL($id, $ruta) {
        $md5 = md5($id);
        $md5 = base64_encode($md5);
        $cad = $ruta . "valida-perfil/" . $md5;
        return $cad;
    }

    public function getEmpresa() {
        $sql = "SELECT a.* FROM empresa AS a INNER JOIN user_has_empresa AS b ON a.id=b.empresa_id WHERE b.user_rel_id = " . $_SESSION["idUser"];
        $reg = indexModel::bd($this->conf)->getSQL($sql)[0];
        return $reg;
    }

    public function generaPass(){
        $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!#$%&=?*^~";
        $longitudCadena=strlen($cadena);
        $cadena2 = "-+|!#$%&=?*^~";
        $longitudCadena2=strlen($cadena);
        $pass = "";
        $longitudPass=6;
        for($i=1 ; $i<=$longitudPass ; $i++){
            $pos=rand(0,$longitudCadena-1);
            $pass .= substr($cadena,$pos,1);
        }
        $longitudPass2=4;
        for($i=1 ; $i<=$longitudPass2 ; $i++){
            $pos=rand(0,$longitudCadena2-1);
            $pass .= substr($cadena2,$pos,1);
        }
        return $pass;
    }

    public function generaPassAPP(){
        $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $longitudCadena=strlen($cadena);
        $pass = "";
        $longitudPass=6;
        for($i=1 ; $i<=$longitudPass ; $i++){
            $pos=rand(0,$longitudCadena-1);
            $pass .= substr($cadena,$pos,1);
        }

        return $pass;
    }
    
    public function controlAcceso2($conf,$dat) {
      //var_dump($conf);
      //var_dump($dat);




      $sqlValidate1="SELECT * FROM campana WHERE id = {$idReg2}";
      $campana = indexModel::bd($conf)->getSQL($sqlValidate1)[0];



    }

    public function controlAcceso($tabla) {
        /*1.-Saber si es un usuario que esta logeado
        2.-En caso de no estarlo salir
        3.-Si esta logeado revisar que rol es
        4.-validar si el rol tiene acceso al modulo qu eintenta entrar
        5.-Si no lo tiene mandar al home  
        6.-Si lo tiene dejar pasar*/
        //$usuario = $_COOKIE["variable"];

         foreach ($_COOKIE as $key => $value) {
                $$key=$value;
         }

         if($idUser !=0){
            //1.-Buscar en la tabla de modulos la tabla para extraer id del modulo
            //2.-Extraer el id del rol
            //3.-Si en la tabla de persmiso existe el rol con el modulo y tiene permiso entonces dejamos pasar
     
                if($tabla != ""){
                     $query = "SELECT * FROM modulo WHERE tabla ='$tabla'";
                     $modul = indexModel::bd($this->conf)->getSQL($query)[0];
                  
                     $id_modulo = $modul->id;
                     $idRol;
                 
                     $query = "SELECT * FROM permisos WHERE rol_id = '$idRol' AND modulo_id = '$id_modulo'";
                     $tables = indexModel::bd($this->conf)->getSQL($query)[0];
            


                 if($tables->permiso_crear_id != 1 && $tables->permiso_leer_id  != 1
                    && $tables->permiso_actualizar_id != 1 && $tables->permiso_borrar_id != 1){
                    
                  echo "No acceso";
                        $rutt = "home";
                        echo '<meta http-equiv="refresh" content="0;url='.$this->conf["pathCMSSite"].$rutt.'">';
         
            
                 }
                    
           
                }
             
         }else{
             $rutt = "";
              echo '<meta http-equiv="refresh" content="0;url='.$this->conf["pathCMSSite"].$rutt.'">';
         }

      
    }

    public function getCicloActual() {
        $empresa = $this->getEmpresa();
        $sql = "SELECT * FROM ciclo WHERE status_ciclo_id = 1 AND empresa_id = {$empresa->id} ORDER BY fecha_final DESC LIMIT 1 ";
        $reg = indexModel::bd($this->conf)->getSQL($sql)[0];
        return $reg;
    }

    public function generaUserAPP($name) {
        $na = rand(1, 99);
        $na = str_pad($na, 2, "0", STR_PAD_LEFT);
        $dd = explode(" ", $name);
        $name = strtolower($dd[0])."_".  substr(strtolower($dd[1]), 0,1). substr(strtolower($dd[2]), 0,1).$na;
        return $name;
    }

    public function url_exists_I($url) {
        //echo $url."<br>";
        $ch = @curl_init($url);
        @curl_setopt($ch, CURLOPT_HEADER, TRUE);
        @curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        @curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
        @curl_setopt($ch, CURLOPT_USERPWD, "desarrollo:1q2w3e4r");
        @curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $status = array();
        $d = @curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        preg_match('/HTTP\/.* ([0-9]+) .*/', $d , $status);
        //echo $status[1]."<br>";
        return ($status[1] == 200);
    }

    public function getImgProfile($path){
        $cad=$path."includes/img/user.png";
        if(@isset($_COOKIE["idUser"])){
            $isJPG = $path."/includes/images/usuarios/".$_COOKIE["idUser"].".jpg";
            $isPNG = $path."/includes/images/usuarios/".$_COOKIE["idUser"].".png";
            $isJPEG = $path."/includes/images/usuarios/".$_COOKIE["idUser"].".jpeg";
            if($this->url_exists_I($isJPG)){
                $cad=$isJPG;
            }elseif($this->url_exists_I($isPNG)){
                $cad=$isPNG;
            }elseif($this->url_exists_I($isJPEG)){
                $cad=$isJPEG;
            }
        }
        return $cad;
    }

    public function SecurityParams($_Cadena) {
        $_Cadena = htmlspecialchars(trim(addslashes(stripslashes(strip_tags($_Cadena)))));
        $_Cadena = str_replace(chr(160),'',$_Cadena);
        return $_Cadena;
        //return mysql_real_escape_string($_Cadena);
    }

public function getFormatoFecha($fec) {
        $diaSemana = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $axo = substr($fec, 0, 4);
        $mes = (int) substr($fec, 5, 2);
        $dia = substr($fec, 8, 2);
        $numeroDia = date("w", mktime(0, 0, 0, $mes, $dia, $axo));
        $fec1 = $diaSemana[$numeroDia] . ", " . $dia . " de " . $meses[$mes] . " del " . $axo;
        return $fec1;
    }

    public function sendNotification($tokens, $message) {
        $url = "https://fcm.googleapis.com/fcm/send";
        $fields = array(
            'registration_ids' => $tokens,
            'data' => $message
        );
        $headers = array(
            'Authorization:key = AAAAy7vdPjo:APA91bGNw2ryLBYc47ts8VpBoGAQo9Rwt3pHOJT9n8_2XHiBvcYcadfPYz93F0AjpH_24chEMIUB7eQQOVPs-y-vCI1sapn6EbJvbU_viiED_EjJZQlGHkndM1-eu3L2UpyPyoGl7Zy8bO9fNm4R4KBKztIjE5BKBg',
            'Content-Type:application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl Failed: ' . curl_error($ch));
        }
        curl_close($ch);

        $datoss = array(
            "Dominio" => "push",
            "txtrequest" => json_encode($fields),
            "txtresponse" => $result,
        );
        $res = indexModel::bd($this->conf)->updateDominio($datoss);

        return $result;
    }


}
?>
