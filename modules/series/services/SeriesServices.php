<?php
/**
 * Contiene la lógica de negocio necesaria para gestionar las series, subseries y dependencias del sistema y sus relaciones con el radicado.
 *
 * @author Alexander Giraldo
 * @since 26/04/2011 04:59:18 PM
 */
class SeriesServices {
    
    /**
     * Contiene la conexión a usar
     * @var ConnectionHandler 
     */
    private $db;
    
    /**
     * Recibe la conexión a usar para la base de datos.
     * @param ConnectionHandler $db 
     */
    public function SeriesServices(ConnectionHandler $db) {
        $this->db = $db;
    }
    
    /**
     * Obtiene el listado de regionales cargadas en el sistema.
     */
    public function GetRegionalList() {
        $regionalEntity = new RegionalEntity($this->db);
        return $regionalEntity->GetList();
    }
    
    public function GetClasificationsBySerieAndCode($idSerie, $codClaSerie)
    {
    	$clasificacionEntity = new ClasificacionEntity($this->db);
    	return $clasificacionEntity->GetClasificationsBySerieAndCode($idSerie, $codClaSerie);
    }
    
    /**
     * Permite buscar clasificaciones por la serie o por código
     * @param type $idSerie
     * @param type $codClaSerie
     * @return type 
     */
    public function SearchClasificacionsByCode($idSerie, $codClaSerie)
    {
    	$clasificacionEntity = new ClasificacionEntity($this->db);
    	return $clasificacionEntity->SearchClasificacionsByCode($idSerie, $codClaSerie);
    }
    
    /**
     * Obtiene el listado de series habilitadas en el sistema sin filtrar por las fechas de valides de las series.
     * @return array 
     */
    public function GetSeriesList() {
        $serieEntity = new SerieEntity($this->db);
        return $serieEntity->GetList();
    }
    
    /**
     * Obtiene el listado completo de laa series que pertenecen a una serie en el sistema.
     * No se validan las fechas de inicio y fin de la susserie.
     * Este método no valida la dependencia a la cual pertenece, simplemente se obtiene el listado total de subseries
     * asociadas a una serie.
     * @param int $idSerie 
     */
    public function GetSubSeriesListBySerie($idSerie) {
        $subSerieEntity = new SubSerieEntity($this->db);
        return $subSerieEntity->GetListBySerie($idSerie);
    }
    
    /**
     * Obtiene el listado de tipos de documentos ACTIVOS para una serie y subserie.
     * Se asume la dependencia como la misma subserie.
     * @param type $idSerie
     * @param type $idSubSerie 
     */
    public function GetDocumentsBySubserie($idSerie, $idSubserie) {
        $documentEntity = new DocumentoEntity($this->db);
        return $documentEntity->GetDocumentsBySubserie($idSerie, $idSubserie);
    }
    
    /**
     * Obtiene el listado de clasificaciones relacionadas a la serie
     * @param int $idSerie Identificador de la serie de la cual se desean obtener las clasificaciones
     * @return array 
     */
    public function GetClasificacionsBySerie($idSerie) {
        $clasificacionEntity = new ClasificacionEntity($this->db);
        return $clasificacionEntity->GetClasificacionsBySerie($idSerie);
    }
    
    /**
     * 
     * Permite actualizar el codigo de clasificacion de los radicados.
     * @param $newClasificationCode Nuevo codigo del clasificado
     * @param $oldClasificationCode Antiguo codigo del clasificado
     * @param $serieCode Codigo de la serie a la que pertenece el clasificado
     */
    public function UpdateClasificationInRadicade($newClasificationCode, $oldClasificationCode, $serieCode)
    {
    	if($newClasificationCode != $oldClasificationCode)
    	{
    		$clasificationEntity = new ClasificacionEntity($this->db);
    		$clasificationEntity->UpdateClasificationInRadicade($newClasificationCode, $oldClasificationCode, $serieCode);
    		return true;
    	}
    }
    
    /**
     * Permite actualizar la informacion de un clasificado.
     * @param $clasificationDTO
     * @param $oldClasificationCode
     */
    public function UpdateClasification(ClasificacionDTO $clasificationDTO, $oldClasificationCode)
    {
    	$clasificationEntity = new ClasificacionEntity($this->db);
    	if($clasificationEntity->UpdateClasification($clasificationDTO, $oldClasificationCode)) {
            $this->UpdateClasificationInRadicade($clasificationDTO->idClasificacion, $oldClasificationCode, $clasificationDTO->idSerie);
            return true;
        } else {
            return false;
        }
    	
    }
    
    /**
     * 
     * Permite crear una nueva clasificación en el sistema.
     * @param ClasificacionDTO $clasification
     */
    public function SaveClasification(ClasificacionDTO $clasification)
    {
    	$clasificationEntity = new ClasificacionEntity($this->db);
    	return $clasificationEntity->SaveClasificationsSerie($clasification);
    }
    
    /**
     * 
     * Permite eliminar un clasificado por el codigo y la serie a la que pertenece.
     * @param $clasificationCode codigo de la clasificacion
     * @param $idSerie id de la serie a la que pertenece.
     */
    public function DeleteClasification($clasificationCode, $idSerie)
    {
    	$clasificationEntity = new ClasificacionEntity($this->db);
    	return $clasificationEntity->DeleteClasification($clasificationCode, $idSerie);
    }
    
    /**
     * Obtiene el listado de clasificaciones que pertenezcan a una serie y que coincidan con el texto buscado.
     * @param type $idSerie
     * @param type $search
     * @return type 
     */
    public function SearchClasificacions($idSerie, $search) {
        $clasificacionEntity = new ClasificacionEntity($this->db);
        return $clasificacionEntity->SearchClasificacions($idSerie, $search);
    }
    
    /**
     * Obtiene la información de la clasificación de un radicado.
     * @param type $numRadicado Número de radicado
     * @return ClasificacionDTO 
     */
    public function GetClasificacionsByRadicado($numRadicado) {
        $clasificacionEntity = new ClasificacionEntity($this->db);
        return $clasificacionEntity->GetClasificacionsByRadicado($numRadicado);
    }
    
    /**
     * Obtiene el listado de medios de recepción habilitados en el sistema.
     * @return array 
     */
    public function GetMediosRecepcion() {
        $medioEntity = new MedioEntity($this->db);
        return $medioEntity->GetList();
    }
    
    /**
     * Método que permite crear en el sistema las subseries correspondientes a la dependencia,
     * enlazandolas con el mismo código de la dependencia actual.
     * @param DependenciaDTO $dependencia 
     */
    public function CreateSubSeriesByDependency(DependenciaDTO $dependencia) {
       $seriesList =  $this->GetSeriesList();
       foreach ($seriesList as $serie) {
           $subserie = $this->GetSubserie($dependencia->code, $serie->code);
           if(!$subserie->code) {
               $subserie = new SubSerieDTO();
               $subserie->idSerie = $serie->code;
               $subserie->code = $dependencia->code;
               $subserie->name = $dependencia->name;
               $subserie->startDate = DateUtils::Now();
               $subserie->finalDate = DateUtils::AddYears(1);
               $this->CreateSubSerie($subserie);
           }
       }
    }
    
    /**
     * Crea una subserie en el sistema
     * @param SubSerieDTO $subserie 
     */
    public function CreateSubSerie(SubSerieDTO $subserie) {
        $subSerieEntity = new SubSerieEntity($this->db);
        return $subSerieEntity->CreateSubSerie($subserie);
    }
    
    /**
     * Permite obtener la información de una subserie a perteneciente a una serie y con un código en particular.
     * @param type $code código de la subserie a buscar
     * @param type $idSerie código de la serie
     * @return SubSerieDTO
     */
    public function GetSubserie($code, $idSerie) {
        $subSerieEntity = new SubSerieEntity($this->db);
        return $subSerieEntity->GetSubSerie($code, $idSerie);
    }
    
    /**
     * Elimina las subseries
     * @param type $dependencyCode
     * @return type 
     */
    public function DeleteSubSeriesByDependency($dependencyCode) {
        $subSerieEntity = new SubSerieEntity($this->db);
        return $subSerieEntity->DeleteSubSeriesByCode($dependencyCode);
    }
    
    /**
     * Obtiene el listado de dependencias habilitadas en el sistema con la posibilida de filtrar por algún estado en particular.
     * @param type $state Si está en null, trae todas las dependencias sin importar el estado.
     * @return type 
     */
    public function GetDependencias($state = null) {
        $dependenciaEntity = new DependenciaEntity($this->db);
        return $dependenciaEntity->GetList($state);
    }
    
    /**
     * Obtiene el listado de dependencias de una regional y asegurandose que la dependencia exista para la serie
     * @param type $idRegional
     * @param type $idSerie
     * @param type $state
     * @return type 
     */
    public function GetDependenciasSubSerieByRegional($idRegional, $idSerie, $state = null) {
        $dependenciaEntity = new DependenciaEntity($this->db);
        return $dependenciaEntity->GetDepeSubserieListByRegional($idRegional, $idSerie, $state);
    }
    
    /**
     * Obtiene los datos básicos de una dependencia a partir de su código.
     * @param type $code
     * @return DependenciaDTO 
     */
    public function GetDependenciaByCode($code) {
        $dependenciaEntity = new DependenciaEntity($this->db);
        return $dependenciaEntity->GetByCode($code);
    }
}
?>
