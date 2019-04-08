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

$dataReturn = [];

switch ($_POST['fondo']) {
    // Caso 1 es para las comuniaciones enviadas 
    case "1":
        $filtros = [];
        $filtroSql = "";

        // Recorre cada uno de los campos correspondientes a los criterios de búsquedas 
        for ($x = 1; $x <= $_POST['total']; $x++) {
            if (isset($_POST['data']['campo' . $x]) && $_POST['data']['campo' . $x] != '') {

                $selectConfig = "SELECT data_type, column_name FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'fondo_acumulado_enviadas' AND column_name ='campo$x'";
                $rsSelect = $db->conn->query($selectConfig);

                $tipo_campo_fondo = $rsSelect->fields["DATA_TYPE"];
                $nombre = $rsSelect->fields['COLUMN_NAME'];
                switch ($tipo_campo_fondo) {
                    case 'character varying':                        
                        if ($nombre == 'campo18' or $nombre == 'campo15' or $nombre == 'campo2' or $nombre == 'campo11') {
                            $explodeFecha = explode(' - ', $_POST['data']['campo' . $x]);
                            $filtros[] = " campo" . $x . " BETWEEN '" . $explodeFecha[0] . "' AND '" . $explodeFecha[1] . "' ";
                        }else{
                            $filtros[] = " campo" . $x . " like '%" . $_POST['data']['campo' . $x] . "%'";
                        }
                        break;
                    case 'date':
                        $explodeFecha = explode(' - ', $_POST['data']['campo' . $x]);
                        $filtros[] = " campo" . $x . " BETWEEN '" . $explodeFecha[0] . "' AND '" . $explodeFecha[1] . "' ";
                        break;
                }
            }
        }

        if (count($filtros) > 0) {
            for ($i = 0; $i < count($filtros); $i++) {
                if ($i == 0) {
                    $filtroSql = " WHERE " . $filtros[$i];
                } else {
                    $filtroSql .= " AND " . $filtros[$i];
                }
            }
        }

        $sqlClient = "SELECT id, campo1, campo2, campo3, campo4, campo5, campo6, campo7, campo8, campo9, campo10, campo11, campo12, campo13, campo14, campo15, campo16, campo17, campo18 FROM fondo_acumulado_enviadas " . $filtroSql. " order by campo2 desc";
        $resultClient = $db->conn->getAll($sqlClient);

        if (count($resultClient) > 0) {
            foreach ($resultClient as $key => $value) {
                $dataClient[] = array(
                    'id' => $value['ID'],
                    'campo1' => $value['CAMPO1'],
                    'campo2' => $value['CAMPO2'],
                    'campo3' => $value['CAMPO3'],
                    'campo4' => $value['CAMPO4'],
                    'campo5' => $value['CAMPO5'],
                    'campo6' => $value['CAMPO6'],
                    'campo7' => $value['CAMPO7'],
                    'campo8' => $value['CAMPO8'],
                    'campo9' => $value['CAMPO9'],
                    'campo10' => $value['CAMPO10'],
                    'campo11' => $value['CAMPO11']
                );
            }

            $dataReturn = array('status' => true, 'message' => 'Proceso correcto', 'result' => $dataClient);
        } else {
            $dataReturn = array('status' => false, 'message' => 'Sin resultados', 'result' => $dataClient);
        }

        header('Content-Type: application/json');
        echo json_encode($dataReturn, JSON_FORCE_OBJECT);

        break;
    // Caso 2 es para las comuniaciones recibidas    
    case "2":
        $filtros = [];
        $filtroSql = "";

        // Recorre cada uno de los campos correspondientes a los criterios de búsquedas 
        for ($x = 1; $x <= $_POST['total']; $x++) {
            if (isset($_POST['data']['campo' . $x]) && $_POST['data']['campo' . $x] != '') {

                $selectConfig = "SELECT data_type, column_name FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'fondo_acumulado_recibidas' AND column_name ='campo$x'";
                $rsSelect = $db->conn->query($selectConfig);

                $tipo_campo_fondo = $rsSelect->fields["DATA_TYPE"];
                $nombre = $rsSelect->fields['COLUMN_NAME'];
                switch ($tipo_campo_fondo) {
                    case 'character varying':                        
                        if ($nombre == 'campo36' or $nombre == 'campo5') {
                            $explodeFecha = explode(' - ', $_POST['data']['campo' . $x]);
                            $filtros[] = " campo" . $x . " BETWEEN '" . $explodeFecha[0] . "' AND '" . $explodeFecha[1] . "' ";
                        }else{
                            $filtros[] = " campo" . $x . " like '%" . $_POST['data']['campo' . $x] . "%'";
                        }
                        break;
                    case 'date':
                        $explodeFecha = explode(' - ', $_POST['data']['campo' . $x]);
                        $filtros[] = " campo" . $x . " BETWEEN '" . $explodeFecha[0] . "' AND '" . $explodeFecha[1] . "' ";
                        break;
                }
            }
        }

        if (count($filtros) > 0) {
            for ($i = 0; $i < count($filtros); $i++) {
                if ($i == 0) {
                    $filtroSql = " WHERE " . $filtros[$i];
                } else {
                    $filtroSql .= " AND " . $filtros[$i];
                }
            }
        }

        $sqlClient = "SELECT id, campo3, campo4, campo5, campo6, campo7, campo8, campo9, campo10, campo11, campo12 FROM fondo_acumulado_recibidas " . $filtroSql. " and tipo = ".$_POST['tipoBusqueda']." order by campo5 desc";
        $resultClient = $db->conn->getAll($sqlClient);
        
//        echo '----- '.$sqlClient;

        if (count($resultClient) > 0) {
            foreach ($resultClient as $key => $value) {
                $dataClient[] = array(
                    'id' => $value['ID'],
                    'campo3' => $value['CAMPO3'],
                    'campo4' => $value['CAMPO4'],
                    'campo5' => $value['CAMPO5'],
                    'campo6' => $value['CAMPO6'],
                    'campo7' => $value['CAMPO7'],
                    'campo8' => $value['CAMPO8'],
                    'campo9' => $value['CAMPO9'],
                    'campo10' => $value['CAMPO10'],
                    'campo11' => $value['CAMPO11'],
                    'campo12' => $value['CAMPO12']
                );
            }

            $dataReturn = array('status' => true, 'message' => 'Proceso correcto', 'result' => $dataClient);
        } else {
            $dataReturn = array('status' => false, 'message' => 'Sin resultados', 'result' => $dataClient);
        }

        header('Content-Type: application/json');
        echo json_encode($dataReturn, JSON_FORCE_OBJECT);

        break;
        // Caso 3 es para las cartas ERC terceros    
    case "3":
        $filtros = [];
        $filtroSql = "";

        // Recorre cada uno de los campos correspondientes a los criterios de búsquedas 
        for ($x = 1; $x <= $_POST['total']; $x++) {
            if (isset($_POST['data']['campo' . $x]) && $_POST['data']['campo' . $x] != '') {

                $selectConfig = "SELECT data_type, column_name FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'fondo_acumulado_externos' AND column_name ='campo$x'";
                $rsSelect = $db->conn->query($selectConfig);

                $tipo_campo_fondo = $rsSelect->fields["DATA_TYPE"];
                $nombre = $rsSelect->fields['COLUMN_NAME'];
                switch ($tipo_campo_fondo) {
                    case 'character varying':                        
                        if ($nombre == 'campo37' or $nombre == 'campo6') {
                            $explodeFecha = explode(' - ', $_POST['data']['campo' . $x]);
                            $filtros[] = " campo" . $x . " BETWEEN '" . $explodeFecha[0] . "' AND '" . $explodeFecha[1] . "' ";
                        }else{
                            $filtros[] = " campo" . $x . " like '%" . $_POST['data']['campo' . $x] . "%'";
                        }
                        break;
                    case 'date':
                        $explodeFecha = explode(' - ', $_POST['data']['campo' . $x]);
                        $filtros[] = " campo" . $x . " BETWEEN '" . $explodeFecha[0] . "' AND '" . $explodeFecha[1] . "' ";
                        break;
                }
            }
        }

        if (count($filtros) > 0) {
            for ($i = 0; $i < count($filtros); $i++) {
                if ($i == 0) {
                    $filtroSql = " WHERE " . $filtros[$i];
                } else {
                    $filtroSql .= " AND " . $filtros[$i];
                }
            }
        }

        $sqlClient = "SELECT id, campo3, campo4, campo5, campo6, campo7, campo8, campo9, campo10, campo11, campo12, campo13 FROM fondo_acumulado_externos " . $filtroSql. " order by campo3 desc";
        $resultClient = $db->conn->getAll($sqlClient);

        if (count($resultClient) > 0) {
            foreach ($resultClient as $key => $value) {
                $dataClient[] = array(
                    'id' => $value['ID'],
                    'campo3' => $value['CAMPO3'],
                    'campo4' => $value['CAMPO4'],
                    'campo5' => $value['CAMPO5'],
                    'campo6' => $value['CAMPO6'],
                    'campo7' => $value['CAMPO7'],
                    'campo8' => $value['CAMPO8'],
                    'campo9' => $value['CAMPO9'],
                    'campo10' => $value['CAMPO10'],
                    'campo11' => $value['CAMPO11'],
                    'campo12' => $value['CAMPO12'],
                    'campo13' => $value['CAMPO13']
                );
            }

            $dataReturn = array('status' => true, 'message' => 'Proceso correcto', 'result' => $dataClient);
        } else {
            $dataReturn = array('status' => false, 'message' => 'Sin resultados', 'result' => $dataClient);
        }

        header('Content-Type: application/json');
        echo json_encode($dataReturn, JSON_FORCE_OBJECT);

        break;
}