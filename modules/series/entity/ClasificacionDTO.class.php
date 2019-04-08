<?php
/**
 * Clase que contiene los atributos para la gestión de las clasificaciones de los radicados en el sistema.
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 09:42:12 AM
 */
class ClasificacionDTO {
    
    /**
     * Identificador de la clasificación en el sistema.
     * @var int
     */
    public $idClasificacion;
    
    /**
     * Nombre de la clasificación
     * @var string 
     */
    public $name;
    
    /**
     * Identificador de la serie a la cual pertenece la clasificación
     * @var int 
     */
    public $idSerie;
}

?>
