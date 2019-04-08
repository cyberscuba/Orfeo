<?php

/**
 * Contiene funcionalidades para la generaci�n de reportes en el sistema.
 *
 * @author Alexander Giraldo
 * @since 11/05/2011 02:46:59 PM
 */
class ReportServices {

    /**
     *
     * @var ConnectionHandler 
     */
    private $db;

    public function ReportServices(ConnectionHandler $db) {
        $this->db = $db;
    }

    /**
     * Obtiene el listado de radicados que un usuario tiene en su bandeja de entrada de acuerdo a los criterios de b�squeda.
     * @param type $NumRadicado
     * @param type $FecIniSeg
     * @param type $FecFinSeg
     * @param type $Dependencia
     * @param type $SegRadUsuario
     * @param type $TipRadicado 
     */
    public function ReporteSeguimientoRadicados($NumRadicado, $FecIniSeg, $FecFinSeg, $Dependencia, $SegRadUsuario, $TipRadicado) {
        $radicadoEntity = new RadicadoEntity($this->db);
        $radicadosList = $radicadoEntity->ReporteSeguimientoRadicados($NumRadicado, $FecIniSeg, $FecFinSeg, $Dependencia, $SegRadUsuario, $TipRadicado);
        $radicadoServices = new RadicadoServices($this->db);
        foreach ($radicadosList as $radicado) {
            $radicado->trd = $radicadoServices->GetTRDRadicado($radicado->numeroRadicado);
        }
        return $radicadosList;
    }

}

?>
