<?php
/**
 * Ejemplo cliente de autenticacion autmatica de orfeo
 *
 * Desc 
 *
 * @category
 * @package      SGD Orfeo
 * @subpackage   Main
 * @author       Skina Technologies SAS (http://www.skinatech.com)
 * @link         http://http://www.skinatech.com
 * @version      SVN: 1.0
 * @since        2018
 */
/* ---------------------------------------------------------+
  |                     INCLUDES                             |
  +--------------------------------------------------------- */


/* ---------------------------------------------------------+
  |                    DEFINICIONES                          |
  +--------------------------------------------------------- */

/**
 * @user        string  nombre de usuario que debe ser proporcionado desde el backEnd de Avansat
 * @password    string  MD5 de la contraseña del usuario debe ser proporcionada desde el backEnd de Avansat
 */
$user = "admon";
$password = "admin";

/* ---------------------------------------------------------+
  |                       MAIN                               |
  +--------------------------------------------------------- */

function encrypt_decrypt($string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'llaveEncriptacion';
    $secret_iv = 'vectorInicializacion';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    
    $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    $output = base64_encode($output);
    return $output;
}
?>

<head>
    <title>.:: ORFEO5 ingreso automatico::.</title>
</head>
<body>	
    <form action="login.php" method="post" name="formDatos">
        <input type="text" id='krd' name="krd" value ="<?= encrypt_decrypt($user) ?>" >
        <input type=text id="drd" name="drd" value="<?= encrypt_decrypt($password) ?>">
        <a href="#" onclick="document.formDatos.submit()">Gestion documental</a>
    </form>
</body>
</html>
