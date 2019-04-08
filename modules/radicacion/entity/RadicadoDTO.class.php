<?php
/**
 * Clase que contiene los atributos de un radicado en el medio de persistencia según la nueva lógica de 
 * asignación de radicados a usuarios específicos.
 *
 * @author Alexander Giraldo
 * @since 25/04/2011 09:58:30 AM
 */
class RadicadoDTO {
    
    /**
     * Número de radicado
     * @var string
     */
    public $numeroRadicado;
    /**
     * Fecha de radicación del documento
     * @var date
     */
    public $fechaRadicacion;
    
    /**
     *
     * @var Fecha ingresada en el formulario como la fecha del radicado. 
     */
    public $fechaOficina;
    
    /**
     * Indica si es un radicado de entrada o de salida.
     * @var int 
     */
    public $tipoRadicado;
    
    /**
     * Código del medio de recepción del radicado.
     * @var int 
     */
    public $medioRecepcion;
    
    /**
     *Tiene una referencia l municipio donde se encuentra ubicado el radicado con el país.
     * @var MunicipioDTO
     */
    public $municipio;
    
    /**
     * Código de la carpeta en la que se guarda el radicado.
     */
    public $codigoCarpeta;
    /**
     * Código del estado
     */
    public $estado;
    /**
     * Código del radicado padre del radicado actual.
     * @var string 
     */
    public $radicadoPadre;
    /**
     * texto que contiene la descripción del anexo
     * @var string 
     */
    public $descrAnexo;
    /**
     * Número de hojas del documento
     * @var int 
     */
    public $numeroHojas;
    
    /**
     * Asunto del documento.
     * @var string
     */
    public $asunto;
    
    /**
     * Contiene la información del destinatario al cual está dirigido el radicado.
     * @var PersonDTO 
     */
    public $destinatario;
    
    /**
     * Persona que firma el documento
     * @var PersonDTO 
     */
    public $signerPerson;
    
    /**
     * Usuario que tiene actualmente asignado el documento.
     * @var UserDTO 
     */
    public $usuarioActual;
    
    /**
     * fecha de asignación del radicado al usuario actual
     * @var date 
     */
    public $fechaAsignacion;
    
    /**
     * Usuario a quién se radicó el documento
     * @var UserDTO 
     */
    public $usuarioRadicacion;
    
    /**
     * Código de nivel de seguridad de radicado
     * @var int 
     */
    public $codigoNivel;
    
    /**
     * código de la carpeta personal del usuario
     * @var int 
     */
    public $carpetaPersonal;
    
    /**
     * Indica si fue o no leido el radicado.
     * @var type 
     */
    public $leido;
    
    /**
     * nivel de seguridad del radicado
     * @var int 
     */
    public $seguro;
        
    public $carpCodi;
    
    public $sgd_apli_codi;
    
    /**
     * Indica si debe o no tener respuesta
     * @var int 
     */
    public $respuesta;
    
    /**
     * Indica si esta o no reasignado el radicado
     * @var int 
     */
    public $reasignado;
    
    /**
     * fecha en que es reasignado el radicado
     * @var date 
     */
    public $fechaReasignacion;
    
    /**
     * contiene la clasificación asignada al radicado
     * @var ClasificacionDTO 
     */
    public $clasificacion;
    
    /**
     * Contiene las palabras clave para buscar el documento posteriormente.
     * @var string 
     */
    public $palabrasClave;
    
    /**
     * Contiene la definición de la TRD Seleccionada para el radicado.
     * @var TrdDTO 
     */
    public $trd;
    
    public $radi_tipo_deri;
    
    public $radiPath;
    public $rutarchivo = "A";
    public $codreg;
    
}

?>
