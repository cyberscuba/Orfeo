<?php
/**
 * Permite interpretar un arreglo de bytes a un archivo.
 * Enter description here ...
 * @param $ContentType encabezado del archivo(jpeg, tiff, etc..)
 * @param $Bytes arreglo de bytes que se desea interpretar
 */
 function TrasmitFile($ContentType, $Bytes, $filename)
 {
 	header('Content-Type:'.$ContentType); 
        header('Content-Disposition: filename="'.$filename.'"');
  	header('Content-Length: '.strlen($Bytes));
  	ob_clean();
  	flush();
  	echo $Bytes;
 }
?>