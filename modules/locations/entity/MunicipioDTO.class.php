<?php
/**
 * Description of MunicipioDTO
 *
 * @author Alex
 * @since 27/04/2011 11:06:01 AM
 */
class MunicipioDTO {
    
    public $idMunicipio;
    public $nombre;
    /**
     * Contiene la referencia l departamento al cual pertenece el municipio
     * @var DepartamentoDTO 
     */
    public $departamento;
}

?>
