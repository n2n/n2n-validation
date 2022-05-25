<?php

namespace n2n\validation\plan;

use n2n\util\type\ArgUtils;

class ValidatableName {

	function __construct(private array $parts) {
		ArgUtils::valArray($parts, 'string');
	}

	/**
	 * @return string[]
	 */
	function toArray() {
		return $this->parts;
	}

	function __toString() {
		return join('/', $this->parts);
	}
}