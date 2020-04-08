<?php
namespace n2n\validation\build\impl;

use n2n\util\type\attrs\DataMap;
use n2n\util\type\attrs\AttributeReader;
use n2n\validation\build\impl\attrs\AttrsValidatableSource;
use n2n\validation\plan\Validator;


class Validate {
	/**
	 * @param mixed|null $value
	 * @param Validator ...$validators
	 */
	static function value($value, Validator ...$validators) {
		
	}
	
	/**
	 * @param DataMap $attrs
	 * @return ValidationComposer
	 */
	static function attrs(AttributeReader $attributeReader) {
		return new ValidationComposer(new AttrsValidatableSource($attributeReader));
	}
	
	/**
	 * @param array $data
	 */
	static function array($data) {
	}
}
