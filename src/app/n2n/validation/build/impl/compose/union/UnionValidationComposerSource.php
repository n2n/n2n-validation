<?php
namespace n2n\validation\build\impl\compose\union;

use n2n\validation\plan\Validatable;
use n2n\validation\plan\ValidatableSource;
use n2n\validation\plan\ValidationContext;

interface UnionValidationComposerSource extends ValidatableSource, ValidationContext {
	
	/**
	 * @return Validatable[]
	 */
	function getValidatables(): array;
}
