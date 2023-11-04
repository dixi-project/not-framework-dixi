<?php
use Aws\S3\S3Client;
include_once "vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;

class indexModel
{

    public $db;
    private $host;
    private $bd;
    private $user;
    private $clave;
    public $pathSite;
    private $conf;
    private $estructura;
    private static $tituloAlternox;

    public function __construct($conf)
    {
        //Traemos la unica instancia de PDO
        $PDOPath = dirname(__FILE__) . '/../../' . $conf['folderModelos'] . 'SPDO.php';
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

    public function lastId()
    {
        return $this->db->lastInsertId();
    }
    public function setJsonV1($error, $array)
    {
        /*
        $result = "error";
        if($status){
        $result = "success";
        }
         */
        $data = array(
            "error" => $error,
            "version" => "1",
            "response" => $array,
        );
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    public function desbloquearUsuario($id)
    {
        $sql = "UPDATE user SET status_id=1 WHERE id = " . $id;
        $reg = indexModel::bd($this->conf)->getSQL($sql);
        return 1;
    }

    public function bloquearUsuario($id)
    {
        $sql = "UPDATE user SET status_id=0 WHERE id = " . $id;
        $reg = indexModel::bd($this->conf)->getSQL($sql);
        return 1;
    }

    public function cambiarClave($datos)
    {
        foreach ($datos as $key => $value) {
            $$key = $value;
        }
        $camposRelacionados = null;
        // --> Buscar registro
        $sql = "SELECT * FROM user WHERE correo='{$TXTemail}'";
        //echo $sql;
        $reg = indexModel::bd($this->bd)->getSQL($sql);
        $id = $reg[0]->id;
        // --> Llenar campos
        $campos = array(
            "password" => $TXTpassword1,
        );
        $cad = Catalogos::editarRegistro($this->conf, $this->bd, $this->pathSite, $this->db, "user", $campos, $camposRelacionados, $id);
        return $id;
    }

    public function crearUsuario($datos)
    {
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

    public function getHascamposAll($table, $id = null)
    {
        return Catalogos::getRelacionTable($this->db, $this->bd, $table, $id);
    }

    public function getEstructuraTable($table)
    {
        return Catalogos::getStructureTable($this->bd, $this->db, $table);
    }

    public static function getNameDominio($var)
    {
        $dat = explode("/", $var["con"]);
        return $dat[1];
    }

    public static function bd($config)
    {
        return new indexModel($config);
    }

    public function getSQL($sql)
    {
        return Catalogos::getSql($this->bd, $this->db, $sql);
    }

    public function getDominioWhere($table, $where, $id = null, $limit = null)
    {
        return Catalogos::getDataWhere($this->db, $table, $this->bd, $where, $id, $limit);
    }

    public function getDominio($table, $id = null, $limit = null)
    {
        return Catalogos::getData($this->db, $table, $this->bd, $id, $limit);
    }

    public function getDominioID($table, $valores = null)
    {
        return Catalogos::getDataArray($this->db, $table, $this->bd, $valores);
    }

    public function htmlPOST($table, $valores = null)
    {
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

    public function htmlPOST2($table, $valores = null)
    {
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

    public function getIDField($table, $campo, $valor)
    {
        return Catalogos::getDataForField($this->db, $table, $campo, $valor);
    }

    public function getcampos($table)
    {
        return Catalogos::getFields($this->bd, $this->db, $table);
    }

    public function getcamposAll($table)
    {
        return Catalogos::getFieldsAll($this->db, $table, $this->bd);
    }

    public function getcamposAjax($table, $origin)
    {
        return Catalogos::getFieldsAjax($this->bd, $this->db, $table, $origin);
    }

    public function getcamposAllAjax($table, $origin)
    {
        return Catalogos::getFieldsAllAjax($this->db, $table, $this->bd, $origin);
    }

    public function updateDominio($datos, $id = null)
    {
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

    public function deleteDominio($table, $id)
    {
        return Catalogos::borrarRegistro($this->conf, $this->db, $table, $id);
    }

    public function getMensaje($data)
    {
        $color = "text-error";

        if ($data["isCorrect"]) {
            $color = "text-success";
        }

        $campos = "";
        if (isset($data["txt"])) {
            foreach ($data["txt"] as $key => $value) {
                if ($key != "con") {
                    $campos .= '<input type="hidden" name="' . $key . '" value="' . $value . '">' . PHP_EOL;
                }
            }
        }

        $res = '
        <form action="' . $data["return"] . '" method="post" name="fmReturn" id="fmReturn">
        ' . $campos . '
        <div class="max-w-md p-6 ">
          <div class="w-full ">
            <img
              class="mx-auto"
              src="includes/images/aloja_preloader_fondo_blanco_150x102px.gif"
              alt="image"
            />
          </div>
          <p class="pt-4 text-7xl font-bold text-primary dark:text-accent">

          </p>
          <p
            class="pt-4 text-xl font-semibold text-center ' . $color . ' dark:' . $color . '"
          >
          ' . $data["tituloMensaje"] . '
          </p>
          <p class="pt-2 text-slate-500 dark:text-navy-200 text-center">
          ' . $data["Mensaje"] . '
          </p>


        </div>
        </form>
        <script>

            function iraFormulario(){
                document.getElementById("fmReturn").submit();
            }
            setTimeout(function(){ iraFormulario(); }, ' . $data["tiempo"] . '000);

        </script>



           ';
        return $res;
    }

    public function getMensaje1($data)
    {
        $color = "text-error";

        if ($data["isCorrect"]) {
            $color = "text-success";
        }

        $campos = "";
        if (isset($data["txt"])) {
            foreach ($data["txt"] as $key => $value) {
                if ($key != "con") {
                    $campos .= '<input type="hidden" name="' . $key . '" value="' . $value . '">' . PHP_EOL;
                }
            }
        }

        $res = '
        <form action="' . $data["return"] . '" method="post" name="fmReturn" id="fmReturn">
        ' . $campos . '
        <div class="max-w-md p-6 ">
          <div class="w-full ">
          <p
            class="pt-4 text-xl font-semibold text-center ' . $color . ' dark:' . $color . '"
          >
          ' . $data["tituloMensaje"] . '
          </p>

            <img
              class="mx-auto"
              src="https://admin.aloja.com/includes/images/aloja_preloader_fondo_blanco_150x102px.gif"
              alt="image"
            />
            <p class=" text-xl font-semibold text-center ' . $color . ' dark:' . $color . '">
          ' . $data["Mensaje"] . '
          </p>
          </div>





        </div>
        </form>
        <script>

            function iraFormulario(){
                document.getElementById("fmReturn").submit();
            }
            setTimeout(function(){ iraFormulario(); }, ' . $data["tiempo"] . '000);

        </script>



           ';
        return $res;
    }

    public function mandarMensajeAdmin($pathSite, $idd)
    {
        $ss = "SELECT * FROM experience WHERE id = " . $idd;
        $ultimas = $this->db->prepare($ss);
        $ultimas->execute();
        $res = $ultimas->fetch(PDO::FETCH_OBJ);

        $dd = md5($idd) . "|" . $idd;
        $dd = urlencode($dd);

        $mensaje = '
        <img src="' . $pathSite . 'includes/images/logo_peque.png"><br>
        Tiene una experiencia por valdiar
        <a href="' . $pathSite . 'validateexperience/' . $dd . '">
               <br>
            ' . $res->experience;

        $ss = "SELECT * FROM user WHERE rol_id = 1";
        $ultimas = $this->db->prepare($ss);
        $ultimas->execute();
        $resp = $ultimas->fetchAll(PDO::FETCH_OBJ);
        foreach ($resp as $row) {
            indexModel::sendMail($row->email, "Admin", "Experiencia nueva", $mensaje, "Revisar experiencia nueva");
        }
    }

    public function mandarMensajeProveedorPublic($pathSite, $idd)
    {
        $ss = "SELECT * FROM experience WHERE id = " . $idd;
        $ultimas = $this->db->prepare($ss);
        $ultimas->execute();
        $res = $ultimas->fetch(PDO::FETCH_OBJ);

        $mensaje = '
        <img src="https://admin.aloja.com/includes/images/logo_peque.png"><br>
        Felicidades experiencia publicada
        <br>
        El equipo de Aloja a liberado su experiencia al público
               <br>
            ' . $res->experience;
        $ss = "SELECT * FROM user WHERE id = " . $res->user_id;
        $ultimas = $this->db->prepare($ss);
        $ultimas->execute();
        $resp = $ultimas->fetchAll(PDO::FETCH_OBJ);
        foreach ($resp as $row) {
            indexModel::sendMail($row->email, "Proveedor", "Experiencia publicada", $mensaje, "Experiencia publicada");
        }
    }

    public function mandarMensajeProveedor($pathSite, $idd)
    {
        $ss = "SELECT * FROM experience WHERE id = " . $idd;
        $ultimas = $this->db->prepare($ss);
        $ultimas->execute();
        $res = $ultimas->fetch(PDO::FETCH_OBJ);

        $dd = md5($idd) . "|" . $idd;
        $dd = urlencode($dd);

        $mensaje = '
        <img src="' . $pathSite . 'includes/images/logo_peque.png"><br>
        Felicidades a creado una nueva experiencia
        <br>
        El equipo de Aloja comenzara a validar su información, pronto tendra noticias sobre la verificación y salida a público
               <br>
            ' . $res->experience;

        $ss = "SELECT * FROM user WHERE id = " . $res->user_id;
        $ultimas = $this->db->prepare($ss);
        $ultimas->execute();
        $resp = $ultimas->fetchAll(PDO::FETCH_OBJ);
        foreach ($resp as $row) {
            indexModel::sendMail($row->email, "Admin", "Experiencia nueva", $mensaje, "Revisar experiencia nueva");
        }

        //
    }

    public function mandarMensajeProveedor2($pathSite, $idd)
    {
        $ss = "SELECT * FROM experience WHERE id = " . $idd;
        $ultimas = $this->db->prepare($ss);
        $ultimas->execute();
        $res = $ultimas->fetch(PDO::FETCH_OBJ);

        $dd = md5($idd) . "|" . $idd;
        $dd = urlencode($dd);

        $mensaje = '
        <img src="https://admin.aloja.com/includes/images/logo_peque.png"><br>
        Se actualizo su experiencia
        <br>
        El equipo de Aloja comenzara a validar su información, pronto tendra noticias sobre la verificación.
               <br>
            ' . $res->experience;

        $ss = "SELECT * FROM user WHERE id = " . $res->user_id;
        $ultimas = $this->db->prepare($ss);
        $ultimas->execute();
        $resp = $ultimas->fetchAll(PDO::FETCH_OBJ);
        foreach ($resp as $row) {
            indexModel::sendMail($row->email, "Admin", "Experiencia nueva", $mensaje, "Revisar experiencia nueva");
        }

        //
    }

    public function validarAcceso($usuario, $pass, $id = null)
    {
        // --> Validar curso para el usuario

        if (!is_null($id)) {
            $ss = "UPDATE user SET status_id = 1 WHERE id = {$id}";
            $ultimas = $this->db->prepare($ss);
            $ultimas->execute();
        }

        if (is_null($id)) {
            $ss = "SELECT a.*, count(*)as nr, b.rol FROM user as a INNER JOIN rol as b ON a.rol_id=b.id WHERE a.email = '" . $usuario . "' AND a.password=MD5('" . $pass . "') AND user_status_id = 1 GROUP BY id";
        } else {
            $ss = "SELECT a.*, count(*)as nr, b.rol FROM user as a INNER JOIN rol as b ON a.rol_id=b.id WHERE a.id={$id} GROUP BY id";
        }
        //echo $ss . "<hr>";
        //exit();
        $ultimas = $this->db->prepare($ss);
        $ultimas->execute();
        $res = $ultimas->fetch(PDO::FETCH_OBJ);
        //var_dump($res);
        //exit();
        // --> Entonces generar relacion de curso modulos y paginas
        if ($res->nr == 1) {
            /*
            $_COOKIE["idUser"] = $res->id;
            $_COOKIE["idRol"] = $res->rol_id;
            $_COOKIE["Rol"] = $res->rol;
            $_COOKIE["Nombre"] = $res->nombre;
             */
            setcookie('idUser', $res->id, time() + (86400 * 30), '/', $_SERVER["SERVER_NAME"]);
            setcookie('empresaID', $res->provider_id, time() + (86400 * 30), '/', $_SERVER["SERVER_NAME"]);
            setcookie('idRol', $res->rol_id, time() + (86400 * 30), '/', $_SERVER["SERVER_NAME"]);
            setcookie('Rol', $res->rol, time() + (86400 * 30), '/', $_SERVER["SERVER_NAME"]);
            setcookie('Nombre', $res->name, time() + (86400 * 30), '/', $_SERVER["SERVER_NAME"]);
            //var_dump($_COOKIE);
            //exit();

            $rr = "{$res->id}|{$res->rol_id}|{$res->rol}|{$res->nombre}|{$res->empresa_id}";
        } else {
            /*
            session_destroy();
            unset($_COOKIE['idUser']);
            unset($_COOKIE['idRol']);
            unset($_COOKIE['Rol']);
            unset($_COOKIE['Nombre']);
             */
            setcookie('idUser', null, time() - 100, '/', $_SERVER["SERVER_NAME"]);
            setcookie('empresaID', null, time() - 100, '/', $_SERVER["SERVER_NAME"]);
            setcookie('idRol', null, time() - 100, '/', $_SERVER["SERVER_NAME"]);
            setcookie('Rol', null, time() - 100, '/', $_SERVER["SERVER_NAME"]);
            setcookie('Nombre', null, time() - 100, '/', $_SERVER["SERVER_NAME"]);
            $rr = "0|0|0|0|0";
        }
        return $rr;
    }

    public function getMenu($type = 1)
    {
        $cad = array(
            1 => array(
                "Generales" => array(
                    "icon" => "fa fa-gear",
                    array(
                        "ruta" => "catalogo/rol",
                        "name" => "Roles",
                        "icon" => "icon-grid",
                    ),
                ),
                "Servicios" => array(
                    "icon" => "icon-note",
                    array(
                        "ruta" => "prospectos",
                        "name" => "Alta de Prospectos",
                        "icon" => "icon-grid",
                    ),
                    array(),

                ),
                2 => array(
                    "Servicios" => array(
                        "icon" => "icon-note",
                        array(
                            "ruta" => "prospectos",
                            "name" => "Alta de Prospectos",
                            "icon" => "icon-grid",
                        ),
                    ),
                ),
            ),
        );
        return $cad[$type];
    }

    public function sendMailGetResponse($correo, $name, $asunto, $mensaje, $opc = 0)
    {
    }

    public function confirmarEmail($llave)
    {
        $sql = "SELECT id, COUNT(*) AS nr, user_status_id FROM user WHERE urlvalidate like '{$llave}' ";
        $ultimas = $this->db->prepare($sql);
        $ultimas->execute();
        $res = $ultimas->fetch(PDO::FETCH_OBJ);
        // --> Si existe entonces activar y validate
        if ($res->nr) {
            $sql = "UPDATE user SET user_status_id = 1, validated = 1 WHERE id = " . $res->id;
            $ultima = $this->db->prepare($sql);
            $ultima->execute();
        }
        $cad = $res->nr . "|" . $res->user_status_id;
        return $cad;
    }

    public function cambiarClaveAhora($mmd5, $clave, $email)
    {
        $sql = "UPDATE user SET password = MD5('{$clave}') WHERE urlvalidate = '{$mmd5}' ";
        $ultimas = $this->db->prepare($sql);
        $ultimas->execute();
        $ultimas->fetch(PDO::FETCH_OBJ);

        $mensaje = '
                <div class="es-wrapper-color" style="background-color:#FFFFFF"><!--[if gte mso 9]>
                    <v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="t">
                    <v:fill type="tile" color="#ffffff"></v:fill>
                    </v:background>
                    <![endif]-->
                    <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top;background-color:#FFFFFF">
                    <tr>
                    <td valign="top" style="padding:0;Margin:0">
                    <table cellpadding="0" cellspacing="0" class="es-header" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top">
                    <tr>
                    <td align="center" style="padding:0;Margin:0">
                    <table bgcolor="#e4007c" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:510px">
                    <tr>
                    <td align="left" style="padding:0;Margin:0;padding-left:20px;padding-right:20px">
                    <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="center" valign="top" style="padding:0;Margin:0;width:470px">
                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="center" height="40" style="padding:0;Margin:0"></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table>
                    <table class="es-content" cellspacing="0" cellpadding="0" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                    <tr>
                    <td align="center" style="padding:0;Margin:0">
                    <table class="es-content-body" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;width:510px" cellspacing="0" cellpadding="0" align="center" bgcolor="#e4007c">
                    <tr>
                    <td align="left" style="padding:0;Margin:0">
                    <table width="100%" cellspacing="0" cellpadding="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td class="es-m-p0r" valign="top" align="center" style="padding:0;Margin:0;width:510px">
                    <table width="100%" cellspacing="0" cellpadding="0" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="center" style="padding:0;Margin:0;position:relative"><img class="adapt-img" src="https://admin.aloja.com/includes/images/sobre.png" alt title width="510" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table>
                    <table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                    <tr>
                    <td align="center" style="padding:0;Margin:0">
                    <table bgcolor="#ffffff" class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#e4007c;border-radius:0 0 50px 50px;width:510px">
                    <tr>
                    <td align="left" style="padding:0;Margin:0;padding-left:20px;padding-right:20px">
                    <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="center" valign="top" style="padding:0;Margin:0;width:470px">
                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">


                    <tr>
                    <td align="center" style="padding:0;Margin:0"><!--[if mso]><a href="https://aloja.com/" target="_blank" hidden>
                    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" esdevVmlButton href="https://aloja.com/"
                    style="height:49px; v-text-anchor:middle; width:231px" arcsize="50%" stroke="f" fillcolor="#EB721F">
                    <w:anchorlock></w:anchorlock>

                    </v:roundrect></a>
                    <![endif]--><!--[if !mso]><!-- --><span class="msohide es-button-border" style="border-style:solid;border-color:#2CB543;background:#EB721F;border-width:0px;display:inline-block;border-radius:30px;width:auto;mso-hide:all">
                    Clave actualizada</span><!--<![endif]--></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    <tr>
                    <td align="left" style="Margin:0;padding-top:20px;padding-left:20px;padding-right:20px;padding-bottom:40px">
                    <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="left" style="padding:0;Margin:0;width:470px">
                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="center" style="padding:0;Margin:0"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:21px;color:#FFFFFF;font-size:14px">Gracias,<br>ALOJA Team!&nbsp;</p></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table>
                    <table cellpadding="0" cellspacing="0" class="es-header" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top">
                    <tr>
                    <td align="center" style="padding:0;Margin:0">
                    <table bgcolor="#e4007c" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:510px">
                    <tr>
                    <td align="left" style="padding:0;Margin:0;padding-left:20px;padding-right:20px">
                    <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="center" valign="top" style="padding:0;Margin:0;width:470px">
                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="center" height="40" style="padding:0;Margin:0"></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table>
                    <table cellpadding="0" cellspacing="0" class="es-footer" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top">
                    <tr>
                    <td align="center" style="padding:0;Margin:0">
                    <table bgcolor="#ffffff" class="es-footer-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#333333;border-radius:50px;width:510px">
                    <tr>
                    <td align="left" style="Margin:0;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px"><!--[if mso]><table style="width:470px" cellpadding="0"
                    cellspacing="0"><tr><td style="width:225px" valign="top"><![endif]-->
                    <table cellpadding="0" cellspacing="0" class="es-left" align="left" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:left">
                    <tr>
                    <td class="es-m-p20b" align="left" style="padding:0;Margin:0;width:225px">
                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="left" class="es-m-txt-c" style="padding:0;Margin:0;font-size:0px"><a target="_blank" href="https://aloja.com/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img src="https://admin.aloja.com/includes/images/logo_aloja_largo_blanco.png" alt="Logo" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic" height="30" title="Logo"></a></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table><!--[if mso]></td><td style="width:20px"></td><td style="width:225px" valign="top"><![endif]-->
                    <table cellpadding="0" cellspacing="0" class="es-right" align="right" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:right">
                    <tr>
                    <td align="left" style="padding:0;Margin:0;width:225px">
                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="right" class="es-m-txt-c" style="padding:0;Margin:0;padding-top:5px;font-size:0">
                    <table cellpadding="0" cellspacing="0" class="es-table-not-adapt es-social" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://aloja.com/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img src="https://migxhe.stripocdn.email/content/assets/img/social-icons/circle-white/facebook-circle-white.png" alt="Fb" title="Facebook" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td>
                    <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://aloja.com/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img src="https://migxhe.stripocdn.email/content/assets/img/social-icons/circle-white/twitter-circle-white.png" alt="Tw" title="Twitter" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td>
                    <td align="center" valign="top" style="padding:0;Margin:0;padding-right:10px"><a target="_blank" href="https://aloja.com/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img src="https://migxhe.stripocdn.email/content/assets/img/social-icons/circle-white/instagram-circle-white.png" alt="Ig" title="Instagram" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td>
                    <td align="center" valign="top" style="padding:0;Margin:0"><a target="_blank" href="https://aloja.com/" style="-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;text-decoration:underline;color:#FFFFFF;font-size:14px"><img src="https://migxhe.stripocdn.email/content/assets/img/social-icons/circle-white/youtube-circle-white.png" alt="Yt" title="Youtube" height="24" style="display:block;border:0;outline:none;text-decoration:none;-ms-interpolation-mode:bicubic"></a></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table><!--[if mso]></td></tr></table><![endif]--></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table>
                    <table cellpadding="0" cellspacing="0" class="es-content" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%">
                    <tr>
                    <td class="es-info-area" align="center" style="padding:0;Margin:0">
                    <table class="es-content-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:transparent;border-radius:50px;width:510px">
                    <tr>
                    <td align="left" style="padding:0;Margin:0;padding-top:20px;padding-left:20px;padding-right:20px">
                    <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="center" valign="top" style="padding:0;Margin:0;width:470px">
                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="center" class="es-infoblock" style="padding:0;Margin:0;line-height:14px;font-size:12px;color:#CCCCCC"><p style="Margin:0;-webkit-text-size-adjust:none;-ms-text-size-adjust:none;mso-line-height-rule:exactly;font-family:Poppins, sans-serif;line-height:14px;color:#CCCCCC;font-size:12px">
                    <a href="' . $this->conf["pathSite"] . 'unsuscribe/' . $urlpathuser . '">Unsubscribe</a>
                    </p></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table>
                    <table cellpadding="0" cellspacing="0" class="es-header" align="center" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;table-layout:fixed !important;width:100%;background-color:transparent;background-repeat:repeat;background-position:center top">
                    <tr>
                    <td align="center" style="padding:0;Margin:0">
                    <table bgcolor="#e4007c" class="es-header-body" align="center" cellpadding="0" cellspacing="0" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;background-color:#FFFFFF;width:510px">
                    <tr>
                    <td align="left" style="padding:0;Margin:0;padding-left:20px;padding-right:20px">
                    <table cellpadding="0" cellspacing="0" width="100%" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="center" valign="top" style="padding:0;Margin:0;width:470px">
                    <table cellpadding="0" cellspacing="0" width="100%" role="presentation" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px">
                    <tr>
                    <td align="center" height="40" style="padding:0;Margin:0"></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table></td>
                    </tr>
                    </table>
                    </td>
                    </tr>
                    </table>
                    </div>
            ';

        indexModel::sendMail($email, "Clave actualizada", "Clave actualizada", $mensaje, "Su clave se actualizo correctamente");

        return 1;
    }

    public function crearProveedor($name, $email, $pass)
    {
        $response = 0;
        $idReg = 0;
        // --> Existe usuario
        $sql = "SELECT COUNT(*) AS nr FROM user WHERE email like '%{$email}%' ";
        $ultimas = $this->db->prepare($sql);
        $ultimas->execute();
        $res = $ultimas->fetch(PDO::FETCH_OBJ);
        // --> Si no existe crea cuenta nueva
        if ($res->nr == 0) {
            $sql = "INSERT INTO user (id,user,name,email,rol_id,user_status_id,password) VALUES (0,'{$name}','{$name}', '{$email}',2,2,MD5('" . $pass . "'))";
            $ultimas = $this->db->prepare($sql);
            $ultimas->execute();
            $idReg = $this->db->lastInsertId();
            $response = 1;
            $urlpathuser = $this->generarURL($idReg);
            $sql3 = "UPDATE user SET urlvalidate='$urlpathuser' WHERE id = " . $idReg;
            $ultimas = $this->db->prepare($sql3);
            $ultimas->execute();
            // --> Actualizar ID registro

            $mensaje = '
            <style>
        /* -------------------------------------
          GLOBAL RESETS
      ------------------------------------- */

        /*All the styling goes here*/

        img {
            border: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%;
        }

        body {
            background-color: #f6f6f6;
            font-family: poppins;
            -webkit-font-smoothing: antialiased;
            font-size: 18px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
        }

        table {
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%;
        }

        table td {
            font-family: poppins;
            font-size: 18px;
            vertical-align: top;
        }

        /* -------------------------------------
          BODY & CONTAINER
      ------------------------------------- */

        .body {
            background-color: #f6f6f6;
            width: 100%;
        }

        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display: block;
            margin: 0 auto !important;
            /* makes it centered */
            max-width: 800px;
            padding: 10px;
            width: 800px;
        }

        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            box-sizing: border-box;
            display: block;
            margin: 0 auto;
            max-width: 800px;
            padding: 10px;
        }

        /* -------------------------------------
          HEADER, FOOTER, MAIN
      ------------------------------------- */
        .main {
            background: #ffffff;
            border-radius: 3px;
            width: 100%;
        }

        .wrapper {
            box-sizing: border-box;
            padding: 20px;
        }

        .content-block {
            padding-bottom: 10px;
            padding-top: 10px;
        }

        .footer {
            clear: both;
            margin-top: 10px;
            text-align: center;
            width: 100%;
        }

        .footer td,
        .footer p,
        .footer span,
        .footer a {
            color: #999999;
            font-size: 18px;
            text-align: center;
        }

        /* -------------------------------------
          TYPOGRAPHY
      ------------------------------------- */
        h1,
        h2,
        h3,
        h4 {
            color: #000000;
            font-family: poppins;
            font-weight: 400;
            line-height: 1.4;
            margin: 0;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 35px;
            font-weight: 300;
            text-align: center;
            text-transform: capitalize;
        }

        p,
        ul,
        ol {
            font-family: poppins;
            font-size: 16px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 15px;
        }

        p li,
        ul li,
        ol li {
            list-style-position: inside;
            margin-left: 5px;
        }

        a {
            color: #3498db;
            text-decoration: underline;
        }

        /* -------------------------------------
          BUTTONS
      ------------------------------------- */
        .btn {
            box-sizing: border-box;
            width: 100%;
        }

        .btn>tbody>tr>td {
            padding-bottom: 15px;
        }

        .btn table {
            width: auto;
        }

        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
        }

        .btn a {
            background-color: #ffffff;
            border: solid 1px #3498db;
            border-radius: 5px;
            box-sizing: border-box;
            color: #3498db;
            cursor: pointer;
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            text-transform: capitalize;
        }

        .btn-primary table td {
            background-color: #3498db;
        }

        .btn-primary a {
            background-color: #3498db;
            border-color: #3498db;
            color: #ffffff;
        }

        /* -------------------------------------
          OTHER STYLES THAT MIGHT BE USEFUL
      ------------------------------------- */
        .last {
            margin-bottom: 0;
        }

        .first {
            margin-top: 0;
        }

        .align-center {
            text-align: center;
        }

        .align-right {
            text-align: right;
        }

        .align-left {
            text-align: left;
        }

        .clear {
            clear: both;
        }

        .mt0 {
            margin-top: 0;
        }

        .mb0 {
            margin-bottom: 0;
        }

        .preheader {
            color: transparent;
            display: none;
            height: 0;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            mso-hide: all;
            visibility: hidden;
            width: 0;
        }

        .powered-by a {
            text-decoration: none;
        }

        hr {
            border: 0;
            border-bottom: 1px solid #f6f6f6;
            margin: 20px 0;
        }

        /* -------------------------------------
          RESPONSIVE AND MOBILE FRIENDLY STYLES
      ------------------------------------- */
        @media only screen and (max-width: 620px) {
            table.body h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important;
            }

            table.body p,
            table.body ul,
            table.body ol,
            table.body td,
            table.body span,
            table.body a {
                font-size: 18px !important;
            }

            table.body .wrapper,
            table.body .article {
                padding: 10px !important;
            }

            table.body .content {
                padding: 0 !important;
            }

            table.body .container {
                padding: 0 !important;
                width: 100% !important;
            }

            table.body .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important;
            }

            table.body .btn table {
                width: 100% !important;
            }

            table.body .btn a {
                width: 100% !important;
            }

            table.body .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important;
            }
        }

        /* -------------------------------------
          PRESERVE THESE STYLES IN THE HEAD
      ------------------------------------- */
        @media all {
            .ExternalClass {
                width: 100%;
            }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }

            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important;
            }

            #MessageViewBody a {
                color: inherit;
                text-decoration: none;
                font-size: inherit;
                font-family: inherit;
                font-weight: inherit;
                line-height: inherit;
            }

            .btn-primary table td:hover {
                background-color: #34495e !important;
            }

            .btn-primary a:hover {
                background-color: #34495e !important;
                border-color: #34495e !important;
            }
        }
    </style>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
    <td align="center">
    <img src="https://admin.aloja.com/includes/images/logo_peque.png" width="200"><br>
    </td>
    </tr>
        <tr>
            <td class="container">
                <div class="content">

                    <!-- START CENTERED WHITE CONTAINER -->
                    <table role="presentation" class="main">

                        <!-- START MAIN CONTENT AREA -->
                        <tr>
                            <td class="wrapper">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td>
                                            <p>Bienvenid@</p>
                                            <p>¡Ya eres parte de la comunidad de <b>ALOJA Experiencias</b>! <br><br>
                                            Estas a un paso de administrar de forma más fácil tu negocio y obtener todos los beneficios de nuestra plataforma.</p>
                                           Solo requieres de confirmar tu correo .</p>
                                            <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                                                class="btn btn-primary">
                                                <tbody>
                                                    <tr>
                                                        <td align="left">
                                                            <table role="presentation" border="0" cellpadding="0"
                                                                cellspacing="0">
                                                                <tbody>
                                                                    <tr>
                                                                        <td> <a href="' . $this->conf["pathSite"] . 'confirm/' . $urlpathuser . '" target="_blank">Confirmar Correo</a> </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            <p>Nos da mucho gusto que ya eres parte de nuestra comunidad.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
    ';

            $r = indexModel::sendMail($email, $name, "Registro exitoso", $mensaje, "Es necesario que aceptes tu alta de correo");
            if ($r != 0) {
                $response = 2;
            }
        }
        // --> Mandar correo para validación de registro
        $cad = $response . "|" . $idReg;
        return $cad;
    }
    public static function sendMail($correo, $name, $asunto, $mensaje, $alt)
    {

        //Load Composer's autoloader
        include_once 'vendor/autoload.php';

        //include_once('../class.phpmailer.php');
        //include("../class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
        //$fs = fsockopen("ssl://smtp.gmail.com", 465);
        //echo 1;
        $mail = new PHPMailer(true);

        //$body             = $mail->getFile('contents.html');
        $body = $mensaje;
        $mail->IsSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true; // enable SMTP authentication
        $mail->SMTPSecure = "ssl"; // sets the prefix to the servier
        $mail->Host = "smtp.gmail.com"; //"ssl://smtp.gmail.com";      // sets GMAIL as the SMTP server
        $mail->Port = 465; // set the SMTP port for the GMAIL server
        $mail->Username = "reservas@aloja.com"; // GMAIL username
        $mail->Password = ":JxKg'p@4=S!"; // GMAIL password

        //$mail->AddReplyTo("reservas@aloja.com", "ALOJA");

        $mail->From = "reservas@aloja.com";
        $mail->FromName = "ALOJA";

        $mail->Subject = $asunto;

        //$mail->Body       = "Hi,<br>This is the HTML BODY<br>";                      //HTML Body
        $mail->AltBody = $alt; // optional, comment out and test
        $mail->WordWrap = strlen($body); // set word wrap

        $mail->MsgHTML($body);

        $mail->AddAddress($correo, $name);

        //$mail->AddAttachment("images/includes/logo.png");             // attachment

        $mail->IsHTML(true); // send as HTML
        if (!$mail->Send()) {
            //echo "Mailer Error: " . $mail->ErrorInfo;
            //exit();
            return 2;
            //echo "Mailer Error: " . $mail->ErrorInfo;
        } else {

            //echo "Message sent!";
            //exit();
            return 1;
        }
    }

    protected function getIDPublico($plaintext)
    {
        $key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a7");
        $key_size = strlen($key);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CBC, $iv);
        $ciphertext = $iv . $ciphertext;
        $ciphertext_base64 = base64_encode(urlencode($ciphertext));
        return $ciphertext_base64;
    }

    public function generarURL($id)
    {
        $md5 = md5($id);
        $md5 = base64_encode($md5);
        return $md5;
    }

    public function getEmpresa()
    {
        $sql = "SELECT a.* FROM empresa AS a INNER JOIN user_has_empresa AS b ON a.id=b.empresa_id WHERE b.user_rel_id = " . $_SESSION["idUser"];
        $reg = indexModel::bd($this->conf)->getSQL($sql)[0];
        return $reg;
    }

    public function generaPass()
    {
        $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!#$%&=?*^~";
        $longitudCadena = strlen($cadena);
        $cadena2 = "-+|!#$%&=?*^~";
        $longitudCadena2 = strlen($cadena);
        $pass = "";
        $longitudPass = 6;
        for ($i = 1; $i <= $longitudPass; $i++) {
            $pos = rand(0, $longitudCadena - 1);
            $pass .= substr($cadena, $pos, 1);
        }
        $longitudPass2 = 4;
        for ($i = 1; $i <= $longitudPass2; $i++) {
            $pos = rand(0, $longitudCadena2 - 1);
            $pass .= substr($cadena2, $pos, 1);
        }
        return $pass;
    }

    public function generaPassAPP()
    {
        $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        $longitudCadena = strlen($cadena);
        $pass = "";
        $longitudPass = 6;
        for ($i = 1; $i <= $longitudPass; $i++) {
            $pos = rand(0, $longitudCadena - 1);
            $pass .= substr($cadena, $pos, 1);
        }

        return $pass;
    }

    public function check_in_range($fecha_inicio, $fecha_fin, $fecha)
    {
        $fecha_inicio = strtotime($fecha_inicio);
        $fecha_fin = strtotime($fecha_fin);
        $fecha = strtotime($fecha);
        if (($fecha >= $fecha_inicio) && ($fecha <= $fecha_fin)) {
            return true;
        } else {
            return false;
        }

    }

    public function controlAcceso($roles)
    {
        $res = 0;
        foreach ($_COOKIE as $key => $value) {
            $$key = $value;
        }
        if (in_array($idRol, $roles)) {
            $res = 1;
        } else {
            $rutt = "";
            echo '<meta http-equiv="refresh" content="0;url=' . $this->conf["pathCMSSite"] . $rutt . '">';
        }
        return $res;
    }

    public function getCicloActual()
    {
        $empresa = $this->getEmpresa();
        $sql = "SELECT * FROM ciclo WHERE status_ciclo_id = 1 AND empresa_id = {$empresa->id} ORDER BY fecha_final DESC LIMIT 1 ";
        $reg = indexModel::bd($this->conf)->getSQL($sql)[0];
        return $reg;
    }

    public function generaUserAPP($name)
    {
        $na = rand(1, 99);
        $na = str_pad($na, 2, "0", STR_PAD_LEFT);
        $dd = explode(" ", $name);
        $name = strtolower($dd[0]) . "_" . substr(strtolower($dd[1]), 0, 1) . substr(strtolower($dd[2]), 0, 1) . $na;
        return $name;
    }

    public static function url_exists_I($url)
    {
        //echo $url."<br>";
        $ch = @curl_init($url);
        @curl_setopt($ch, CURLOPT_HEADER, true);
        @curl_setopt($ch, CURLOPT_NOBODY, true);
        @curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1");
        @curl_setopt($ch, CURLOPT_USERPWD, "desarrollo:1q2w3e4r");
        @curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $status = array();
        $d = @curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        preg_match('/HTTP\/.* ([0-9]+) .*/', $d, $status);
        //echo $status[1]."<br>";
        return ($status[1] == 200);
    }
    public function saveImages3Perfil($id, $tempName, $Namebucket, $Urlbucket)
    {
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => 'us-east-2',
            'credentials' => [
                'key' => 'AKIAWOYX4UJGW5TVESGO',
                'secret' => '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',
            ],
        ]);
        $bucketName = $Namebucket;
        $objectKey = $Urlbucket . $id . ".jpg";

        try {
            // Upload the file to S3
            $result = $s3->putObject([
                'Bucket' => $bucketName,
                'Key' => $objectKey,
                'Body' => fopen($tempName, 'rb'),
                'ACL' => 'public-read', // Make the object publicly accessible
            ]);

            return $result['ObjectURL'];
        } catch (Aws\S3\Exception\S3Exception $e) {
            echo "Error uploading image to S3: " . $e->getMessage();
        }
    }

    public static function getImgProfile($path)
    {
        $cad = $path . "includes/img/user.png";
        if (@isset($_COOKIE["idUser"])) {
            $isJPG = $path . "includes/images/user/" . $_COOKIE["idUser"] . ".jpg";
            $isPNG = $path . "includes/images/user/" . $_COOKIE["idUser"] . ".png";
            $isJPEG = $path . "includes/images/user/" . $_COOKIE["idUser"] . ".jpeg";
            if (indexModel::url_exists_I($isJPG)) {
                $cad = $isJPG;
            } elseif (indexModel::url_exists_I($isPNG)) {
                $cad = $isPNG;
            } elseif (indexModel::url_exists_I($isJPEG)) {
                $cad = $isJPEG;
            }
        }
        return $cad;
    }

    public function SecurityParams($_Cadena)
    {
        $_Cadena = htmlspecialchars(trim(addslashes(stripslashes(strip_tags($_Cadena)))));
        $_Cadena = str_replace(chr(160), '', $_Cadena);
        return $_Cadena;
        //return mysql_real_escape_string($_Cadena);
    }

    public function getFormatoFecha($fec)
    {
        $diaSemana = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $axo = substr($fec, 0, 4);
        $mes = (int) substr($fec, 5, 2);
        $dia = substr($fec, 8, 2);
        $numeroDia = date("w", mktime(0, 0, 0, $mes, $dia, $axo));
        $fec1 = $diaSemana[$numeroDia] . ", " . $dia . " de " . $meses[$mes] . " del " . $axo;
        return $fec1;
    }

    public function sendNotification($tokens, $message)
    {
        $url = "https://fcm.googleapis.com/fcm/send";
        $fields = array(
            'registration_ids' => $tokens,
            'data' => $message,
        );
        $headers = array(
            'Authorization:key = AAAAy7vdPjo:APA91bGNw2ryLBYc47ts8VpBoGAQo9Rwt3pHOJT9n8_2XHiBvcYcadfPYz93F0AjpH_24chEMIUB7eQQOVPs-y-vCI1sapn6EbJvbU_viiED_EjJZQlGHkndM1-eu3L2UpyPyoGl7Zy8bO9fNm4R4KBKztIjE5BKBg',
            'Content-Type:application/json',
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
        if ($result === false) {
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
