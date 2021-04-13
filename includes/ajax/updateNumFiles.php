<?php
include 'configAjax.php';
foreach ($_REQUEST as $key => $value) {
    //echo $key . "--".$value."<br>";
    $$key =  Security($value);
}
$query="UPDATE conf_table SET files='{$numFile}' WHERE id=".$dataTableID;
$sql = $db->prepare($query);
$sql->execute();
?> 