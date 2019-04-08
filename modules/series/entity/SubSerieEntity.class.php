<?php
/**
 * Entidad que contiene los métodos para el acceso a datos de las subseries.
 *
 * @author Alexander Giraldo
 * @since 26/04/2011 05:46:01 PM
 */
class SubSerieEntity {
    
    private $db;
    
    public function SubSerieEntity(ConnectionHandler $db) {
        $this->db = $db;
    }
    
    /**
     * Obtiene el listado completo de laa series que pertenecen a una serie en el sistema.
     * No se validan las fechas de inicio y fin de la susserie.
     * Este método no valida la dependencia a la cual pertenece, simplemente se obtiene el listado total de subseries
     * asociadas a una serie.
     * @param int $idSerie 
     */
    public function GetListBySerie($idSerie) {
        $list = Array();
        $query = "select * from sgd_sbrd_subserierd where sgd_srd_codigo = '".$idSerie."' order by sgd_sbrd_codigo asc";
        $rs = $this->db->query($query);
        while (!$rs->EOF) {
            $subSerie = new SubSerieDTO();
            $subSerie->idSerie = $rs->fields['sgd_srd_codigo'];
            $subSerie->code = $rs->fields['sgd_sbrd_codigo'];
            $subSerie->name = utf8_encode($rs->fields['sgd_sbrd_descrip']);
            $subSerie->startDate = $rs->fields['sgd_sbrd_fechini'];
            $subSerie->endDate = $rs->fields['sgd_sbrd_fechfin'];
            
            $rs->MoveNext();
            array_push($list, $subSerie);
        }
        return $list;
    }
    
    /**
     * Obtiene la información de la subserie a partir de su codigo y serie
     * @param type $code
     * @param type $idSerie
     * @return SubSerieDTO 
     */
    public function GetSubSerie($code, $idSerie) {
        $subSerie = new SubSerieDTO();
        $query = "select * from sgd_sbrd_subserierd where sgd_srd_codigo = '".$idSerie."' and sgd_sbrd_codigo = '".$code."' order by sgd_sbrd_codigo asc";
        $rs = $this->db->query($query);
        if (!$rs->EOF) {
            
            $subSerie->idSerie = $rs->fields['sgd_srd_codigo'];
            $subSerie->code = $rs->fields['sgd_sbrd_codigo'];
            $subSerie->name = utf8_encode($rs->fields['sgd_sbrd_descrip']);
            $subSerie->startDate = $rs->fields['sgd_sbrd_fechini'];
            $subSerie->endDate = $rs->fields['sgd_sbrd_fechfin'];
            
        }
        return $subSerie;
    }
    
    /**
     * Permite insertar un registro de subserie en la tabla
     * @param SubSerieDTO $subserie
     * @return boolean
     */
    public function CreateSubSerie(SubSerieDTO $subserie) {
        $query = "insert into sgd_sbrd_subserierd (
                        sgd_srd_codigo,
                        sgd_sbrd_codigo,
                        sgd_sbrd_descrip,
                        sgd_sbrd_fechini,
                        sgd_sbrd_fechfin,
                        sgd_sbrd_tiemag,
                        sgd_sbrd_tiemac,
                        sgd_sbrd_dispfin,
                        sgd_sbrd_soporte,
                        sgd_sbrd_procedi)
                    values(
                        '".$subserie->idSerie."',
                        '".$subserie->code."',
                        '".$subserie->name."',
                        '".$subserie->startDate."',
                        '".$subserie->finalDate."',
                        1,
                        1,
                        1,
                        1,
                        1
                    );";
        $rs = $this->db->query($query);
        if($rs) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Permite eliminar todas las subseries con un código en particular
     * @param type $code código de la subserie
     * @return type 
     */
    public function DeleteSubSeriesByCode($code) {
        $query = "delete from sgd_sbrd_subserierd where sgd_sbrd_codigo = '".$code."';";
        $rs = $this->db->query($query);
        if($rs) {
            return true;
        } else {
            return false;
        }
    }
    
}

?>
