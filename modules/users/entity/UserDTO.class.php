<?php

require_once(BASE_PATH . "/modules/series/entity/DependenciaEntity.class.php");
require_once(BASE_PATH . "/modules/series/entity/DependenciaDTO.class.php");

/**
 * Clase que define los atributos de un usuario dentro del sistema.
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 10:11:11 AM
 */
class UserDTO {
    
    /**
     * Constructor por defecto
     */
    public function UserDTO() {
        $dependencia = new DependenciaDTO();
    }
    
    public $code;
    /**
     * Contiene la referencia a la dependencia que pertenece el usuario
     * @var DependenciaDTO 
     */
    public $dependencia;
    public $login;
    public $password;
    public $creationDate;
    public $state;
    public $name;
    public $document;
    public $email;
    /**
     * Nivel de privacidad del usuario
     * @var int 
     */
    public $privacyLevel;
    /**
     * indica si tiene o no habilitado el permiso para radicar documentos
     * @var int 
     */
    public $permRadication;
    /**
     * indica si es usuario administrador
     * @var int 
     */
    public $isAdmin;
    
    /**
     * indica si es un usuario nuevo.
     * @var int 
     */
    public $isNew;
    /**
     * contiene la descripci�n de la �ltima sesi�n iniciada por el usuario.
     * @var string 
     */
    public $session;
    
    public $sessionDate;
    
    /**
     * 
     * Indica si tiene permiso de ver la documentaci�n de clientes.
     * @var unknown_type
     */
    public $usuarVerDocs;
    
    
}

?>
