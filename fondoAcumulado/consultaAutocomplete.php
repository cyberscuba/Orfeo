<?php

session_start();
error_reporting(7);
$url_raiz = $_SESSION['url_raiz'];
$dir_raiz = $_SESSION['dir_raiz'];
$assoc = $_SESSION['assoc'];
define('ADODB_ASSOC_CASE', $assoc);


if (!$dir_raiz)
    $dir_raiz = "..";
require_once("$dir_raiz/include/db/ConnectionHandler.php");
if (!$db)
    $db = new ConnectionHandler("$dir_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
if (empty($_SESSION)) {
    reportarErrores("Error de permisos: No se encuentran datos del usuario");
}

$response = [];

if (isset($_POST['campo'])) {
    $campo = $_POST['campo'];
    
    if (isset($_POST['search'])) {
        if($_POST['tipo'] == 1){
            $sqlClaSerie = "SELECT distinct campo".$campo." FROM fondo_acumulado_enviadas where campo".$campo." like '%" . $_POST['search'] . "%' and campo".$campo." is not null";
            $resultClaSerie = $db->conn->getAll($sqlClaSerie);
        }elseif($_POST['tipo'] == 2){
            $sqlClaSerie = "SELECT distinct campo".$campo." FROM fondo_acumulado_recibidas where campo".$campo." like '%" . $_POST['search'] . "%' and campo".$campo." is not null";
            $resultClaSerie = $db->conn->getAll($sqlClaSerie);
        }else{
            $sqlClaSerie = "SELECT distinct campo".$campo." FROM fondo_acumulado_externos	 where campo".$campo." like '%" . $_POST['search'] . "%' and campo".$campo." is not null";
            $resultClaSerie = $db->conn->getAll($sqlClaSerie);
        }
        

        if (count($resultClaSerie) > 0) {
            foreach ($resultClaSerie as $key => $value) {
                $response[] = array('value' => $value['CAMPO'.$campo], 'label' => $value['CAMPO'.$campo]);
            }

            $dataReturn = array('status' => true, 'message' => 'Proceso correcto', 'result' => $response);
        } else {
            $dataReturn = array('status' => false, 'message' => 'No hay datos', 'result' => $response);
        }

        header('Content-Type: application/json');
        echo json_encode($dataReturn, JSON_FORCE_OBJECT);
    }
}

exit;
