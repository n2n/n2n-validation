<?php
namespace n2n\validation\build\impl\source;

use n2n\util\type\attrs\AttributeReader;
use n2n\util\type\attrs\AttributePath;
use n2n\validation\err\UnresolvableValidationException;
use n2n\util\type\attrs\AttributesException;
use n2n\validation\plan\impl\ValueValidatable;
use n2n\validation\build\impl\compose\prop\PropValidationComposerSource;

class AttrsPropValidationComposerSource extends ComposerSourceAdapter implements PropValidationComposerSource {

	function __construct(private AttributeReader $attributeReader) {
		parent::__construct([]);
	}
	
	public function resolveValidatables(string $expression, bool $mustExist): array {
		$attributePath = AttributePath::create($expression);
		$detailedName = new AttributePath($attributePath->toArray());

		$validatable = $this->getValidatable($detailedName);
		if ($validatable !== null && (!$mustExist || $validatable->doesExist())) {
			return [$validatable];
		}

		if ($validatable !== null) {
			throw new UnresolvableValidationException('Validatable not found: ' . $attributePath);
		}

		$valueValidatable = null;
		try {
			$value = $this->attributeReader->readAttribute($attributePath);
			$valueValidatable = new ValueValidatable($detailedName, $value, true);
		} catch (AttributesException $e) {
			if ($mustExist) {
				throw new UnresolvableValidationException('Could not resolve validatable: '
						. $expression, null, $e);
			}

			$valueValidatable = new ValueValidatable($detailedName, null, false);
		}

		$this->addValidatable($valueValidatable);

		return [$valueValidatable];
	}	
}
