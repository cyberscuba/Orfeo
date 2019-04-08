<?php

/**
 * Clase que contiene los métodos para acceder a los datos de las regionales
 *
 * @author Alexander Giraldo
 * @since 26/04/2011 04:44:02 PM
 */
class RegionalEntity {

    /**
     * Objeto que contiene el manejador de la conexión a la base de datos.
     * @var ConnectionHandler 
     */
    var $db;

    /**
     * Constructor que recibe la coneción a la base de datos a usar.
     * @param ConnectionHandler $db 
     */
    public function RegionalEntity(ConnectionHandler $db) {
        $this->db = $db;
    }

    /**
     * Obtiene el listado de Regionales habilitadas en el sistema.
     */
    public function GetList() {
        $list = Array();
        $query = "select * from regional order by reg_nombre asc";
        $rs = $this->db->query($query);
        while (!$rs->EOF) {
            $regional = new RegionalDTO();
            $regional->idRegional = $rs->fields['reg_codi'];
            $regional->name = $rs->fields['reg_nombre'];
            
            $rs->MoveNext();
            array_push($list, $regional);
        }
        return $list;
    }

}

?>
