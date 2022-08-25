<?php
namespace n2n\validation\build\impl\source;

use n2n\validation\plan\Validatable;
use n2n\validation\build\impl\compose\union\UnionValidationComposerSource;

class StaticUnionValidationComposerSource extends ComposerSourceAdapter implements UnionValidationComposerSource {
	
	/**
	 * @param Validatable[] $validatables
	 */
	function __construct(array $validatables) {
		parent::__construct($validatables);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \n2n\validation\build\impl\compose\union\UnionValidationComposerSource::getValidatables()
	 */
	function getValidatables(): array {
		return parent::getValidatables();
	}	
}