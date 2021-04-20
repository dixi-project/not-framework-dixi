# Not Framework Dixi 

#es-Es una aplicación que permite crear sistemas administrativos de forma simple en abse a la estructura de la base de datos
 
#en-Is an application that allows you to create administrative systems in a simple way based on the structure of the database

#es-Intalación

1. Configurar el archivo ./protected/config/data.php

En este archivo se debera configurar las rutas y accesos a la base de datos

2. Dar permisos para escritura en la carpeta structure que se localiza en  //<domain>/<directory>/includes/structure

3. Ejecutar el //<domain>/<directory>/includes/ajax/createStructure.php

4. Para el diseño deberá de existir una carpeta llamada design en la siguiente ruta //<domain>/<directory>/design

5. En cuya ruta habra una carpeta por diseño nuevo implementado ejemplo diseño 1 estaria en //<domain>/<directory>/design/1

    Nota: Aqui la ruta para descargar 3 diseños que ya se encuentran implmentados y que se pueden utilizar 

    https://framework.dixi-project./design/1.zip
    https://framework.dixi-project./design/2.zip
    https://framework.dixi-project./design/3.zip

6. Para crear un módulo nuevo habra que agregar un archivo en la siguiente ruta //<domain>/<directory>/protected/controller/ con el siguiente formato 

    Controller<NombredelControler>.php 
    Ejemplo si quicieramos crear una ruta llamada 
    //<domain>/<directory>/micontrolernuevo
    ControllerMicontrolernuevo.php

    ejemplo:

```
<?php
class ControllerMicontrolernuevo extends Controller {
    function __construct($view, $conf, $var, $acc) {
        parent::__construct($view, $conf, $var, $acc);
    }
        public function main() {
                foreach ($this->var as $key => $value) {
            $this->data[$key] = $value;
        }
        indexModel::bd($this->conf)->controlAcceso(["1","2"]);
        $this->view->show("home.html", $this->data, $this->accion);
    }
}
?>
```

# en-Installation

1. Configure the ./protected/config/data.php file

In this file you must configure the routes and accesses to the database

2. Give permission to write to the structure folder located at // <domain> / <directory> / includes / structure

3. Run the //<domain>/<directory>/includes/ajax/createStructure.php

4. For the design there must be a folder called design in the following path // <domain> / <directory> / design

5. In whose path there will be a folder by new design implemented example design 1 would be in // <domain> / <directory> / design / 1

    > Note: Here is the path to download 3 designs that are already implemented and that can be used

    https://framework.dixi-project./design/1.zip
    https://framework.dixi-project./design/2.zip
    https://framework.dixi-project./design/3.zip

6. To create a new module you will have to add a file in the following path // <domain> / <directory> / protected / controller / with the following format

    Controller<NombredelControler>.php 
    Example if we wanted to create a route called
    //<domain>/<directory>/micontrolernuevo
    ControllerMicontrolernuevo.php

    example:

```
<?php
class ControllerMicontrolernuevo extends Controller {
    function __construct($view, $conf, $var, $acc) {
        parent::__construct($view, $conf, $var, $acc);
    }
        public function main() {
                foreach ($this->var as $key => $value) {
            $this->data[$key] = $value;
        }
        indexModel::bd($this->conf)->controlAcceso(["1","2"]);
        $this->view->show("home.html", $this->data, $this->accion);
    }
}
?>
    ```

End