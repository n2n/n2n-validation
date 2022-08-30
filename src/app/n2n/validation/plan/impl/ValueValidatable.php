<?php
namespace n2n\validation\plan\impl;

use n2n\validation\plan\DetailedName;

class ValueValidatable extends ValidatableAdapter {

	function __construct(DetailedName $name, private mixed $value, private bool $doesExist, string $label = null) {
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