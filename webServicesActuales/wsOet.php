<?php
/**
 * Funciones para la consulta de dependencias, roles, creacion y modificacion 
 * de usuarios 
 * ip/orfeo/webServicesOet/wsOet.php?wsdl
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


/* ---------------------------------------------------------+
  |                       MAIN                               |
  +--------------------------------------------------------- */


/**
 * @author SkinaTechnologies
 */
$ruta_raiz = "../";
define('RUTA_RAIZ', '../');

require_once "nusoap-0.9.5/lib/nusoap.php";
include_once RUTA_RAIZ . "include/db/ConnectionHandler.php";

$server = new soap_server();
$ns = "webServices/nusoap";
$server->configureWSDL('WS Sistema de Gestión Documental', $ns);

/*
 * Registro de los servicios a exponer
 */

//Servicio que crea usuarios en el sistema
$server->register('crearUsuario', array(
    'usuaCodi' => 'xsd:string',
    'usuaDoc' => 'xsd:string',
    'depeCodi' => 'xsd:string',
    'idperfil' => 'xsd:string',
    'usuaLogin' => 'xsd:string',
    'usuaPasw' => 'xsd:string',
    'usuanomb' => 'xsd:string',
    'usuaEmail' => 'xsd:string'
        ), array('return' => 'tns:userInfo'), $ns, false, '', '', 'Crea usuarios en orfeo');

//Servicio que crea actualiza usuarios en el sistema
$server->register('actualizarUsuario', array(
    'usuaLogin' => 'xsd:string',
    'usuaPasw' => 'xsd:string',
    'usuanomb' => 'xsd:string',
    'usuaEmail' => 'xsd:string'
        ), array('return' => 'tns:userInfo'), $ns, false, '', '', 'Actualiza usuarios de orfeo');

//Servicio que entrega todos los usuarios de Orfeo
//$server->register('consultarUsuarios', array(), array('return' => 'tns:userInfoArray'), $ns, false, '', '', 'Listado de todos los usuarios de orfeo');
//Servicio que consulta un usuario
$server->register('consultarUsuario', array('usuaLogin' => 'xsd:string'), array('return' => 'tns:userInfo'), $ns, false, '', '', 'Consulta de usuario de orfeo');
//Servicio que entrega la lista de roles
$server->register('consultarPerfiles', array(), array('return' => 'tns:perfilesInfoArray'), $ns, false, '', '', 'Listado de perfiles de orfeo');
////Servicio que entrega el listado de los tipos de radicacion
//$server->register('consultarTiposRadicados', array(), array('return' => 'tns:tiposRadicacionInfoArray'), $ns, false, '', '', 'Listado tipos de radicacion');
//Servicio que entrega la lista de dependencias
$server->register('consultarDependencias', array(), array('return' => 'tns:dependenciasInfoArray'), $ns, false, '', '', 'Listado de dependencias de orfeo');

/*
 * Registro con la estrutura de las respuestas
 */
//Arreglo de datos de usuarios
$server->wsdl->addComplexType(
        'userInfoArray', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), array(
    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:userInfo[]')
        ), 'tns:userInfo'
);
//Estructura de los datos de un usuario
$server->wsdl->addComplexType(
        'userInfo', 'complextType', 'struct', 'sequence', '', array(
    'usua_login' => array('name' => 'usua_login', 'type' => 'xsd:string'),
    'usua_doc' => array('name' => 'usua_doc', 'type' => 'xsd:string'),
    'usua_depe' => array('name' => 'usua_depe', 'type' => 'xsd:string'),
    //'usua_nivel' => array('name' => 'usua_nivel', 'type' => 'xsd:string'),
    //'usua_codi' => array('name' => 'usua_codi', 'type' => 'xsd:string'),
    'usua_nomb' => array('name' => 'usua_nomb', 'type' => 'xsd:string'),
    'usua_email' => array('name' => 'usua_email', 'type' => 'xsd:string'),
        )
);

//Arreglo de datos de perfiles
$server->wsdl->addComplexType(
        'perfilesInfoArray', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), array(
    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:perfilesInfo[]')
        ), 'tns:perfilesInfo'
);
//Estructura de los datos de un usuario
$server->wsdl->addComplexType(
        'perfilesInfo', 'complextType', 'struct', 'sequence', '', array(
    'codi_perfil' => array('name' => 'codi_perfil', 'type' => 'xsd:string'),
    'nomb_perfil' => array('name' => 'nomb_perfil', 'type' => 'xsd:string'),
        )
);

//Arreglo de datos de dependencias
$server->wsdl->addComplexType(
        'dependenciasInfoArray', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), array(
    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:dependenciasInfo[]')
        ), 'tns:dependenciasInfo'
);
//Estructura de los datos de un usuario
$server->wsdl->addComplexType(
        'dependenciasInfo', 'complextType', 'struct', 'sequence', '', array(
    'depe_codi' => array('name' => 'depe_codi', 'type' => 'xsd:string'),
    'depe_nomb' => array('name' => 'depe_nomb', 'type' => 'xsd:string'),
        )
);

//Arreglo de tipos de radicacion
$server->wsdl->addComplexType(
        'tiposRadicacionInfoArray', 'complexType', 'array', '', 'SOAP-ENC:Array', array(), array(
    array('ref' => 'SOAP-ENC:arrayType', 'wsdl:arrayType' => 'tns:tiposRadicacionInfo[]')
        ), 'tns:tiposRadicacionInfo'
);
//Estructura de los tipos de radicacion
$server->wsdl->addComplexType(
        'tiposRadicacionInfo', 'complextType', 'struct', 'sequence', '', array(
    'sgd_trad_codigo' => array('name' => 'sgd_trad_codigo', 'type' => 'xsd:string'),
    'sgd_trad_descr' => array('name' => 'sgd_trad_descr', 'type' => 'xsd:string'),
        )
);

/*
 * Servicios
 */

/**
 * Creacion de usuarios 
 *
 * @param string $usuaCodi  debe ser 1 en caso de ser jefe, sino dejar vacio
 * @param string $usuaDoc   cedula usuario
 * @param string $depeCodi  codigo dependencia
 * @param string $idperfil  identificador del perfil (tabla perfiles)
 * @param string $usuaLogin username del usuario
 * @param string $usuaPasw  MD5 de la contraseña del usuario
 * @param string $usuanomb  Nombres de usuario
 * @param string $usuaEmail Correo usuario
 * @return array Arreglo con los datos del usuario
 */
function crearUsuario($usuaCodi, $usuaDoc, $depeCodi, $idperfil, $usuaLogin, $usuaPasw, $usuanomb, $usuaEmail) {
    //$data = $usuaCodi . "----" . $usuaDoc . "---" . $depeCodi . "---" . $idperfil . "---" . $usuaLogin . "---" . $usuaPasw . "---" . $usuanomb . "---" . $usuaEmail;
    //return new soap_fault('Servidor Orfeo', 'Registro de usuario', $data, '');

    global $ruta_raiz;
    $db = new ConnectionHandler($ruta_raiz);
    //Verifica que la cedula y user login no esten ya en el sistema
    //Verifica que que no exista jefe encaso que el usuario se este creando como jefe
    $usuaLogin = trim(strtoupper($usuaLogin));
    $error = array();

    if (!is_numeric($usuaDoc) || !is_numeric($idperfil)) {
        return new soap_fault('Servidor Orfeo', 'Registro de usuario', 'El documento y el perfil deben ser valores numéricos', '');
    }

    if ((integer) $usuaDoc < 1 || (integer) $idperfil < 1) {
        return new soap_fault('Servidor Orfeo', 'Registro de usuario', 'El documento y el perfil deben ser valores numéricos positivos', '');
    }

    if ($usuaDoc == '' || $depeCodi == '' || $idperfil == '' || $usuaLogin == '' || $usuaPasw == '' || $usuanomb == '' || $usuaEmail == '') {
        return new soap_fault('Servidor Orfeo', 'Registro de usuario', 'Ha enviado parámetros obligatorios en vacío', '');
    }

    if (!verificarCorreo($usuaEmail)) {
        return new soap_fault('Servidor Orfeo', 'Registro de usuario', 'Correo no valido', '');
    }

    if (validarCedula($usuaDoc))
        $error[] = "La cédula ya existe";
    if (validarLogin($usuaLogin))
        $error[] = "El nombre de usuario ya existe";
    if ($usuaCodi == 1) {
        if (validarJefe($depeCodi))
            $error[] = "La dependencia ya tiene un jefe";
    }else {
        $isqlCod = "select max(usua_codi) as numero from usuario where depe_codi = '" . $depeCodi . "'";
        $isqlCodRs = $db->query($isqlCod);
        $usuaCodi = $isqlCodRs->fields["numero"] + 1;
    }


    if (!empty($error)) {
        return new soap_fault('Servidor Orfeo', 'Registro de usuario', $error, '');
    }

    $sqlUser = "insert into usuario (usua_codi,usua_fech_crea,depe_codi,usua_nomb,usua_login,usua_pasw,usua_doc,usua_email,usua_esta, "
            . "usua_nuevo) values($usuaCodi,"
            . "'" . date('Y-m-d') . "',"
            . "'$depeCodi',"
            . "'$usuanomb',"
            . "'$usuaLogin',"
            . "'$usuaPasw',"
            . "'$usuaDoc',"
            . "'$usuaEmail','1','1')";
    if (!$db->conn->Execute($sqlUser)) {
        return new soap_fault('Servidor Orfeo', 'Registro de usuario', 'Error guardando el usuario', '');
    }

    $insertCarpetaPer = "insert into carpeta_per (usua_codi,depe_codi,nomb_carp,desc_carp,codi_carp) values("
            . "$usuaCodi,"
            . "'$depeCodi',"
            . "'Masiva',"
            . "'Radicacion Masiva'"
            . ",99)";
    if (!$db->conn->Execute($insertCarpetaPer)) {
        return new soap_fault('Servidor Orfeo', 'Registro de usuario', 'Error creando las carpetas del usuario', '');
    }

    $sqlPerfil = "select * from perfiles where codi_perfil='$idperfil'";
    $rssqlPerfil = $db->conn->Execute($sqlPerfil);

    if (!empty($rssqlPerfil)) {

        $perm_radi = $rssqlPerfil->fields['perm_radi'];
        $usua_admin = $rssqlPerfil->fields['usua_admin'];
        $codi_nivel = $rssqlPerfil->fields['codi_nivel'];
        $perm_radi_sal = $rssqlPerfil->fields['perm_radi_sal'];
        $usua_admin_archivo = $rssqlPerfil->fields['usua_admin_archivo'];
        $usua_masiva = $rssqlPerfil->fields['usua_masiva'];
        $usua_perm_dev = $rssqlPerfil->fields['usua_perm_dev'];
        $usua_perm_numera_res = $rssqlPerfil->fields['usua_perm_numera_res'];
        $usua_perm_numeradoc = $rssqlPerfil->fields['usua_perm_numeradoc'];
        $sgd_panu_codi = $rssqlPerfil->fields['sgd_panu_codi'];
        $usua_prad_tp1 = $rssqlPerfil->fields['usua_prad_tp1'];
        $usua_prad_tp2 = $rssqlPerfil->fields['usua_prad_tp2'];
        $usua_prad_tp4 = $rssqlPerfil->fields['usua_prad_tp4'];
        $usua_perm_envios = $rssqlPerfil->fields['usua_perm_envios'];
        $usua_perm_modifica = $rssqlPerfil->fields['usua_perm_modifica'];
        $usua_perm_impresion = $rssqlPerfil->fields['usua_perm_impresion'];
        $sgd_aper_codigo = $rssqlPerfil->fields['sgd_aper_codigo'];
        $sgd_perm_estadistica = $rssqlPerfil->fields['sgd_perm_estadistica'];
        $usua_admin_sistema = $rssqlPerfil->fields['usua_admin_sistema'];
        $usua_perm_trd = $rssqlPerfil->fields['usua_perm_trd'];
        $usua_perm_firma = $rssqlPerfil->fields['usua_perm_firma'];
        $usua_perm_prestamo = $rssqlPerfil->fields['usua_perm_prestamo'];
        $usuario_publico = $rssqlPerfil->fields['usuario_publico'];
        $usuario_reasignar = $rssqlPerfil->fields['usuario_reasignar'];
        $usua_perm_notifica = $rssqlPerfil->fields['usua_perm_notifica'];
        $usua_perm_expediente = $rssqlPerfil->fields['usua_perm_expediente'];
        $usua_auth_ldap = $rssqlPerfil->fields['usua_auth_ldap'];
        $perm_archi = $rssqlPerfil->fields['perm_archi'];
        $perm_vobo = $rssqlPerfil->fields['perm_vobo'];
        $perm_borrar_anexo = $rssqlPerfil->fields['perm_borrar_anexo'];
        $perm_tipif_anexo = $rssqlPerfil->fields['perm_tipif_anexo'];
        $usua_perm_adminflujos = $rssqlPerfil->fields['usua_perm_adminflujos'];
        $usua_exp_trd = $rssqlPerfil->fields['usua_exp_trd'];
        $usua_perm_rademail = $rssqlPerfil->fields['usua_perm_rademail'];
        $usua_perm_accesi = $rssqlPerfil->fields['usua_perm_accesi'];
        $usua_perm_agrcontacto = $rssqlPerfil->fields['usua_perm_agrcontacto'];

        $sqlupdate = "update usuario set perm_radi='$perm_radi', usua_admin='$usua_admin', codi_nivel=$codi_nivel,"
                . "perm_radi_sal=$perm_radi_sal, usua_admin_archivo=$usua_admin_archivo, usua_masiva=$usua_masiva, "
                . "usua_perm_dev=$usua_perm_dev, usua_perm_numera_res='$usua_perm_numera_res', "
                . "usua_perm_numeradoc=$usua_perm_numeradoc, sgd_panu_codi=$sgd_panu_codi, usua_prad_tp1=$usua_prad_tp1, "
                . "usua_prad_tp2=$usua_prad_tp2, usua_prad_tp4=$usua_prad_tp4, usua_perm_envios=$usua_perm_envios, "
                . "usua_perm_modifica=$usua_perm_modifica, usua_perm_impresion=$usua_perm_impresion, "
                . "sgd_aper_codigo=$sgd_aper_codigo, sgd_perm_estadistica=$sgd_perm_estadistica, "
                . "usua_admin_sistema=$usua_admin_sistema, usua_perm_trd=$usua_perm_trd, usua_perm_firma=$usua_perm_firma,"
                . "usua_perm_prestamo=$usua_perm_prestamo, usuario_publico=$usuario_publico, "
                . "usuario_reasignar=$usuario_reasignar, usua_perm_notifica=$usua_perm_notifica, "
                . "usua_perm_expediente=$usua_perm_expediente, usua_auth_ldap=$usua_auth_ldap,"
                . "perm_archi='$perm_archi', perm_vobo='$perm_vobo', perm_borrar_anexo=$perm_borrar_anexo, "
                . "perm_tipif_anexo=$perm_tipif_anexo, usua_perm_adminflujos=$usua_perm_adminflujos, "
                . "usua_exp_trd=$usua_exp_trd, usua_perm_rademail=$usua_perm_rademail,"
                . "usua_perm_accesi=$usua_perm_accesi, usua_perm_agrcontacto=$usua_perm_agrcontacto "
                . "where usua_login='$usuaLogin'";

        if (!$db->conn->Execute($sqlupdate)) {
            return new soap_fault('Servidor Orfeo', 'Registro de usuario', 'Error cargando los permisos del usuario', '');
        }
    } else {
        return new soap_fault('Servidor Orfeo', 'Registro de usuario', 'Error consultando los permisos del rol', '');
    }
    return consultarUsuario($usuaLogin);
}

/**
 * Creacion de usuarios 
 * Los parametros recibidos como vacio no seran actualizados (minimo actualiza un parametro)
 * @param string $usuaLogin username del usuario
 * @param string $usuaPasw  MD5 de la contraseña del usuario
 * @param string $usuanomb  Nombres de usuario
 * @param string $usuaEmail Correo usuario
 * @return array Arreglo con los datos del usuario
 */
function actualizarUsuario($usuaLogin, $usuaPasw, $usuanomb, $usuaEmail) {
    $usuaLogin = trim(strtoupper($usuaLogin));

    if (empty($usuaLogin)) {
        return new soap_fault('Servidor Orfeo', 'Actualización de usuario', 'Error: No ha ingresado el "usuaLogin" del usuario a actualizar', '');
    }

    global $ruta_raiz;
    $db = new ConnectionHandler($ruta_raiz);
    $sql = "SELECT usua_login from usuario where usua_login='$usuaLogin'";
    $rs = $db->getResult($sql);

    //error_log($rs->fields["usua_login"]);

    if (empty($rs->fields["usua_login"])) {
        return new soap_fault('Servidor', 'Actualización de usuario', "El usuario '$usuaLogin' no existe", '');
    }

    if (!empty($usuaPasw)) {
        $datos[] = "usua_pasw='$usuaPasw'";
    }

    if (!empty($usuanomb)) {
        $datos[] = "usua_nomb='$usuanomb'";
    }

    if (!empty($usuaEmail)) {
        $datos[] = "usua_email='$usuaEmail'";
        if (!verificarCorreo($usuaEmail)) {
            return new soap_fault('Servidor Orfeo', 'Actualización de usuario', 'Correo no valido', '');
        }
    }

    if (!empty($datos)) {
        $sqlUser = "update usuario set " . implode(' , ', $datos) . " where usua_login = '$usuaLogin' ";
    } else {
        return new soap_fault('Servidor Orfeo', 'Actualización de usuario', 'Error: No ha ingresado datos', '');
    }

    //error_log($sqlUser);
    if (!$db->conn->Execute($sqlUser)) {
        return new soap_fault('Servidor Orfeo', 'Actualización de usuario', 'Error guardando el usuario', '');
    }

    return consultarUsuario($usuaLogin);
}

/**
 * verifica si la dependencia ya tiene un usuario jefe
 * @param string $depeCodi codigo de la dependencia
 * @return array Arreglo con los datos del usuario
 */
function validarJefe($depeCodi) {
    global $ruta_raiz;
    $db = new ConnectionHandler($ruta_raiz);
    $isql = "SELECT USUA_NOMB, USUA_LOGIN FROM USUARIO WHERE DEPE_CODI = '$depeCodi' AND USUA_CODI = 1";
    $rs = $db->query($isql);
    $nombreJefe = $rs->fields["USUA_NOMB"];
    return !empty($nombreJefe);
}

/**
 * verifica si la cedula ya existe
 * @param string $usuaDoc codigo de la dependencia
 * @return array Arreglo con los datos del usuario
 */
function validarCedula($usuaDoc) {
    global $ruta_raiz;
    $db = new ConnectionHandler($ruta_raiz);
    $isql = "SELECT USUA_DOC FROM USUARIO WHERE USUA_DOC = '$usuaDoc'";
    $rsCedula = $db->query($isql);
    $cedula = $rsCedula->fields["USUA_DOC"];
    return !empty($cedula);
}

/**
 * verifica si el username del usuario ya existe
 * @param string $usuaLogin codigo de la dependencia
 * @return array Arreglo con los datos del usuario
 */
function validarLogin($usuaLogin) {
    global $ruta_raiz;
    $usuaLogin = trim(strtoupper($usuaLogin));
    $db = new ConnectionHandler($ruta_raiz);
    $isql = "SELECT usua_login FROM USUARIO WHERE usua_login = '$usuaLogin'";
    $rsLogin = $db->query($isql);
    $LoginEncon = $rsLogin->fields["usua_login"];
    return !empty($LoginEncon);
}

/**
 * Funcion que entrega todos los usuarios del sistema
 * @return Array Arreglo con todos los usuarios de Orfeo (contenidos dentro de arreglos por cada usuario)
 */
//function consultarUsuarios() {
//    global $ruta_raiz;
//    $db = new ConnectionHandler($ruta_raiz);
//    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//    $sql = "SELECT USUA_LOGIN,USUA_DOC,DEPE_CODI,CODI_NIVEL,USUA_CODI,USUA_NOMB from usuario";
//    $rs = $db->getResult($sql);
//    $i = 0;
//    while (!$rs->EOF) {
//        $salida[$i]['usua_login'] = ($rs->fields["USUA_LOGIN"]);
//        $salida[$i]['usua_doc'] = ($rs->fields["USUA_DOC"]);
//        $salida[$i]['usua_depe'] = ($rs->fields["DEPE_CODI"]);
//        //$salida[$i]['usua_nivel'] = ($rs->fields["CODI_NIVEL"]);
//        //$salida[$i]['usua_codi'] = ($rs->fields["USUA_CODI"]);
//        $salida[$i]['usua_nomb'] = ($rs->fields["USUA_NOMB"]);
//        $salida[$i]['usua_email'] = ($rs->fields["USUA_EMAIL"]);
//        $i = $i + 1;
//        $rs->MoveNext();
//    }
//
//    return $salida;
//}

/**
 * Funcion que entrega un usuario especifico 
 *
 * @param string $usuaLogin username unico del usuario a buscar
 * @return array Arreglo con los datos del usuario
 */
function consultarUsuario($usuaLogin) {
    $usuaLogin = trim(strtoupper($usuaLogin));
    global $ruta_raiz;
    $db = new ConnectionHandler($ruta_raiz);

    $sql = "SELECT USUA_LOGIN,USUA_DOC,DEPE_CODI,CODI_NIVEL,USUA_CODI,USUA_NOMB,USUA_EMAIL  FROM USUARIO 
                        WHERE  usua_login='{$usuaLogin}'";
    $rs = $db->query($sql);
    if ($rs && !$rs->EOF) {
        $salida['usua_login'] = ($rs->fields["USUA_LOGIN"]);
        $salida['usua_doc'] = ($rs->fields["USUA_DOC"]);
        $salida['usua_depe'] = ($rs->fields["DEPE_CODI"]);
        //$salida['usua_nivel'] = ($rs->fields["CODI_NIVEL"]);
        //$salida['usua_codi'] = ($rs->fields["USUA_CODI"]);
        $salida['usua_nomb'] = ($rs->fields["USUA_NOMB"]);
        $salida['usua_email'] = ($rs->fields["USUA_EMAIL"]);
    } else {
        return new soap_fault('Servidor', 'Ejecución de consulta', "El usuario $usuaLogin no existe", '');
    }
    return $salida;
}

/**
 * Funcion que entrega todos los roles 
 *
 * @return array Arreglo con los roles
 */
function consultarPerfiles() {
    global $ruta_raiz;
    $db = new ConnectionHandler($ruta_raiz);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $sql = "select codi_perfil, nomb_perfil from perfiles";
    $rs = $db->getResult($sql);
    $i = 0;
    while (!$rs->EOF) {
        $salida[$i]['codi_perfil'] = ($rs->fields["codi_perfil"]);
        $salida[$i]['nomb_perfil'] = ($rs->fields["nomb_perfil"]);
        $i = $i + 1;
        $rs->MoveNext();
    }
    return $salida;
}

/**
 * Funcion que entrega todos los tipos de radicados 
 *
 * @return array Arreglo con los tipos de radicado
 */
//function consultarTiposRadicados() {
//    global $ruta_raiz;
//    $db = new ConnectionHandler($ruta_raiz);
//    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//    $sql = "select sgd_trad_codigo,sgd_trad_descr from sgd_trad_tiporad order by sgd_trad_codigo";
//    $rs = $db->getResult($sql);
//    $i = 0;
//    while (!$rs->EOF) {
//        $salida[$i]['sgd_trad_codigo'] = ($rs->fields["sgd_trad_codigo"]);
//        $salida[$i]['sgd_trad_descr'] = ($rs->fields["sgd_trad_descr"]);
//        $i = $i + 1;
//        $rs->MoveNext();
//    }
//    return $salida;
//}

/**
 * Funcion que entrega todas las dependencias 
 *
 * @return array Arreglo con todas las dependencias
 */
function consultarDependencias() {
    global $ruta_raiz;
    $db = new ConnectionHandler($ruta_raiz);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    $sql = "select depe_codi, depe_nomb from dependencia";
    $rs = $db->getResult($sql);
    $i = 0;
    while (!$rs->EOF) {
        $salida[$i]['depe_codi'] = ($rs->fields["depe_codi"]);
        $salida[$i]['depe_nomb'] = ($rs->fields["depe_nomb"]);
        $i = $i + 1;
        $rs->MoveNext();
    }
    return $salida;
}

function verificarCorreo($correo) {
    $expresion = preg_match("(^\w+([\.-] ?\w+)*@\w+([\.-]?\w+)*(\.\w+)+)", $correo);
    return $expresion;
}

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>

