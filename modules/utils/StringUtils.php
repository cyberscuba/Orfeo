<?php

/**
 * Clase que contiene funciones de utilidad para el manejo de cadenas
 * @package utils
 */
class StringUtils {

    /**
     * EndsWith
     * Tests whether a text ends with the given
     * string or not.
     *
     * @param     string
     * @param     string
     * @return    bool
     */
    public static function endsWith($Haystack, $Needle) {
        return strrpos($Haystack, $Needle) === strlen($Haystack) - strlen($Needle);
    }

    public static function startsWith($Haystack, $Needle) {
        return strpos($Haystack, $Needle) === strlen($Haystack) - strlen($Needle);
    }

    public static function splitBaseName($path) {
        $name = strrchr($path, "/");
        if ($name) {
            $legth = strlen($name);
            $baseName = substr($name, 1);
            $basePath = substr($path, 0, strlen($path) - $legth);
            return array($basePath . "/", $baseName);
        } else {
            return false;
        }
    }

    public static function generatePassword() {
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        for ($i = 0; $i < 10; $i++) {
            $pass .= (substr($str, rand(0, 62), 1));
        }
        return $pass;
    }

}

/**
 * Generates an UUID
 *
 * @author     Alexander Giraldo
 * @param      string  an optional prefix
 * @return     string  the formatted uuid
 */
function uuid($prefix = '') {
    $chars = md5(uniqid(mt_rand(), true));
    $uuid = substr($chars, 0, 8) . '-';
    $uuid .= substr($chars, 8, 4) . '-';
    $uuid .= substr($chars, 12, 4) . '-';
    $uuid .= substr($chars, 16, 4) . '-';
    $uuid .= substr($chars, 20, 12);
    return $prefix . $uuid;
}

/**
 * Crear el path de carpetas si no existen
 * @param type $path 
 */
function createPath($path) {
    $folders = explode("/", $path);
    $newpath = ".";
    foreach ($folders as $folder) {
        $newpath = $newpath . "/" . $folder;
        if (!is_dir($newpath)) {
            mkdir($newpath);
            chmod($newpath, 0777);
        }
    }
}
