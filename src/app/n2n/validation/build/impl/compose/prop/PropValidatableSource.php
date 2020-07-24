<?php
namespace n2n\validation\build\impl\source\prop;


use n2n\validation\build\ValidatableSource;
use n2n\validation\plan\Validatable;
use n2n\validation\err\UnresolvableValidationException;

interface PropValidatableSource extends ValidatableSource {
	
	/**
	 * @param string $expression
	 * @param bool $mustExist
	 * @return Validatable[]
	 * @throws UnresolvableValidationException
	 */
	function resolveValidatables(string $expression, bool $mustExist): array;
}