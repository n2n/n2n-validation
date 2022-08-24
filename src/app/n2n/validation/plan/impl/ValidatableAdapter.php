<?php
namespace n2n\validation\plan\impl;

use n2n\l10n\Message;
use n2n\validation\plan\Validatable;
use n2n\l10n\Lstr;
use n2n\validation\plan\DetailedName;

abstract class ValidatableAdapter implements Validatable {
	private DetailedName $name;
	private Lstr|string|null $label;
	private array $messages = [];

	function __construct(DetailedName $name, string|Lstr $label = null) {
		$this->name = $name;
		$this->label = $label;
	}
	
	function getName(): DetailedName {
		return $this->name;
	}
	
	function getLabel(): string|Lstr|null {
		return $this->label;
	}
	
	public function addError(Message $message) {
		$this->messages[] = $message;
	}

	function isValid(): bool {
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
	function clearErrors(): void {
		$this->messages = [];
	}
	
}