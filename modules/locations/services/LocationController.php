<?php
/**
 * Controlador que procesa las acciones relacionadas a las ubicaciones en el sistema.
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 12:03:40 PM
 */
class LocationController extends BaseController{
    
    /**
     * Obtiene el listado de continentes en condificación JSON
     */
    public function GetContinentes() {
        $locationServices = new LocationServices($this->db);
        $continentes = $locationServices->GetContinentes();
        
        echo json_encode($continentes);
    }
    
    /**
     * Obtiene el listado de paises a partir de un continente
     */
    public function GetPaises() {
        $locationServices = new LocationServices($this->db);
        $paises = $locationServices->GetPaises($_POST['idContinente']);
        
        echo json_encode($paises);
    }
    
    /**
     * Obtiene el listado de departamentos a partir de un pais
     */
    public function GetDepartamentos() {
        $locationServices = new LocationServices($this->db);
        $departamentos = $locationServices->GetDepartamentos($_POST['idPais']);
        
        echo json_encode($departamentos);
    }
    
    /**
     * Obtiene el listado de municipios a partir de un departamento
     */
    public function GetMunicipios() {
        $locationServices = new LocationServices($this->db);
        $municipios = $locationServices->GetMunicipios($_POST['idDepartamento']);
        
        echo json_encode($municipios);
    }
}

?>
