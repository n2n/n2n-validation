<?php
namespace n2n\validation\validator\impl\string;

use n2n\validation\plan\Validatable;
use n2n\validation\lang\ValidationMessages;
use n2n\l10n\Message;
use n2n\util\type\TypeConstraints;
use n2n\util\magic\MagicContext;
use n2n\validation\validator\impl\SimpleValidatorAdapter;
use n2n\util\io\IoUtils;

class SpecialCharsValidator extends SimpleValidatorAdapter {
	public function __construct(Message $errorMessage = null) {
		parent::__construct($errorMessage);
	}

	protected function testSingle(Validatable $validatable, MagicContext $magicContext): bool {
		$value = $this->readSafeValue($validatable, TypeConstraints::type(['string', 'null']));

		if ($value === null || !IoUtils::hasSpecialChars($value)) {
			return true;
		}

		return false;
	}

	protected function validateSingle(Validatable $validatable, MagicContext $magicContext): void {
		$value = $this->readSafeValue($validatable, TypeConstraints::type(['string', 'null']));

		if ($value === null || !IoUtils::hasSpecialChars($value))  {
			return;
		}

		$validatable->addError($this->getErrorMessage()
				?? ValidationMessages::specialChars($this->readLabel($validatable)));
	}
}
