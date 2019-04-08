<?php
/**
 * Clase que contiene la lógica de negocio para los agendamiento de radicados.
 *
 * @author Alexander Giraldo
 * @since 12/06/2012 01:04:40 PM
 */
class AgendaRadicadoServices {
    /**
     *
     * @var ConnectionHandler 
     */
    private $db;

    public function AgendaRadicadoServices(ConnectionHandler $db) {
        $this->db = $db;
    }
    
    /**
     * Método que permite registrar un agendamiento de un radicado
     * @param AgendaRadicadoDTO $agenda Información de la agenda del radicado
     * @param RadicadoDTO $radicado información del radicado a aagendar
     * @return boolean 
     */
    public function AgendarRadicado(AgendaRadicadoDTO $agenda, RadicadoDTO $radicado) {
        $agendaEntity = new AgendaRadicadoEntity($this->db);
        if($agendaEntity->Create($agenda)) 
        {
            $historico = new Historico($this->db); //Inserta en Histórico
            $arrRadicados[0] = $radicado->numeroRadicado;
            $historico->insertarHistorico($arrRadicados, $radicado->usuarioRadicacion->dependencia->code, $radicado->usuarioRadicacion->code, $radicado->usuarioActual->dependencia->code, $radicado->usuarioActual->code, $agenda->observacion, Historico::$TX_AGENDA_RADICADO);
            return true;
        }
        return false;
    }
}


?>
