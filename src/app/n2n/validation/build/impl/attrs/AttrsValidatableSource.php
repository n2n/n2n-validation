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
use n2n\validation\build\impl\SimpleValidationResult;
use n2n\util\type\attrs\MissingAttributeFieldException;
use n2n\validation\err\UnresolvableValidationException;
use n2n\util\type\attrs\AttributesException;

class AttrsValidatableSource implements ValidatableSource {
	private $attributeReader;
	private $generalMessages = [];
	private $attrValidatables = [];
	
	function __construct(AttributeReader $attributeReader) {
		$this->attributeReader = $attributeReader;
	}
	
	public function resolveValidatables(string $expression, bool $mustExist): array {
		$attrValidatable = null;
		if (isset($this->attrValidatables[$expression])) {
			$attrValidatable = $this->attrValidatables[$expression];
			
			if (!$mustExist || $attrValidatable->doesExist()) {
				return [$attrValidatable];
			}
		}
		
		try {
			$value = $this->attributeReader->readAttribute(AttributePath::create($expression));
			if ($attrValidatable === null) {
				return [$this->attrValidatables[$expression] = new AttrValidatable($expression, $value, true)];
			}
			
			$attrValidatable->setValue($value);
			$attrValidatable->setDoesExist(true);
			return $attrValidatable;
		} catch (AttributesException $e) {
			if ($mustExist) {
				throw new UnresolvableValidationException('Could not resolve validatable: ' . $expression, null, $e);
			}
			
			return [$this->attrValidatables[$expression] = new AttrValidatable($expression, null, false)];
		}
	}

	public function createValidationResult(): ValidationResult {
		$errorMap = new ErrorMap($this->generalMessages);
		
		foreach ($this->attrValidatables as $key => $attrValidatable) {
			$errorMap->putChild($key, new ErrorMap($attrValidatable->getMessages()));
		}
		
		return new SimpleValidationResult($errorMap->isEmpty() ? null : $errorMap);
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
	private $doesExist;
	
	function __construct(string $name, $value, bool $doesExist) {
		parent::__construct($name);
		$this->name = $name;
		$this->value = $value;
		$this->doesExist = $doesExist;
	}
	
	function getValue() {
		return $this->value;
	}
	
	function doesExist(): bool {
		return $this->doesExist; 
	}
		
	function setDoesExist(bool $doesExists) {
		return $this->doesExist = $doesExists;
	}

	public function getTypeConstraint(): ?TypeConstraint {
		return null;
	}
}