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

class ValidationComposer { 
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
		return $this->props([$expression], $validators);
	}
	
	/**
	 * @param string[] $expressions
	 * @param Validator ...$validators
	 * @return ValidationComposer
	 * @throws UnresolvableValidationException
	 */
	function props(array $expressions, Validator ...$validators) {
		$validatables = [];
		foreach ($expressions as $expression) {
			$validatables = $this->validatableSource->resolveValidatables($expression);
			ArgUtils::valArrayReturn($validatables, $this->validatableSource, 'resolveValidatables', Validatable::class);
			array_push($validatables, ...$validatables);
		}
		
		$this->validationPlan->addValidationGroup(new ValidationGroup($validators, $validatables));
		return $this;
	}
	
	/**
	 * @return \n2n\validation\build\ValidationResult
	 */
	function exec() {
		$this->validatableSource->onValidationStart();
		$this->validationPlan->exec();
		return $this->validatableSource->createValidationResult();
	}
}