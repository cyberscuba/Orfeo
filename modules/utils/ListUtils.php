<?php

/**
 * Obtiene una lista con las opciones del nivel de privacidad de un radicado.
 * @return array 
 */
function GetPrivacyOptions() {
    $options[0] = array("id" => 1, "name" => "Público");
    $options[1] = array("id" => 2, "name" => "Reservado");
    $options[2] = array("id" => 3, "name" => "Confidencial");
    
    return $options;
}

/**
 * Obtiene null o el valor para insertar en SQL
 * @param type $value
 * @return type 
 */
function GetSQLValue($value) {
    if($value == '') {
        return "NULL";
    } else {
        if(is_numeric($value)) {
            return $value;
        } else {
            return "'".$value."'";
        }
    }
}

?>
