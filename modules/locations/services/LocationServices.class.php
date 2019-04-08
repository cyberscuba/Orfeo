<?php

require_once(BASE_PATH . "/modules/locations/entity/ContinenteDTO.class.php");
require_once(BASE_PATH . "/modules/locations/entity/ContinenteEntity.class.php");
require_once(BASE_PATH . "/modules/locations/entity/DepartamentoDTO.class.php");
require_once(BASE_PATH . "/modules/locations/entity/DepartamentoEntity.class.php");
require_once(BASE_PATH . "/modules/locations/entity/PaisDTO.class.php");
require_once(BASE_PATH . "/modules/locations/entity/PaisEntity.class.php");
require_once(BASE_PATH . "/modules/locations/entity/MunicipioDTO.class.php");
require_once(BASE_PATH . "/modules/locations/entity/MunicipioEntity.class.php");

/**
 * Contiene la definici�n de la l�gica de negocio
 *
 * @author Alex
 * @since 27/04/2011 11:56:07 AM
 */
class LocationServices {

    /**
     *
     * @var ConnectionHandler 
     */
    private $db;

    public function LocationServices(ConnectionHandler $db) {
        $this->db = $db;
    }

    /**
     * Obtiene el listado completo de los continentes habilitados en el sistema.
     * @return Array 
     */
    public function GetContinentes() {
        $continenteEntity = new ContinenteEntity($this->db);
        return $continenteEntity->GetList();
    }

    /**
     * Obtiene el listado de paises de un continente en particular.
     * @param int $idContinente identificador del contiete
     * @return Array 
     */
    public function GetPaises($idContinente) {
        $paisEntity = new PaisEntity($this->db);
        return $paisEntity->GetList($idContinente);
    }

    /**
     * Obtiene el listado de departamentos de un pais
     * @param int $idPais Identificador del pais
     * @return Array 
     */
    public function GetDepartamentos($idPais) {
        $departamentoEntity = new DepartamentoEntity($this->db);
        return $departamentoEntity->GetList($idPais);
    }

    /**
     * Obtiene el listado de municipios del sistema.
     * @param int $idDepartamento Identificador del departamento
     * @return array 
     */
    public function GetMunicipios($idDepartamento) {
        $municipioEntity = new MunicipioEntity($this->db);
        return $municipioEntity->GetList($idDepartamento);
    }

    /**
     * Obtiene la informaci�n del municipio con su respectivo pais, depto, y continente.
     * @param type $idMunicipio 
     * @param int $idDepartamento
     * @return MunicipioDTO
     */
    public function GetMunicipioFull($idMunicipio, $idDepartamento) {
        $municipioEntity = new MunicipioEntity($this->db);
        return $municipioEntity->GetMunicipioById($idMunicipio, $idDepartamento);
    }

}

?>
