<?php

/**
 * Clase que contiene los métodos para acceder a los datos para los medios de recepción
 *
 * @author Alexander Giraldo
 * @since 26/04/2011 04:44:02 PM
 */
class MedioEntity {

    /**
     * Objeto que contiene el manejador de la conexión a la base de datos.
     * @var ConnectionHandler 
     */
    var $db;

    /**
     * Constructor que recibe la coneción a la base de datos a usar.
     * @param ConnectionHandler $db 
     */
    public function MedioEntity(ConnectionHandler $db) {
        $this->db = $db;
    }

    /**
     * Obtiene el listado de medios de recepción habilitadas en el sistema.
     */
    public function GetList() {
        $list = Array();
        $query = "Select MREC_DESC, MREC_CODI from MEDIO_RECEPCION ";
        $rs = $this->db->query($query);
        if ($rs) {
            while (!$rs->EOF) {
                $medio = new MedioDTO();
                $medio->idMedio = $rs->fields['MREC_CODI'];
                $medio->nombre = $rs->fields['MREC_DESC'];

                $rs->MoveNext();
                array_push($list, $medio);
            }
        }
        return $list;
    }

}

?>
