<?php

require_once BASE_PATH . '/modules/utils/StringUtils.php';

/**
 * Clase que contiene los m�todos necesarios para manejar la forma de importaci�n de los archivos de cada m�dulo.
 *
 * @package utils
 */
class ModulesUtils {

    /**
     * Instancia que cfontiene la informaci�n global de los m�dulos
     * @var unknown_type
     */
    private static $instance;

    /**
     * Obtiene instancia del manejador de m�dulos
     * @return ModulesUtils 
     */
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Funci�n que se encarga de generar los includes de todos los m�dulos que se encuentren instalados
     */
    public function generateModulesIncludes($directory = null, $verifymodule = false) {
        if ($directory == null) {
            $directory = BASE_PATH . "/modules/";
        }
        self::readDirectory($directory, $verifymodule, true);
    }

    private function readDirectory($directory, $verifymodule, $top = false) {
        if (!strstr($directory, "svn")) {
            $handle = opendir($directory);

            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_dir($directory . "/" . $file)) {
                        self::readDirectory($directory . "/" . $file, $verifymodule);
                    } else {
                        if (StringUtils::endsWith($file, "php")) {
                            //if ((strstr($directory, "/classes")) || (strstr($directory, "/exception")) || (strstr($directory, "/services")) || (strstr($directory, "/config"))) {
                            if (!StringUtils::endsWith("$file", ".inc.php") && !StringUtils::endsWith("$file", ".ws.php")) {
                                include_once $directory . "/" . $file;
//                                echo '**************** '.$directory . "/" . $file.'<br>';
                            }
                            //}
                        }
                    }
                }
            }

            closedir($handle);
        }
    }

}

//Se ejecuta la inclusi�n autom�ticamente
include_once(BASE_PATH . "/modules/mvc/classes/BaseController.class.php");
ModulesUtils::getInstance()->generateModulesIncludes();
?>