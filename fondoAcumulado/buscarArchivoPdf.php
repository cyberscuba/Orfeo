<?php

session_start();
error_reporting(7);
$url_raiz = $_SESSION['url_raiz'];
$dir_raiz = $_SESSION['dir_raiz'];
$assoc = $_SESSION['assoc'];
define('ADODB_ASSOC_CASE', $assoc);
$ruta = 'https://ohl.orfeoexpress.com/pruebas';


if (!$dir_raiz)
    $dir_raiz = "..";

include_once $url_raiz . "/config.php";
require_once("$dir_raiz/include/db/ConnectionHandler.php");

if (!$db)
    $db = new ConnectionHandler("$dir_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

if (empty($_SESSION)) {
    reportarErrores("Error de permisos: No se encuentran datos del usuario");
}

$dataReturn = [];
$token = "";

if (isset($_POST['id'])) {
    switch ($_POST['tipo']) {
        // Case 1 -->  Identifica si es para ver el detalle de las imagenes principales
        case '1':
            $explode = explode('.', $_POST['id']);

            /* Esta condición es para validar la tabla a la que se va afectar, si es 2 para fondo_acumulado_recibidas
             * ya que para cada una cambia el campo en el que se guarda la ruta principal
             */
            if (isset($_POST['busquedaTipo']) && $_POST['busquedaTipo'] == 2) {

                $selectRuta = "select campo6 from fondo_acumulado_recibidas where campo4 like '%" . $explode[0] . "'";
                $rsConsulta = $db->conn->query($selectRuta);
                $explodeRuta = str_replace('%20', " ", $rsConsulta->fields['CAMPO6']);
                
            }
            /* Esta condición es para validar la tabla a la que se va afectar, si es 3 para fondo_acumulado_externos
             * ya que para cada una cambia el campo en el que se guarda la ruta principal
             */ elseif (isset($_POST['busquedaTipo']) && $_POST['busquedaTipo'] == 3) {

                $selectRuta = "select campo7 from fondo_acumulado_externos where campo5 like '%" . $explode[0] . "'";
                $rsConsulta = $db->conn->query($selectRuta);
                $explodeRuta = str_replace('%20', " ", $rsConsulta->fields['CAMPO7']);

            }
            /* Esta condición es para validar la tabla a la que se va afectar, para fondo_acumulado_enviadas
             * ya que para cada una cambia el campo en el que se guarda la ruta principal
             */ else {

                $selectRuta = "select campo3 from fondo_acumulado_enviadas where campo1 like '%" . $explode[0] . "'";
                $rsConsulta = $db->conn->query($selectRuta);
                $explodeRuta = str_replace('%20', " ", $rsConsulta->fields['CAMPO3']);
                
            }
            $ruta = '/bodega' . $explodeRuta . '/' . $_POST['id'];

            if (file_exists($ruta)) {
                $token = base64_encode(file_get_contents($ruta));
                $dataReturn = array('status' => true, 'message' => 'Ok', 'token' => $token, 'name' => $_POST['id']);
            } else {
                $dataReturn = array('status' => false, 'message' => 'Archivo no disponible', 'token' => $token);
            }

            header('Content-Type: application/json');
            echo json_encode($dataReturn, JSON_FORCE_OBJECT);
            break;

        // Case 2 -->  Identifica si es para ver el detalle de los anexos   
        case '2':
            $ruta = $_POST['id'];

            if (file_exists($ruta)) {
                $token = base64_encode(file_get_contents($ruta));
                $dataReturn = array('status' => true, 'message' => 'Ok', 'token' => $token, 'name' => $_POST['id']);
            } else {
                $dataReturn = array('status' => false, 'message' => 'Archivo no disponible', 'token' => $token);
            }

            header('Content-Type: application/json');
            echo json_encode($dataReturn, JSON_FORCE_OBJECT);
            break;
    }
}
?>