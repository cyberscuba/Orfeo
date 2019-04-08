<?php

include_once "../entity/AnexoDTO.class.php";
//include_once "../entity/PersonDTO.class.php";
include_once "../entity/PersonEntity.class.php";
include_once "../entity/RadicadoDTO.class.php";
include_once "../entity/TrdDTO.class.php";
include_once "../entity/RadicadoEntity.class.php";
include_once "../exceptions/ObjectNotFoundException.class.php";
include_once "../exceptions/RadicacionException.class.php";
include_once "../exceptions/SequenceNumberException.class.php";
include_once "RadicadoController.class.php";
include_once "RadicadoServices.class.php";
include_once "PersonServices.class.php";
include_once "ReportServices.class.php";
/**
 * Esta clase implementa las acciones que se deben procesar desde un formulario enviado para procesar la informaci�n relacionada a un radicado
 * en el sistema.
 *
 * @author Alexander Giraldo
 * @since 25/04/2011 10:03:20 AM
 */
class RadicadoController extends BaseController {

    /**
     * Funci�n que contiene toda la l�gica para procesar el guardado de un radicado en la base de datos.
     * Se asume que las inclusiones necesarias ya est�n seteadas en el archivo que contiene el llamado al m�todo.
     * @param Radicado $radicado 
     */
    public function SaveRadicado($request) {
        try {
            $radicadoServices = new RadicadoServices($this->db);
            $userServices = new UserServices($this->db);
            $locationServices = new LocationServices($this->db);
            $agendaServices = new AgendaRadicadoServices($this->db);

            $radicado = new RadicadoDTO();
            $municipio = $locationServices->GetMunicipioFull($_POST["muni_us1"], $_POST["codep_us1"]);
            $radicado->municipio = $municipio;

            $fechaRadicacion = DateUtils::ParseDate($_POST["FecRadicado"], true, "dd/mm/yyyy H:i:s", CREATE_RADI_DATE_FORMAT);
            $fechaOficina = DateUtils::ParseDate($_POST["fecha_gen_doc"], true, "dd/mm/yyyy H:i:s", CREATE_RADI_DATE_FORMAT);

            //$radicado->fechaRadicacion = DateUtils::ParseDate($_POST["FecRadicado"]);
            $radicado->fechaRadicacion = $fechaRadicacion;
            $radicado->tipoRadicado = $_POST["tpRadicado"];
            $radicado->medioRecepcion = $_POST["med"];
            //$radicado->fechaOficina = DateUtils::ParseDate($_POST["fecha_gen_doc"]);
            $radicado->fechaOficina = $fechaOficina;
            $radicado->radicadoPadre = !$_POST["radi_nume_radi_asoc"] ? null : trim($_POST["radi_nume_radi_asoc"]);
            $radicado->descrAnexo = $_POST["descrAnexo"];
            $radicado->palabrasClave = $_POST["descrAnexo"]; //Se asignan las mismas palabras clave a la descripcion del anexo
            $radicado->asunto = $_POST["asunto"];
            $radicado->seguro = $_POST["segu_radi"];

            $radicado->tdidCodi = $_POST["tip_doc"];
            if ($radicado->tipoRadicado == 1) {
                $radicado->carpCodi = 1;
            } else if ($radicado->tipoRadicado == 2) {
                $radicado->carpCodi = 0;
            }
            $radicado->leido = 0; //Se marca como no leido
            $radicado->carpetaPersonal = 0; //Se asigna en cero para que la muestre en la bandeja de entrada
            $radicado->trteCodi = $_POST["tip_rem"];
            $radicado->codreg = $_POST["codreg"];


            $destinatario = new PersonDTO();
            $destinatario->code = $_POST["documento_us1"];
            $destinatario->document = $_POST["cc_documento_us1"];
            $destinatario->lastName2 = $_POST["seg_apel_us1"];
            $destinatario->phone = $_POST["telefono_us1"];
            $destinatario->address = $_POST["direccion_us1"];
            $destinatario->email = $_POST["mail_us1"];
            $destinatario->municipio = $municipio; //El mismo municipio que el radicado
            //Determinan el remitente y destinatario del sticker. En salida el remitente es el firmante.
            $destinatario->name = $_POST["nombre_us1"];
            $destinatario->lastName1 = $_POST["prim_apel_us1"];
            $destinatario->funcionario = $_POST["otro_us1"];

            $radicado->destinatario = $destinatario;

            $signer = new PersonDTO();
            $signer->name = $_POST["FrmNombres"];
            $signer->lastName1 = $_POST["FrmApellidos"];
            $signer->factory = $_POST["FrmEmpresa"];
            $signer->job = $_POST["FrmCargo"];
            $signer->address = $_POST["FrmDireccion"];
            $signer->phone = $_POST["FrmTelefono"];
            $signer->email = $_POST["FrmCorreo"];

            $radicado->signerPerson = $signer;

            try {
                $radicado->usuarioActual = $userServices->GetUser($_POST["usuarioDestino"], $_POST["coddepe"]);
            } catch (ObjectNotFoundException $e) {
                WebContext::getContext()->addMessage("Error al obtener el usuario Actual del radicado.", ERROR);
                throw $e;
            }

            try {
                $radicado->usuarioRadicacion = $userServices->GetUser($_SESSION["codusuario"], $_SESSION["dependencia"]);
            } catch (ObjectNotFoundException $e) {
                WebContext::getContext()->addMessage("Error al obtener el usuario de radicacion del radicado.", ERROR);
                throw $e;
            }

            $radicado->trd = new TrdDTO();
            $radicado->trd->serie = $_POST["CodSerie"];
            $radicado->trd->dependencia = $_POST["coddepe"];
            $radicado->trd->subserie = $_POST["coddepe"];
            $radicado->trd->tipoDocumento = $_POST["CodTipDocumento"];
            $radicado->clasificacion->idClasificacion = $_POST["clasificacion"];

            $numRadicado = $radicadoServices->CreateRadicado($radicado);
            if ($numRadicado != "") {
                //Se agenda si es necesario
                if ($_POST["fechaAgenda"] != '') {
                    //Se verifica si tiene fecha de agenda para agendar.
                    $agenda = new AgendaRadicadoDTO();
                    $agenda->numeroRadicado = $numRadicado;
                    $agenda->dependenciaRadicado = $radicado->usuarioActual->dependencia->code;
                    $agenda->docUsuario = $radicado->usuarioActual->document;
                    $agenda->fechaNotificacion = DateUtils::ParseDate($_POST["fechaAgenda"]);
                    $agenda->fechaTransaccion = DateUtils::Now();
                    //TODO Validar si el usuario no ingresa comentarios.
                    $agenda->observacion = "La fecha de vencimiento del radicado " . $numRadicado . " es " . $agenda->fechaNotificacion;
                    $agenda->activo = true;

                    if (!$agendaServices->AgendarRadicado($agenda, $radicado)) {
                        WebContext::getContext()->addMessage("No se pudo ingresar el radicado en la agenda del usuario. " . $e->getMessage(), ERROR);
                    }
                }

                try {
                    $this->InformarRadicados($radicado);
                } catch (Exception $e) {
                    WebContext::getContext()->addMessage("Ha ocurrido un error al informar a los usuarios. " . $e->getMessage(), ERROR);
                }

                try {
                    $this->SendCreateNotification($radicado);
                } catch (Exception $e) {
                    WebContext::getContext()->addMessage("No se ha podido enviar la notificaci�n al usuario. " . $e->getMessage(), ERROR);
                }
            }
            WebContext::getContext()->addMessage("Se ha Generado correctamente el radicado No. <font color='red' size='4'><b>" . $numRadicado . "</b></font>", CONFIRMATION);
        } catch (Exception $e) {
            WebContext::getContext()->addMessage("Ha ocurrido el siguiente error: " . $e->getMessage(), ERROR);
            $this->ViewData['Error'] = true;
        }
        $this->ViewData['Radicado'] = $radicado;
    }

    /**
     * Env�a el Email de notificacion para el usuario cuando se crea un radicado y le es asignado.
     * @param RadicadoDTO $radicado
     * @return type 
     */
    private function SendCreateNotification(RadicadoDTO $radicado) {
        $fp = new read(BASE_PATH . "/lib/plantilla.html");
        if ($radicado->tipoRadicado == 1) {
            $bandeja = "salida";
        } else {
            $bandeja = "entrada";
        }
        $linkRadicado = "<a href='" . RUTA_SITE . "?radi_nume_radi=" . $radicado->numeroRadicado . "'>" . $radicado->numeroRadicado . "</a>";
        $array_replace = array("RADICADO" => $linkRadicado, "RUTA_SITE" => RUTA_SITE, "BANDEJA" => $bandeja);
        $contentMail = $fp->replaceContent($array_replace);
        $contentMail = str_replace("RUTA_SITE", RUTA_SITE, $contentMail);
        $contentMail = str_replace("RADICADO", RUTA_SITE, $contentMail);
        $contentMail = str_replace("BANDEJA", $bandeja, $contentMail);

        $mail = new sendmail();
        $mail->SetCharSet("ISO-8859-1");
        $mail->from2(MAIL_FROM); //Debe estar configurado en el config.php
        $mail->to($radicado->usuarioActual->email);
        $mail->subject("Nuevo Radicado de " . $bandeja);
        $mail->text($contentMail);
        $sent = $mail->send();
        /* if (!$sent) {
          throw new Exception("No se ha podido enviar la notificaci�n al email: ".$radicado->usuarioActual->email);
          } */
        return $sent;
    }

    /**
     * Permite informar un radicado seg�n los campos seleccionados en el formulario
     * @param RadicadoDTO $radicado Radicado a informar
     */
    private function InformarRadicados(RadicadoDTO $radicado) {
        $radicados[0] = $radicado->numeroRadicado;

        $observaciones = "Informado desde radicaci�n";
        $currentDepe = $radicado->usuarioActual->dependencia->code;

        if ($_POST["informarJefe"] == "true") {
            $tx = new Tx($this->db);
            $userServices = new UserServices($this->db);
            try {
                $usuarioJefe = $userServices->GetUserJefe($currentDepe);
                if ($usuarioJefe->code != $radicado->usuarioActual->code) {
                    $tx->informar($radicados, $radicado->usuarioActual->login, $usuarioJefe->dependencia->code, $currentDepe, $usuarioJefe->code, $radicado->usuarioActual->code, $observaciones, $radicado->usuarioActual->document);
                }
            } catch (ObjectNotFoundException $e) {
                echo "No se encontr� el usuario jefe para informar.";
            }
        }

        $cant = count($_POST['copyUsers']);
        //Se informa a los otros usuarios si se seleccionan en el formulario de radicaci�n
        if ($_POST['informarOtro'] == "true" && $cant > 0) {
            for ($i = 0; $i < $cant; $i++) {
                try {
                    $copyRow = explode("-", $_POST['copyUsers'][$i]);
                    $copyDepe = $copyRow[0];
                    $copyUser = $copyRow[1];

                    $tx = new Tx($this->db);
                    if (!($copyDepe == $currentDepe && $copyUser == $radicado->usuarioActual->code)) {
                        $tx->informar($radicados, $radicado->usuarioActual->login, $copyDepe, $currentDepe, $copyUser, $radicado->usuarioActual->code, $observaciones, $radicado->usuarioActual->document);
                    }
                } catch (Exception $e) {
                    WebContext::getContext()->addMessage("Error al informar al usuario " . $_POST['copyUsers'][$i] . "" . $e->getMessage(), ERROR);
                }
            }
        }
    }

    /**
     * Permite buscar la informaci�n de un radicado a partir de su c�digo
     * @param REQUEST $request 
     */
    public function SearchRadicado($request) {
        try {
            $radicadoServices = new RadicadoServices($this->db);
            $this->ViewData["radicado"] = $radicadoServices->GetRadicadoByNumber($_POST["searchRadicado"]);
        } catch (ObjectNotFoundException $e) {
            WebContext::getContext()->addMessage("No se encontr� el radicado n�mero: <b>" . $_POST["searchRadicado"] . "</b>", ERROR);
        }
    }

    /**
     * Permite guardar los datos de la clasificaci�n de un radicado
     * @param type $request 
     */
    public function UpdateClasificacionRadicado($request) {
        $radicadoServices = new RadicadoServices($this->db);

        try {
            $radicado = $radicadoServices->GetRadicadoByNumber($_POST["numRadicado"]);
            $radicado->numeroRadicado = $_POST["numRadicado"];
            $radicado->trd = new TrdDTO();
            $radicado->trd->serie = $_POST["CodSerie"];
            $radicado->trd->dependencia = $_POST["coddepe"];
            $radicado->trd->tipoDocumento = $_POST["CodTipDocumento"];
            $radicado->clasificacion = new ClasificacionDTO();
            $radicado->clasificacion->idClasificacion = $_POST["clasificacion"];

            if ($radicadoServices->SaveTRDRadicado($radicado)) {
                WebContext::getContext()->addMessage("Se actualizaron los datos de la clasificaci�n del radicado.", CONFIRMATION);
            } else {
                WebContext::getContext()->addMessage("Error al actualizar la clasificaci�n del radicado.", ERROR);
            }
        } catch (Exception $e) {
            WebContext::getContext()->addMessage("Error al actualizar la clasificaci�n del radicado. " . $e->getMessage(), ERROR);
        }

        $this->ViewData["radicado"] = $this->ViewData["radicado"] = $radicado;
    }

    /**
     * Crea un radicado desde un email
     */
    public function CreateRadicadoFromMail($request) {
        /* try to connect */
        $inbox = imap_open(MAIL_HOSTNAME, MAIL_USERNAME, MAIL_PASSWORD) or die('Cannot connect to mail server: ' . imap_last_error());

        PHPLogger::getInstance()->write("Abriendo la bandeja de correos no leidos.", INFO, "test Log");
        /* grab emails */
        $emails = imap_search($inbox, 'UNSEEN');

        if ($emails) {
            $radicadoServices = new RadicadoServices($this->db);
            $userServices = new UserServices($this->db);
            $locationServices = new LocationServices($this->db);
            $agendaServices = new AgendaRadicadoServices($this->db);

            PHPLogger::getInstance()->write(count($emails) . " correos encontrados sin leer.", INFO, "test Log");

            foreach ($emails as $email_number) {
                $header = imap_header($inbox, $email_number);
                $fromaddr = $header->from[0]->mailbox . "@" . $header->from[0]->host;
                PHPLogger::getInstance()->write("Leyendo el correo " . $email_number . " proveniente la cuenta " . $fromaddr, INFO, "test Log");

                $user = $userServices->GetUserByEmail($fromaddr);

                if ($user->code != "") {
                    $date = $header->date;
                    $fechaEmail = new DateTime(date('Y-m-d H:i:s', strtotime($date)));
                    $timezone = new DateTimeZone('America/Bogota');

                    $fechaEmail->setTimezone($timezone);
                    $fechaRadicado = $fechaEmail->format(CREATE_RADI_DATE_FORMAT);

                    try {

                        $radicado = new RadicadoDTO();
                        $radicado->municipio = MAIL_RADI_MUNI_DEF;

                        $radicado->fechaRadicacion = $fechaRadicado;
                        $radicado->tipoRadicado = MAIL_RADI_TIPO_DEF;
                        $radicado->medioRecepcion = MAIL_RADI_TIPO_DEF;
                        $radicado->fechaOficina = $fechaRadicado;
                        $radicado->descrAnexo = "Anexo - " . $header->subject;
                        $radicado->palabrasClave = "Anexo - " . $header->subject;
                        $radicado->asunto = $header->subject;
                        $radicado->seguro = 1;

                        if ($radicado->tipoRadicado == 1) {
                            $radicado->carpCodi = 1;
                        } else if ($radicado->tipoRadicado == 2) {
                            $radicado->carpCodi = 0;
                        }
                        $radicado->leido = 0; //Se marca como no leido
                        $radicado->carpetaPersonal = 0; //Se asigna en cero para que la muestre en la bandeja de entrada

                        $destinatario = new PersonDTO();
                        $destinatario->code = $user->code;
                        $destinatario->document = $user->document;
                        $destinatario->name = $user->name;
                        $destinatario->lastName2 = "";
                        $destinatario->phone = MAIL_RADI_PHONE;
                        $destinatario->address = MAIL_RADI_ADDRESS;
                        $destinatario->email = $user->email;
                        $destinatario->municipio = $municipio; //El mismo municipio que el radicado
                        $destinatario->lastName1 = "";

                        $radicado->destinatario = $destinatario;

                        $signer = new PersonDTO();
                        $signer->name = $user->name;
                        $signer->lastName1 = "";
                        $signer->job = MAIL_RADI_CARGO;
                        $signer->phone = MAIL_RADI_PHONE;
                        $signer->address = MAIL_RADI_ADDRESS;
                        $signer->email = $user->email;

                        $radicado->signerPerson = $signer;

                        try {
                            $radicado->usuarioActual = $user;
                        } catch (ObjectNotFoundException $e) {
                            throw new Exception("Error al obtener el usuario Actual del radicado." . $e->getMessage());
                        }

                        try {
                            $radicado->usuarioRadicacion = $user;
                        } catch (ObjectNotFoundException $e) {
                            throw new Exception("Error al obtener el usuario de radicacion del radicado." . $e->getMessage());
                        }


                        $pos = stripos($radicado->asunto, "[");
                        $str = substr($radicado->asunto, $pos);
                        $str_two = substr($str, strlen("["));
                        $second_pos = stripos($str_two, "]");
                        $str_three = substr($str_two, 0, $second_pos);
                        $serie = trim($str_three);

                        switch ($serie) {
                            case "NEGOCIOS FIDUCIARIOS":
                                $radicadoSerie = 2;
                                break;
                            case "NEGOCIOS DE INVERSION":
                                $radicadoSerie = 3;
                                break;
                            default:
                                $radicadoSerie = 1;
                                break;
                        }


                        $radicado->trd = new TrdDTO();
                        $radicado->trd->serie = $radicadoSerie;
                        $radicado->trd->dependencia = $user->dependencia->code;
                        $radicado->trd->subserie = $user->dependencia->code;
                        $radicado->trd->tipoDocumento = MAIL_RADI_TIPO_DOC;

                        //Crea el radicado
                        $numRadicado = $radicadoServices->CreateRadicado($radicado);
                        PHPLogger::getInstance()->write("Se ha creado correctamente el radicado No. " . $numRadicado, INFO, "test Log");

                        if ($numRadicado != "") {

                            //Guarda los archivos adjuntos y guarda copia del email en PDF
                            $emailMessage = new EmailMessage($inbox, $email_number);

                            if ($emailMessage) {
                                try {
                                    $emailMessage->fetch();
                                    $ano = date('Y', strtotime($radicado->fechaRadicacion));
                                    $dependencia = $user->dependencia->code;

                                    $radicadoPath = "../bodega/" . $ano . "/" . $dependencia . "/";
                                    createPath($radicadoPath);

                                    preg_match_all('/src="cid:(.*)"/Uims', $emailMessage->bodyHTML, $matches);
                                    if (count($matches)) {

                                        $search = array();
                                        $replace = array();

                                        foreach ($matches[1] as $match) {
                                            $uniqueFilename = $emailMessage->attachments[$match]['filename'];
                                            file_put_contents($radicadoPath . $uniqueFilename, $emailMessage->attachments[$match]['data']);
                                            $search[] = "src=\"cid:$match\"";
                                            $replace[] = "src=\"" . RUTA_SITE . "bodega/" . $ano . "/" . $dependencia . "/" . $uniqueFilename . "\"";
                                        }

                                        $emailMessage->bodyHTML = str_replace($search, $replace, $emailMessage->bodyHTML);
                                    }

                                    $mpdf = new mPDF();
                                    $mpdf->WriteHTML($emailMessage->bodyHTML);
                                    $mpdf->Output($radicadoPath . $numRadicado . ".pdf", 'F');

                                    foreach ($matches[1] as $match) {
                                        $uniqueFilename = $emailMessage->attachments[$match]['filename'];
                                        unlink($radicadoPath . $uniqueFilename);
                                    }
                                    $archivoRadicado = "/" . $ano . "/" . $dependencia . "/" . $numRadicado . ".pdf";
                                    $radicadoServices->ActualizarAdjunto($numRadicado, $archivoRadicado);
                                    PHPLogger::getInstance()->write("Se ha actualizado correctamente el adjunto del radicado No. " . $numRadicado, INFO, "test Log");

                                    //Crea los archivos anexos
                                    if (count($emailMessage->attachments) > 0) {
                                        $a = 1;
                                        foreach ($emailMessage->attachments as $attachment) {
                                            if ($attachment['inline'] != 1) {
                                                $uniqueFilename = $numRadicado . "_" . $attachment['filename'];

                                                $filename = explode(".", $attachment['filename']);
                                                $ext = end($filename);
                                                if ($ext == "docx") { //soporte para extensiones nuevas office
                                                    $ext = "doc";
                                                }
                                                if ($ext == "xlsx") { //soporte para extensiones nuevas office
                                                    $ext = "xls";
                                                }
                                                if ($ext == "pptx") { //soporte para extensiones nuevas office
                                                    $ext = "ppt";
                                                }

                                                $codigoAnexo = $radicadoServices->getTipoAnexoByExt($ext);

                                                if ($codigoAnexo != "") {
                                                    if (!is_dir($radicadoPath . "/docs/")) {
                                                        createPath($radicadoPath . "/docs/");
                                                    }

                                                    file_put_contents($radicadoPath . "/docs/" . $uniqueFilename, $attachment['data']);
                                                    $anexo = new AnexoDTO();
                                                    $anexo->destinatario = 1; //El mismo remitente
                                                    $anexo->numeroRadicado = $numRadicado;
                                                    $numeroAnexo = trim(str_pad($a, 5, "0", STR_PAD_LEFT));
                                                    $anexo->codigo = trim($numRadicado) . $numeroAnexo;
                                                    $anexo->tipoAnexo = $codigoAnexo;
                                                    $anexo->tamano = (filesize($radicadoPath . "/docs/" . $uniqueFilename) / 1000);
                                                    $anexo->soloLectura = "S"; //Solo lectura
                                                    $anexo->creador = $user->login;
                                                    $anexo->descripcion = "Archivo adjunto al correo";
                                                    $anexo->numero = $numeroAnexo;
                                                    $anexo->nombreArchivo = $uniqueFilename;
                                                    $anexo->salida = 0;
                                                    $anexo->dirTipo = 1; // El mismo remitente
                                                    //$anexo->tipoRadicacion = MAIL_RADI_TIPO_DEF;
                                                    $anexo->dependenciaCreador = $user->dependencia->code;
                                                    $anexo->SGD_APLI_CODI = null;
                                                    $anexo->SGD_TRAD_CODIGO = null;
                                                    $anexo->SGD_EXP_NUMERO = null;

                                                    $radicadoServices->createAnexo($anexo);
                                                    PHPLogger::getInstance()->write("Se ha creado correctamente el anexo No. " . $anexo->codigo . " con nombre " . $anexo->nombreArchivo, INFO, "test Log");
                                                } else {
                                                    PHPLogger::getInstance()->write("Ha ocurrido un error al adjuntar el archivo. " . $attachment['filename'] . ". Extension no valida.", ERROR, "test Log");
                                                }
                                                $a++;
                                            }
                                        }
                                    }
                                } catch (Exception $ex) {
                                    //imap_close($inbox);
                                    PHPLogger::getInstance()->write("Ha ocurrido un error al radicar el correo. " . $e->getMessage(), ERROR, "test Log");
                                }
                            }


                            try {
                                $this->InformarRadicados($radicado);
                            } catch (Exception $e) {
                                PHPLogger::getInstance()->write("Ha ocurrido un error al informar a los usuarios. " . $e->getMessage(), ERROR, "test Log");
                            }

                            try {
                                $this->SendCreateNotification($radicado);
                            } catch (Exception $e) {
                                PHPLogger::getInstance()->write("No se ha podido enviar la notificaci�n al usuario. " . $e->getMessage(), ERROR, "test Log");
                            }
                        }

                        PHPLogger::getInstance()->write("Se ha Generado correctamente el radicado No. " . $numRadicado, INFO, "test Log");
                    } catch (Exception $e) {
                        //imap_close($inbox);
                        PHPLogger::getInstance()->write("Ha ocurrido el siguiente error: " . $e->getMessage(), ERROR, "test Log");
                    }
                } else {
                    PHPLogger::getInstance()->write("No se ha encontrado el usuario remitente en la base de datos", ERROR, "test Log");
                    //Se ejecuta la obtenci�n del mensaje para marcarlo como no leido
                    $emailMessage = new EmailMessage($inbox, $email_number);
                    $emailMessage->fetch();
                }
            }
        } else {
            PHPLogger::getInstance()->write("No hay correos pendientes por radicar", INFO, "test Log");
        }

        /* close the connection */
        imap_close($inbox);
    }

}

?>
