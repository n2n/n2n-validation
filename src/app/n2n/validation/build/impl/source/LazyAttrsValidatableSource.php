<?php
namespace n2n\validation\build\impl\source;

use n2n\util\type\attrs\AttributeReader;
use n2n\util\type\attrs\AttributePath;
use n2n\validation\err\UnresolvableValidationException;
use n2n\util\type\attrs\AttributesException;
use n2n\util\type\ArgUtils;
use n2n\validation\build\impl\val\ValueValidatable;
use n2n\validation\build\impl\compose\prop\PropValidatableSource;
use n2n\validation\plan\ValidatableName;

class LazyAttrsValidatableSource extends ValidatableSourceAdapter implements PropValidatableSource {

	function __construct(private AttributeReader $attributeReader) {
		parent::__construct([]);
	}
	
	public function resolveValidatables(string $expression, bool $mustExist): array {
		ArgUtils::valType($expression, 'string', false, 'expression');
		
		$attrValidatable = null;
		if (isset($this->validatables[$expression])) {
			$attrValidatable = $this->validatables[$expression];
			
			if (!$mustExist || $attrValidatable->doesExist()) {
				return [$attrValidatable];
			}
		}

		$attributePath = AttributePath::create($expression);
		$validatableName = new ValidatableName($attributePath->toArray());

		try {
			$value = $this->attributeReader->readAttribute($attributePath);
			if ($attrValidatable === null) {
				return [$this->validatables[$expression] = new ValueValidatable($validatableName, $value, true)];
			}

			$attrValidatable->setValue($value);
			$attrValidatable->setDoesExist(true);
			return $attrValidatable;
		} catch (AttributesException $e) {
			if ($mustExist) {
				throw new UnresolvableValidationException('Could not resolve validatable: ' . $expression, null, $e);
			}

			return [$this->validatables[$expression] = new ValueValidatable($validatableName, null, false)];
		}
	}	
}
