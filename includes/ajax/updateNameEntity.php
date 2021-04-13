<?php
include 'configAjax.php';
foreach ($_REQUEST as $key => $value) {
    //echo $key . "--".$value."<br>";
    $$key =  Security($value);
}
$query="UPDATE conf_table SET name='{$newName}' WHERE id=".$dataTableID;
$tonelaje = $db->prepare($query);
$tonelaje->execute();
?> 