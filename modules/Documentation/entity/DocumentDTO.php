<?php

/**
 * 
 * Documento que se encuentra relacionado a un documento requerido
 * @author Desarrolador
 *
 */
class DocumentDTO {

    /**
     * 
     * Identificador unico del documento
     * @var Int
     */
    public $IdDocument;

    /**
     * 
     * Nombre del documento relacionado
     * @var string
     */
    public $Name;

    /**
     * 
     * Descripcion del documento requerido
     * @var string
     */
    public $Description;

    /**
     * 
     * Identificador de privacidad de un documento.
     * @var bool
     */
    public $PrivateDocument;

}

?>