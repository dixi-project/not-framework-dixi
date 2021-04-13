<?php
include 'configAjax.php';
foreach ($_REQUEST as $key => $value) {
    //echo $key . "--".$value."<br>"; 
    $$key =  Security($value);
}

// --> si activo crear 
    if($activar==1){
        $query="INSERT INTO  conf_view  VALUES (0,{$dataTableID},{$dataType},{$dataFieldID},0)";
        $sql = $db->prepare($query);
        $sql->execute();
    }else{ // --> En todo caso eliminar
        $query="DELETE FROM  conf_view  WHERE conf_table_id = {$dataTableID} AND conf_type_view_id = {$dataType} AND conf_field_id = {$dataFieldID}";
        $sql = $db->prepare($query);
        $sql->execute();
    }
    //echo $query;
?> 