<?php

/**
 * Clase que contiene los métodos de acceso a datos para los agendamientos de radicados.
 *
 * @author Alexander Giraldo
 * @since 12/06/2012 12:26:36 PM
 */
class AgendaRadicadoEntity {

    /**
     *
     * @var ConnectionHandler 
     */
    private $db;

    public function AgendaRadicadoEntity(ConnectionHandler $db) {
        $this->db = $db;
    }

    /**
     * Permite crear un registro de agendamiento para un radicado.
     * @param AgendaRadicadoDTO $agenda
     * @return boolean que indica si se creo o no el registro de la agenda para el radicado. 
     */
    public function Create(AgendaRadicadoDTO $agenda) {
        $query = "insert into sgd_agen_agendados (
                        sgd_agen_fech,
                        sgd_agen_observacion,
                        radi_nume_radi,
                        usua_doc,
                        depe_codi,
                        sgd_agen_codigo,
                        sgd_agen_fechplazo,
                        sgd_agen_activo)
                    Values (
                        ".GetSQLValue($agenda->fechaTransaccion).",
                        ".GetSQLValue($agenda->observacion).",
                        ".GetSQLValue($agenda->numeroRadicado).",
                        ".GetSQLValue($agenda->docUsuario).",
                        ".GetSQLValue($agenda->dependenciaRadicado).",
                        ".GetSQLValue($agenda->codigo).",
                        ".GetSQLValue($agenda->fechaNotificacion).",
                        ".GetSQLValue($agenda->activo)."
                    )";
        
        $result = $this->db->conn->query($query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}

?>
