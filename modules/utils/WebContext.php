<?php

/**
 * Clase que contiene operaciones y atributos utiles para utilizar en el contexto Web.
 *
 * @author Alexander Giraldo
 * @since 10/02/2010 11:20:53
 * @package utils
 */
class WebContext {

	/**
	 * Contiene la instancia de la clase que guarda la información del contexto
	 * @var unknown_type
	 */
	private static $context;

	/**
	 * Contiene los mensajes de error
	 * @var unknown_type
	 */
	private $errorMessages = Array();

	/**
	 * Contiene el listado de mensajes de Warning
	 * @var unknown_type
	 */
	private $warningMessages = Array();

	/**
	 * Contiene los mensajes de confirmación
	 * @var unknown_type
	 */
	private $confirmationMessages = Array();

	/**
	 * Constantes que definen el tipo de mensaje
	 * @var unknown_type
	 */
	const ERROR = 1;
	const WARNING = 2;
	const CONFIRMATION = 3;

	/**
	 * Obtiene el contexto utilizado
         * @return WebContext
	 */
	public static function getContext() {
		if(!self::$context instanceof self) {
			self::$context = new self;
		}

		return self::$context;
	}

	/**
	 * Adiciona un mensaje a la lista de mensajes dependiendo el tipo
	 * @param $message
	 * @param $type Tipo de mensaje ERROR, WARNING o CONFIRMATION
	 */
	public function addMessage($message, $type) {
		if($type == ERROR) {
			array_push($this->errorMessages, $message);
		} else if($type == WARNING) {
			array_push($this->warningMessages, $message);
		} else if($type == CONFIRMATION) {
			array_push($this->confirmationMessages, $message);
		}
	}

	/**
	 * Imprime los mensajes de un tipo en un Div con su clase
	 * @param unknown_type $type
	 */
	public function printMessagesAsDiv($type, $styleClass) {
		$messages = self::obtainMessages($type);
		if(count($messages) > 0) {
			echo '<div class="'.$styleClass.'" style="display: block;">';
			foreach ($messages as $msg) {
				echo $msg."<br/>";
			}
			echo "</div>";
		}
	}

	/**
	 * Obtiene los mensajes a partir de su tipo
	 * @param unknown_type $type
	 */
	private function obtainMessages($type) {
		if($type == ERROR) {
			return $this->errorMessages;
		} else if($type == WARNING) {
			return $this->warningMessages;
		} else if($type == CONFIRMATION) {
			return $this->confirmationMessages;
		}
	}

	/**
	 * Obtiene el listado de mensajes de error adicionados
	 */
	public function getErrorMessages() {
		return $this->errorMessages;
	}

	/**
	 * Obtiene el listado de mensajes de Warning adicionados
	 */
	public function getWarningMessages() {
		return $this->warningMessages;
	}

	/**
	 * Obtiene el listado de mensajes de Confirmacion adicionados
	 */
	public function getConfirmationMessages() {
		return $this->confirmationMessages;
	}

}

?>