<?php
namespace n2n\validation\validator\impl\closure;

use n2n\validation\validator\impl\ValidatorAdapter;
use n2n\util\magic\MagicContext;
use n2n\validation\plan\ValidationContext;
use Closure;
use n2n\validation\validator\Validator;

class ConditionalValidator extends ValidatorAdapter {
	private Closure $condition;
	private Validator $validator;

	public function __construct(Closure $condition, Validator $validator) {
		$this->condition = $condition;
		$this->validator = $validator;
	}

	function test(array $validatables, ValidationContext $validationContext, MagicContext $magicContext): bool {
		if (($this->condition)($validatables, $validationContext, $magicContext)) {
			return $this->validator->test($validatables, $validationContext, $magicContext);
		}

		return true;
	}

	function validate(array $validatables, ValidationContext $validationContext, MagicContext $magicContext) {
		if (($this->condition)($validatables, $validationContext, $magicContext)) {
			$this->validator->validate($validatables, $validationContext, $magicContext);
		}
	}
}