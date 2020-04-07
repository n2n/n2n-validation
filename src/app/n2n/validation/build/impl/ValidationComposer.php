<?php
namespace n2n\bind\validation;

use n2n\validation\err\UnresolvableValidationException;
use n2n\validation\plan\ValidationGroup;
use n2n\validation\plan\ValidationPlan;

class ValidationComposer { 
	/**
	 * @var ValidatableResolver
	 */
	private $validatableResolver;
	/**
	 * @var ValidationPlan
	 */
	private $validationPlan;
	
	/**
	 * @param ValidatableResolver $validatableResolver
	 */
	function __construct(ValidatableResolver $validatableResolver) {
		$this->validatableResolver = $validatableResolver;
		$this->validationPlan = new ValidationPlan($validatableResolver);
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
			array_push($validatables, ...$this->validatableResolver->defineValidatables($expression));
		}
		
		$this->plan->addGroup(new ValidationGroup($validators, $validatables));
		return $this;
	}
}