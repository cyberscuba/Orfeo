<?php

/**
 * Clase abstracta que difine las operaciones que deben ser implementadas por un controlado
 * y las operaciones b�sicas para definir el ciclo de vida sobre el controlador.
 *
 *  En esta clase se define el controlador particular a ser llamado y el ciclo de vida que debe implementar.
 *
 * @author Alexander Giraldo
 * @since 17/07/2010 11:17:30
 * @package mvc
 */

abstract class BaseController {

    /**
     * Conexi�n a la base de datos a usar.
     * @var ConnectionHandler 
     */
    protected $db;
    
    /**
     * Contiene un arreglo de campos en los que se debe setear toda la información
     * a ser mostrada en el template
     * @var Array
     */
    public $ViewData = Array();

    /**
     * Contiene el request de la solicitud
     * @var Array
     */
    public $request;

    /**
     * Contiene los parámetros de sesión
     * @var Array
     */
    public $sessionContext;

    /**
     * Objeto utilizado para la reflexi�n
     * @var ReflectionClass
     */
    protected $reflector;

    /**
     * Obtiene una referencia al objeto controlador que debe ejecutar las acciones de negocio
     * @param string $name Nombre del controlador a construir
     * @param ConnectionHandler Se debe pasar la conexi�n a usar.
     */
    public static function controllerFactory($name, ConnectionHandler $db) {
        $reflector = new ReflectionClass($name);
        $controller = $reflector->newInstance();
        $controller->db = $db;
        $controller->reflector = $reflector;
        $controller->request = $_REQUEST;
        $controller->sessionContext = $_SESSION;

        return $controller;
    }

    /**
     * M�todo que permite ejecutar un callback en el controlador previamente construido.
     * Se debe tener en cuenta que todo callback definido en un controlador, debe recibir
     * como unico par�metro, el request.
     * @param string $callBack nombre del m�todo a utilizar
     * @param Request $request Objeto que contiene las variables del request.
     */
    public function executeCallBack($callBack, $request) {
        $method = $this->reflector->getMethod($callBack);
        $method->invoke($this, $request);
    }

    /**
     * Obtiene el usuario logueado. Si no existe sesi�n se retorna null
     */
    public function getLoggedUser() {
        if($_SESSION['userSession']) {
            return unserialize($_SESSION['userSession']);
        } else {
            return null;
        }
    }

    /**
     * Registra en la sesi�n el objeto UserDTO con la información del usuario logueado
     * @param UserDTO $user
     */
    public function registerUserInSession($user) {
        $this->sessionContext['userSession'] = serialize($user);
        $_SESSION['userSession'] = serialize($user);
    }

    /**
     * M�todo que se ejecuta antes de hacer el llamado a alg�n callback. Debe ser redefinido en la clase
     * controladora que implementa el callback
     * @param request $request
     */
    public function beforeCallback($request) {
        //debe ser implementado por cada controlador
    }

    /**
     * M�todo que se ejecuta despu�s de hacer el llamado a alg�n callback. Debe ser redefinido en la clase
     * controladora que implementa el callback
     * @param request $request
     */
    public function afterCallback($request) {
        // Debe ser implementada por cada controlador
    }

}

?>