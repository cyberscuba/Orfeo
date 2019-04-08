<?php
/**
 * Contiene los métodos de acceso a datos para el manejo de los continentes en el sistema.
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 11:32:10 AM
 */
class ContinenteEntity {
    
    /**
     * COnexión a la base de datos.
     * @var ConnectionHandler 
     */
    private $db;
    
    /**
     * Constructor con la conexión a la base de datos.
     * @param ConnectionHandler $db 
     */
    public function ContinenteEntity(ConnectionHandler $db) {
        $this->db = $db;
    }
    
    /**
     * Obtiene el listado completo de continentes
     */
    public function GetList() {
        $list = Array();
        $query = "SELECT * FROM sgd_def_continentes ORDER BY nombre_cont";
        $rs = $this->db->query($query);
        while (!$rs->EOF) {
            $continente = new ContinenteDTO();
            $continente->idContinente = $rs->fields['id_cont'];
            $continente->nombre = utf8_encode($rs->fields['nombre_cont']);
            
            $rs->MoveNext();
            array_push($list, $continente);
        }
        return $list;
    }
    
}

?>
