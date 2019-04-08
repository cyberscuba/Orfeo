<?php
/**
 * Se enruta al controlador que debe procesar la solicitud y el callback
 * que se debe ejecutar.
 */

if(!$db) {
    $db = new ConnectionHandler(BASE_PATH);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
}

//Se llama el controlador y el callback definidos
if($_REQUEST['controllerName'] != "") {
	$controller = BaseController::controllerFactory($_REQUEST['controllerName'], $db);
    try {
        $controller->executeCallBack($_REQUEST['callback']."_before", $_REQUEST);
    } catch (ReflectionException $e) {
    }
	$controller->executeCallBack($_REQUEST['callback'], $_REQUEST);
    try {
        $controller->executeCallBack($_REQUEST['callback']."_after", $_REQUEST);
    } catch (ReflectionException $e) {
    }
}

?>