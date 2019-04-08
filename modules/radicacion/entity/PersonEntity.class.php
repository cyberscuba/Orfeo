<?php

require_once(BASE_PATH . "/include/tx/Historico.php");

/**
 * Entidad que gestiona los datos de las personas que firman los radicados y de los terceros
 *
 * @author Alexander Giraldo
 * @since 30/04/2011 02:27:09 PM
 */
class PersonEntity {
    
    /**
     * Conexi�n a la base de datos
     * @var ConnectionHandler 
     */
    private $db;
    
    public function PersonEntity(ConnectionHandler $db) {
        $this->db = $db;
    }
    
    /**
     * Guarda la informaci�n de una persona como quien firma un radicado
     * @param type $numeroRadicado n�mero de radicado que firma
     * @param PersonDTO $person persona que 
     * @return boolean 
     */
    public function SaveAsSigner($numeroRadicado, PersonDTO $person ) {
        $query = "insert into Usr_Frm_Radicado values (
                    '" . $numeroRadicado . "',
                    '" . $person->name . "',
                    '" . $person->lastName1 . "',
                    '" . $person->factory . "',
                    '" . $person->job . "',
                    '" . $person->address . "',
                    '" . $person->phone . "',
                    '" . $person->email . "'
                  );";
        $result = $this->db->conn->query($query);
        if($result) {
            return true;
        } else {
            return false;
        }  
    }
    
    /**
     * Inserta la informaci�n de direcciones para un radicado.
     * @param type $numeroRadicado Radicado a asociar
     * @param PersonDTO $person Informaci�n de la persona remitente o destinataria del radicado.
     * @return type 
     */
    public function SaveAsDireccion($numeroRadicado, PersonDTO $person) {
        $person->idPerson = $this->db->conn->nextId("sec_dir_direcciones", $this->db->driver);     
        
        $query = "insert into SGD_DIR_DRECCIONES 
                   (SGD_DIR_CODIGO, 
                    SGD_TRD_CODIGO, 
                    SGD_DIR_NOMREMDES, 
                    SGD_DIR_DOC, 
                    MUNI_CODI, 
                    DPTO_CODI,
                    id_pais, 
                    id_cont, 
                    SGD_DOC_FUN, 
                    SGD_OEM_CODIGO, 
                    SGD_CIU_CODIGO, 
                    SGD_ESP_CODI, 
                    RADI_NUME_RADI, 
                    SGD_SEC_CODIGO,
                    SGD_DIR_DIRECCION, 
                    SGD_DIR_TELEFONO, 
                    SGD_DIR_MAIL, 
                    SGD_DIR_TIPO, 
                    SGD_ANEX_CODIGO, 
                    SGD_DIR_NOMBRE) 
                values (
                    ".GetSQLValue($person->idPerson).", 
                    '1', 
                    ".GetSQLValue($person->name." ".$person->lastName1).", 
                    ".GetSQLValue($person->document).", 
                    ".GetSQLValue($person->municipio->idMunicipio).", 
                    ".GetSQLValue($person->municipio->departamento->idDepartamento).", 
                    ".GetSQLValue($person->municipio->departamento->pais->idPais).", 
                    ".GetSQLValue($person->municipio->departamento->pais->continente->idContinente).", 
                    0, 
                    0, 
                    ".GetSQLValue($person->code).", 
                    0, 
                    '".$numeroRadicado."', 
                    0, 
                    ".GetSQLValue($person->address).", 
                    ".GetSQLValue($person->phone).", 
                    ".GetSQLValue($person->email).", 
                    1, 
                    NULL, 
                    ".GetSQLValue($person->funcionario)."
                  )";
        $result = $this->db->conn->query($query);
        if($result) {
            return true;
        } else {
            throw new RadicacionException("Error al guardar los datos del destinatario o remitente");
        }  
    }
    
    /**
     * Obtiene la informaci�n del destinatario o remitente de un radicado.
     * @param type $numRadicado N�mero de radicado.
     * @return PersonDTO 
     */
    public function GetDireccionRadicado($numRadicado) {
        $person = new PersonDTO();
        $query = "select 
                    SGD_DIR_CODIGO, 
                    SGD_TRD_CODIGO, 
                    SGD_DIR_NOMREMDES, 
                    SGD_DIR_DOC, 
                    MUNI_CODI, 
                    DPTO_CODI,
                    id_pais, 
                    id_cont, 
                    SGD_DOC_FUN, 
                    SGD_OEM_CODIGO, 
                    SGD_CIU_CODIGO, 
                    SGD_ESP_CODI, 
                    RADI_NUME_RADI, 
                    SGD_SEC_CODIGO,
                    SGD_DIR_DIRECCION, 
                    SGD_DIR_TELEFONO, 
                    SGD_DIR_MAIL, 
                    SGD_DIR_TIPO, 
                    SGD_ANEX_CODIGO, 
                    SGD_DIR_NOMBRE
                  from SGD_DIR_DRECCIONES
                  where RADI_NUME_RADI = '".$numRadicado."'";
        $rs = $this->db->conn->query($query);
        if($rs && !$rs->EOF) {
            $person->idPerson = $rs->fields["SGD_DIR_CODIGO"];
            $person->factory = $rs->fields["SGD_DIR_NOMREMDES"];
            $person->document = $rs->fields["SGD_DIR_DOC"];
            $person->document = $rs->fields["SGD_DIR_DOC"];
            
            $municipio = new MunicipioDTO();
            $municipio->idMunicipio = $rs->fields["MUNI_CODI"];
            $municipio->departamento = new DepartamentoDTO($rs->fields["DPTO_CODI"]);
            $municipio->departamento->pais = new PaisDTO($rs->fields["id_pais"]);
            $municipio->departamento->pais->continente = new ContinenteDTO($rs->fields["id_cont"]);
            $person->municipio = $municipio;
            
            $person->code = $rs->fields["SGD_CIU_CODIGO"];
            $person->address = $rs->fields["SGD_DIR_DIRECCION"];
            $person->phone = $rs->fields["SGD_DIR_TELEFONO"];
            $person->email = $rs->fields["SGD_DIR_MAIL"];
            $person->name = $rs->fields["SGD_DIR_NOMBRE"];
        }
        return $person;
    }
    
    
}

?>
