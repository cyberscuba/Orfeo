<?php

/**
 * Contiene los métodos de acceso a datos para el manejo de los Municipios en el sistema.
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 11:32:10 AM
 */
class MunicipioEntity {

    /**
     * COnexión a la base de datos.
     * @var ConnectionHandler 
     */
    private $db;

    /**
     * Constructor con la conexión a la base de datos.
     * @param ConnectionHandler $db 
     */
    public function MunicipioEntity(ConnectionHandler $db) {
        $this->db = $db;
    }

    /**
     * Obtiene el listado de municipioes en el sistema pertenecientes a un continente
     * @param int $idDepartamento
     * @return array 
     */
    public function GetList($idDepartamento) {
        $list = Array();
        $query = "SELECT MUNI_CODI,MUNI_NOMB,DPTO_CODI,ID_PAIS,ID_CONT FROM MUNICIPIO where DPTO_CODI = '" . $idDepartamento . "' order by MUNI_NOMB";
        $rs = $this->db->query($query);
        if ($rs) {
            while (!$rs->EOF) {
                $municipio = new MunicipioDTO();
                $municipio->idMunicipio = $rs->fields['MUNI_CODI'];
                $municipio->departamento = new DepartamentoDTO($rs->fields['DPTO_CODI']);
                $municipio->nombre = utf8_encode($rs->fields['MUNI_NOMB']);

                $rs->MoveNext();
                array_push($list, $municipio);
            }
        }

        return $list;
    }

    /**
     * Obtiene la información del municipio a partir de su identificador. Obtiene la información de
     * pais, continente y departamento.
     * @param int $idMunicipio 
     * @param int $idDepartamento
     * @return MunicipioDTO
     */
    public function GetMunicipioById($idMunicipio, $idDepartamento) {
        $municipio = new MunicipioDTO();
        $query = "SELECT MUNI_CODI,MUNI_NOMB,DPTO_CODI,ID_PAIS,ID_CONT FROM MUNICIPIO where MUNI_CODI = '" . $idMunicipio . "' and DPTO_CODI = '".$idDepartamento."'";
        $rs = $this->db->query($query);
        if (!$rs->EOF) {
            $municipio->idMunicipio = $rs->fields['MUNI_CODI'];
            $municipio->departamento = new DepartamentoDTO($rs->fields['DPTO_CODI']);
            $municipio->departamento->pais = new PaisDTO($rs->fields['ID_PAIS']);
            $municipio->departamento->pais->continente = new ContinenteDTO($rs->fields['ID_CONT']);
            $municipio->nombre = utf8_encode($rs->fields['MUNI_NOMB']);
        } else {
            throw new ObjectNotFoundException("No se encontó el municipio con Id " . $idMunicipio);
        }
        return $municipio;
    }

}

?>
