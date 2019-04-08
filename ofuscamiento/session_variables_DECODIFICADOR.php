<?php
//config
//28287132b2182a6b0a24653b25169701efb38e9c
//28287132b2182a6b0a24653b25169701efb38e9c
//configOET
//af643673f7a3d067825b43ffe1c087aba5452e00
//af643673f7a3d067825b43ffe1c087aba5452e00
//phpinfo(INFO_VARIABLES);
//http://192.168.8.26/orfeo/session_variables_DECODIFICADOR.php?data=
//cExoTWFNK0VmalpUd3BacHJUajRZMEdxbWJzakRLNUVibE1FK1hoM2N1TDcxbFV5VHNhRUtTengxQUZPR1dNQkFheTR1a0NpU0V1RjZZSDNkWllteG5QcnlXUEo0cHhGRnpHQlhzU1k2d3BxWnlYZGpsVTRDS0VpeXZSYjRqR3lwMmtvZnlhd0VmUTZuT25SY21IL3NKYk1KSm5lNEpYREpSenlGT0RHemF3MXRuemhaYTJDVW91Zzg3ZHI5Q1dkNW42MEFla1hSMDBkZ1ZSTDRKZWJwMjhwUmlmSjJDb1FpV3A5Yi9IMnhPMGJrb2M3Rjhic1oyTG94L25OamRyczRSMkF6eG56WWtqVkp2cGJ6enBLMkNoY25CeTZkTzllVElpeUJnRDNTclNmWkRpMEhxNlFYZWlDQ25GMEo2aG9wd25heDRWZlRiblJmNWFqMDdSdDFicjR0TmFIQ3g4STZ5QUVSL2Z5UTFVN3VwS2l4NlozSGNrbk9MQmpZZkEzVG5pbkp3V3FRRGp2bkxxZ1pCcFZUTFpGOWRDaEpGRXNkb3FnSVNFbitBYU4vY09McnlUM2FkdGoyMXQzQ0F5WA


switch ($_POST['accion']) {
    case "descifrar":
        $data = encrypt_decrypt($_POST['data']);
        break;
    case "cifrar":
        $fileHash = sha1(preg_replace("\x2f\x5c\50\x5c\x22\x2e\x2a\x5c\x22\134\51\x2f", "\50\x22\x22\51", preg_replace("\x2f\xd\x7c\xa\x2f", "", file_get_contents($_POST['file']))));
        break;

    default:
        break;
}

function encrypt_decrypt($string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = '3ncr1pt4ac10n';
    $secret_iv = 'v3ct0r3ncr1pt4ac10n';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    return $output;
}
?>


<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Title of the document</title>
    </head>

    <body>	
        <form  id="form_descifrar"method="post">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5">
                    <p class="boton">Texto cifrado</p>
                </div>
                <div class="col-xs-5 col-sm-5 col-md-5">
                    <input type="text"  name="data"  title="Ingrese cadena cifrada" size="150">
                </div>
            </div>
            <div class="row">
                <input  name="accion" type="submit"  value="descifrar">
            </div>
            <?php if (!empty($data)): ?>
                <div class="col-xs-5 col-sm-5 col-md-5">
                    <textarea cols="100" rows="9" ><?= $data ?></textarea>
                </div>
            <? endif; ?>
        </form>
        <br>
        <br>
        <form  id="form_cifrar"method="post">
            <div class="row">
                <div class="col-xs-5 col-sm-5 col-md-5">
                    <p class="boton">Ruta del archivo en el servidor, ruta actual ->  <?= dirname(__FILE__)?></p>
                </div>
                <div class="col-xs-5 col-sm-5 col-md-5">
                    <input type="text"  name="file"  title="ruta del archivo en el servidor" size="150">
                </div>
            </div>
            <div class="row">
                <input  name="accion" type="submit"  value="cifrar">
            </div>
            <?php if (!empty($fileHash)): ?>
                <div class="col-xs-5 col-sm-5 col-md-5">
                    <textarea cols="50" rows="2" ><?= $fileHash ?></textarea>
                </div>
            <? endif; ?>
        </form>
    </body>
</html>