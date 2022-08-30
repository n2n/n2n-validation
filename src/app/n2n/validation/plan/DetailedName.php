<?php

namespace n2n\validation\plan;

use n2n\util\type\ArgUtils;
use n2n\util\col\ArrayUtils;

class DetailedName {

	function __construct(private array $parts) {
		ArgUtils::valArray($parts, 'string');
		ArgUtils::assertTrue(!empty($this->parts), 'Name must not be empty.');
	}

	/**
	 * @return string[]
	 */
	function toArray() {
		return $this->parts;
	}

	/**
	 * @return string
	 */
	function getSymbolicName() {
		return ArrayUtils::end($this->parts);
	}

	function __toString() {
		return join('/', $this->parts);
	}
}