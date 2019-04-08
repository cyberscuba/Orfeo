<?php
/**
 * Entidad que contiene los métodos de acceso a datos para la ´gestión de las series dentro del sistema
 *
 * @author Alexander Giraldo
 * @since 26/04/2011 05:18:46 PM
 */
class SerieEntity {
    
    private $db;
    
    public function SerieEntity(ConnectionHandler $db) {
        $this->db = $db;
    }
    
    /**
     * Obtiene el listado de serieshabilitadas en el sistema ordenadas por el código.
     * No valida las fechas de validés de la serie.
     * @return array  
     */
    public function GetList() {
        $list = Array();
        $query = "select sgd_srd_codigo, sgd_srd_descrip from sgd_srd_seriesrd order by sgd_srd_codigo asc";
        $rs = $this->db->query($query);
        while (!$rs->EOF) {
            $serie = new SerieDTO();
            $serie->code = $rs->fields['sgd_srd_codigo'];
            $serie->name = utf8_encode($rs->fields['sgd_srd_descrip']);
            $serie->startDate = $rs->fields['sgd_srd_fechini'];
            $serie->endDate = $rs->fields['sgd_srd_fechfin'];
            
            $rs->MoveNext();
            array_push($list, $serie);
        }
        return $list;
    }
    
}

?>
