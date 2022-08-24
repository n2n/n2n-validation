<?php
namespace n2n\validation\build\impl\compose\prop;

use n2n\validation\plan\Validatable;
use n2n\validation\err\UnresolvableValidationException;
use n2n\validation\plan\ValidatableSource;

interface PropValidatableSource extends ValidatableSource {
	
	/**
	 * @param string $expression
	 * @param bool $mustExist
	 * @return Validatable[]
	 * @throws UnresolvableValidationException only if $mustExist is true
	 */
	function resolveValidatables(string $expression, bool $mustExist): array;
}