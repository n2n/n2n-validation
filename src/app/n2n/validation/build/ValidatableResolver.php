<?php
namespace n2n\bind\validation;

use rocket\ei\manage\entry\ValidationResult;
use n2n\validation\plan\Validatable;
use n2n\validation\err\UnresolvableValidationException;

interface ValidatableResolver {

	/**
	 * @param string
	 * @return Validatable[]
	 * @throws UnresolvableValidationException
	 */
	function defineValidatables(string $expression): array;
	
	/**
	 * A new validation cycle begins. All errors of defined validatables should be removed
	 */
	function restart();
	
	/**
	 * @return ValidationResult
	 */
	function createValidationResult(): ValidationResult;
}