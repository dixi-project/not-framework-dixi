<?php
include 'configAjax.php';
foreach ($_REQUEST as $key => $value) {
    //echo $key . "--".$value."<br>"; 
    $$key =  Security($value);
}
$query="UPDATE conf_field SET conf_field_new='{$namFiled}' WHERE id=".$dataFieldID;
$sql = $db->prepare($query);
$sql->execute();
?> 