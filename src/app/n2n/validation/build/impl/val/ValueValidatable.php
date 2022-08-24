<?php
namespace n2n\validation\build\impl\val;

use n2n\util\type\TypeConstraint;
use n2n\validation\plan\ValidatableName;

class ValueValidatable extends ValidatableAdapter {

	function __construct(ValidatableName $name, private mixed $value, private bool $doesExist, string $label = null) {
		parent::__construct($name, $label);
	}
	
	function getValue() {
		return $this->value;
	}
	
	function doesExist(): bool {
		return $this->doesExist;
	}
	
	function setDoesExist(bool $doesExists) {
		$this->doesExist = $doesExists;
	}
	
//	public function getTypeConstraint(): ?TypeConstraint {
//		return null;
//	}
}