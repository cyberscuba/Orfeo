<?php

/**
 * Esta clase contiene los atributos de un tercero en el sistema, es decir, un destinatario a qui�n puede ir dirigido el documento
 * o persona que firma el documento.
 *
 * @author Alexander Giraldo
 * @since 25/04/2011 02:37:37 PM
 */
class PersonDTO {

    public $idPerson;
    public $code;
    public $document;

    /**
     * Indica el tipo de persona (Cliente, preveedor, etc)
     * @var string
     */
    public $type;

    /**
     * Nombre de la persona
     * @var string 
     */
    public $name;

    /**
     * Primer apellido de la persona
     * @var string 
     */
    public $lastName1;

    /**
     * Segundo apellido
     * @var string 
     */
    public $lastName2;

    /**
     * Empresa del usuario
     * @var string 
     */
    public $factory;
    public $job;
    public $address;
    public $phone;
    public $email;
    public $funcionario;

    /**
     * Contiene la referencia al municipio en el que est� ubicado el tercero.
     * @var MunicipioDTO 
     */
    public $municipio;

}

?>
