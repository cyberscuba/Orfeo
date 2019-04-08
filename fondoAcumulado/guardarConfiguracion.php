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
include_once "../class_control/AplIntegrada.php";
if (!$db)
    $db = new ConnectionHandler("$dir_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

foreach ($_GET as $key => $valor)
    ${$key} = $valor;
foreach ($_POST as $key => $valor)
    ${$key} = $valor;

$datosGuardar = json_decode($_POST['numerocampo']);
$tipo = $_POST['tipo'];
if (isset($datosGuardar) && isset($tipo)) {

    switch ($tipo) {
        case 1:
            $x = 1;
            $sqlDelete = 'drop table fondo_acumulado_orfeo38';
            $db->conn->query($sqlDelete);

            /* Creo la tabla correspondiente a la pestaña de enviadas */
            /* con cada uno de los campos que se reciben en el formulario que construye la estrutura de la tabla */
            $createTabla = 'create table fondo_acumulado_orfeo38(';
            $createTabla .= ' id integer NOT NULL,';
            $createTabla .= ' tipo numeric(1,0) NOT NULL,';

            for ($i = 0; $i < count($datosGuardar); $i++) {

                $nombreCampo = $datosGuardar[$i]->imputsNombreCampo;
                $tipoCampo = $datosGuardar[$i]->selecTipoCampo;
                $imputsDescCampo = $datosGuardar[$i]->imputsDescCampo;

                switch ($tipoCampo) {
                    case 'Texto':
                        $tipo_dato = 'character varying(10000)';
                        break;
                    case 'Numero':
                        $tipo_dato = 'numeric(11, 0)';
                        break;
                    case 'Fecha':
                        $tipo_dato = 'date';
                        break;
                }

                $createTabla .= 'campo' . $x . ' ' . $tipo_dato . ' ,';
                $x++;
            }

            $createTabla .= ');';
            $explodeConsulta = explode(" ,);", $createTabla);

            $createTabla = $explodeConsulta[0] . " );";
            $rscreateTabla = $db->conn->query($createTabla);

            if ($rscreateTabla) {
                $secuencia = "ALTER TABLE public.fondo_acumulado_orfeo38 OWNER TO orfeousr;";
                $rssecuencia = $db->conn->query($secuencia);

                $mensaje = 'Se creó la tabla fondo_acumulado_orfeo38 correctamente con ' . count($datosGuardar) . ' columnas.';
                $dataReturn = array('status' => true, 'message' => $mensaje);
            } else {
                $mensaje = 'No se pudo crear la tabla fondo_acumulado_orfeo38. ';
                $dataReturn = array('status' => false, 'message' => $mensaje);
            }

            header('Content-Type: application/json');
            echo json_encode($dataReturn, JSON_FORCE_OBJECT);
            break;

    }
}
?>