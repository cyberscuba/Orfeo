<?php

/**
 * Clase que contiene los métodos de acceso a datos para las clasificaciones de los radicados.
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 09:44:08 AM
 */
class ClasificacionEntity {

    /**
     * Conexión a la base de datos.
     * @var ConnectionHandler 
     */
    private $db;

    public function ClasificacionEntity(ConnectionHandler $db) {
        $this->db = $db;
    }

    /**
     * Obtiene el listado de clasificaciones habilitadas en el sistema para una serie en particular.
     * @param int $idSerie 
     */
    public function GetClasificacionsBySerie($idSerie) {
        $list = Array();
        $query = "SELECT * FROM cla_serie WHERE COD_SERIE = '" . $idSerie . "' ORDER BY nom_claserie";
        $rs = $this->db->query($query);
        while (!$rs->EOF) {
            $clasificacion = new ClasificacionDTO();
            $clasificacion->idClasificacion = $rs->fields['cod_claserie'];
            $clasificacion->name = utf8_encode($rs->fields['nom_claserie']);
            $clasificacion->idSerie = $rs->fields['cod_serie'];

            $rs->MoveNext();
            array_push($list, $clasificacion);
        }
        return $list;
    }

    /**
     * Permite buscar una clasificacion por codigo de serie y clasificación
     * @param int $idSerie 
     */
    public function SearchClasificacionsByCode($idSerie, $codClaSerie) {
        $list = Array();
        $query = "SELECT * FROM cla_serie WHERE 1=1";
        if ($idSerie > 0) {
            $query .= " AND cod_serie = '" . $idSerie . "' ";
        }

        $query .= " AND cod_claserie = '" . $codClaSerie . "' ORDER BY nom_claserie";
        $rs = $this->db->query($query);
        while (!$rs->EOF) {
            $clasificacion = new ClasificacionDTO();
            $clasificacion->idClasificacion = $rs->fields['cod_claserie'];
            $clasificacion->name = utf8_encode($rs->fields['nom_claserie']);
            $clasificacion->idSerie = $rs->fields['cod_serie'];

            $rs->MoveNext();
            array_push($list, $clasificacion);
        }
        return $list;
    }

    /**
     * Permite obtener un clasificado por medio de la serie a la que pertenece y el codigo de la clasificación.
     * @param $idSerie id de la serie a la que pertenece.
     * @param $codClaSerie codigo de la clasificación.
     */
    public function GetClasificationsBySerieAndCode($idSerie, $codClaSerie) {
        $query = "SELECT * FROM cla_serie WHERE 1=1";
        if ($idSerie > 0) {
            $query .= " AND cod_serie = '" . $idSerie . "' ";
        }

        $query .= " AND cod_claserie = '" . $codClaSerie . "' ORDER BY nom_claserie";
        $rs = $this->db->query($query);
        $clasificacion = new ClasificacionDTO();
        try {
            $clasificacion->idClasificacion = $rs->fields['cod_claserie'];
            $clasificacion->name = utf8_encode($rs->fields['nom_claserie']);
            $clasificacion->idSerie = $rs->fields['cod_serie'];
        } catch (Exception $ex) {
            throw new ObjectNotFoundException("No se encontró el usuario con login " . $login);
        }
        return $clasificacion;
    }

    /**
     * Permite insertar la información de un nuevo registro de clasificación.
     * @param $clasifications nueva clasificación.
     */
    public function SaveClasificationsSerie(ClasificacionDTO &$clasifications) {
        $query = "INSERT INTO cla_serie (cod_claserie, nom_claserie, cod_serie) VALUES ('" . $clasifications->idClasificacion . "',
    	'" . $clasifications->name . "', '" . $clasifications->idSerie . "')";
        try {
            $rs = $this->db->query($query);
            if($rs) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $ex) {
            throw new Exception("No se ha insertado la nueva clasificación.", $ex);
        }
    }

    /**
     * 
     * Actualiza la informacion de un radicado con respercto al clasificado al que corresponde.
     * @param $newClasificationCode Nuevo codigo de clasificacion.
     * @param $oldClasificationCode Antiguo codigo de clasificacion
     * @param $serieCode Serie a la  que pertenece.
     */
    public function UpdateClasificationInRadicade($newClasificationCode, $oldClasificationCode, $serieCode) {
        $query = "update Radicado set Radicado.cod_clasificacion = '" . $newClasificationCode . "'
				from Radicado R 
				INNER JOIN sgd_rdf_retdocf S ON S.radi_nume_radi = R.radi_nume_radi
				INNER JOIN SGD_MRD_MATRIRD M ON M.SGD_MRD_CODIGO = S.sgd_mrd_codigo
				where R.cod_clasificacion = '" . $oldClasificationCode . "'
				AND M.sgd_srd_codigo = '" . $serieCode . "'";
        try {
            $rs = $this->db->query($query);
            if($rs) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $ex) {
            throw new Exception("No se ha actualizado la información de los radicados.", $ex);
        }
    }

    /**
     * 
     * Permite actualizar la informacion de un clasificado.
     * @param $clasificationDTO
     * @param $oldClasificationCode
     */
    public function UpdateClasification(ClasificacionDTO $clasificationDTO, $oldClasificationCode) {
        $query = "UPDATE cla_serie SET
                        cod_claserie = '" . $clasificationDTO->idClasificacion . "',
    			nom_claserie = '" . $clasificationDTO->name . "'
                        WHERE cod_serie = '" . $clasificationDTO->idSerie . "' AND cod_claserie = '" . $oldClasificationCode . "'";
        try {
            $rs = $this->db->query($query);
            if($rs) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $ex) {
            throw new Exception("No se ha actualizado la información del clasificado.", $ex);
        }
    }

    /**
     * Permite eliminar una clasificacion de la base de datos.
     * @param $clasificationCode Codigo de la clasificacion que se desea eliminar.
     * @param $idSerie Id de la serie a la que pertenece la clasificación.
     */
    public function DeleteClasification($clasificationCode, $idSerie) {
        $query = "select count(R.radi_nume_radi) as Cant
                    from Radicado R 
                    INNER JOIN sgd_rdf_retdocf S ON S.radi_nume_radi = R.radi_nume_radi
                    INNER JOIN SGD_MRD_MATRIRD M ON M.SGD_MRD_CODIGO = S.sgd_mrd_codigo
                    where R.cod_clasificacion = " . $clasificationCode . "
                    AND M.sgd_srd_codigo = " . $idSerie;
        $rs = $this->db->query($query);
        if ($rs->fields['Cant'] == 0) {
            $query = "DELETE FROM cla_serie WHERE cod_serie = '" . $idSerie . "' AND cod_claserie = '" . $clasificationCode . "'";
            try {
                $rs = $this->db->query($query);
                if($rs) {
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $ex) {
                throw new Exceptioneption("No se ha eliminado el clasificado verifique que no tenga radicados asignados.", $ex);
            }
        } else {
            throw new Exception("La clasificacion tiene radicados asignados.");
        }
    }

    /**
     * Busca la clasificación
     * @param int idSerie
     * @param string search texto a buscar
     */
    public function SearchClasificacions($idSerie, $search) {
        $list = Array();
        $query = "SELECT * FROM cla_serie WHERE COD_SERIE = '" . $idSerie . "' and nom_claserie like('%" . $search . "%') ORDER BY nom_claserie";
        $rs = $this->db->query($query);
        while (!$rs->EOF) {
            $clasificacion = new ClasificacionDTO();
            $clasificacion->idClasificacion = $rs->fields['cod_claserie'];
            $clasificacion->name = utf8_encode($rs->fields['nom_claserie']);
            $clasificacion->idSerie = $rs->fields['cod_serie'];

            $rs->MoveNext();
            array_push($list, $clasificacion);
        }
        return $list;
    }

    /**
     * Obtiene los datos de la clasificación de un radicado.
     * @param type $numRadicado
     * @return ClasificacionDTO 
     */
    public function GetClasificacionsByRadicado($numRadicado) {
        $clasificacion = new ClasificacionDTO();
        $query = "select C.*
                  from cla_serie C
                  INNER JOIN radicado R ON R.cod_clasificacion = C.cod_claserie
                  WHERE R.radi_nume_radi = '" . $numRadicado . "'";
        $rs = $this->db->query($query);
        if (!$rs->EOF) {
            $clasificacion->idClasificacion = $rs->fields['cod_claserie'];
            $clasificacion->name = utf8_encode($rs->fields['nom_claserie']);
            $clasificacion->idSerie = $rs->fields['cod_serie'];
        }
        return $clasificacion;
    }

}

?>
