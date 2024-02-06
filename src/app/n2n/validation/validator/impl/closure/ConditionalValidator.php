<?php
namespace n2n\validation\validator\impl\closure;

use n2n\validation\validator\impl\ValidatorAdapter;
use n2n\util\magic\MagicContext;
use n2n\validation\plan\ValidationContext;
use Closure;
use n2n\validation\validator\Validator;
use n2n\reflection\magic\MagicMethodInvoker;
use n2n\util\type\TypeConstraints;

class ConditionalValidator extends ValidatorAdapter {
	private Closure $condition;
	private Validator $validator;

	public function __construct(Closure|bool $condition, Validator $validator) {
		$this->condition = $condition;
		$this->validator = $validator;
	}

	function test(array $validatables, ValidationContext $validationContext, MagicContext $magicContext): bool {
		if ($this->checkCondition($validationContext, $magicContext)) {
			return $this->validator->test($validatables, $validationContext, $magicContext);
		}

		return true;
	}

	function validate(array $validatables, ValidationContext $validationContext, MagicContext $magicContext) {
		if ($this->checkCondition($validationContext, $magicContext)) {
			$this->validator->validate($validatables, $validationContext, $magicContext);
		}
	}

	private function checkCondition(ValidationContext $validationContext, MagicContext $magicContext) {
		if (is_bool($this->condition)) {
			return $this->condition;
		}

		$invoker = new MagicMethodInvoker($magicContext);
		$invoker->setClassParamObject(ValidationContext::class, $validationContext);
		$invoker->setReturnTypeConstraint(TypeConstraints::bool());
		$invoker->setClosure($this->condition);
		return $invoker->invoke();
	}
}