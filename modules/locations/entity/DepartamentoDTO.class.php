<?php
/**
 * Contiene la definicion de atributos para los Departamentos 
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 11:28:49 AM
 */
class DepartamentoDTO {
    
    public $idDepartamento;
    
    public $nombre;
    
    /**
     * País al cual está asociado el departamento.
     * @var PaisDTO 
     */
    public $pais;
    
    public function DepartamentoDTO($idDepartamento = NULL) {
        if($idDepartamento != NULL) {
            $this->idDepartamento = $idDepartamento;
        }
    }
}

?>
