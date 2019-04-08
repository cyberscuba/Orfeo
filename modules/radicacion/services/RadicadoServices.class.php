<?php
/**
 * Contiene la l�gica de negocio necesaria para gestionar los radicados
 * @author Alexander Giraldo
 * @since 30/04/2011 12:18:29 PM
 
 * By - Skinatech 
 * @author Jenny Gamez
 * @since 02/10/2018 17:06:29 PM
 * Esta clase fue modificada, ya que hay campos nuevos que son requeridos para el ORFEO 5.5
 */
class RadicadoServices {

    /**
     *
     * @var ConnectionHandler 
     */
    private $db;
    private $estructuraRad;

    public function RadicadoServices(ConnectionHandler $db) {
        $this->db = $db;
        $this->noDigitosRad = 6;
        $this->estructuraRad = date("Ymd");;
    }

    /**
     * Obtiene la informaci�n b�sica del radicado a partir de su n�mero de radicado.
     * Adicionalmente obtiene la informaci�n del remitente y persona que firma el documento, as� como su TRD asignada.
     * @param string $numRadicado 
     * @return RadicadoDTO
     */
    public function GetRadicadoByNumber($numRadicado) {
        $radicadoEntity = new RadicadoEntity($this->db);
        $radicado = $radicadoEntity->GetRadicadoByNumber($numRadicado);
        $personServices = new PersonServices($this->db);
        $radicado->destinatario = $personServices->GetDireccionRadicado($numRadicado);
        $radicado->trd = $this->GetTRDRadicado($numRadicado);

        return $radicado;
    }

    /**
     * Obtiene el id de la secuencia
     * 
     * @param int $tipoRadicado Tipo de radicado (Entrada o salida)
     * @param int $codDependencia c�digo de la dependencia de radicado
     */
    public function GenerateNumeroRadicado($fecha, $tipoRadicado, $codDependencia) {

        $newRadicado = "";
        $SecName = "SECR_TP$tipoRadicado" . "_" . $codDependencia;

        /* Se coloca un ciclo para asegurarse que el numuero de radicado quede con 18 digitos */
        $cont = 0;
        while (strlen(trim($newRadicado)) != 18 && $cont < 5) {
            $secNew = $this->db->conn->nextId($SecName, $this->db->driver);
            if ($secNew == 0) {
                throw new SequenceNumberException("No se gener� el n�mero de secuencia");
            }
            if ($fecha == "") {
                throw new SequenceNumberException("Fecha de radicado " . $fecha . " inv�lida");
            }

            $fechaRadicado = DateUtils::GetDateField($fecha, RADI_FORMAT_DATE, "yyyy") . "" . DateUtils::GetDateField($fecha, RADI_FORMAT_DATE, "mm") . "" . DateUtils::GetDateField($fecha, RADI_FORMAT_DATE, "dd");
            $newRadicado = $this->estructuraRad . $codDependencia . str_pad($secNew, $this->noDigitosRad, "0", STR_PAD_LEFT)  . $tipoRadicado;
            $cont++;
        }

        if (strlen(trim($newRadicado)) != 18) {
            throw new SequenceNumberException("Error al generar el numero de radicado: " + $newRadicado);
        }

        return $newRadicado;
    }

    /**
     * Permite asignar la TRD a un radicado
     * @param RadicadoDTO $radicado
     * @return type 
     */
    public function AssignTRDToRadicado(RadicadoDTO $radicado) {
        $radicadoEntity = new RadicadoEntity($this->db);
        /**
        * By - Skinatech
        * Consulta que la dependencia tenga asignada la trd seleccionada (Serie, subserie, tipo doc)
        * Esto regresa el identificador de la tabla donde se guardan las TRDs
        */
        $TRDCode = $radicadoEntity->GetTRDCode($radicado->trd->serie, $radicado->trd->dependencia, $radicado->trd->subserie, $radicado->trd->tipoDocumento);
        
        /**
        * By - Skinatech
        * Se valida que la serie/subserie/tipo documental existan para la dependencia, si no existe
        * se procede a agregar esa clasificación a la TRD.
        */
        if($TRDCode == ''){
           $TRDCode =  $radicadoEntity->CrearTRDCode($radicado->trd->serie, $radicado->trd->dependencia, $radicado->trd->subserie,$radicado->trd->tipoDocumento);
        }        
        return $radicadoEntity->AssignTrd($radicado->numeroRadicado, $TRDCode, $radicado->trd->dependencia, $radicado->usuarioRadicacion->code, $radicado->usuarioRadicacion->document);
    }

    /**
     * Obtiene los datos de la TRD asignada a un radicado.
     * @param type $numRadicado
     * @return TrdDTO 
     */
    public function GetTRDRadicado($numRadicado) {
        $radicadoEntity = new RadicadoEntity($this->db);
        return $radicadoEntity->GetTRDRadicado($numRadicado);
    }

    /**
     * Permite actualizar la clasificaci�n de un radicado. y el tipo de documento
     * @param type $numRadicado
     * @param type $idClasificacion 
     * @param int $tipoDocumento Tipo de documento que es actualizado tambien
     */
    public function UpdateClasificacionRadicado($numRadicado, $idClasificacion, $tipoDocumento) {
        $radicadoEntity = new RadicadoEntity($this->db);
        return $radicadoEntity->UpdateClasificacionRadicado($numRadicado, $idClasificacion, $tipoDocumento);
    }

    /**
     * Permite guardar los datos de la TRD del radicado y la clasificaci�n del mismo
     * @param RadicadoDTO $radicado 
     */
    public function SaveTRDRadicado(RadicadoDTO $radicado) {
        if ($this->AssignTRDToRadicado($radicado)) {
            return $this->UpdateClasificacionRadicado($radicado->numeroRadicado, $radicado->clasificacion->idClasificacion, $radicado->trd->tipoDocumento);
        }
    }

    /**
     * Crea un radicado en el sistema a partir de la informaci�n ingresada. Este m�todo genera autom�ticamente el n�mero de radicado
     * y lo asigna y si se inserta de forma correcta se retorna eln�mero de radicado generado
     * @param RadicadoDTO $radicado
     * @return type 
     */
    public function CreateRadicado(RadicadoDTO &$radicado) {
        //TODO Implementar transacci�n
        $radicadoEntity = new RadicadoEntity($this->db);
        try {
            //TODO Asignar nivel de seguridad al radicado
            $radicado->numeroRadicado = $this->GenerateNumeroRadicado($radicado->fechaRadicacion, $radicado->tipoRadicado, $radicado->usuarioRadicacion->dependencia->code);
            if ($radicadoEntity->CreateRadicado($radicado)) {
                $radicadoEntity->CreateDiasRadicado($radicado); // Guarda el radicado con los dias de termino
                $this->AssignTRDToRadicado($radicado); //Se asigna la TRD al documento
                $personServices = new PersonServices($this->db);
                $personServices->SaveAsDireccion($radicado->numeroRadicado, $radicado->destinatario);
                $personServices->SaveAsSigner($radicado->numeroRadicado, $radicado->signerPerson); // Se guarda quien firma el documento
                //TODO Guardar las personas o direcciones (Remitente o destinatario)
                $historico = new Historico($this->db); //Inserta en Hist�rico
                $arrRadicados[0] = "'".$radicado->numeroRadicado."'";
                $historico->insertarHistorico($arrRadicados, $radicado->usuarioRadicacion->dependencia->code, $radicado->usuarioRadicacion->code, $radicado->usuarioActual->dependencia->code, $radicado->usuarioActual->code, " ", 2);
                
                //TODO Se deben enviar notificaciones v�a E-mail
                return $radicado->numeroRadicado;
            } else {
                throw new RadicacionException("No se guardaron los datos del radicado.");
            }
        } catch (Exception $e) {
            throw new RadicacionException("Ha ocurrido un error al guardar el radicado. " . $e->getMessage());
        }
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
        $radicadoEntity = new RadicadoEntity($this->db);
        return $radicadoEntity->CountRadicadosByUser($userCode, $depeCode, $tipo);
    }

    /**
     * Obtiene la cantidad de radicados informados que un usuario tiene.
     * @param type $userCode
     * @param type $depeCode
     * @param type $tipo
     * @return type 
     */
    public function CountRadicadosInformadosByUser($userCode, $depeCode, $tipo = null) {
        $radicadoEntity = new RadicadoEntity($this->db);
        return $radicadoEntity->CountRadicadosInformadosByUser($userCode, $depeCode, $tipo);
    }

    /**
     * Permite mover los radicados de un usuario a otro. Adem�s mueve los radicados informados.
     * @param type $usuaOrigen
     * @param type $depeOrigen

     * @param type $usuaDestino
     * @param type $depeDestino
     * @return type 
     */
    public function moverRadicados($usuaOrigen, $depeOrigen, $usuaDestino, $depeDestino) {
        $radicadoEntity = new RadicadoEntity($this->db);
        if ($radicadoEntity->MoveRadicados($usuaOrigen, $depeOrigen, $usuaDestino, $depeDestino)) {
            return $radicadoEntity->MoveRadicadosInformados($usuaOrigen, $depeOrigen, $usuaDestino, $depeDestino);
        }
    }

    /**
     * Permite actualizar el archivo a un radicado.
     * @param string $numeroRadicado
     * @param string $pathArchivo
     * @return boolean
     */
    public function ActualizarAdjunto($numeroRadicado, $pathArchivo) {
        $radicadoEntity = new RadicadoEntity($this->db);
        return $radicadoEntity->ActualizarAdjunto($numeroRadicado, $pathArchivo);
    }

    /**
     * Permite anexar un archivo a un radicado.
     * @param AnexoDTO Objeto con la informaci�n del anexo
     * @return boolean
     */
    public function createAnexo(AnexoDTO $anexo) {
        $radicadoEntity = new RadicadoEntity($this->db);
        return $radicadoEntity->createAnexo($anexo);
    }

    /**
     * obtiene el codigo de tipo de anexo segun su extension
     * */
    public function getTipoAnexoByExt($ext) {
        $radicadoEntity = new RadicadoEntity($this->db);
        return $radicadoEntity->getTipoAnexoByExt($ext);
    }

}

?>
