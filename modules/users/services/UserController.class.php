<?php
/**
 * Controlado que implementa las acciones b�sicas para la gesti�n de los usuarios del sistema.
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 10:40:06 AM
 */
class UserController extends BaseController {
    
    /**
     * obtiene el listado de usuarios activos de una dependencia y los retorna a trav�s de JSON
     * @param type $request 
     */
    public function GetUsersByDependency($request) {
        $userServices = new UserServices($this->db);
        $userList = $userServices->GetActiveUsersByDependency($_POST["dependencia"]);
        
        echo json_encode($userList);
    }
    
}

?>
