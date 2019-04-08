<?php
/**
 * Contiene la definición de atributos para los paises manejados en el sistema.
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 11:26:58 AM
 */
class PaisDTO {
    
    public $idPais;
    
    public $nombre;
    
    /**
     * Continente al cual está asociado el país
     * @var ContinenteDTO 
     */
    public $continente;
    
    public function PaisDTO($idPais = NULL) {
        if($idPais != NULL) {
            $this->idPais = $idPais;
        }
    }
}

?>
