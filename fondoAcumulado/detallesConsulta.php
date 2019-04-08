<?php

session_start();
error_reporting(7);
$url_raiz = $_SESSION['url_raiz'];
$dir_raiz = $_SESSION['dir_raiz'];

require_once("$dir_raiz/include/db/ConnectionHandler.php");
if (!$db)
    $db = new ConnectionHandler("$dir_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

if (empty($_SESSION)) {
    reportarErrores("Error de permisos: No se encuentran datos del usuario");
}

if (isset($_POST['dato'])) {
    $dato = $_POST['dato'];
    $jsondata = array();

    if ($_POST['opc'] == 1) {

        $consultoDetalle = "select campo12, campo13, campo14, campo15, campo17, campo18 from fondo_acumulado_enviadas where id='$dato'";
        $rs = $db->conn->query($consultoDetalle);

        $jsondata['campo12'] = $rs->fields["CAMPO12"];
        $jsondata['campo13'] = $rs->fields["CAMPO13"];
        $jsondata['campo14'] = $rs->fields["CAMPO14"];
        $jsondata['campo15'] = $rs->fields["CAMPO15"];
        $jsondata['campo17'] = $rs->fields["CAMPO17"];
        $jsondata['campo18'] = $rs->fields["CAMPO18"];
        
    } elseif ($_POST['opc'] == 2) {

        $text = '';

        $consultoDetalle = "select campo13, campo14, campo15, campo17, campo18, campo19, campo20, campo21, "
                . "campo22, campo23, campo24, campo25, campo26, campo27, campo28, campo29, campo30, campo31, "
                . "campo32, campo33, campo34, campo35, campo36, campo37 from fondo_acumulado_recibidas where id='$dato'";
        $rs = $db->conn->query($consultoDetalle);
        
        for ($x = 13; $x <= 33; $x++) {
            if ($rs->fields["CAMPO" . $x] != '') {
                $consultoDescrip = "select nombre_campo, descripcion_campo from configuracion_campos_recibidas where consultar=0 and nombre_campo='campo$x'";
                $rsDescrip = $db->conn->query($consultoDescrip);
                
                $text .= $rsDescrip->fields["DESCRIPCION_CAMPO"] . ' <b>--</b> '.$rs->fields["CAMPO" . $x].' , ';
            }
        }

        $jsondata['responsable'] = $text;
        $jsondata['campo35'] = $rs->fields["CAMPO35"];
        $jsondata['campo36'] = $rs->fields["CAMPO36"];
        
    } elseif ($_POST['opc'] == 3) {

        $text = '';

        $consultoDetalle = "select campo14, campo15, campo16, campo17, campo18, campo19, campo20, campo21, "
                . "campo22, campo23, campo24, campo25, campo26, campo27, campo28, campo29, campo30, campo31, "
                . "campo32, campo33, campo34, campo35, campo36, campo37 from fondo_acumulado_externos where id='$dato'";
        $rs = $db->conn->query($consultoDetalle);

        for ($x = 14; $x <= 26; $x++) {
            if ($rs->fields["CAMPO" . $x] != '') {
                $consultoDescrip = "select nombre_campo, descripcion_campo from configuracion_campos_externos where consultar=0 and nombre_campo='campo$x'";
                $rsDescrip = $db->conn->query($consultoDescrip);

                $text .= $rsDescrip->fields["DESCRIPCION_CAMPO"] . ' <b>--</b> '.$rs->fields["CAMPO" . $x].' , ';
            }
        }

        $jsondata['responsable'] = $text;
        $jsondata['campo35'] = $rs->fields["CAMPO36"];
        $jsondata['campo36'] = $rs->fields["CAMPO37"];
    }

    echo json_encode($jsondata);
}