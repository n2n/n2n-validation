<?php
namespace n2n\validation\build\impl\attrs;

use n2n\l10n\Message;
use n2n\util\type\TypeConstraint;
use n2n\util\type\attrs\AttributeReader;
use n2n\util\type\attrs\AttributePath;
use n2n\validation\build\impl\ValidatableAdapter;
use n2n\validation\build\ValidatableSource;
use n2n\validation\build\ValidationResult;
use n2n\validation\build\ErrorMap;

class AttrsValidatableSource implements ValidatableSource {
	private $attributeReader;
	private $generalMessages = [];
	private $attrValidatables = [];
	
	function __construct(AttributeReader $attributeReader) {
		$this->attributeReader = $attributeReader;
	}
	
	public function resolveValidatables(string $expression): array {
		if (!isset($this->attrValidatables[$expression])) {
			$this->attrValidatables[$expression] = new AttrValidatable($expression, 
					$this->attributeReader->readAttribute(AttributePath::create($expression)));
		}
		
		return [$this->attrValidatables[$expression]];
	}

	public function createValidationResult(): ValidationResult {
		$errorMap = new ErrorMap($this->generalMessages);
		
		foreach ($this->attrValidatables as $key => $attrValidatable) {
			$errorMap->putChild($key, new ErrorMap($attrValidatable->getMessages()));
		}
		
		return new ValidationResult($errorMap->isEmpty() ? null : $errorMap);
	}

	public function onValidationStart() {
		$this->messages = [];
		foreach ($this->attrValidatables as $attrValidatable) {
			$attrValidatable->clearErrors();
		}
	}
	
	public function addGeneralError(Message $message) {
		$this->messages[] = $message;
	}
}

class AttrValidatable extends ValidatableAdapter {
	private $name;
	private $value;
	
	function __construct(string $name, $value) {
		parent::__construct($name);
		$this->name = $name;
		$this->value = $value;
	}
	
	function getValue() {
		return $this->value;
	}

	public function getTypeConstraint(): ?TypeConstraint {
		return null;
	}
}