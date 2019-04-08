<?php
/**
 * Contiene los métodos de acceso a datos para el manejo de los Departamentoes en el sistema.
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 11:32:10 AM
 */
class DepartamentoEntity {
    
    /**
     * COnexión a la base de datos.
     * @var ConnectionHandler 
     */
    private $db;
    
    /**
     * Constructor con la conexión a la base de datos.
     * @param ConnectionHandler $db 
     */
    public function DepartamentoEntity(ConnectionHandler $db) {
        $this->db = $db;
    }
    
    /**
     * Obtiene el listado de departamentoes en el sistema pertenecientes a un continente
     * @param int $idPais
     * @return array 
     */
    public function GetList($idPais) {
        $list = Array();
        $query = "SELECT * FROM departamento ORDER BY dpto_nomb";
        $rs = $this->db->query($query);
        while (!$rs->EOF) {
            $departamento = new DepartamentoDTO();
            $departamento->idDepartamento = $rs->fields['dpto_codi'];
            $departamento->pais = new PaisDTO($rs->fields['id_pais']);
            $departamento->nombre = utf8_encode($rs->fields['dpto_nomb']);
            
            $rs->MoveNext();
            array_push($list, $departamento);
        }
        return $list;
    }
    
}

?>
