<?php

/**
 * Clase que contiene m�todos utilitarios para el manejo de fechas en e sistema
 *
 * @author Alexander Giraldo
 * @since 25/04/2011 12:25:25 PM
 */
class DateUtils {
    
    private static function SetTimeZone() {
        date_default_timezone_set("America/Bogota");
    }
    
    /**
     * Obtienela fecha del sistema en formato largo 
     * @param string $format Par�metro opcional que indica el formato en el que se quiere tomar la fecha. Por defecto es "d-m-Y H:i:s"
     * @return string 
     */
    public static function Now($format = null){
        if($format == null) {
            $format = "Y-m-d H:i:s";
        }
        self::SetTimeZone();
        return date($format);
    }
    
    /**
     * Obtiene el la fecha en formato Ymd
     * @return Date 
     */
    public static function NowShort(){
        self::SetTimeZone();
        return date("Ymd");
    }
    
    /**
     * Adiciona un numero de a�os  la fecha actual
     * @param type $years
     * @param string $format 
     */
    public static function AddYears($years, $format = null) {
        self::SetTimeZone();
        $nextDate = mktime(date("s"),date("i"),date("H"),date("m"),date("d"),date("Y") + $years);  
        if($format == null) {
            $format = "d-m-Y H:i:s";
        }
        return date($format, $nextDate);
    }
    
    public static function ParseDate($date, $completeTime = true, $format = "dd/mm/yyyy", $targetFormat = "d/m/Y H:i:s") {
		self::SetTimeZone();
        $df = Array();
        $df["day"] = self::GetDateField($date, $format, "dd");
        $df["month"] = self::GetDateField($date, $format, "mm");
        $df["year"] = self::GetDateField($date, $format, "yyyy");
        $df["hour"] = self::GetDateField($date, $format, "HH");
        $df["minutes"] = self::GetDateField($date, $format, "ii");
        $df["seconds"] = self::GetDateField($date, $format, "ss");
        
        if($completeTime) {
            if(!$df["hour"]) {
                $df["hour"] = date("H");
            }
            if(!$df["minutes"]) {
                $df["minutes"] = date("i");
            }
            if(!$df["seconds"]) {
                $df["seconds"] = date("s");
            }
        }
        
        $stamp = mktime($df["hour"], $df["minutes"], $df["seconds"], $df["month"], $df["day"], $df["year"]);
        return date($targetFormat, $stamp);
    }
    
    /**
     * Permite obtener el valor de un campo fecha en una cadena 
     * @param type $stringDate
     * @param type $format
     * @param type $field
     * @return type 
     */
    public static function GetDateField($stringDate, $format, $field) {
        $value = 0;
        $ini = strpos($format, $field);
        $end = strlen($field);
        if($ini !== false && $ini >= 0) { //Se manja === por que debe validar el tipo de dato
            $value = substr($stringDate, $ini, $end);
        }
        
        return $value;
    }
}

?>
