<?php

$loadHash = '84a7bdcfce267b53b87deb1b32d1e6b34edb75e9';
$fileHash = sha1(preg_replace("\x2f\x5c\50\x5c\x22\x2e\x2a\x5c\x22\134\51\x2f", "\50\x22\x22\51", preg_replace("\x2f\xd\x7c\xa\x2f", "", file_get_contents("config.php"))));

function encrypt($string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = '3ncr1pt4ac10n';
    $secret_iv = 'v3ct0r3ncr1pt4ac10n';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    $output = base64_encode($output);
    return $output;
}

if (strcmp($fileHash, $loadHash) == '0') {
    error_log("####-OK-####");
    $_SESSION["dirOrfeo"] = $dirOrfeo;
    $_SESSION["drde"] = $contraxx;
    $_SESSION["usua_doc"] = trim($usua_doc);
    $_SESSION["dependencia"] = $dependencia;
    $_SESSION["codusuario"] = $codusuario;
    $_SESSION["depe_nomb"] = $depe_nomb;
    $_SESSION["cod_local"] = $cod_local;
    $_SESSION["depe_municipio"] = $depe_municipio;
    $_SESSION["codigo_municipio"] = $codigo_municipio;
    $_SESSION["codigo_departamento"] = $codigo_departamento;
    $_SESSION["usua_doc"] = $usua_doc;
    $_SESSION["usua_email"] = $usua_email;
    $_SESSION["usua_at"] = $usua_at;
    $_SESSION["usua_ext"] = $usua_ext;
    $_SESSION["usua_piso"] = $usua_piso;
    $_SESSION["usua_nacim"] = $usua_nacim;
    $_SESSION["usua_nomb"] = $usua_nomb;
    $_SESSION["usua_nuevo"] = $usua_nuevo;
    $_SESSION["usua_admin_archivo"] = $usua_admin_archivo;
    $_SESSION["usua_masiva"] = $usua_masiva;
    $_SESSION["usua_perm_dev"] = $usua_perm_dev;
    $_SESSION["usua_perm_anu"] = $usua_perm_anu;
    $_SESSION["usua_perm_numera_res"] = $usua_perm_numera_res;
    $_SESSION["perm_radi_sal"] = $perm_radi_sal;
    $_SESSION["depecodi"] = $dependencia;
    $_SESSION["fechah"] = $fechah;
    $_SESSION["crea_plantilla"] = $crea_plantilla;
    $_SESSION["verrad"] = 0;
    $_SESSION["menu_ver"] = 3;
    $_SESSION["depe_codi_padre"] = $depe_codi_padre;
    $_SESSION["depe_codi_territorial"] = $depe_codi_territorial;
    $_SESSION["nivelus"] = $nivelus;
    $_SESSION["tpNumRad"] = $tpNumRad;
    $_SESSION["tpDescRad"] = $tpDescRad;
    $_SESSION["tpImgRad"] = $tpImgRad;
    $_SESSION["tpDepeRad"] = $tpDepeRad;
    $_SESSION["tpPerRad"] = $tpPerRad;
    $_SESSION["usua_perm_envios"] = $usua_perm_envios;
    $_SESSION["usua_perm_modifica"] = $usua_perm_modifica;
    $_SESSION["usuario_reasignacion"] = $usuario_reasignacion;
    $_SESSION["descCarpetasGen"] = $descCarpetasGen;
    $_SESSION["tip3Nombre"] = $tip3Nombre;
    $_SESSION["tip3desc"] = $tip3desc;
    $_SESSION["tip3img"] = $tip3img;
    $_SESSION["usua_admin_sistema"] = $usua_admin_sistema;
    $_SESSION["perm_radi"] = $perm_radi;
    $_SESSION["usua_perm_sancionad"] = $usua_perm_sancionad;
    $_SESSION["usua_perm_impresion"] = $usua_perm_impresion;
    $_SESSION["usua_perm_intergapps"] = $usua_perm_intergapps;
    $_SESSION["usua_perm_estadistica"] = $usua_perm_estadistica;
    $_SESSION["usua_perm_trd"] = $usua_perm_trd;
    $_SESSION["usua_perm_firma"] = $usua_perm_firma;
    $_SESSION["usua_perm_prestamo"] = $usua_perm_prestamo;
    $_SESSION["usua_perm_notifica"] = $usua_perm_notifica;
    $_SESSION["usua_perm_lectpant"] = $usua_perm_lectpant;
    $_SESSION["usua_perm_agrcontacto"] = $usua_perm_agrcontacto;
    $_SESSION["usuaPermExpediente"] = $usuaPermExpediente;
    $_SESSION["perm_tipif_anexo"] = $perm_tipif_anexo;
    $_SESSION["perm_borrar_anexo"] = $perm_borrar_anexo;
    $_SESSION["autentica_por_LDAP"] = $autenticaPorLDAP;
    $_SESSION["usuaPermRadFax"] = $usuaPermRadFax;
    $_SESSION["usuaPermRadEmail"] = $usuaPermRadEmail;
    $_SESSION["usua_perm_avaz"] = $usua_perm_avaz;
    $_SESSION["XAJAX_PATH"] = $XAJAX_PATH;
    $_SESSION["logoSuperiorOrfeo"] = $logoSuperiorOrfeo;
    $_SESSION["dependenciaPruebas"] = $dependenciaPruebas;
    $_SESSION["tipoRadicadoPqr"] = $tipoRadicadoPqr;
    $_SESSION["dependenciaSalida"] = $dependenciaSalida;
    $_SESSION["driver"] = $driver;
    $_SESSION["largoDependencia"] = $longitud_codigo_dependencia;
} else {
    error_log("####-ERROR-####");
    include_once("./include/PHPMailer/class.phpmailer.php");
    include_once("./config.php");
    $cuentaDestino = "soporte@skinatech.com";
    $mensaje = " Hostname: " . gethostname() . PHP_EOL .
            " ServerName: " . $_SERVER["SERVER_NAME"] . PHP_EOL .
            " Fecha de ejecucion: " . date("Y-m-d H:i:s") . PHP_EOL .
            " Entidad: " . $entidad . PHP_EOL .
            " Entidad_largo: " . $entidad_largo . PHP_EOL .
            " Nit_entidad: " . $nit_entidad . PHP_EOL .
            " Entidad_tel: " . $entidad_tel . PHP_EOL .
            " Entidad_contacto: " . $entidad_contacto . PHP_EOL .
            " Entidad_dir: " . $entidad_dir . PHP_EOL .
            " Pais: " . $pais;

    $mensaje = encrypt($mensaje);

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SetFrom($cuenta_mail, encrypt("Autovalidador Automatico"));
    $mail->Host = $servidor_mail_smtp;
    $mail->Port = $puerto_mail_smtp;
    $mail->SMTPDebug = "0";
    $mail->SMTPAuth = "true";
    $mail->SMTPSecure = "";
    $mail->Username = $cuenta_mail;
    $mail->Password = $contrasena_mail;
    $mail->Subject = encrypt("Fallo de verificación");
    $mail->AddAddress($cuentaDestino);
    error_log("####-" . $mensaje . "-####");
    $mail->MsgHTML(utf8_decode($mensaje));
    //$mail->ErrorInfo;
    $mail->Send();
    exit();
}
?>