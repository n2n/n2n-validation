<?php
namespace n2n\validation\build\impl\source;

use n2n\l10n\Message;
use n2n\validation\plan\Validatable;
use n2n\validation\plan\ErrorMap;
use n2n\util\type\ArgUtils;
use n2n\validation\plan\ValidatableSource;
use n2n\validation\plan\ValidationContext;
use n2n\util\ex\IllegalStateException;
use n2n\util\type\attrs\AttributePath;

abstract class ComposerSourceAdapter implements ValidatableSource, ValidationContext {
	/**
	 * @var Validatable[]
	 */
	private array $validatables = [];
	private $generalMessages = [];

	function __construct(array $validatables = []) {
		ArgUtils::valArray($validatables, Validatable::class);
		foreach ($validatables as $validatable) {
			$this->addValidatable($validatable);
		}
	}
	
	public function addGeneralError(Message $message) {
		$this->generalMessages[] = $message;
	}

	/**
	 * @param Validatable $validatable
	 * @return void
	 */
	protected function addValidatable(Validatable $validatable): void {
		$nameStr = $validatable->getPath()->__toString();
		if (isset($this->validatables[$nameStr])) {
			throw new IllegalStateException('Validatable \''  . $nameStr . '\' already defined.');
		}

		$this->validatables[$nameStr] = $validatable;
	}

	/**
	 * @param AttributePath $detailedName
	 * @return Validatable|null
	 */
	protected function getValidatable(AttributePath $detailedName) {
		return $this->validatables[$detailedName->__toString()] ?? null;
	}

	/**
	 * @return Validatable[]
	 */
	protected function getValidatables() {
		return $this->validatables;
	}
	
	function createErrorMap(): ErrorMap {
		$errorMap = new ErrorMap($this->generalMessages);
		
		foreach ($this->validatables as $attrValidatable) {
			$errorMap->putDecendant($attrValidatable->getPath()->toArray(), new ErrorMap($attrValidatable->getMessages()));
		}
		
		return $errorMap;
	}
	
	function reset() {
		$this->generalMessages = [];
		foreach ($this->getValidatables() as $validatable) {
			$validatable->reset();
		}
	}

	
}