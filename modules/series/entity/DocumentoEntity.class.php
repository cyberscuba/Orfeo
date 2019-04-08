<?php

/**
 * Contiene los métodos para el acceso a datos de los tipos de documentos del sistema.
 *
 * @author Alexander Giraldo
 * @since 26/04/2011 07:02:56 PM
 */
class DocumentoEntity {

    private $db;

    public function DocumentoEntity(ConnectionHandler $db) {
        $this->db = $db;
    }

    /**
     * Obtiene el listado de tipos de documentos ACTIVOS para una serie y subserie.
     * Se asume la dependencia como la misma subserie.
     * @param type $idSerie
     * @param type $idSubSerie 
     */
    public function GetDocumentsBySubserie($idSerie, $idSubSerie) {
        $list = Array();
        $query = "select distinct (t.sgd_tpr_descrip) as detalle , t.sgd_tpr_codigo 
                  from sgd_mrd_matrird m 
                  inner join sgd_tpr_tpdcumento t ON m.depe_codi = '" . $idSubSerie . "' and m.sgd_mrd_esta = '1' and m.sgd_srd_codigo = '" . $idSerie . "' and m.sgd_sbrd_codigo = '" . $idSubSerie . "' 
                  and m.sgd_tpr_codigo=t.sgd_tpr_codigo 
                  order by t.sgd_tpr_codigo";
        $rs = $this->db->query($query);
        if ($rs) {
            while (!$rs->EOF) {
                $documento = new DocumentoDTO();
                $documento->code = $rs->fields['sgd_tpr_codigo'];
                $documento->name = utf8_encode($rs->fields['detalle']);

                $rs->MoveNext();
                array_push($list, $documento);
            }
        }

        return $list;
    }

}

?>
