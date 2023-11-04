<?php
$debug = false;
if (@$_SERVER["SERVER_NAME"] == "localhost") {
    $debug = true;
}
if ($debug || $confCron) {
    return array(
        'title' => 'SR22 POS System',
        'connectionString' => 'mysql',
        'host' => 'localhost',
        'dbname' => 'dixiproj_pagossr',
        'username' => 'root',
        'password' => 'j^sIrz!uhSC7*09',
        'folderControladores' => 'protected/controller/',
        'folderModelos' => 'protected/model/',
        'folderVistas' => 'protected/views/',
        'pathSite' => 'http://' . $_SERVER["SERVER_NAME"] . ':8080/not-framework-dixi/',
        'pathCMSSite' => 'http://' . $_SERVER["SERVER_NAME"] . ':8080/not-framework-dixi/',
        'design' => '1',
        'timezone' => 'America/Mexico_City',
        'createby' => 'Create By DIXI PROJECT',

    );
} else if ($_SERVER["SERVER_NAME"] == "admin.aloja.com") {
    return array(
        'title' => 'SR22 POS System',
        'connectionString' => 'mysql',
        'host' => 'obd-kao-db.chb62izcemfq.us-east-2.rds.amazonaws.com',
        'dbname' => 'aloja',
        'username' => 'admin',
        'password' => 'KhSEQy6Jna0rCoruNPDX',
        'folderControladores' => 'protected/controller/',
        'folderModelos' => 'protected/model/',
        'folderVistas' => 'protected/views/',
        'pathSite' => 'https://admin.aloja.com/',
        'pathCMSSite' => 'https://admin.aloja.com/',
        'design' => '1',
        'timezone' => 'America/Mexico_City',
        'createby' => 'Create By DIXI PROJECT',
    );
}
