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


/* ---------------------------------------------------------+
  |                       MAIN                               |
  +--------------------------------------------------------- */


if (isset($count) && $count == 0) {
    $mensajes = " Se ha descargado la versión de ORFEO5 desde orfeolibre y realizaron la siguiente configuración </br></br> " .
            " <b> Fecha de ejecucion:</b> " . date("Y-m-d H:i:s") . '</br>' .
            " <b>Razón Social:</b> " . $entidad_largo . '</br>' .
            " <b>Nit:</b> " . $nit_entidad . '</br>' .
            " <b>Telefono:</b> " . $entidad_tel . '</br>' .
            " <b>Contacto:</b> " . $entidad_contacto . '</br>' .
            " <b>Dirección:</b> " . $entidad_dir . '</br>' .
            " <b>Pais:/<b> " . $pais;

    $data = array(
        'Fecha de ejecucion ' => date("Y-m-d H:i:s"),
        'Razón Social ' => $entidad_largo,
        'Nit ' => $nit_entidad,
        'Telefono ' => $entidad_tel,
        'Contacto ' => $entidad_contacto,
        'Dirección' => $entidad_dir);
    $json = json_encode($data);
    $file = 'informacion_' . trim($entidad) . '.json';

    $fh = fopen($file, 'w');
    fwrite($fh, $json);
    fclose($fh);

    $mails = new PHPMailer();
    $mails->IsSMTP();
    $mails->SetFrom($cuenta_mail, utf8_decode("Instalación nueva"));
    $mails->Host = $servidor_mail_smtp;
    $mails->Port = $puerto_mail_smtp;
    $mails->SMTPDebug = "0";
    $mails->SMTPAuth = "true";
    $mails->SMTPSecure = "";
    $mails->Username = $cuenta_mail;
    $mails->Password = $contrasena_mail;
    $mails->Subject = utf8_decode("Información de instalación nueva");
    $mails->AddAddress($cuentaDestino);
    $mails->addAttachment($file, $file);
    $mails->MsgHTML(utf8_decode($mensajes));
    $mails->Send();
}
?>
