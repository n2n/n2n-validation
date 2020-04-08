<?php
namespace n2n\validation\build\impl\attrs;

use n2n\l10n\Message;
use n2n\util\type\TypeConstraint;
use n2n\util\type\attrs\AttributeReader;
use n2n\util\type\attrs\AttributePath;
use n2n\validation\impl\ValidatableAdapter;
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
		if ($this->attrValidatables[$expression]) {
			return $this->attrValidatables[$expression];
		}
		
		return $this->attrValidatables[$expression] = $this->attributeReader->readAttribute(AttributePath::create($expression));
	}

	public function createValidationResult(): ValidationResult {
		$errorMap = new ErrorMap($this->generalMessages);
		
		foreach ($this->attrValidatables as $attrValidatable) {
			$errorMap->addChild(new ErrorMap($attrValidatable->getMessages()));
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

	public function getName(): string {
	}

	public function getTypeConstraint(): ?TypeConstraint {
	}

	public function isOpenForValidation(): bool {
	}

	
}