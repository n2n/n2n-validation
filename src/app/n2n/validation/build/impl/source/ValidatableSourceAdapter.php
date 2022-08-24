<?php
namespace n2n\validation\build\impl\source;

use n2n\l10n\Message;
use n2n\validation\build\ValidationResult;
use n2n\validation\plan\Validatable;
use n2n\validation\build\ErrorMap;
use n2n\util\type\ArgUtils;
use n2n\validation\build\impl\val\SimpleValidationResult;
use n2n\validation\plan\ValidatableSource;
use n2n\validation\plan\ValidationContext;

abstract class ValidatableSourceAdapter implements ValidatableSource, ValidationContext {
	/**
	 * @var Validatable[]
	 */
	protected array $validatables = [];
	private $generalMessages = [];

	function __construct(array $validatables) {
		ArgUtils::valArray($validatables, Validatable::class);
		$this->validatables = $validatables;
	}
	
	public function addGeneralError(Message $message) {
		$this->generalMessages[] = $message;
	}
	
	function createValidationResult(): ValidationResult {
		$errorMap = new ErrorMap($this->generalMessages);
		
		foreach ($this->validatables as $attrValidatable) {
			$errorMap->putDecendant($attrValidatable->getName()->toArray(), new ErrorMap($attrValidatable->getMessages()));
		}
		
		return new SimpleValidationResult($errorMap->isEmpty() ? null : $errorMap);
	}
	
	function reset() {
		$this->generalMessages = [];
		foreach ($this->validatables as $validatable) {
			$validatable->clearErrors();
		}
	}

	
}