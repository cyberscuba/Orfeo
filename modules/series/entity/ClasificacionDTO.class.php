<?php
/**
 * Clase que contiene los atributos para la gesti�n de las clasificaciones de los radicados en el sistema.
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 09:42:12 AM
 */
class ClasificacionDTO {
    
    /**
     * Identificador de la clasificaci�n en el sistema.
     * @var int
     */
    public $idClasificacion;
    
    /**
     * Nombre de la clasificaci�n
     * @var string 
     */
    public $name;
    
    /**
     * Identificador de la serie a la cual pertenece la clasificaci�n
     * @var int 
     */
    public $idSerie;
}

?>
