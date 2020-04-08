<?php
namespace n2n\validation\impl;

use n2n\l10n\Message;
use n2n\validation\plan\Validatable;
use n2n\util\type\ArgUtils;
use n2n\l10n\Lstr;

abstract class ValidatableAdapter implements Validatable {
	private $name;
	private $label;
	private $messages = [];

	function __construct(string $name, $label) {
		$this->name = $name;
		ArgUtils::valType($label, [Lstr::class, 'string']);
		$this->label = $label;
	}
	
	function getName() {
		return $this->name;
	}
	
	public function addError(Message $message) {
		array_push($this->messages, $message);
	}

	

	function isOpenForValidation(): bool {
		return empty($this->messages);
	}
	
	/**
	 * @return Message[]
	 */
	function getMessages() {
		return $this->messages;
	}

	/**
	 * 
	 */
	function clearErrors() {
		$this->messages = [];
	}
	
}