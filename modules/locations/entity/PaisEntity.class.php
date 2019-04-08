<?php
/**
 * Contiene los métodos de acceso a datos para el manejo de los Paises en el sistema.
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 11:32:10 AM
 */
class PaisEntity {
    
    /**
     * COnexión a la base de datos.
     * @var ConnectionHandler 
     */
    private $db;
    
    /**
     * Constructor con la conexión a la base de datos.
     * @param ConnectionHandler $db 
     */
    public function PaisEntity(ConnectionHandler $db) {
        $this->db = $db;
    }
    
    /**
     * Obtiene el listado de paises en el sistema pertenecientes a un continente
     * @param int $idContinente
     * @return array 
     */
    public function GetList($idContinente) {
        $list = Array();
        $query = "SELECT * FROM sgd_def_Paises ORDER BY nombre_pais";
        $rs = $this->db->query($query);
        while (!$rs->EOF) {
            $pais = new PaisDTO();
            $pais->idPais = $rs->fields['id_pais'];
            $pais->continente = new ContinenteDTO($rs->fields['id_cont']);
            $pais->nombre = utf8_encode($rs->fields['nombre_pais']);
            
            $rs->MoveNext();
            array_push($list, $pais);
        }
        return $list;
    }
    
}

?>
