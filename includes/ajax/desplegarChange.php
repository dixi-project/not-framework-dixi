<?php
include 'configAjax.php';
foreach ($_REQUEST as $key => $value) {
    //echo $key . "--".$value."<br>";
    $$key =  Security($value);
}

$sqlValidate  ="SELECT * FROM conf_table WHERE id = {$dataTableID}";
$recordset = $db->prepare($sqlValidate);
$recordset->execute();
$resp = $recordset->fetch(PDO::FETCH_OBJ);
$nameFile = "structure_".$resp->conf_table.".str";
$imgTable=false;
$pdfTable=false;
$filesTable=0;
if($resp->img>0) $imgTable=true;
if($resp->pdf>0) $pdfTable=true;
if($resp->files>0) $filesTable=$resp->files;

// --> Obeter campos por tipo
$sqlValidate2  ="SELECT * FROM conf_type_view ";
$recordset2 = $db->prepare($sqlValidate2);
$recordset2->execute();
while($type = $recordset2->fetch(PDO::FETCH_OBJ)){
    // --> Buscar registros
    $sqlValidate3  ="SELECT count(*) as nr FROM conf_view   WHERE  conf_table_id = {$dataTableID} AND conf_type_view_id={$type->id}";
    //echo $sqlValidate3."<br>\n";
    $recordset3 = $db->prepare($sqlValidate3);
    $recordset3->execute();
    $nr  = $recordset3->fetch(PDO::FETCH_OBJ)->nr;

    $sqlValidate3  ="SELECT a.*,b.id, b.conf_field, b.conf_field_new  FROM conf_view as a INNER JOIN conf_field AS b ON b.id=a.conf_field_id  WHERE  a.conf_table_id = {$dataTableID} AND a.conf_type_view_id={$type->id}";
    //echo $sqlValidate3."<br>\n";
    $recordset3 = $db->prepare($sqlValidate3);
    $recordset3->execute();


    //echo $type->conf_type_view."---X:".$nr."<br>\n";
    $campos=array();
    if(count($nr)>0){
        while($field= $recordset3->fetch(PDO::FETCH_OBJ)){
            $campos[$field->conf_field]=$field->conf_field_new;
        }
    }
    //
    $types[$type->conf_type_view]=$campos;
    //var_dump($types);
}

$sqlValidate33="SELECT * FROM conf_field WHERE conf_table_id = {$dataTableID} ";
$recordset33 = $db->prepare($sqlValidate33);
$recordset33->execute();
while($field= $recordset33->fetch(PDO::FETCH_OBJ)){
    $commField = '{"name":"'.$field->conf_field_new.'"}';
    $sqlValidate5="SHOW FULL COLUMNS FROM `{$resp->conf_table}` WHERE FIELD = '{$field->conf_field}'";
    $recordset5 = $db->prepare($sqlValidate5);
    $recordset5->execute();
    $field5= $recordset5->fetch(PDO::FETCH_OBJ);
    $ss5 = "ALTER TABLE `{$resp->conf_table}` CHANGE `{$field->conf_field}` `{$field->conf_field}` $field5->Type COMMENT '{$commField}'";
    $recordset55 = $db->prepare($ss5);
    $recordset55->execute();
}




$guardar = array(
    "structure"=>array
    (
        "name"=> $resp->name,
		"img"=>$imgTable,
		"pdf"=>$pdfTable,
		"files"=> array(
				"nf"=>$filesTable
        ),
		"return"=> array(
				"0"=>$resp->conf_table
        ),
        "views"=>$types
    )
);

$structure = json_encode($guardar,true);
//echo $structure;
//echo "------------------------------------------------------------------------";


$sqlValidate4="ALTER TABLE {$resp->conf_table} COMMENT = '{$structure}'";
//echo $sqlValidate4."\n";
$recordset3 = $db->prepare($sqlValidate4);
$recordset3->execute();

//

/*
$query="UPDATE conf_table SET name='{$newName}' WHERE id=".$dataTableID;
$tonelaje = $db->prepare($query);
$tonelaje->execute();
*/
include 'scriptCreateStructure.php';
?> 