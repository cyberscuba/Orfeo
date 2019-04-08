<?php
/**
 * Clase que contiene los atributos de una agendación de un radicado.
 *
 * @author Alexander Giraldo
 * @since 12/06/2012 12:17:36 PM
 */
class AgendaRadicadoDTO {
    
    /**
     * Fecha en que se realiza el agendamiento 
     * @var date 
     */
    public $fechaTransaccion;
    
    /**
     * Número de radicado agendado.
     * @var string  
     */
    public $numeroRadicado;
    
    /**
     * Observaciones del motivo de agendamiento del radicado.
     * @var string 
     */
    public $observacion;
    
    /**
     * Documento del usuario al cual se agenda el radicado. Debe ser el mismo a quien esta asignado el radicado.
     * @var string 
     */
    public $docUsuario;
    
    /**
     * Código de dependencia del radicado
     * @var int 
     */
    public $dependenciaRadicado;
    
    /**
     * código de agendamiento del radicado. No se usa.
     * @var string 
     */
    public $codigo;
    
    /**
     * fecha en la cual se notificará al usuario.
     * @var date 
     */
    public $fechaNotificacion;
    
    /**
     * indica si aún está activo el agendamiento del radicado. 
     * @var boolean 
     */
    public $activo;
    
}

?>
