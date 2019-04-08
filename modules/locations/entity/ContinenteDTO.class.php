<?php
/**
 * Contiene la definición para un continente
 *
 * @author Alexander Giraldo
 * @since 27/04/2011 11:19:01 AM
 */
class ContinenteDTO {
    
    public $idContinente;
    
    public $nombre;
    
    public function ContinenteDTO($idContinente = NULL) {
        if($idContinente != NULL) {
            $this->idContinente = $idContinente;
        }
    }
    
}

?>
