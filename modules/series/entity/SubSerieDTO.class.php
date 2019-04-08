<?php
/**
 * Clase que contiene la definición de los atributos de las subseries en el sistema.
 *
 * @author Alexander Giraldo
 * @since 26/04/2011 05:41:26 PM
 */
class SubSerieDTO {
    
    /**
     * Identificador de la Serie a la que pertenece el item de la subserie.
     * @var int 
     */
    public $idSerie;
    
    /**
     * Código o identificador de la subserie
     * @var int 
     */
    public $code;
    
    public $name;
    
    public $startDate;
    
    public $finalDate;
    
    
}

?>
