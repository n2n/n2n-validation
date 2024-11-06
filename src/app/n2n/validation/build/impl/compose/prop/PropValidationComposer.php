<?php
namespace n2n\validation\build\impl\compose\prop;

use n2n\validation\plan\ValidationGroup;
use n2n\validation\plan\ValidationPlan;
use n2n\validation\plan\ValidationContext;
use n2n\validation\validator\Validator;
use n2n\util\type\ArgUtils;
use n2n\validation\plan\Validatable;
use n2n\validation\plan\ValidationTask;
use n2n\util\magic\MagicContext;
use n2n\validation\plan\impl\SimpleValidationResult;
use n2n\validation\err\ValidationException;
use n2n\validation\plan\ValidationResult;
use n2n\util\magic\impl\MagicContexts;

class PropValidationComposer implements ValidationTask {
	/**
	 * @var PropValidationComposerSource
	 */
	private $validatableSource;
	/**
	 * @var ValidationPlan
	 */
	private $validationPlan;
	/**
	 * @var \Closure[]
	 */
	private $assembleClosures = [];
	
	/**
	 * @param ValidationContext $validationContext
	 */
	function __construct(PropValidationComposerSource $validatableSource) {
		$this->validatableSource = $validatableSource;
		$this->validationPlan = new ValidationPlan($validatableSource);
	}
	
	/**
	 * 
	 * @param string $expression
	 * @param Validator ...$validators
	 * @return PropValidationComposer
	 */
	function prop(string $expression, Validator ...$validators) {
		return $this->props([$expression], ...$validators);
	}
	
	/**
	 * @param string[] $expressions
	 * @param Validator ...$validators
	 * @return PropValidationComposer
	 */
	function props(array $expressions, Validator ...$validators) {
		$this->assembleValidationGroup($expressions, $validators, true);
		return $this;
	}
	
	/**
	 *
	 * @param string $expression
	 * @param Validator ...$validators
	 * @return PropValidationComposer
	 */
	function optProp(string $expression, Validator ...$validators) {
		return $this->optProps([$expression], ...$validators); 
	}
	
	/**
	 * @param string[] $expressions
	 * @param Validator ...$validators
	 * @return PropValidationComposer
	 */
	function optProps(array $expressions, Validator ...$validators) {
		$this->assembleValidationGroup($expressions, $validators, false);
		return $this;
	}
	
	/**
	 *
	 * @param string $expression
	 * @param bool $mustExist
	 * @param Validator ...$validators
	 * @return PropValidationComposer
	 */
	function dynProp(string $expression, bool $mustExist, Validator ...$validators) {
		return $this->dynProps([$expression], $mustExist, ...$validators);
	}
	
	/**
	 * @param string[] $expressions
	 * @param bool $mustExist
	 * @param Validator ...$validators
	 * @return PropValidationComposer
	 */
	function dynProps(array $expressions, bool $mustExist, Validator ...$validators) {
		$this->assembleValidationGroup($expressions, $validators, $mustExist);
		return $this;
	}
	
	private function assembleValidationGroup(array $expressions, array $validators, bool $mustExist) {
		ArgUtils::valArray($expressions, 'string', false, 'expressions');
		
		$this->assembleClosures[] = function() use ($expressions, $validators, $mustExist) {
			$validatables = [];
			foreach ($expressions as $expression) {
				$resolvedValidatables = $this->validatableSource->resolveValidatables($expression, $mustExist);
				ArgUtils::valArrayReturn($resolvedValidatables, $this->validatableSource, 'resolveValidatables', Validatable::class);
				array_push($validatables, ...$resolvedValidatables);
			}

			$this->validationPlan->addValidationGroup(new ValidationGroup($validators, $validatables,
					$this->validatableSource));
		};
	}
	
	private function prepareJob() {
		while (null !== ($closure = array_shift($this->assembleClosures))) {
			$closure();
		}
	}
	
	/**
	 * @param MagicContext $magicContext
	 * @return bool
	 */
	function test(MagicContext $magicContext): bool {
		$this->prepareJob();
		
		return $this->validationPlan->test($magicContext);
	}

	/**
	 * @param MagicContext|null $magicContext
	 * @param mixed|null $input
	 * @return ValidationResult
	 */
	function exec(MagicContext $magicContext = null, mixed $input = null): ValidationResult {
		$magicContext ??= MagicContexts::simple([]);

		$this->prepareJob();

		return $this->validationPlan->exec($magicContext);
	}
}