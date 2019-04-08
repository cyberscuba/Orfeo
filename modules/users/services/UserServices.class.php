<?php

require_once(BASE_PATH . "/modules/users/entity/UserEntity.class.php");

/**
 * Clase que contiene la l�gica de negocio para gestionar los usuarios en el sistema.
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 10:36:04 AM
 */
class UserServices {

    /**
     * Conexi�n a la base de datos.
     * @var ConnectionHandler 
     */
    private $db;

    public function UserServices(ConnectionHandler $db) {
        $this->db = $db;
    }

    /**
     * Obtiene el listado de usuarios activos pertenecientes a una dependencia
     * @param int $dependency
     * @return Array 
     */
    public function GetActiveUsersByDependency($dependency) {
        $userEntity = new UserEntity($this->db);
        return $userEntity->GetUsersByDependency($dependency, 1);
    }

    /**
     * Obtiene la informaci�n de un usuario a partir de su c�digo y dependencia
     * @param type $userCode C�digo del usuario
     * @param type $depeCode C�digo de la dependencia
     * @return UserDTO 
     */
    public function GetUser($userCode, $depeCode) {
        $userEntity = new UserEntity($this->db);
        return $userEntity->GetUser($userCode, $depeCode);
    }

    /**
     * Obtiene la informaci�n del usuario a trav�s de su login
     * @param type $login
     * @return UserDTO 
     */
    public function GetUserByLogin($login) {
        $userEntity = new UserEntity($this->db);
        return $userEntity->GetUserByLogin($login);
    }

    /**
     * Obtiene la informaci�n del usuario a trav�s de su email
     * @param type $email
     * @return UserDTO 
     */
    public function GetUserByEmail($email) {
        $userEntity = new UserEntity($this->db);
        return $userEntity->GetUserByEmail($email);
    }

    /**
     * Permite obtener la informaci�n del usuario jefe de una dependencia
     * @param string $depeCode 
     * @return UserDTO
     */
    public function GetUserJefe($depeCode) {
        $userEntity = new UserEntity($this->db);
        return $userEntity->GetUser(1, $depeCode); //Los jefes sol,los que tienen c�digo 1
    }

}

?>
