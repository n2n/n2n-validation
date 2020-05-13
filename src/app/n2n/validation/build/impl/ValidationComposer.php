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
	 * @throws UnresolvableValidationException
	 */
	function prop(string $expression, Validator ...$validators) {
		return $this->props([$expression], ...$validators);
	}
	
	/**
	 * @param string[] $expressions
	 * @param Validator ...$validators
	 * @return ValidationComposer
	 * @throws UnresolvableValidationException
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
	 * @throws UnresolvableValidationException
	 */
	function optProp(string $expression, Validator ...$validators) {
		return $this->optProps([$expression], ...$validators);
	}
	
	/**
	 * @param string[] $expressions
	 * @param Validator ...$validators
	 * @return ValidationComposer
	 * @throws UnresolvableValidationException
	 */
	function optProps(array $expressions, Validator ...$validators) {
		$this->assembleValidationGroup($expressions, $validators, false);
		return $this;
	}
	
	private function assembleValidationGroup(array $expressions, array $validators, bool $mustExist) {
		$validatables = [];
		foreach ($expressions as $expression) {
			ArgUtils::assertTrue(is_string($expression), 'Property expressions must be of type string.');
			$resolvedValidatables = $this->validatableSource->resolveValidatables($expression, $mustExist);
			ArgUtils::valArrayReturn($resolvedValidatables, $this->validatableSource, 'resolveValidatables', Validatable::class);
			array_push($validatables, ...$resolvedValidatables);
		}
		
		$this->validationPlan->addValidationGroup(new ValidationGroup($validators, $validatables));
	}
	
	/**
	 * @return \n2n\validation\build\ValidationResult
	 */
	function exec(MagicContext $magicContext): ValidationResult {
		$this->validatableSource->onValidationStart();
		$this->validationPlan->exec($magicContext);
		return $this->validatableSource->createValidationResult();
	}
}