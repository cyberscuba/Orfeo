<?php
/**
 * En este frame se van cargado cada una de las funcionalidades del sistema
 *
 * Descripcion Larga
 *
 * @category
 * @package      SGD Orfeo
 * @subpackage   Main
 * @author       Community
 * @author       Skina Technologies SAS (http://www.skinatech.com)
 * @license      GNU/GPL <http://www.gnu.org/licenses/gpl-2.0.html>
 * @link         http://www.orfeolibre.org
 * @version      SVN: $Id$
 * @since
 */
/* ---------------------------------------------------------+
  |                     INCLUDES                             |
  +--------------------------------------------------------- */


/* ---------------------------------------------------------+
  |                    DEFINICIONES                          |
  +--------------------------------------------------------- */
session_start();
error_reporting(7);
$url_raiz = "..";
$dir_raiz = $_SESSION['dir_raiz'];
$ESTILOS_PATH2 = $_SESSION['ESTILOS_PATH2'];

/* ---------------------------------------------------------+
  |                       MAIN                               |
  +--------------------------------------------------------- */
/**
 * Modificacion Docx Skina
 * Licencia GNU/GPL 
 */
foreach ($_GET as $key => $valor)
    ${$key} = $valor;
foreach ($_POST as $key => $valor)
    ${$key} = $valor;

$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc = $_SESSION["usua_doc"];
$codusuario = $_SESSION["codusuario"];
$tpNumRad = $_SESSION["tpNumRad"];
$tpPerRad = $_SESSION["tpPerRad"];
$tpDescRad = $_SESSION["tpDescRad"];
$tip3Nombre = $_SESSION["tip3Nombre"];

$assoc = $_SESSION['assoc'];
define('ADODB_ASSOC_CASE', $assoc);

include_once "$dir_raiz/include/db/ConnectionHandler.php";

if (!$ent)
    $ent = substr(trim($numrad), strlen($numrad) - 1, 1);

$nombreTp3 = $tip3Nombre[3][$ent];

if (!$db)
    $db = new ConnectionHandler($dir_raiz);
//$db->conn->debug = true;

$dbAux = new ConnectionHandler($dir_raiz);
$dbAux->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

/* Verifico si el anexo esta registrado en la tabla que almacena los
 * datos del analisis OCR, se coloca alias en mayúscula para evitar
 * problemas al consultar con varios motores de DB
 */
$existeIndice = $db->conn->query("select indice as INDEX from datosocr where nume_radi='$codigo'");
$alertaModificacion = (!$existeIndice->EOF && $existeIndice->fields['INDEX'] != '')? true : false;

$conexion = & $db;
$rowar = array();
$mensaje = "";

$isql = "select USUA_LOGIN,USUA_PASW,CODI_NIVEL from usuario where (usua_login ='$krd') ";
$rs = $db->conn->Execute($isql);

if ($rs->EOF) {
    $mensaje = "No tiene permisos para ver el documento";
} else {
    $nivel = $assoc == 0 ? $rs->fields["codi_nivel"] : $rs->fields["CODI_NIVEL"];
    $isql = "select ANEX_TIPO_CODI, ANEX_TIPO_DESC, ANEX_TIPO_EXT from anexos_tipo order by anex_tipo_desc desc";
    $rs = $db->conn->Execute($isql);
}

if ($resp1 == "OK") {
    if ($subir_archivo) {
        $mensaje = "<script type='text/javascript'>alert('Archivo anexado correctamente');
         f_close();
 </script>";
    } else {
        $mensaje = "<script type='text/javascript>alert('Anexo Modificado Correctamente No se anexó ningún archivo'); f_close();</script>";
    }
} else if ($resp1 == "ERROR") {
    $mensaje = "<script type='text/javascript'>alert('Error al anexar los archivos');</script>";
}

include "$dir_raiz/radicacion/crea_combos_universales.php";

if (!function_exists(return_bytes)) {

    /**
     * Retorna la cantidad de bytes de una expresion como 7M, 4G u 8K.
     *
     * @param char $var
     * @return numeric
     */
    function return_bytes($val) {
        $val = trim($val);
        $ultimo = strtolower($val{strlen($val) - 1});
        switch ($ultimo) {    // El modificador 'G' se encuentra disponible desde PHP 5.1.0
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

}
?>
<html>
    <head>
        <title>Informaci&oacute;n de Anexos</title>
        <?$url_raiz=".";?>
        <link href="<?= $url_raiz . $ESTILOS_PATH2 ?>bootstrap.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" href="<?= $url_raiz . $_SESSION['ESTILOS_PATH_ORFEO'] ?>">
        <SCRIPT Language="JavaScript" SRC="js/crea_combos_2.js"></SCRIPT>
        <script language="javascript">
<?php
// Convertimos los vectores de los paises, dptos y municipios creados en crea_combos_universales.php a vectores en JavaScript.
echo arrayToJsArray($vpaisesv, 'vp');
echo arrayToJsArray($vdptosv, 'vd');
echo arrayToJsArray($vmcposv, 'vm');
?>
            function mostrar(nombreCapa)
            {
            document.getElementById(nombreCapa).style.display = "";
            }

            function mostrarNombre(nombreCapa)
            {
            document.formulario.elements[nombreCapa].style.display = "";
            }

            function ocultarNombre(nombreCapa)
            {
            document.formulario.elements[nombreCapa].style.display = "none";
            }

            function ocultar(nombreCapa)
            {
            document.getElementById(nombreCapa).style.display = "none";
            }

            function doc_radicado(ent)
            {
            mostrarForm();
            swSelRadic = 0;
            for (n = 1; n < document.formulario.tpradic.length; n++)
                    if (document.formulario.tpradic.options[n].selected) {
            swSelRadic = 1;
            }

            if (!document.formulario.radicado_salida.checked)
            {
            document.formulario.tpradic.disabled = false;
            eval(document.formulario.elements['tpradic'].options[0] = new Option('- Tipos de Radicacion -', 'null'));
            document.formulario.elements['tpradic'].options[0].selected = true;
            document.formulario.elements['tpradic'].disabled = true;
            //alert('valor del ent '+ent+' linea 134');       
            } else
            {
            document.formulario.tpradic.disabled = false;
            //El radicado de entrada(2) o pqr
            if (ent == 2) {
            document.formulario.elements['tpradic'].options[1].selected = true;
            //by skina modifico los tipos de documento de entrada
            }
            else {
            if (ent == 4) {
            document.formulario.elements['tpradic'].options[1].selected = true;
            //by skina modifico los tipos de documento de entrada
            } else{
            // alert('valor del ent '+ent+' linea 143');
            document.formulario.elements['tpradic'].options[ent].selected = true;
            //by skina modifico los tipos de documento de entrada
            }
            }
            }
            }

            function f_close() {
            //window.history.go(0);
            window.opener.location.reload();
            window.close();
            }

            /*
             * By skina 
             *
             * Valida si la casilla de verificacion de radicacion del documento
             * esta activa. Si lo esta se verifica que este seleccionado un radicado
             * del select.
             */
            function checkRecord(e) {
            if (!document.getElementById('radicado_salida').checked && document.getElementsByName('tpradic')[0].value != "null") {
            e.preventDefault();
            alert("Accion no permitida");
            }
            }

            function regresar() {
            f_close();
            }

            function escogio_archivo()
            {
            var valor;
            archivo_up = document.getElementById('userfile').value;
            valor = 0;
            extension = (archivo_up.substring(archivo_up.lastIndexOf(".") + 1)).toLowerCase();
            <?
     while (!$rs->EOF) {
                        if ($assoc == 0){ $anexoscodigo = $rs-> fields["anex_tipo_codi"]; }else{ $anexoscodigo = $rs-> fields["ANEX_TIPO_CODI"]; }
        $anex_tipo_ext = $assoc == 0 ? $rs->fields["anex_tipo_ext"] : $rs->fields["ANEX_TIPO_EXT"];
            echo "if (extension == " . '"' . $anex_tipo_ext . '"' . ") { valor = " .$anexoscodigo. "; }\n";
        
        $rs->MoveNext();
    }
    
        $anexos_isql = $isql;
        ?>
        document.getElementById('tipo_clase').value = valor;
        if (document.getElementById('radicado_salida').checked == true && valor != 14 && valor != 16 && valor != 24)
        {
                        alert("Atenci\363n. Si el archivo no es ODT o DOCX no podr\341 realizar combinaci\363n de correspondencia. \n\n Otros tipo de archivos no facilitan su acceso");
                document.formulario.radicado_salida.checked = false;
                document.formulario.sololect.checked = true;
                if (!document.formulario.radicado_salida.checked){
                document.formulario.tpradic.disabled = false;
                eval(document.formulario.elements['tpradic'].options[0] = new Option('- Tipos de Radicacion -', 'null'));
                document.formulario.elements['tpradic'].options[0].selected = true;
                document.formulario.elements['tpradic'].disabled = true;
                //alert('valor del ent '+ent+' linea 134');
            }
            
            }
            }
            
            function validarGenerico(){
                        if (document.formulario.radicado_salida.checked && document.formulario.tpradic.value == 'null'){
                alert("Debe seleccionar el tipo de radicacion");
                return false;
            }
            
            if (document.getElementById('tsub').value == '0' || document.getElementById('tdoc').value == '0' || document.getElementById('codserie').value == '0') {
                        alert("Debe seleccionar serie, subserie y tipo documental. ");
                return false;
            }
            
            archivo = document.getElementById('userfile').value;
            if (archivo == "")
            {
<?php
if ($tipo == 0 and ! $codigo) {
    echo "alert('Por favor escoja un archivo'); "
    . "return false;";
} else {
    echo "return true;";
}
?>
            }
            
            return true;
            }
            
            function actualizar() {
                        if (!validarGenerico())
                        return;
                if (document.getElementById('tipo_clase').value != 14 && document.getElementById('tipo_clase').value != 24 && document.getElementById('radicado_salida').checked == true) {
                alert("Error: Esta intentando colocar radicado a un archivo que no es radicable!!\n\n Por favor modifique sus opciones e intentelo de nuevo.\n\n Hint: Intente quitar el check de radicaci\u00F3n a este documento.");
            } else if (document.getElementById('descr').value == '') {
                        alert("El campo de descripci\u00F3n no puede estar vacio, verifique sus datos e intente nuevamente. ");
            } else {
                        document.formulario.submit();
            }
            }
            
            function mostrarForm(  )
            {
                        var tipifica = document.formulario.radicado_salida.checked;
                if (tipifica)
                        document.getElementById("anexaExp").style.display = 'block';
                else
                        document.getElementById("anexaExp").style.display = 'none';
             }
             
             function add_text_rta(msg) {
                        if (document.formulario.radicado_salida.checked == true && document.formulario.tpradic.value == 1 && <?= substr($_GET['numrad'], -1) ?> == 2) {
                document.formulario.descr.value = msg;
            } else {
                        document.formulario.descr.value = '';
            }
            }
            // Si al anexar un documento en el radicado de salida la lsita esta en blanco se debe revisar esta funci&oacute;n ya que es la que contiene la variable
            function disableSelect() {
                        //var tipo_radicado = '<?= $_GET['tpradic'] ?>';
                        var tipo_radicado = '<?= $ent ?>';
                if (tipo_radicado != null && document.getElementById('radicado_salida').checked) {
                document.getElementById('tpradic').value = tipo_radicado;
                document.getElementById('tpradic').disabled = false;
        } else {
                        document.getElementById('radicado_salida').checked = false;
                document.getElementById('tpradic').value = null;
                document.getElementById('tpradic').disabled = true;
        }
        }
        
        </script>

    </head>
    <body bgcolor="#FFFFFF" topmargin="0" onload="disableSelect()">
        <div id="spiffycalendar" class="text"></div>
        <link rel="stylesheet" type="text/css" href="js/spiffyCal/spiffyCal_v2_1.css">
        <script language="JavaScript" src="js/spiffyCal/spiffyCal_v2_1.js"></script>
        <script language="javascript"><!--
        var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "formulario", "fecha_doc", "btnDate1", "", scBTNMODE_CUSTOMBLUE);
                        //--></script>
        <?php
        $i_copias = 0;

        if ($codigo) {
            $isql = "select CODI_NIVEL
                        ,ANEX_SOLO_LECT
                        ,ANEX_CREADOR
                        ,ANEX_DESC
                        ,ANEX_TIPO_EXT
                        ,ANEX_NUMERO
                        ,ANEX_RADI_NUME 
                        ,ANEX_NOMB_ARCHIVO AS nombre
                        ,ANEX_SALIDA
                        ,ANEX_ESTADO
                        ,SGD_DIR_TIPO
                        ,RADI_NUME_SALIDA
                        ,SGD_DIR_DIRECCION 
                        ,sgd_tpr_codigo
                        ,sgd_srd_codigo
                        ,sgd_sbrd_codigo
                    from anexos, 
                        anexos_tipo,radicado " .   
                    "where anex_codigo='$codigo' and anex_radi_nume=radi_nume_radi and anex_tipo=anex_tipo_codi";

            $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
            $rs = $db->conn->Execute($isql);
//            echo '************************************* '.$isql;

            if (!$rs->EOF) {
                $docunivel = $assoc == 0 ? ($rs->fields["codi_nivel"]) : ($rs->fields["CODI_NIVEL"]);
                $sololect = $assoc == 0 ? ($rs->fields["anex_solo_lect"] == "S") : ($rs->fields["ANEX_SOLO_LECT"] == "S");
                $remitente = $assoc == 0 ? $rs->fields["sgd_dir_tipo"] : $rs->fields["SGD_DIR_TIPO"];
                $extension = $assoc == 0 ? $rs->fields["anex_tipo_ext"] : $rs->fields["ANEX_TIPO_EXT"];
                $radicado_salida = $assoc == 0 ? $rs->fields["anex_salida"] : $rs->fields["ANEX_SALIDA"];
                $anex_estado = $assoc == 0 ? $rs->fields["anex_estado"] : $rs->fields["ANEX_ESTADO"];
                $descr = $assoc == 0 ? $rs->fields["anex_desc"] : $rs->fields["ANEX_DESC"];
                $radsalida = $assoc == 0 ? $rs->fields["radi_nume_salida"] : $rs->fields["RADI_NUME_SALIDA"];
                $direccionAlterna = $assoc == 0 ? $rs->fields["sgd_dir_direccion"] : $rs->fields["SGD_DIR_DIRECCION"];
                $codserie = $assoc == 0 ? $rs->fields["sgd_srd_codigo"] : $rs->fields["SGD_SRD_CODIGO"];
                $tsub = $assoc == 0 ? $rs->fields["sgd_sbrd_codigo"] : $rs->fields["SGD_SBRD_CODIGO"];
                $tdoc = $assoc == 0 ? $rs->fields["sgd_tpr_codigo"] : $rs->fields["SGD_TPR_CODIGO"];
//                echo '*** '.$tdoc.' *** '.$tsub.' *** '.$codserie;          
            }
        }
        ?>

        <table width="90%" border="0" align="center" cellspacing="0" cellpadding="0">  
            <tr>
                <td>
                    <?php
                    $datos_envio = "&otro_us11=$otro_us11&codigo=$codigo&dpto_nombre_us11=$dpto_nombre_us11&direccion_us11=" . urlencode($direccion_us11) . "&muni_nombre_us11=$muni_nombre_us11&nombret_us11=$nombret_us11";
                    $datos_envio .= "&otro_us2=$otro_us2&dpto_nombre_us2=$dpto_nombre_us2&muni_nombre_us2=$muni_nombre_us2&direccion_us2=" . urlencode($direccion_us2) . "&nombret_us2=$nombret_us2";
                    $datos_envio .= "&dpto_nombre_us3=$dpto_nombre_us3&muni_nombre_us3=$muni_nombre_us3&direccion_us3=" . urlencode($direccion_us3) . "&nombret_us3=$nombret_us3";

                    $variables = "ent=$ent&" . session_name() . "=" . trim(session_id()) . "&tipo=$tipo&codigo=$codigo$datos_envio&tpradic=$tpradic";
                    //$variables = "ent=$ent&radi=$radi&krd=$krd&".session_name()."=".trim(session_id())."&usua=$krd&contra=$drde&tipo=$tipo&ent=$ent&codigo=$codigo$datos_envio&numrad=$numrad";
                    //if ($_POST['tsub'] != '' ) {
                    if ($codserie ) {
                        echo '<form enctype="multipart/form-data" method="POST" name="formulario" id="formulario" action="upload2.php?' . $variables . '">';
                    } else {
                        echo '<form enctype="multipart/form-data" method="POST" name="formulario" id="formulario" action="">';
                    }
                    ?>
                    <!--<form enctype="multipart/form-data" method="POST" name="formulario" id="formulario" action="upload2.php?<?= $variables ?>">-->

                    <input type="hidden" name="anex_origen" value="<?= $tipo ?>">
                    <input type="hidden" name="tipo" value="<?= $tipo ?>">
                    <input type="hidden" name="numrad" value="<?= $numrad ?>">
                    <input type="hidden" name="tipoLista" value="<?= $tipoLista ?>">

                    <div align="center">
                        <table width="95%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
                            <tr>
                                <td  colspan="2">
                                    <table border=1 width=100% class="borde_tab" >
                                        <tr>
                                        <div id="titulo" style="width: 100%;" align="center">Descripci&oacute;n del documento</div>
                            </tr>
                            
                            <?php if($alertaModificacion): ?>
                                <tr>
                                    <td height="23" align="left" colspan="3" class="listado2">
                                    Este anexo tiene datos asociados del an&aacute;lisis de caracteres, actualizarlo eliminar&aacute; dichos datos.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            
                            <tr>
                                <td class="titulos2" height="25" align="left" colspan="2" >
                                    Tipo de Archivo
                                </td>
                            </tr>
                            <tr>
                                <td height="23" align="left" colspan="3" class="listado2">
                                    Tipo de Anexo:
                                    <select name="tipo" aria-label="Listado con el tipo de archivo para subir como anexo" class="select" id="tipo_clase" >
                                        <?php
                                        $db->conn->SetFetchMode(ADODB_FETCH_NUM);
                                        $rs = $db->conn->Execute($anexos_isql);
                                        while (!$rs->EOF) {
                                            if ($extension == $rs->fields[2]) {
                                                $datoss = " selected ";
                                            } else {
                                                $datoss = "";
                                            }
                                            ?>
                                            <option value="<?= $rs->fields[0] ?>" <?= $datoss ?>>
                                                <?= $rs->fields[1] ?>
                                            </option>
                                            <?php
                                            $rs->MoveNext();
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="titulos2"  colspan="3" >Asignaci&oacute;n TRD</td>
                            </tr>
                            <tr>
                                <td height="23" colspan="1" class="listado2">
                                    <font color="">Serie: 
                                    <?php
                                    /* Consulta las series existentes por dependencia en la que pertece la persona logeada */
                                    /* By - Skinatech */
                                    $nomb_varc = "s.sgd_srd_codigo";
                                    $nomb_varde = "s.sgd_srd_descrip";
                                    include "$dir_raiz/include/query/trd/queryCodiDetalle.php";
                                    /*
                                     * Estos roles aplcian unicamente para acción fiduciaria ya que el codigo puede cambiar
                                     * para los demas clientes 
                                     */

                                    $queryD = "select distinct ($sqlConcat) as detalle, s.sgd_srd_codigo as SGD_SRD_CODIGO "
                                            . "from sgd_mrd_matrird m, sgd_srd_seriesrd s "
                                            . "where m.depe_codi = '$dependencia' and s.sgd_srd_codigo = m.sgd_srd_codigo and s.sgd_srd_codigo <> 1";
                                    $rsD = $db->conn->query($queryD);
                                    
                                    // Se agrega la validación para identificar si la trd asignada esta incluida en la dependencia 
                                    // de lo contrario consulta directamente en la tabla
                                    if($codserie != $rsD->fields["SGD_SRD_CODIGO"]){
                                        $infoserie = "select distinct ($sqlConcat) as detalle, s.sgd_srd_codigo as SGD_SRD_CODIGO from sgd_srd_seriesrd s where s.sgd_srd_codigo=" . $codserie;
                                        $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
                                        $rsD = $db->conn->query($infoserie);
                                        $codserie = $rsD->fields["SGD_SRD_CODIGO"];
                                    }
//                                    error_log('************** '.$codserie);
                                    $varQuery = $queryD;
                                    if ($rsD) {
                                        print $rsD->GetMenu2("codserie", $codserie, "0:-- Seleccione --", false, "", " onChange='submit()' id='codserie' class='select form-control' style='width: 99%' aria-label='Lista de series disponibles para asignar' ");
                                    }
                                    ?>
                                    </font>
                                </td>
                                <td height="10" colspan="1" class="listado2">
                                    <font color="">Subserie:
                                    <?php
                                    $nomb_varc = "su.sgd_sbrd_codigo";
                                    $nomb_varde = "su.sgd_sbrd_descrip";
                                    include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
                                    
                                    if($codserie == ''){
                                        $codserie = 0;
                                    }
                                    
                                    $querySub = "select distinct ($sqlConcat) as detalle, su.sgd_sbrd_codigo as SGD_SBRD_CODIGO 
                                            from sgd_mrd_matrird m, sgd_sbrd_subserierd su
                                            where m.depe_codi = '$dependencia'
                                            and m.sgd_srd_codigo = '$codserie'
                                            and su.sgd_srd_codigo = '$codserie'
                                            and su.sgd_sbrd_codigo = m.sgd_sbrd_codigo
                                            order by detalle";
                                    $rsSub = $db->conn->query($querySub);
                                    include "$ruta_raiz/include/tx/ComentarioTx.php";
                                    
                                    // Se agrega la validación para identificar si la trd asignada esta incluida en la dependencia 
                                    // de lo contrario consulta directamente en la tabla
//                                    if($tsub != $rsSub->fields["SGD_SBRD_CODIGO"]){
//                                        $infosubserie = "select distinct ($sqlConcat) as detalle, su.sgd_sbrd_codigo as SGD_SBRD_CODIGO from sgd_sbrd_subserierd su where sgd_srd_codigo=" . $codserie . " and sgd_sbrd_codigo=" . $tsub;
//                                        $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//                                        $rsSub = $db->conn->query($infosubserie);
//                                    }
                                    $tsub = $rsSub->fields["SGD_SBRD_CODIGO"];
                                    
                                    print $rsSub->GetMenu2("tsub", $tsub, "0:-- Seleccione --", false, "", " class='select form-control' id='tsub' style='width: 99%' aria-label='Lista de subseries disponibles de la serie seleccionada'");
                                    ?>
                                    </font>
                                </td>
                            </tr>
                            <tr>
                                <td width=50% colspan="3" class="listado2">            
                                    <font color="">Tipo Documental
                                    <input name="hoj" type=hidden value="<?php echo $hoj; ?>">
                                    <?php
                                    $query = "select SGD_TPR_DESCRIP,SGD_TPR_CODIGO from SGD_TPR_TPDCUMENTO WHERE SGD_TPR_TP$ent='1' and SGD_TPR_RADICA='1' ORDER BY SGD_TPR_DESCRIP ";
                                    $opcMenu = "0:-- Seleccione un tipo --";
                                    $fechaHoy = date("Y-m-d");
                                    $fechaHoy = $fechaHoy . "";
                                    $ADODB_COUNTRECS = true;
                                    $rs = $db->conn->query($query);
                                    if ($rs && !$rs->EOF) {
                                        $numRegs = "!" . $rs->RecordCount();
                                        $varQuery = $query;
                                        
                                        // Se agrega la validación para identificar si la trd asignada esta incluida en la dependencia 
                                        // de lo contrario consulta directamente en la tabla
//                                        if($tdoc != $rs->fields["SGD_TPR_CODIGO"]){
//                                            $infoTipos = "select SGD_TPR_DESCRIP,SGD_TPR_CODIGO from SGD_TPR_TPDCUMENTO WHERE  SGD_TPR_TPDCUMENTO=$tdoc and SGD_TPR_RADICA='1'";
//                                            $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//                                            $rsD = $db->conn->query($infoTipos);
//                                            $tdoc = $rsD->fields["SGD_TPR_TPDCUMENTO"];
//                                        }                                                                               
                                        print $rs->GetMenu2("tdoc", $tdoc, "$opcMenu", false, "", "class='selectReducido form-control' style='width: 99%' id='tdoc' onChange='typeDoc()' id='tdoc' title='Lista con tipos de dpcumentos'");
                                    } else {
                                        $tdoc = 0;
                                    }
                                    $ADODB_COUNTRECS = false;
                                    ?>
                                    </font>
                                </td>
                            </tr>
                            <tr>
                                <td height="23" colspan="3" class="listado2">
                                    <table border=0 width=100%>
                                        <tr>
                                            <td width=50% class="listado2">
                                                <?php
                                                $us_1 = "";
                                                $us_2 = "";
                                                $us_3 = "";
                                                $datoss = "";
                                                if ($nombret_us11 and $direccion_us11 and $dpto_nombre_us11 and $muni_nombre_us11) {
                                                    $us_1 = "si";
                                                    $usuar = 1;
                                                    $datoss1 = " checked ";
                                                } else {
                                                    $datoss1 = " disabled ";
                                                }

                                                $sqlFecha = $db->conn->SQLDate("Y-m-D", "RADI_FECH_RADI");
                                                //Se agrega para traer fecha para el mensaje automaticio
                                                $sqlt_rad = "select " . $sqlFecha . " as FECHA from RADICADO where RADI_NUME_RADI = '$numrad'";
                                                $rs_rad = $db->conn->Execute($sqlt_rad);
                                                $rad_fech = $rs_rad->fields[0];
                                                //Se crea mensaje para mostrar
                                                $msj_aut = "Respuesta a comunicaci&oacute;n $numrad de fecha $rad_fech.";

                                                if ($us_1 or $us_2 or $us_3) {
                                                    if ($radicado_salida)
                                                        $datoss = " checked ";
                                                    else
                                                        $datoss = " ";
                                                    $swDischekRad = "";
//    if (strlen(trim($radsalida))>0)    $swDischekRad = "disabled=true";
                                                    $datoss = $datoss . $swDischekRad;
                                                    ?>
                                                    <input type="checkbox" class="select" title='Casilla de verificacion, Seleccione si su archivo se va a radicar' name="radicado_salida" <?= $datoss ?> value="radsalida" '
                                                    <?php
//                                                    if (!$radicado_salida and $ent == 1)
//                                                        $radicado_salida = 1;
                                                    if ($radicado_salida == 1) {
                                                        echo " checked ";
                                                    }
                                                    ?>' onChange="doc_radicado('<?= $ent ?>');
            add_text_rta('<?= $msj_aut ?>');" id="radicado_salida">    Este documento ser&aacute; radicado? </input>
                                                           <?php
                                                       } else {
                                                           ?>

                                                    Este documento no puede ser radicado ya que faltan datos.<br>
                                                    (Para envio son obligatorios Nombre, Direcci&oacute;n, Departamento,
                                                    Municipio)
                                                    <?php
                                                }
                                                ?><br>
                                                <input type="checkbox" class="select"  name="sololect" title='Casilla para marcra si el archivo anexo sera de solo lectura' id="sololect">
                                                Solo lectura</input>
                                            </td>
                                            <td class="listado2">
                                                <?php
                                                $comboRadOps = "";
                                                $eventoIntegra = "";
                                                if ($ent != 1) {
                                                    $deshab = " disabled=true ";
                                                }
                                                $comboRad = "<select id='tpradic' name='tpradic' aria-label='Listado con los tipos de radicacion disponibles para generar' class='select' $deshab  $eventoIntegra >";
                                                $comboRadSelecc = "<option selected value='null'>- Tipos de Radicacion -</option>";
                                                $sel = "";
                                                if (!$tpradic) {
                                                    if ($ent == 1 or $ent == 2 or $ent == $tipoRadicadoPqr) {
                                                        $tpradic = 1;
                                                    } else {
                                                        $tpradic = $ent;
                                                    }
                                                }

                                                foreach ($tpNumRad as $key => $valueTp) {
                                                    if (strcmp(trim($tpradic), trim($valueTp)) == 0) {
                                                        $sel = "selected";
                                                        $comboIntSwSel = 1;
                                                    }
                                                    //Si se definio prioridad en algun tipo de radicacion

                                                    $valueDesc = $tpDescRad[$key];
                                                    if ($tpPerRad[$valueTp] == 2 or $tpPerRad[$valueTp] == 3) {
                                                        $comboRadOps = $comboRadOps . "<option value='" . $valueTp . "' $sel>" . $valueDesc . "</option>";
                                                    }
                                                    $sel = "";
                                                }
                                                $comboRad = $comboRad . $comboRadSelecc . $comboRadOps . "</select>";
                                                ?>
                                                Seleccione tipo de radicaci&oacute;n:  <?= $comboRad ?> <BR>
                                                <?php
                                                if ($ent == 1) {
                                                    echo ("<script>doc_radicado($ent);</script>");
                                                }

                                                if (strlen(trim($swDischekRad)) > 0) {
                                                    echo ("<script>document.formulario.tpradic.disabled=true;</script>");
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr id="anexaExp" style="display:none">
                                <td class="titulos2"  width="50%">Expediente:</td>
                                <!--<td  valign="top" colspan="2">-->
                                <?php
                                $q_exp = "SELECT  SGD_EXP_NUMERO as valor, SGD_EXP_NUMERO as etiqueta, SGD_EXP_FECH as fecha";
                                $q_exp .= " FROM SGD_EXP_EXPEDIENTE ";
                                $q_exp .= " WHERE RADI_NUME_RADI = '" . $numrad . "'";
                                $q_exp .= " AND SGD_EXP_ESTADO <> 2";
                                $q_exp .= " ORDER BY fecha desc";
                                $rs_exp = $db->conn->Execute($q_exp);

                                $numFilas = $rs_exp->RecordCount();
                                if ($numFilas == 0) {
                                    $mostrarAlerta = "<td align=\"center\" class=\"titulos2\" colspan=\"3\">";
                                    $mostrarAlerta .= "<b>El radicado padre no est&aacute; incluido  en un expediente.</b> </td>";
                                    $sqlt = "select RADI_USUA_ACTU,RADI_DEPE_ACTU from RADICADO where RADI_NUME_RADI LIKE '$numrad'";
                                    $rsE = $db->conn->Execute($sqlt);
                                    $depe = $assoc == 0 ? $rsE->fields['radi_depe_actu'] : $rsE->fields['RADI_DEPE_ACTU'];
                                    $usua = $assoc == 0 ? $rsE->fields['RADI_USUA_ACTU'] : $rsE->fields['radi_usua_actu'];
                                    echo $mostrarAlerta;
                                } else {
                                    ?>
                                    <td align="center" colspos="3" class="listado2">
                                        <?php print $rs_exp->GetMenu('expIncluidoAnexo', $expIncluidoAnexo, false, false, 0, "class='select' style= 'width:98%' aria-label='Listado de expedientes disponibles para incluir el anexo'", false); ?>
                                    </td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <tr>
                                <td class="titulos2"  colspan="2" >Destinatario</td>
                            </tr>
                            <tr valign="top">
                                <td  valign="top" class="listado2" colspan="3">
                                    <input type="radio"   aria-label='Seleccione para colocar como destinatario principal' name="radicado_rem" value=1  id="rusuario" <?= $datoss1 ?> '<?php
                                    if (!$radicado_rem and $ent == 1 and $radicado_rem != 7)
                                        $radicado_rem = 1;
                                    if ($radicado_rem == 1 and $remitente != 7) {
                                        echo " checked ";
                                    }
                                    ?> '>
                                    <label for="rusuario">
                                        <?= $tip3Nombre[1][$ent] ?>
                                        <br>
                                        <?= $otro_us11 . " - " . substr($nombret_us11, 0, 35) ?>
                                        <br>
                                        <?= $direccion_us11 ?>
                                        <br>

                                        <?= "$dpto_nombre_us11/$muni_nombre_us11" ?>
                                    </label>
                                    <?php
                                    if (!empty($codigo)) {
                                        $anexo = $codigo;
                                    }
                                    ?>
                                </td>
                                    <!--<td  valign="top" class="listado2">
                                        <input type="radio" name="radicado_rem" aria-label='Seleccione para colocar un destinatario diferente' value=7 <?= $datoss4 ?> '<?php
                                if ($radicado_rem == 7) {
                                    echo " checked ";
                                }
                                ?> ' id="rotro" readonly>
                                        Otro
                                    </td>-->
                            </tr>
                            <tr valign="top" >
                                <td  class='titulos2' valign="top" colspan="2">Descripci&oacute;n</td>
                            </tr>
                            <tr valign="top">
                                <td  valign="top" colspan="2" height="66" class="listado2"  ><br>
                                    <textarea maxlength="350" name="descr" aria-label='Campo de descripcion del archivo o anexo' cols="70" rows="4" class="tex_area" id="descr"><?= $descr ?></textarea>
                                    </b><input name="usuar" type="hidden" id="usuar" value="<?php echo $usuar ?>"><br>
                                    <input name="predi" type="hidden" id="predi" value="<?php echo $predi ?>">
                                    <input name="empre" type="hidden" id="empre" value="<?php echo $empre ?>">
                                    </span></font>
                                </td>
                            </tr>

                            </td>
                            </tr>

                    </div>
                    <table width="100%"  border="1" cellpadding="0" cellspacing="5" align="center" class="borde_tab">
                        <tr align="center">
                            <td width="100%" height="25"  class="titulos2">
                                <input type="hidden" aria-label='Boton para examinar y seleccionar archivo para subir' name="MAX_FILE_SIZE" value="<?php echo return_bytes(ini_get('upload_max_filesize')); ?>">
                                Adjuntar archivo
                            </td>
                        </tr>
                        <tr align="center"class="listado2">
                            <td>
                                <p align="left">
                                    <input name="userfile1" type="file" class="tex_area" onChange="escogio_archivo();" id="userfile" value="valor" aria-label='Boton para examinar y seleccionar archivo para subir'>
                                </p>
                            </td>
                        </tr>
                        <tr align="center" class="listado2" >
                            <td>
                        <center>
                            <label>
                                <input name="button" type="button" aria-label='Boton para subir archivo seleccionado y grabar los datos del anexo' class="botones_largo" onClick="actualizar()" value="Actualizar <?= $codigo ?>">
                            </label>
                        </center>
                </td>
            </tr>
        </table>
    </table>
</table>
<input type=hidden name=i_copias value='<?= $i_copias ?>'id="i_copias" >
</form>
</td>
</tr>
</table>
<center><span class="etextomenu">
        <table width="90%" border="0" cellspacing="1" cellpadding="0" align="center" class="t_bordeGris">
            <tr align="center">
                <td class="celdaGris" height="25"> <span class="etextomenu">
                        <?php
                        if ($radicado_rem == 7 and $i_copias == 0) {
                            echo " $mensaje <br><b><span  class='alarmas' >No puede generar envio, No se ha anexado destinatario </span></b>";
                        } else {
                            echo "  $mensaje <input type='button' class ='botones' aria-label='Cerrar ventana' value='cerrar' onclick='f_close()'> ";
                        }
                        ?>
                </TD>
            </TR>
        </TABLE>
    </span></center>

<?php if(false): ?>
    <script language="javascript">
        if (!confirm("Este anexo tiene datos asociados del an\xE1lisis de caracteres, actualizarlo eliminar\xE1 dichos datos.")) {
         window.close();
        }                   
    </script>
<?php endif; ?>
                                    
</body>
</html>
