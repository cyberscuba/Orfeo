<?php
/**
 * Clase que contiene los métodos de acceso a datos para las dependencias
 *
 * @author Alexander Giraldo
 * @since 11/05/2011 02:16:44 PM
 */
class DependenciaEntity {
    
    /**
     *
     * @var ConnectionHandler 
     */
    private $db;
    
    public function DependenciaEntity($db) {
        $this->db = $db;
    }
    
    /**
     * Obtiene el listado de dependencias de una regional y puedo filtrat por estado.
     * Obtiene las dependencias asegurandose que existan como subserie 
     * @param type $idRegional
     * @param type $state
     * @return array 
     */
    public function GetDepeSubserieListByRegional($idRegional, $idSerie, $state = null) {
        $list = Array();
        $query = "select D.* 
                    from dependencia D
                    INNER JOIN sgd_sbrd_subserierd S ON D.depe_codi = S.sgd_sbrd_codigo
                    WHERE sgd_srd_codigo = '".$idSerie."'";
        if($idRegional > 0) {
            $query .= "  AND reg_codi = '".$idRegional."'";
        }
        if($state != null) {
            $query .= " AND D.depe_estado = '".$state."' ";
        }
        $query .= " ORDER BY D.depe_codi";
        $rs = $this->db->query($query);
        if($rs) {
            while (!$rs->EOF) {
                $dependencia = new DependenciaDTO();
                $dependencia->code = $rs->fields['depe_codi'];
                $dependencia->name = utf8_encode($rs->fields['depe_nomb']);

                $rs->MoveNext();

                array_push($list, $dependencia);
            }
        }
        return $list;
    }
    
    /**
     * Obtiene el listado de dependencias habilitadas en el sistema.
     * @return Array
     */
    public function GetList($state = null) {
        $list = Array();
        $query = "select * from dependencia ";
        if($state != null) {
            $query .= " WHERE depe_estado = '".$state."' ";
        }
        $query .= " ORDER BY depe_codi";
        $rs = $this->db->query($query);
        while (!$rs->EOF) {
            $dependencia = new DependenciaDTO();
            $dependencia->code = $rs->fields['depe_codi'];
            $dependencia->name = $rs->fields['depe_nomb'];
            
            $rs->MoveNext();
            
            array_push($list, $dependencia);
        }
        return $list;
    }
    
    /**
     * Obtiene los datos básicos de una dependencia a partir de su código.
     * @param type $code Código de la dependencia
     * @return DependenciaDTO 
     */
    public function GetByCode($code) {
        $dependencia = new DependenciaDTO();
        $query = "select * from dependencia where depe_codi = '".$code."'";
        
        $rs = $this->db->query($query);
        if ($rs && !$rs->EOF) {
            $dependencia->code = $rs->fields['depe_codi'];
            $dependencia->name = $rs->fields['depe_nomb'];
        }
        return $dependencia;
    }
    
}

?>
