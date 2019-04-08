<?php

/**
 * Contiene la l�gica de negocio para la administraci�n de las personas que firman los radicados
 * y de los terceros destinatarios o remitentes de los radicados.
 *
 * @author Alexander Giraldo
 * @since 30/04/2011 02:35:37 PM
 */
class PersonServices {

    /**
     *
     * @var ConnectionHandler 
     */
    private $db;

    public function PersonServices(ConnectionHandler $db) {
        $this->db = $db;
    }

    /**
     * Guarda la informaci�n de la persona como quien firma un radicado.
     * @param type $numeroRadicado
     * @param PersonDTO $person
     * @return boolean 
     */
    public function SaveAsSigner($numeroRadicado, PersonDTO $person) {
        $personEntity = new PersonEntity($this->db);
        return $personEntity->SaveAsSigner($numeroRadicado, $person);
    }

    /**
     * Permite guardar la informaci�n de la persona como un destinatario o remitente del radicado
     * @param type $numeroRadicado
     * @param PersonDTO $person
     * @return type 
     */
    public function SaveAsDireccion($numeroRadicado, PersonDTO $person) {
        $personEntity = new PersonEntity($this->db);
        return $personEntity->SaveAsDireccion($numeroRadicado, $person);
    }

    /**
     * Permite obtener la informaci�n del destinatario o remitente de un radicado.
     * @param string $numRadicado
     * @return PersonDTO 
     */
    public function GetDireccionRadicado($numRadicado) {
        $personEntity = new PersonEntity($this->db);
        return $personEntity->GetDireccionRadicado($numRadicado);
    }

}

?>
