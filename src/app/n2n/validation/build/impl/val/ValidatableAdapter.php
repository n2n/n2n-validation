<?php
namespace n2n\validation\build\impl\val;

use n2n\l10n\Message;
use n2n\validation\plan\Validatable;
use n2n\util\type\ArgUtils;
use n2n\l10n\Lstr;
use n2n\validation\plan\ValidatableName;

abstract class ValidatableAdapter implements Validatable {
	private ValidatableName $name;
	private Lstr|string|null $label;
	private array $messages = [];

	function __construct(ValidatableName $name, string|Lstr $label = null) {
		$this->name = $name;
		$this->label = $label;
	}
	
	function getName(): ValidatableName {
		return $this->name;
	}
	
	function getLabel(): string|Lstr|null {
		return $this->label;
	}
	
	public function addError(Message $message) {
		$this->messages[] = $message;
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
	function clearErrors(): void {
		$this->messages = [];
	}
	
}