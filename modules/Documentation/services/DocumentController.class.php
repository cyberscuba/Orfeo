<?php

session_start();

class DocumentController extends BaseController {

    /**
     * Obtiene los documentos relacionados a un cliente
     */
    public function GetDocumentsByIdentification($SearchType) {
        $documentServices = new DocumentServices();
        if ($_POST['Identification'] != "") {
            try {
                $Nick = base64_decode($_REQUEST['krd']);
                $Ip = $_SERVER['REMOTE_ADDR'];
                if ($SearchType['SearchType'] == "C") {
                    $result = $documentServices->GetDocumentsByIdentification($_POST['Identification'], $Nick, $Ip, 1);
                    $RClient = $documentServices->GetClientByIdentification($_POST['Identification']);
                }
                if ($SearchType['SearchType'] == "H") {
                    $result = $documentServices->GetDocumentsByIdentification($_POST['Identification'], $Nick, $Ip, 2);
                    $RClient = $documentServices->GetBusinessInformation($_POST['Identification']);
                }
            } catch (Exception $ex) {
                WebContext::getContext()->addMessage("Ha ocurrido un error al consultar la informaci�n." . utf8_decode($ex->getMessage()), ERROR);
            }
            if ($RClient->Name != "") {
                $this->ViewData["DocumentList"] = $result;
                $this->ViewData["RClient"] = $RClient;
            } else {
                WebContext::getContext()->addMessage("No se encontr� la informaci�n solicitada.", ERROR);
            }
        } else {
            WebContext::getContext()->addMessage("Ingrese un n�mero de identificaci�n para buscar.", ERROR);
        }
    }

    /**
     * 
     * Permite obtener un documento por la identificacion y el id del documento
     */
    public function GetDocument() {
        $documentServices = new DocumentServices();
        try {
            $Nick = base64_decode($_REQUEST['krd']);
            $Ip = $_SERVER['REMOTE_ADDR'];
            $rowType = 2;
            if ($_REQUEST['RowType'] == 'C') {
                $rowType = 1;
            }
            $docArray = Array();
            $docArray = $_SESSION['DocumentClient'];
            $key = $_REQUEST['Identification'] . $_REQUEST['IdDocument'] . $Nick . $Ip . $rowType;
            //Se valida la llave de identificaci�n del documento para determinar si se consulta o esta en sesi�n.
            if ($docArray[0] == null || $docArray[0] != $key) {
                $doc = $documentServices->GetDocument($_REQUEST['Identification'], $_REQUEST['IdDocument'], $Nick, $Ip, $rowType);
                $doc = $doc[ConsultDocumentResult][base64Binary];

                $docArray[0] = $key;
                $docArray[1] = $doc;
                $_SESSION['DocumentClient'] = $docArray; //Se guarda en sesi�n el documento con su llave de identificaci�n.
            } else {
                $doc = $docArray[1];
            }
            $totalPages = count($doc);
            $_SESSION["totalPages"] = $totalPages;
            if ($_REQUEST['IndexPage'] == 'null') {
                $page = 0;
            } else {
                $page = $_REQUEST['IndexPage'];
                $page -= 1;
            }
            if ($totalPages > 1) {
                TrasmitFile("image/jpg", $doc[$page], $_REQUEST['Identification'] . '-' . $_REQUEST['IdDocument'] . '.jpg');
            } else {
                TrasmitFile("image/jpg", $doc, $_REQUEST['Identification'] . '-' . $_REQUEST['IdDocument'] . '.jpg');
            }
        } catch (Exception $ex) {
            WebContext::getContext()->addMessage("Ha ocurrido un error al consultar el documento seleccionado. " . $ex->getMessage(), ERROR);
        }
    }

    /**
     * Permite obtener el total de las paginas del documento.
     */
    public function GetDocumentPages() {
        $totalPages = $_SESSION["totalPages"];
        echo $totalPages;
    }

}

?>