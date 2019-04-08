<?php

/* Controlador implementado para procesar las acciones básicas sobre las series y subseries.
 *
 * @author Alexander Giraldo
 * @since 26/04/2011 06:30:08 PM
 */

class SeriesController extends BaseController {

    /**
     * Obtiene el listado de subseries a partir de la serie en codificación JSON
     */
    public function GetSubSerieListBySerie($request) {
        $serieSerives = new SeriesServices($this->db);
        $subseries = $serieSerives->GetSubSeriesListBySerie($_POST["idSerie"]);

        echo json_encode($subseries);
    }

    /**
     * Obtiene el listado de dependencias por una regional asegurandose que la subserie exista para la la serie seleccionada
     * @param type $request 
     */
    public function GetDependenciasByRegional($request) {
        $serieSerives = new SeriesServices($this->db);
        $subseries = $serieSerives->GetDependenciasSubSerieByRegional($_POST["idRegional"], $_POST["idSerie"]);

        echo json_encode($subseries);
    }

    /**
     * Obtiene el listado de tipos de documentos de acuerdo a la serie y subserie seleccionada. La respuesta se escribe en formato JSON 
     * @param type $request 
     */
    public function GetDocumentTypes($request) {
        $serieSerives = new SeriesServices($this->db);
        $documentos = $serieSerives->GetDocumentsBySubserie($_POST["idSerie"], $_POST["idSubserie"]);

        echo json_encode($documentos);
    }

    /**
     * Obtiene el listado de clasificaciones de una serie y retorna a través de ajax
     * @param type $request 
     */
    public function GetClasificacionsBySerie($request) {
        $serieSerives = new SeriesServices($this->db);
        $clasificaciones = $serieSerives->GetClasificacionsBySerie($_POST["idSerie"]);
        echo json_encode($clasificaciones);
        $this->ViewData["clasifications"] = $clasificaciones;
    }

    /**
     * Consulta los datos del listado de clasificaciones seg[un los paraétros de busqueda
     * @param type $request 
     */
    public function AdminClasifications($request) {
        $serieSerives = new SeriesServices($this->db);
        if ($_POST["code"] != '') {
            $clasificaciones = $serieSerives->SearchClasificacionsByCode($_POST["idSerie"], $_POST["code"]);
        } else {
            $clasificaciones = $serieSerives->GetClasificacionsBySerie($_POST["idSerie"]);
        }
        $this->ViewData["clasifications"] = $clasificaciones;
    }

    /**
     * Busca las clasificaciones por el Idde la serie y el texto ingresado.
     * @param type $request 
     */
    public function SearchClasificacion($request) {
        $serieSerives = new SeriesServices($this->db);
        $clasificaciones = $serieSerives->SearchClasificacions($_REQUEST["idSerie"], $_REQUEST["q"]);

        echo json_encode($clasificaciones);
    }

    /**
     * 
     * Permite obtener el listado de series del sistema.
     * @param $request
     */
    public function GetSeriesList($request) {
        $serieServices = new SeriesServices($this->$db);
        $series = $serieServices->GetSeriesList();
        echo json_encode($series);
    }

    /**
     * 
     * Permite actualizar la información de un clasificado si viene el codigo del clasificado,
     * si no viene lo permite crear en el sistema.
     * @param $request
     */
    public function EditClasificacionSerie($request) {
        $serieServices = new SeriesServices($this->db);
        $clasification = new ClasificacionDTO();
        $clasification->idSerie = $_REQUEST['idSerie'];
        $clasification->idClasificacion = $_REQUEST["clasificationCode"];
        $clasification->name = $_REQUEST["clasificationName"];
        $oldClasificationCode = $_REQUEST["oldClasificationCode"];
        try {
            if ($oldClasificationCode) { //Esta editando la clasificación
                if ($serieServices->UpdateClasification($clasification, $oldClasificationCode)) {
                    WebContext::getContext()->addMessage("Se actualizo la clasificación correctamente.", CONFIRMATION);
                } else {
                    WebContext::getContext()->addMessage("Error al modificar la clasificación.", ERROR);
                }
            } else {
                if ($serieServices->SaveClasification($clasification)) {
                    WebContext::getContext()->addMessage("Se creó la clasificación correctamente.", CONFIRMATION);
                } else {
                    WebContext::getContext()->addMessage("Error al crear la clasificación.", ERROR);
                }
            }
        } catch (Exception $ex) {
            WebContext::getContext()->addMessage("Ha ocurrido un error al guardar la clasificación. ".$ex->getMessage(), ERROR);
        }
        $this->ViewData["Clasification"] = $clasification;
    }

    /**
     * 
     * Permite eliminar una clasificacion del sistema, desde que no tenga radicados asociados.
     */
    public function DeleteClasification($request) {
        $serieServices = new SeriesServices($this->db);
        $clasificationIdSerie = $_REQUEST['idSerie'];
        $clasificationCode = $_REQUEST["ClasificationCode"];
        try {
            if ($serieServices->DeleteClasification($clasificationCode, $clasificationIdSerie)) {
                WebContext::getContext()->addMessage("Se elimino la clasificación correctamente.", CONFIRMATION);
            }
        } catch (Exception $ex) {
            WebContext::getContext()->addMessage("Ha ocurrido un error al eliminar la clasificación. " . $ex->getMessage(), ERROR);
        }
    }

}

?>
