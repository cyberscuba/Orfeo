<?php

define("BASE_PATH", $_SERVER["DOCUMENT_ROOT"] . '/orfeo/');
require_once(BASE_PATH . "modules/utils/ListUtils.php");
require_once(BASE_PATH . "modules/series/entity/ClasificacionDTO.class.php");

/**
 * Clase que contiene los m�todos de acceso a datos para los radicados.
 *
 * @author Alexander Giraldo
 * @since 30/04/2011 11:26:59 AM

 * By - Skinatech 
 * @author Jenny Gamez
 * @since 02/10/2018 17:06:29 PM
 * Esta clase fue modificada, ya que hay campos nuevos que son requeridos para el ORFEO 5.5
 */
class RadicadoEntity {

    /**
     *
     * @var ConnectionHandler 
     */
    private $db;

    public function RadicadoEntity(ConnectionHandler $db) {
        $this->db = $db;
    }

    /**
     * M�todo que permite crear un radicado en el sistema con los datos b�sicos
     * @param RadicadoDTO $radicado 
     */
    public function CreateRadicado(RadicadoDTO $radicado) {
        //TODO Se debe verificar el formato de fechas para evitar errores
        $query = "INSERT INTO Radicado (
                      radi_nume_radi,
                      radi_fech_radi,
                      radi_fech_ofic,
                      tdoc_codi,
                      mrec_codi,
                      radi_pais, 
                      muni_codi, 
                      carp_codi, 
                      radi_nume_hoja, 
                      radi_desc_anex, 
                      radi_nume_deri, 
                      radi_usua_actu, 
                      radi_depe_actu, 
                      radi_fech_asig,  
                      ra_asun, 
                      radi_depe_radi, 
                      radi_usua_radi,
                      codi_nivel, 
                      carp_per,
                      radi_leido, 
                      radi_tipo_deri, 
                      sgd_apli_codi, 
                      rut_archivo, 
                      radi_regional, 
                      radi_depend, 
                      radi_carpcliente, 
                      radi_seguro, 
                      cod_tipo_documento, 
                      radi_reasignado, 
                      radi_fech_reasignado, 
                      cod_clasificacion, 
                      rad_pclave, 
                      radi_nume_radi_asoc,
                      id_pais
                  ) values (
                      '" . $radicado->numeroRadicado . "',
                      '" . $radicado->fechaRadicacion . "',
                      " . GetSQLValue($radicado->fechaOficina) . ", 
                      " . GetSQLValue($radicado->trd->tipoDocumento) . ",  
                      '" . $radicado->medioRecepcion . "',
                      " . GetSQLValue($radicado->municipio->departamento->pais->idPais) . ", 
                      " . GetSQLValue($radicado->municipio->idMunicipio) . ", 
                      '" . $radicado->carpCodi . "',
                      " . GetSQLValue($radicado->numeroHojas) . ",
                      " . GetSQLValue($radicado->descrAnexo) . ",
                      0, 
                      " . GetSQLValue($radicado->usuarioActual->code) . ", 
                      " . GetSQLValue($radicado->usuarioActual->dependencia->code) . ",
                      " . GetSQLValue($radicado->fechaAsignacion) . ",
                      " . GetSQLValue($radicado->asunto) . ", 
                      " . GetSQLValue($radicado->usuarioRadicacion->dependencia->code) . ",                       
                      " . GetSQLValue($radicado->usuarioRadicacion->code) . ", 
                      " . GetSQLValue($radicado->codigoNivel) . ",  
                      '" . $radicado->carpetaPersonal . "',  
                      '" . $radicado->leido . "',  
                      " . GetSQLValue($radicado->radi_tipo_deri) . ",  
                      " . GetSQLValue($radicado->sgd_apli_codi) . ",  
                      " . GetSQLValue($radicado->radiPath) . ",  
                      " . GetSQLValue($radicado->codreg) . ",  
                      0,
                      0, 
                      '" . $radicado->seguro . "',  
                      " . GetSQLValue($radicado->trd->tipoDocumento) . ",  
                      " . GetSQLValue($radicado->reasignado) . ",  
                      " . GetSQLValue($radicado->fechaReasignacion) . ",  
                      " . GetSQLValue($radicado->clasificacion->idClasificacion) . ",  
                      " . GetSQLValue($radicado->palabrasClave) . ",  
                      " . GetSQLValue($radicado->radicadoPadre) . ",
                      170    
                  )";

        $result = $this->db->conn->query($query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * By - skinatech
     * 02/11/2018
     * Método que permite crear el radicado en la tabla sgd_dt_radicado, para los dias de termino del documento
     * @param RadicadoDTO $radicado 
     */
    public function CreateDiasRadicado(RadicadoDTO $radicado) {
        $query = "INSERT INTO sgd_dt_radicado (
                    radi_nume_radi,
                    dias_termino
                ) values (
                    '" . $radicado->numeroRadicado . "',
                    5
                )";

        $result = $this->db->conn->query($query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Obtiene el c�digo de la TRD seg�n los datos de la serie y subserie seleccionados para un radicado.
     * @param type $CodSerie C�digo de la serie
     * @param type $CodDependencia C�digo de la Dependencia o subserie. Se asumen como iguales
     * @param type $CodTdocumento Tiop de documento
     * @return type 
     */
    function GetTRDCode($CodSerie, $CodDependencia, $CodSubserie, $CodTdocumento) {
        $query = "select SGD_MRD_CODIGO  
                from SGD_MRD_MATRIRD 
                where DEPE_CODI = '$CodDependencia'
                and SGD_SRD_CODIGO = '$CodSerie' 
                and SGD_SBRD_CODIGO = '$CodSubserie' 
                and SGD_TPR_CODIGO = '$CodTdocumento'";

        $rs = $this->db->conn->query($query);
        if ($rs) {
            return $rs->fields["SGD_MRD_CODIGO"];
        }
    }

    /**
     * Obtiene el codigo de la TRD, si no hay resultados, se procede a insertar
     * @param type $CodSerie C�digo de la serie
     * @param type $CodDependencia C�digo de la Dependencia o subserie. Se asumen como iguales
     * @param tyoe $CodSubserie Codigo de la subserie ingresada
     * @param type $CodTdocumento Tiop de documento
     * @return type 
     */
    function CrearTRDCode($CodSerie, $CodDependencia, $CodSubserie, $CodTdocumento) { 
        /**
        * By - skinatech
        * 02/11/2018
        * Se consulta el ultimo consecutivo generado para las trds, segun este consecutivo
        * se inserta la información de la TRD al radicado que se esta generando.   
        */     
        $isqlCount = "select max(sgd_mrd_codigo) as NUMREGT from sgd_mrd_matrird";
        $this->db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rsC = $this->db->conn->query($isqlCount);
        $numreg = $rsC->fields["NUMREGT"];
        $numreg = $numreg + 1;
        $fechahoy =  date('Y-m-d');

        $query = "insert into sgd_mrd_matrird "
                . "("
                . "sgd_mrd_codigo"
                . ",depe_codi"
                . ",sgd_srd_codigo"
                . ",sgd_sbrd_codigo"
                . ",sgd_tpr_codigo"
                . ",sgd_mrd_fechini"
                . ",sgd_mrd_esta"
                . ") "
                . " values ($numreg,'$CodDependencia',$CodSerie,$CodSubserie,$CodTdocumento,'$fechahoy',1)";
        $rs = $this->db->conn->query($query);
        if ($rs) {
            $matriz = $this->GetTRDCode($CodSerie, $CodDependencia, $CodSubserie, $CodTdocumento);
            return $matriz;
        }
    }

    /**
     * Permite asignar un C�digo de TRD a un Radicado
     * @param type $NumRadicado N�mero de radicado
     * @param type $codTRD C�digo de la TRD obtenido seg�n la serie, subserie, etc
     * @param type $CodDependencia Dependencia del usuario que asign� la TRD
     * @param type $CodUsuario C�digo del usuario
     * @param type $DocUsuario N�mero de documento del usuario
     * @return bool 
     */
    public function AssignTrd($NumRadicado, $codTRD, $CodDependencia, $CodUsuario, $DocUsuario) {
        //TODO Hacer en una transaccion
        //Se eliminan los registros de TRD del radicado
        $query = "delete from sgd_rdf_retdocf where radi_nume_radi = '" . $NumRadicado . "'";
        $result = $this->db->conn->query($query);

        $query = "insert into sgd_rdf_retdocf values ('" . $codTRD . "','" . $NumRadicado . "','" . $CodDependencia . "','" . $CodUsuario . "','" . $DocUsuario . "',getdate())";
        $result = $this->db->conn->query($query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Actualiza la clasificaci�n del radicado y el tipo de documento
     * @param type $numRadicado
     * @param type $idClasificacion 
     * @param int $tipoDocumento
     */
    public function UpdateClasificacionRadicado($numRadicado, $idClasificacion, $tipoDocumento) {
        $query = "update radicado set cod_clasificacion = '" . $idClasificacion . "', cod_tipo_documento = '" . $tipoDocumento . "' where radi_nume_radi = '" . $numRadicado . "'";
        $result = $this->db->conn->query($query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Obtiene los datos de la TRD asignada a un radicado
     * @param type $numRadicado
     * @return TrdDTO 
     */
    public function GetTRDRadicado($numRadicado) {
        $trd = new TrdDTO();
        $query = "SELECT r.sgd_mrd_codigo, r.depe_codi, s.sgd_srd_codigo, s.sgd_srd_descrip, su.sgd_sbrd_codigo, su.sgd_sbrd_descrip, t.sgd_tpr_codigo, t.sgd_tpr_descrip 
                    FROM sgd_rdf_retdocf r, sgd_mrd_matrird m, sgd_srd_seriesrd s, sgd_sbrd_subserierd su, sgd_tpr_tpdcumento t 
                    WHERE r.sgd_mrd_codigo = m.sgd_mrd_codigo 
                    AND r.RADI_NUME_RADI = '" . $numRadicado . "' 
                    AND s.sgd_srd_codigo = m.sgd_srd_codigo 
                    AND su.sgd_srd_codigo = m.sgd_srd_codigo 
                    AND su.sgd_sbrd_codigo = m.sgd_sbrd_codigo 
                    AND t.sgd_tpr_codigo = m.sgd_tpr_codigo";
        $rs = $this->db->conn->query($query);
        if ($rs && !$rs->EOF) {
            $trd->code = $rs->fields["sgd_mrd_codigo"];
            $trd->dependencia = $rs->fields["depe_codi"];
            $trd->serie = $rs->fields["sgd_srd_codigo"];
            $trd->serieName = $rs->fields["sgd_srd_descrip"];
            $trd->subserie = $rs->fields["sgd_sbrd_codigo"];
            $trd->subserieName = $rs->fields["sgd_sbrd_descrip"];
            $trd->tipoDocumento = $rs->fields["sgd_tpr_codigo"];
            $trd->tipoDocumentoName = $rs->fields["sgd_tpr_descrip"];
        }
        return $trd;
    }

    /**
     * Obtiene la informaci�n de un radicado a partir de su n�mero de radicado.
     * @param type $numRadicado
     * @return RadicadoDTO 
     */
    public function GetRadicadoByNumber($numRadicado) {
        $radicado = new RadicadoDTO();
        $query = "SELECT cast(radi_nume_radi as varchar) as radi_nume_radi,
                      radi_fech_radi,
                      radi_fech_ofic,
                      tdoc_codi,
                      radi_pais, 
                      muni_codi, 
                      carp_codi, 
                      radi_nume_hoja, 
                      radi_desc_anex, 
                      radi_nume_deri, 
                      radi_usua_actu, 
                      radi_depe_actu, 
                      radi_fech_asig,  
                      ra_asun, 
                      radi_depe_radi, 
                      radi_usua_radi,
                      codi_nivel, 
                      carp_per,
                      radi_leido, 
                      radi_tipo_deri, 
                      sgd_apli_codi, 
                      radi_path, 
                      radi_regional, 
                      radi_depend, 
                      radi_carpcliente, 
                      radi_seguro, 
                      cod_tipo_documento, 
                      radi_reasignado, 
                      radi_fech_reasignado, 
                      cod_clasificacion, 
                      rad_pclave, 
                      radi_nume_radi_asoc
                 FROM Radicado
                 WHERE radi_nume_radi = '" . $numRadicado . "'";
        $rs = $this->db->conn->query($query);
        if ($rs && !$rs->EOF) {
            $radicado->numeroRadicado = $rs->fields["radi_nume_radi"];
            $radicado->fechaRadicacion = $rs->fields["radi_fech_radi"];
            $radicado->fechaOficina = $rs->fields["radi_fech_ofic"];
            $radicado->fechaOficina = $rs->fields["radi_fech_ofic"];

            $municipio = new MunicipioDTO();
            $municipio->idMunicipio = $rs->fields["muni_codi"];
            $radicado->municipio = $municipio;

            $radicado->carpCodi = $rs->fields["carp_codi"];
            $radicado->numeroHojas = $rs->fields["radi_nume_hoja"];
            $radicado->descrAnexo = $rs->fields["radi_desc_anex"];
            $radicado->descrAnexo = $rs->fields["radi_desc_anex"];

            $usuarioActual = new UserDTO();
            $usuarioActual->code = $rs->fields["radi_usua_actu"];
            $usuarioActual->dependencia->code = $rs->fields["radi_depe_actu"];
            $radicado->usuarioActual = $usuarioActual;

            $radicado->fechaAsignacion = $rs->fields["radi_fech_asig"];
            $radicado->asunto = $rs->fields["ra_asun"];

            $usuarioRadicacion = new UserDTO();
            $usuarioRadicacion->code = $rs->fields["radi_usua_radi"];
            $usuarioRadicacion->dependencia->code = $rs->fields["radi_depe_radi"];
            $radicado->usuarioRadicacion = $usuarioRadicacion;

            $radicado->codigoNivel = $rs->fields["codi_nivel"];
            $radicado->carpetaPersonal = $rs->fields["carp_per"];
            $radicado->leido = $rs->fields["radi_leido"];
            $radicado->radi_tipo_deri = $rs->fields["radi_tipo_deri"];
            $radicado->radiPath = $rs->fields["radi_path"];
            $radicado->codreg = $rs->fields["radi_regional"];
            $radicado->codreg = $rs->fields["radi_regional"];
            $radicado->seguro = $rs->fields["radi_seguro"];

            $trd = new TrdDTO();
            $trd->tipoDocumento = $rs->fields["cod_tipo_documento"];
            $radicado->trd = $trd;

            $radicado->reasignado = $rs->fields["radi_reasignado"];
            $radicado->fechaReasignacion = $rs->fields["radi_fech_reasignado"];

            $clasificacion = new ClasificacionDTO();
            $clasificacion->idClasificacion = $rs->fields["cod_clasificacion"];
            $radicado->clasificacion = $clasificacion;

            $radicado->palabrasClave = $rs->fields["rad_pclave"];
            $radicado->radicadoPadre = $rs->fields["radi_nume_radi_asoc"];
        } else {
            throw new ObjectNotFoundException("No se encontr� el radicado n�mero " . $numRadicado);
        }
        return $radicado;
    }

    /**
     * Permite generar el listado de radicados para el reporte de seguimiento
     * @param type $NumRadicado
     * @param type $FecIniSeg
     * @param type $FecFinSeg
     * @param type $Dependencia
     * @param type $SegRadUsuario
     * @param type $TipRadicado
     * @return array 
     */
    public function ReporteSeguimientoRadicados($NumRadicado, $FecIniSeg, $FecFinSeg, $Dependencia, $SegRadUsuario, $TipRadicado) {
        $datos = array();
        if ($FecIniSeg <> NULL && $FecFinSeg <> NULL) {
            $query = "set dateformat dmy; ";
        }
        $query .= "select radi_nume_radi as 'num_radi',radi_path,radi_fech_radi,
                    (select USUA_NOMB from usuario us where us.usua_codi=rad.radi_usua_actu and us.depe_codi=radi_depe_actu) as us_actual,
                    (select USUA_NOMB from usuario us where us.usua_codi=rad.radi_usua_radi and us.depe_codi=radi_depe_radi) as us_rad,				
                    (select carp_desc as NomRegistro from carpeta where carp_codi = rad.carp_codi) as carpeta,				
                    dep.DEPE_NOMB as dep_actu,
                    dep2.DEPE_NOMB as dep_rad,
                    radi_usua_radi,radi_depe_radi,
                    carp_codi,radi_regional,radi_carpcliente,repues_radi,radi_nume_radi_asoc 
                    from radicado rad 
                    join dependencia dep on rad.radi_depe_actu=dep.depe_codi 
                    join dependencia dep2 on rad.radi_depe_radi=dep2.depe_codi
                    where radi_nume_radi=radi_nume_radi 
                    and radi_depe_actu != 999 "; //Se valida que no est� archivado
        if ($NumRadicado <> NULL) {
            $query .= " and radi_nume_radi='" . $NumRadicado . "' ";
        }
        if ($FecIniSeg <> NULL && $FecFinSeg <> NULL) {
            $query .= " and radi_fech_radi between '$FecIniSeg' and '$FecFinSeg' ";
        }
        if ($Dependencia <> NULL) {
            $query .= " and radi_depe_actu=$Dependencia ";
        }
        if ($SegRadUsuario <> NULL) {
            $query .= " and radi_usua_actu=$SegRadUsuario ";
        }
        $query .= " and SUBSTRING (cast(radi_nume_radi as Varchar(18)) , len(radi_nume_radi), 1) = $TipRadicado ";

        $rs = $this->db->conn->query($query);
        while ($rs && !$rs->EOF) {
            $radicado = new RadicadoDTO();
            $radicado->numeroRadicado = $rs->fields['num_radi'];
            $radicado->fechaRadicacion = $rs->fields['radi_fech_radi'];
            $radicado->usuarioActual = new UserDTO();
            $radicado->usuarioActual->name = $rs->fields['us_actual'];
            $radicado->usuarioActual->dependencia = new DependenciaDTO();
            $radicado->usuarioActual->dependencia->name = $rs->fields['dep_actu'];
            $radicado->usuarioRadicacion = new UserDTO();
            $radicado->usuarioRadicacion->name = $rs->fields['us_rad'];
            $radicado->usuarioRadicacion->dependencia = new DependenciaDTO();
            $radicado->usuarioRadicacion->dependencia->name = $rs->fields['dep_rad'];
            $radicado->carpCodi = $rs->fields['carpeta'];
            $radicado->respuesta = $rs->fields['repues_radi'];
            $radicado->radicadoPadre = $rs->fields['radi_nume_radi_asoc'];

            $rs->MoveNext();
            array_push($datos, $radicado);
        }

        return $datos;
    }

    /**
     * Obtiene el n�mero de radicados que un usuario tiene actualmente asignados. Si se enc�a el tipo, discrimina por radicados de entrada o salida.
     * @param type $userCode C�digo del usuario que se desea consultar.
     * @param type $depeCode C�digo de la dependencia que se desea consultar.
     * @param type $tipo Par�metro opcional que permite enviar si se desean ver los radicados de entrada o salida.
     *  Se pueden enviar los valores (1= Salida, 2= Entrada).
     * @return int retorna el numero de radicados a asignar o false si no se pudo ejecutar la consulta. 
     */
    public function CountRadicadosByUser($userCode, $depeCode, $tipo = null) {
        $query = "SELECT COUNT(radi_nume_radi) as cant FROM Radicado
                  WHERE radi_usua_actu = '" . $userCode . "'
                  AND radi_depe_actu = '" . $depeCode . "' ";
        if ($ipo != null) {
            $query .= " AND radi_nume_radi like('%" . $tipo . "')";
        }

        $rs = $this->db->conn->query($query);
        if ($rs) {
            return $rs->fields['cant'];
        } else {
            return false;
        }
    }

    /**
     * Permite contar los radicados informados que tiene un usuario
     * @param type $userCode
     * @param type $depeCode
     * @param type $tipo
     * @return type 
     */
    public function CountRadicadosInformadosByUser($userCode, $depeCode, $tipo = null) {
        $query = "SELECT COUNT(radi_nume_radi) as cant FROM Informados
                  WHERE usua_codi = '" . $userCode . "'
                  AND depe_codi = '" . $depeCode . "' ";
        if ($ipo != null) {
            $query .= " AND radi_nume_radi like('%" . $tipo . "')";
        }

        $rs = $this->db->conn->query($query);
        if ($rs) {
            return $rs->fields['cant'];
        } else {
            return false;
        }
    }

    /**
     * Este m�todo permite cambiar el usuario actual de los radicados que tenga actualmente un usuario.
     * @param type $usuaOrigen
     * @param type $depeOrigen
     * @param type $usuaDestino
     * @param type $depeDestino
     * @return boolean indica si se realiz� o no la actualizaci�n del usuario actual. 
     */
    public function MoveRadicados($usuaOrigen, $depeOrigen, $usuaDestino, $depeDestino) {
        $query = "UPDATE Radicado Set 
                    radi_usua_actu = '" . $usuaDestino . "',
                    radi_depe_actu = '" . $depeDestino . "',
                    radi_leido = 0
                  WHERE
                    radi_usua_actu = '" . $usuaOrigen . "'
                    AND radi_depe_actu = '" . $depeOrigen . "'";
        $rs = $this->db->conn->query($query);
        if ($rs) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Mueve los radicados informados de un usuario a otro
     * @param type $usuaOrigen
     * @param type $depeOrigen
     * @param type $usuaDestino
     * @param type $depeDestino
     * @return type 
     */
    public function MoveRadicadosInformados($usuaOrigen, $depeOrigen, $usuaDestino, $depeDestino) {
        $query = "UPDATE Informados Set 
                    usua_codi = '" . $usuaDestino . "',
                    depe_codi = '" . $depeDestino . "',
                    info_leido = 0
                  WHERE
                    usua_codi = '" . $usuaOrigen . "'
                    AND depe_codi = '" . $depeOrigen . "'";
        $rs = $this->db->conn->query($query);
        if ($rs) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Permite actualizar el archivo a un radicado.
     * @param string $numeroRadicado
     * @param string $pathArchivo
     * @return boolean
     */
    public function ActualizarAdjunto($numeroRadicado, $pathArchivo) {
        $query = "UPDATE Radicado Set 
                    radi_path = '" . $pathArchivo . "'
                  WHERE
                    radi_nume_radi = '" . $numeroRadicado . "'";
        $rs = $this->db->conn->query($query);
        if ($rs) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Crea un anexo a un radicado
     */

    public function createAnexo(AnexoDTO $anexo) {
        $query = "insert into 
				ANEXOS
				(sgd_rem_destino,
				anex_radi_nume,
				anex_codigo,
				anex_tipo,
				anex_tamano,
				anex_solo_lect,
				anex_creador,
				anex_desc,
				anex_numero,
				anex_nomb_archivo,
				anex_borrado,
				anex_salida,
				sgd_dir_tipo,
				anex_depe_creador,
				sgd_tpr_codigo,
				anex_fech_anex,
				SGD_APLI_CODI,
				SGD_TRAD_CODIGO, 
				SGD_EXP_NUMERO)
	           values (
			   " . $anexo->destinatario . ",
			   " . $anexo->numeroRadicado . ",
			   '" . $anexo->codigo . "',
			   " . $anexo->tipoAnexo . ",
			   " . $anexo->tamano . ",
			   '" . $anexo->soloLectura . "',
			   '" . $anexo->creador . "',
			   '" . $anexo->descripcion . "',
			   " . $anexo->numero . ",
			   '" . $anexo->nombreArchivo . "',
			   'N',
			   " . $anexo->salida . ",
			   " . $anexo->dirTipo . ",
			   " . $anexo->dependenciaCreador . ",
			   " . GetSQLValue($anexo->tipoRadicacion) . ",
			   GETDATE(),
			   " . GetSQLValue($anexo->SGD_APLI_CODI) . ",
			   " . GetSQLValue($anexo->SGD_TRAD_CODIGO) . ", 
			   " . GetSQLValue($anexo->SGD_EXP_NUMERO) . " ) ";
        //echo $query;

        $rs = $this->db->conn->query($query);
        if ($rs) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Obtiene el codigo de una anexo dependiendo de la extension
     * @param type $ext extension del anexo
     * @return codigo tipo de anexo 
     * */
    function getTipoAnexoByExt($ext) {
        $query = "select anex_tipo_codi as tipo  from anexos_tipo
             where anex_tipo_ext='" . $ext . "'";
        $rs = $this->db->conn->query($query);
        if ($rs) {
            return $rs->fields['tipo'];
        }
    }

}

?>
