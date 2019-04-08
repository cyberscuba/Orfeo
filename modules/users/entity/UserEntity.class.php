<?php

require_once(BASE_PATH . "/modules/users/entity/UserDTO.class.php");

/**
 * Contiene los m�todos de acceso a datos para la entidad de Usuarios en el sistema.
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 10:21:26 AM
 */
class UserEntity {

    /**
     * Conexi�n a la base de datos.
     * @var ConnectionHandler 
     */
    private $db;

    public function UserEntity(ConnectionHandler $db) {
        $this->db = $db;
    }

    /**
     * Obtiene el listado de usuarios pertenecientes a una dependencia en particular.
     * @param type $dependencia c�digo de la dependencia de la cual se desean obtener los usuarios
     * @param int $state Par�metro opcional que indica si se quieren obtener los usuarios que est�n en un estado determinado. Si no se envia nada no se filtra por estado.
     */
    public function GetUsersByDependency($dependencia, $state = NULL) {
        $list = Array();
        $query = "SELECT * FROM Usuario WHERE depe_codi = '" . $dependencia . "' ";
        if ($state != NULL) {
            $query .= " AND usua_esta = '" . $state . "'";
        }
        $query .= " ORDER BY usua_nomb";

        $rs = $this->db->query($query);
        if ($rs) {
            while (!$rs->EOF) {
                $user = new UserDTO();
                $user->code = $rs->fields['usua_codi'];
                $dependencia = new DependenciaDTO();
                $dependencia->code = $rs->fields['depe_codi'];
                $user->dependencia = $dependencia;
                $user->login = $rs->fields['usua_login'];
                $user->creationDate = $rs->fields['usua_fech_crea'];
                $user->password = $rs->fields['usua_pasw'];
                $user->state = $rs->fields['usua_esta'];
                $user->name = utf8_encode($rs->fields['usua_nomb']);
                $user->permRadication = $rs->fields['perm_radi'];
                $user->isAdmin = $rs->fields['usua_admin'];
                $user->isNew = $rs->fields['usua_nuevo'];
                $user->document = $rs->fields['usua_doc'];
                $user->privacyLevel = $rs->fields['codi_nivel'];
                $user->session = $rs->fields['usua_sesion'];
                $user->sessionDate = $rs->fields['usua_fech_sesion'];
                $user->email = $rs->fields['usua_email'];
                $user->usuarVerDocs = $rs->fields['usua_ver_docs'];

                $rs->MoveNext();
                array_push($list, $user);
            }
        }
        return $list;
    }
    
    /**
     * Obtiene la informaci�n de un usuario a partir de su c�digo y de su dependencia
     * @param type $userCode c�digo del usuario
     * @param type $depeCode c�digo de la dependencia
     * @return UserDTO 
     */
    public function GetUser($userCode, $depeCode) {
        $user = new UserDTO();
        $query = "SELECT * FROM Usuario WHERE usua_codi = '".$userCode."' and depe_codi = '" . $depeCode . "' ";

        $rs = $this->db->query($query);
        if ($rs) {
            $user->code = $rs->fields['usua_codi'];
            $dependencia = new DependenciaDTO();
            $dependencia->code = $rs->fields['depe_codi'];
            $user->dependencia = $dependencia;
            $user->login = $rs->fields['usua_login'];
            $user->creationDate = $rs->fields['usua_fech_crea'];
            $user->password = $rs->fields['usua_pasw'];
            $user->state = $rs->fields['usua_esta'];
            $user->name = utf8_encode($rs->fields['usua_nomb']);
            $user->permRadication = $rs->fields['perm_radi'];
            $user->isAdmin = $rs->fields['usua_admin'];
            $user->isNew = $rs->fields['usua_nuevo'];
            $user->document = $rs->fields['usua_doc'];
            $user->privacyLevel = $rs->fields['codi_nivel'];
            $user->session = $rs->fields['usua_sesion'];
            $user->sessionDate = $rs->fields['usua_fech_sesion'];
            $user->email = $rs->fields['usua_email'];
            $user->usuarVerDocs = $rs->fields['usua_ver_docs'];
            
        } else {
            throw new ObjectNotFoundException("No se encontr� el usuario con codigo ". $userCode ." y dependencia ". $depeCode);
        }
        
        return $user;
    }
    
    /**
     * Obtiene la informaci�n de un usuario atrav�s de su login
     * @param type $login
     * @return UserDTO 
     */
    public function GetUserByLogin($login) {
        $user = new UserDTO();
        $query = "SELECT * FROM Usuario WHERE usua_login = '".$login."'";

        $rs = $this->db->query($query);
        if ($rs) {
            $user->code = $rs->fields['usua_codi'];
            $dependencia = new DependenciaDTO();
            $dependencia->code = $rs->fields['depe_codi'];
            $user->dependencia = $dependencia;
            $user->login = $rs->fields['usua_login'];
            $user->creationDate = $rs->fields['usua_fech_crea'];
            $user->password = $rs->fields['usua_pasw'];
            $user->state = $rs->fields['usua_esta'];
            $user->name = utf8_encode($rs->fields['usua_nomb']);
            $user->permRadication = $rs->fields['perm_radi'];
            $user->isAdmin = $rs->fields['usua_admin'];
            $user->isNew = $rs->fields['usua_nuevo'];
            $user->document = $rs->fields['usua_doc'];
            $user->privacyLevel = $rs->fields['codi_nivel'];
            $user->session = $rs->fields['usua_sesion'];
            $user->sessionDate = $rs->fields['usua_fech_sesion'];
            $user->email = $rs->fields['usua_email'];
            $user->usuarVerDocs = $rs->fields['usua_ver_docs'];
            
        }else {
            throw new ObjectNotFoundException("No se encontr� el usuario con login ". $login);
        }
        
        return $user;
    }

	
    /**
     * Obtiene la informaci�n de un usuario a trav�s de su email
     * @param type $email
     * @return UserDTO 
     */
    public function GetUserByEmail($email) {
        $user = new UserDTO();
        $query = "SELECT * FROM Usuario WHERE usua_email = '".$email."'";

        $rs = $this->db->query($query);
        if ($rs) {
            $user->code = $rs->fields['usua_codi'];
            $dependencia = new DependenciaDTO();
            $dependencia->code = $rs->fields['depe_codi'];
            $user->dependencia = $dependencia;
            $user->login = $rs->fields['usua_login'];
            $user->creationDate = $rs->fields['usua_fech_crea'];
            $user->password = $rs->fields['usua_pasw'];
            $user->state = $rs->fields['usua_esta'];
            $user->name = utf8_encode($rs->fields['usua_nomb']);
            $user->permRadication = $rs->fields['perm_radi'];
            $user->isAdmin = $rs->fields['usua_admin'];
            $user->isNew = $rs->fields['usua_nuevo'];
            $user->document = $rs->fields['usua_doc'];
            $user->privacyLevel = $rs->fields['codi_nivel'];
            $user->session = $rs->fields['usua_sesion'];
            $user->sessionDate = $rs->fields['usua_fech_sesion'];
            $user->email = $rs->fields['usua_email'];
            $user->usuarVerDocs = $rs->fields['usua_ver_docs'];
            
        }else {
            throw new ObjectNotFoundException("No se encontr� el usuario con login ". $login);
        }
        
        return $user;
    }
	
}

?>
