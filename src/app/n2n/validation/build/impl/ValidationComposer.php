<?php
namespace n2n\validation\build\impl;

use n2n\validation\err\UnresolvableValidationException;
use n2n\validation\plan\ValidationGroup;
use n2n\validation\plan\ValidationPlan;
use n2n\validation\plan\ValidatableResolver;
use n2n\validation\plan\Validator;
use n2n\validation\build\ValidatableSource;
use n2n\util\type\ArgUtils;
use n2n\validation\plan\Validatable;
use n2n\validation\build\ValidationJob;
use n2n\validation\build\ValidationResult;
use n2n\util\magic\MagicContext;
use n2n\validation\err\ValidationMismatchException;

class ValidationComposer implements ValidationJob { 
	/**
	 * @var ValidatableSource
	 */
	private $validatableSource;
	/**
	 * @var ValidationPlan
	 */
	private $validationPlan;
	/**
	 * @var \Closure
	 */
	private $assembleClosures = [];
	
	/**
	 * @param ValidatableResolver $validatableResolver
	 */
	function __construct(ValidatableSource $validatableSource) {
		$this->validatableSource = $validatableSource;
		$this->validationPlan = new ValidationPlan($validatableSource);
	}
	
	/**
	 * 
	 * @param string $expression
	 * @param Validator ...$validators
	 * @return ValidationComposer
	 */
	function prop(string $expression, Validator ...$validators) {
		return $this->props([$expression], ...$validators);
	}
	
	/**
	 * @param string[] $expressions
	 * @param Validator ...$validators
	 * @return ValidationComposer
	 */
	function props(array $expressions, Validator ...$validators) {
		$this->assembleValidationGroup($expressions, $validators, true);
		return $this;
	}
	
	/**
	 *
	 * @param string $expression
	 * @param Validator ...$validators
	 * @return ValidationComposer
	 */
	function optProp(string $expression, Validator ...$validators) {
		return $this->optProps([$expression], ...$validators); 
	}
	
	/**
	 * @param string[] $expressions
	 * @param Validator ...$validators
	 * @return ValidationComposer
	 */
	function optProps(array $expressions, Validator ...$validators) {
		$this->assembleValidationGroup($expressions, $validators, false);
		return $this;
	}
	
	private function assembleValidationGroup(array $expressions, array $validators, bool $mustExist) {
		ArgUtils::valArray($expressions, 'string', false, 'expressions');
		
		array_push($this->assembleClosures, function () use ($expressions, $validators, $mustExist) {
			$validatables = [];
			foreach ($expressions as $expression) {
				$resolvedValidatables = $this->validatableSource->resolveValidatables($expression, $mustExist);
				ArgUtils::valArrayReturn($resolvedValidatables, $this->validatableSource, 'resolveValidatables', Validatable::class);
				array_push($validatables, ...$resolvedValidatables);
			}
			
			$this->validationPlan->addValidationGroup(new ValidationGroup($validators, $validatables));
		});
	}
	
	/**
	 * @throws UnresolvableValidationException
	 * @throws ValidationMismatchException
	 * @return \n2n\validation\build\ValidationResult
	 */
	function exec(MagicContext $magicContext): ValidationResult {
		while (null !== ($closure = array_shift($this->assembleClosures))) {
			$closure();
		}
		
		$this->validatableSource->onValidationStart();
		$this->validationPlan->exec($magicContext);
		return $this->validatableSource->createValidationResult();
	}
}