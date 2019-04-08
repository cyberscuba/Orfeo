<?php
/**
 * By - Skinatech 
 * @author Jenny Gamez
 * @since 02/10/2018 17:06:29 PM
 * Este archivo fue modificado en relación al acceso a los diferentes includes necesarios en el ORFEO 5.5.
 */

define("BASE_PATH", $_SERVER["DOCUMENT_ROOT"] . '/orfeo/');
require_once("../../../config.php");
ini_set("display_errors", 0);
require_once(BASE_PATH . "/modules/nusoap-0.9.5/lib/nusoap.php");
require_once(BASE_PATH . "/include/db/ConnectionHandler.php");
require_once(BASE_PATH . "/modules/utils/DateUtils.php");
require_once(BASE_PATH . "/modules/radicacion/entity/AnexoDTO.class.php");
require_once(BASE_PATH . "/modules/radicacion/entity/PersonEntity.class.php");
require_once(BASE_PATH . "/modules/radicacion/entity/RadicadoDTO.class.php");
require_once(BASE_PATH . "/modules/radicacion/entity/TrdDTO.class.php");
require_once(BASE_PATH . "/modules/radicacion/entity/RadicadoEntity.class.php");
require_once(BASE_PATH . "/modules/radicacion/entity/PersonDTO.php");
require_once(BASE_PATH . "/modules/radicacion/exceptions/ObjectNotFoundException.class.php");
require_once(BASE_PATH . "/modules/radicacion/exceptions/RadicacionException.class.php");
require_once(BASE_PATH . "/modules/radicacion/exceptions/SequenceNumberException.class.php");
require_once(BASE_PATH . "/modules/radicacion/services/PersonServices.class.php");
require_once(BASE_PATH . "/modules/radicacion/services/ReportServices.class.php");
require_once(BASE_PATH . "/modules/radicacion/services/RadicadoServices.class.php");
require_once(BASE_PATH . "/modules/users/services/UserServices.class.php");
require_once(BASE_PATH . "/modules/locations/services/LocationServices.class.php");
require_once(BASE_PATH . "/modules/agendar/services/AgendaRadicadoServices.class.php");
require_once(BASE_PATH . "/modules/utils/StringUtils.php");
require_once BASE_PATH . "/modules/PHPLogger.php";

// Crea la instancia del servidor nusoap.  
$server = new nusoap_server(BASE_PATH . '/modules/radicacion/ws/RadicacionOrfeoManagement.wsdl');

// Guarda informacion referente a una radicacion que llega por parametros y la almacena en base de datos.
function RadicarDocumento($radicarDocumentoReq) {

    $infoRadicado = $radicarDocumentoReq['infoRadicado'];
    $infoRemitente = $radicarDocumentoReq['remitente'];
    $infoDestinatario = $radicarDocumentoReq['destinatario'];

    $radicarDocumentoResp = null;

    //DEBUG,INFO,NOTICE,WARNING,ERROR    
    PHPLogger::getInstance()->write("Se recibe informaci�n del Radicado.", DEBUG, "test Log");
    PHPLogger::getInstance()->write("Remitente: " . $infoRemitente["nombres"]["primerNombre"] . ' ' . $infoRemitente["nombres"]["primerApellido"], DEBUG, "test Log");
    PHPLogger::getInstance()->write("Destinatario: " . $infoDestinatario["nombres"]["primerNombre"] . ' ' . $infoDestinatario["nombres"]["primerApellido"], DEBUG, "test Log");
    PHPLogger::getInstance()->write("Tipo Radicado: " . $infoRadicado["tipoRadicado"], DEBUG, "test Log");

    // Obtiene conexi�n a la base de datos
    $db = new ConnectionHandler(BASE_PATH);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
    // $db->conn->debug = true;

    try {
        $radicadoServices = new RadicadoServices($db);
        $userServices = new UserServices($db);
        $locationServices = new LocationServices($db);
        $agendaServices = new AgendaRadicadoServices($db);

        $radicado = new RadicadoDTO();
        $municipio = $locationServices->GetMunicipioFull($infoDestinatario["direccion"]["idMunicipio"], $infoDestinatario["direccion"]["idDepartamento"]); // idMunicipio, idDepartamento
        $radicado->municipio = $municipio;

        $radicado->fechaRadicacion = DateUtils::Now(); //DateUtils::ParseDate($infoRadicado["fechaRadicacion"], true, 'yyyy-mm-ddTHH:ii:ss');
        $radicado->tipoRadicado = $infoRadicado["tipoRadicado"] == 'Entrada' ? 2 : 1;
        $radicado->medioRecepcion = 1; //$_POST["med"]; 
        $radicado->fechaOficina = DateUtils::Now(); //DateUtils::ParseDate($infoRadicado["fechaFirma"], true, 'yyyy-mm-ddTHH:ii:ss');
        $radicado->descrAnexo = $infoRadicado["descripcion"]; //$_POST["descrAnexo"];
        $radicado->palabrasClave = $infoRadicado["palabraClave"]; //$_POST["descrAnexo"];//Se asignan las mismas palabras clave a la descripcion del anexo
        $radicado->asunto = $infoRadicado["asunto"];
        $radicado->seguro = 1; //1 : publico, 2: Reservado, 3: Confidencial  //$infoRadicado["segu_radi"];

        $radicado->tdidCodi = $infoRadicado["tipoDocumento"]; //$_POST["tip_doc"];
        if ($radicado->tipoRadicado == 1) {
            $radicado->carpCodi = 1;
        } else if ($radicado->tipoRadicado == 2) {
            $radicado->carpCodi = 0;
        }
        $radicado->leido = 0; //Se marca como no leido
        $radicado->carpetaPersonal = 0; //Se asigna en cero para que la muestre en la bandeja de entrada
        $radicado->trteCodi = ""; //$_POST["tip_rem"];
        $radicado->codreg = 1; //Regional Bogot� //$_POST["codreg"];

        $destinatario = new PersonDTO();

        if ($infoDestinatario["nombres"]["razonSocial"] != null && $infoDestinatario["nombres"]["razonSocial"] != '') {
            $destinatario->name = $infoDestinatario["nombres"]["razonSocial"];
        } else {
            $destinatario->name = $infoDestinatario["nombres"]["primerNombre"];
            $destinatario->lastName2 = $infoDestinatario["nombres"]["segundoApellido"];
            $destinatario->lastName1 = $infoDestinatario["nombres"]["primerApellido"];
        }
        $destinatario->code = 12; //$_POST["documento_us1"];
        $destinatario->document = $infoDestinatario["numeroDocumento"]; //$_POST["cc_documento_us1"];
        $destinatario->phone = $infoDestinatario["telefono"]; // $_POST["telefono_us1"];
        $destinatario->address = $infoDestinatario["direccion"]["direccion"]; //$_POST["direccion_us1"];
        $destinatario->email = $infoDestinatario["email"]; //$_POST["mail_us1"];
        $destinatario->municipio = $municipio; //El mismo municipio que el radicado
        $destinatario->funcionario = $infoDestinatario["cargo"]; //"Funcionario";//$_POST["otro_us1"];

        $radicado->destinatario = $destinatario;

        $signer = new PersonDTO();
        if ($infoRemitente["nombres"]["razonSocial"] != null && $infoRemitente["nombres"]["razonSocial"] != '') {
            $signer->name = $infoRemitente["nombres"]["razonSocial"];
        } else {
            $signer->name = $infoRemitente["nombres"]["primerNombre"] . ' ' . $infoRemitente["nombres"]["segundoNombre"];
            $signer->lastName1 = $infoRemitente["nombres"]["primerApellido"] . ' ' . $infoRemitente["nombres"]["segundoApellido"];
        }
        $signer->factory = ""; //$_POST["FrmEmpresa"];
        $signer->job = $infoRemitente["cargo"]; //$_POST["FrmCargo"];
        $signer->address = $infoRemitente["direccion"]["direccion"]; //$_POST["FrmDireccion"];
        $signer->phone = $infoRemitente["telefono"]; //$_POST["FrmTelefono"];
        $signer->email = $infoRemitente["email"]; //$_POST["FrmCorreo"];

        $radicado->signerPerson = $signer;

        try {
            $radicado->usuarioActual = $userServices->GetUserByLogin($infoRadicado["usuarioActual"]);
        } catch (ObjectNotFoundException $e) {
            PHPLogger::getInstance()->write("Error al obtener el usuario actual del Radicado. " . $infoRadicado["usuarioActual"] . " - " . $e->getMessage(), ERROR, "test Log");
            return new soap_fault('300', '', "No se encontró el usuario Actual del radicado: " . $infoRadicado["usuarioActual"] . " - " . $e->getMessage(), '');
        }

        try {
            $radicado->usuarioRadicacion = $userServices->GetUserByLogin($infoRadicado["usuarioRadicacion"]);
        } catch (ObjectNotFoundException $e) {
            PHPLogger::getInstance()->write("Error al obtener el usuario de radicacion. " . $e->getMessage(), ERROR, "test Log");
            return new soap_fault('300', '', "No se encontró el usuario de radicación: " . $infoRadicado["usuarioRadicacion"] . " - " . $e->getMessage(), '');
        }

        $radicado->trd = new TrdDTO();
        $radicado->trd->serie = $infoRadicado["serie"];
        $radicado->trd->dependencia = 440; //$_POST["coddepe"];
        $radicado->trd->subserie = $infoRadicado["subserie"];
        $radicado->trd->tipoDocumento = $infoRadicado["tipoDocumento"];
        $radicado->clasificacion->idClasificacion = $infoRadicado["clasificacion"];

        $numRadicado = $radicadoServices->CreateRadicado($radicado);

        if ($numRadicado != "") {
            PHPLogger::getInstance()->write("Se ha Generado correctamente el radicado No. " . $numRadicado, INFO, "test Log");
        } else {
            return new soap_fault('200', '', 'No se generó un Numero de radicado.', '');
        }
    } catch (Exception $e) {
        PHPLogger::getInstance()->write("Ha ocurrido el siguiente error: " . $e->getMessage(), ERROR, "test Log");
        return new soap_fault('1', '', 'Ha ocurrido el siguiente error al radicar el documento: ' . $e->getMessage(), '');
    }

    $radicarDocumentoResp['return']['numeroRadicado'] = $numRadicado;
    $radicarDocumentoResp['return']['fechaRadicacion'] = DateUtils::ParseDate($radicado->fechaRadicacion, true, 'dd/mm/yyyy HH:ii:ss', 'Y-m-d\TH:i:s');
    $radicarDocumentoResp['return']['remitente'] = null;
    $radicarDocumentoResp['return']['destinatario'] = null;

    PHPLogger::getInstance()->close();

    return $radicarDocumentoResp;
}

function AdjuntarDocumento($adjuntarDoumentoReq) {
    $response = null;
    $rutaRadicado = null;

    $numeroRadicado = $adjuntarDoumentoReq['numeroRadicado'];
    $nombreRadicado = $adjuntarDoumentoReq['nombreRadicado'];
    $adjunto = $adjuntarDoumentoReq['adjunto'];
    $appKey = $adjuntarDoumentoReq['appKey'];
    $hash = $adjuntarDoumentoReq['hash'];
    PHPLogger::getInstance()->write("Adjuntando documento al radicado " . $numeroRadicado, DEBUG, "test Log");

    // Obtiene conexi�n a la base de datos
    $db = new ConnectionHandler(BASE_PATH);
    $db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
//    $db->conn->debug = true;

    try {
        $radicadoServices = new RadicadoServices($db);
        $radicado = $radicadoServices->GetRadicadoByNumber($numeroRadicado);
        if ($radicado) {
            PHPLogger::getInstance()->write("Radicado encontrado " . $numeroRadicado . "-" . $radicado->numeroRadicado, DEBUG, "test Log");
            if ($radicado->rutarchivo == null || $radicado->rutarchivo == "A") {                
                $ano = substr($radicado->numeroRadicado, 0, 4);
                $dependencia = substr($radicado->numeroRadicado, 8, 3);
                $rutaDestino = "../../../bodega/" . $ano . "/" . $dependencia . "/";
                $nombreArchivo = $radicado->numeroRadicado . ".pdf"; //TODO Decidir la extension seg�n el contentType o recibir el tipo de archivo
                PHPLogger::getInstance()->write("Creardo archivo en " . $rutaDestino . "" . $nombreArchivo, DEBUG, "test Log");
                
                createPath($rutaDestino);
                PHPLogger::getInstance()->write("Ruta--------- " . $rutaDestino . " Creada!", DEBUG, "test Log");

                $radicado->rutarchivo = $rutaDestino . "" . $nombreArchivo;
                
                if (writeFile($radicado->rutarchivo, base64_decode($adjunto))) {                    
                    PHPLogger::getInstance()->write("Se creo el archivo!", DEBUG, "test Log");
                    $rutaRadicado = "/" . $ano . "/" . $dependencia . "/" . $nombreArchivo;                    
                    $radicadoServices->ActualizarAdjunto($radicado->numeroRadicado, $rutaRadicado);                    
                    PHPLogger::getInstance()->write("Radicado actualizado! " . $radicado->numeroRadicado, DEBUG, "test Log");
                }
            } else {
                throw new Exception("El radicado " . $numeroRadicado . " ya tiene un archivo asociado. {" . $radicado->rutarchivo . "}");
            }
        } else {
            throw new Exception("No se encontraron los datos del radicado " . $numeroRadicado);
        }
    } catch (Exception $e) {
        PHPLogger::getInstance()->write("Ha ocurrido el siguiente error: " . $e->getMessage(), ERROR, "test Log");
        PHPLogger::getInstance()->close();
        return new soap_fault('1', '', 'Ha ocurrido el siguiente error al adjuntar el documento: ' . $e->getMessage(), '');
    }

    $response['return']['rutaRadicado'] = $rutaRadicado;

    PHPLogger::getInstance()->close();

    return $response;
}

function writeFile($name, $bytes) {
    try {
        $fp = fopen($name, 'wb');
        fwrite($fp, $bytes);
        fclose($fp);
        return true;
    } catch (Exception $ex) {
        throw new Exception("Error al escribir el archivo " . $name);
    }
}

$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : '';
$server->service($HTTP_RAW_POST_DATA);
?>