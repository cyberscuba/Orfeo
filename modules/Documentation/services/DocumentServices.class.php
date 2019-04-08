<?php

class DocumentServices {

    /**
     * Obtiene los documentos requeridos de un usuario por su
     * Identificacion
     * @param string $Identification
     */
    public function GetDocumentsByIdentification($Identification, $Nick, $Ip, $rowType) {
        $servicio = URL_DOCUMENTATION_SERVICE;
        $key = DOCUMENT_SERVICE_KEY;
        $parametros = array();
        $parametros['Identification'] = $_REQUEST['Identification'];
        $Nick = base64_encode($Nick);
        $parametros['Nick'] = $Nick;
        $parametros['ip'] = $Ip;
        $parametros['RowType'] = $rowType;
        $localKey = $key . $Nick . $Ip . $_REQUEST['Identification'] . $rowType;
        $localKey = md5($localKey);
        $parametros['key'] = $localKey;
        $client = new SoapClient($servicio, $parametros);
        $result = $client->ConsultDocumentsByIdentification($parametros);
        $result = $this->obj2array($result);

        $ArrayResult = $result["ConsultDocumentsByIdentificationResult"]["Document"];

        $DocuemntList = array();
        if (is_array($ArrayResult) && is_array($ArrayResult[0])) {
            $n = count($ArrayResult);
            for ($i = 0; $i < $n; $i++) {
                $data = $ArrayResult[$i];
                $document = $this->GetDocumentFromArray($data);
                array_push($DocuemntList, $document);
            }
        } else if (is_array($ArrayResult)) {
            $document = $this->GetDocumentFromArray($ArrayResult);
            array_push($DocuemntList, $document);
        }
        return $DocuemntList;
    }

    /**
     *
     * Obtiene la informaci�n de un documento a partir de un arreglo con los campos del documento
     * @param array
     * @throws Exception Si ocurre alguna excepci�n al procesar el arreglo
     */
    public function GetDocumentFromArray($data) {
        try {
            $Docuemnt = new DocumentDTO();
            $Docuemnt->IdDocument = $data['IdDocument'];
            $Docuemnt->Name = $data['Name'];
            $Docuemnt->Description = $data['Description'];
            $Docuemnt->PrivateDocument = $data['PrivateDocument'];
        } catch (Exception $ex) {
            throw new Exception("Ha fallado la carga de documentos. " . $ex->getMessage());
        }
        return $Docuemnt;
    }

    /**
     * 
     * Resive un arreglo y lo transforma en un arreglo de objetos para poder acceder a el.
     * @param $obj
     */
    function obj2array($obj) {
        $out = array();
        foreach ($obj as $key => $val) {
            switch (true) {
                case is_object($val):
                    $out[$key] = $this->obj2array($val);
                    break;
                case is_array($val):
                    $out[$key] = $this->obj2array($val);
                    break;
                default:
                    $out[$key] = $val;
            }
        }
        return $out;
    }

    /**
     * Obtiene el documento relacionado a un usuario,
     * lo obtiene a partir de su identificacion e identificacion del documento
     * solicitante.
     * @param string $Identification
     * @param int $IdDocument
     * @return Retorna el documento 
     */
    public function GetDocument($Identification, $IdDocument, $Nick, $Ip, $rowType) {
        $servicio = URL_DOCUMENTATION_SERVICE;
        $key = DOCUMENT_SERVICE_KEY;
        $parametros = array();
        $parametros['Identification'] = $Identification;
        $parametros['IdDocument'] = $IdDocument;
        $parametros['RowType'] = $rowType;
        $Nick = base64_encode($Nick);
        $parametros['nick'] = $Nick;
        $parametros['ip'] = $Ip;
        $LocalKey = $key . $Nick . $Ip . $Identification . $IdDocument . $rowType;
        $LocalKey = md5($LocalKey);
        $parametros['key'] = $LocalKey;
        $client = new SoapClient($servicio);
        try {
            $result = $client->ConsultDocument($parametros);
            $result = $this->obj2array($result);
            return $result;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 
     * Obtiene un cliente registrado en la base unica de clientes a partir de su documento de identificaci�n.
     * @param $Identification
     */
    public function GetClientByIdentification($Identification) {
        $servicio = URL_CLIENT_SERVICE;
        $parametros = array();
        $parametros['DocumentType'] = "";
        $parametros['DocumentNumber'] = $Identification;
        $parametros['ClientName'] = "";
        $client = new SoapClient($servicio, $parametros);
        $result = $client->SearchClients($parametros);
        $result = $this->obj2array($result);
        $RClient = new RClientDTO();
        if ($result["SearchClientsResult"]["RClient"]["ClientType"][Code]) {
            $RClient->IdentificationType = $result["SearchClientsResult"]["RClient"]["IdentificationType"][Description];
            $RClient->Identification = $result["SearchClientsResult"]["RClient"][IdentificationNumber];
            $RClient->Name = $result["SearchClientsResult"]["RClient"][FirstName];
            $RClient->LastName = $result["SearchClientsResult"]["RClient"][LastName];
            $RClient->IdentificationCode = $result["SearchClientsResult"]["RClient"]["ClientType"][Code];
        } else { //Retorna mas de un elemento
            $RClient->IdentificationType = $result["SearchClientsResult"]["RClient"][0]["IdentificationType"][Description];
            $RClient->Identification = $result["SearchClientsResult"]["RClient"][0][IdentificationNumber];
            $RClient->Name = $result["SearchClientsResult"]["RClient"][0][FirstName];
            $RClient->LastName = $result["SearchClientsResult"]["RClient"][0][LastName];
            $RClient->IdentificationCode = $result["SearchClientsResult"]["RClient"][0]["ClientType"][Code];
        }
        return $RClient;
    }

    /**
     * 
     * Obtiene la informaci�n de un negocio a partir de su identificaci�n.
     * @param $Identification
     */
    public function GetBusinessInformation($Identification) {
        $servicio = URL_CLIENT_SERVICE;
        $parametros = array();
        $parametros['Code'] = $Identification;
        $parametros['Name'] = "";
        $client = new SoapClient($servicio, $parametros);
        $result = $client->SearchBusiness($parametros);
        $result = $this->obj2array($result);
        $ArrayBusiness = $result['SearchBusinessResult']['RBusiness'];
        $business = new BusinessDTO();
        $business->Name = $ArrayBusiness['Name'];
        $business->SuperCode = $ArrayBusiness['Code'];
        return $business;
    }

}

?>