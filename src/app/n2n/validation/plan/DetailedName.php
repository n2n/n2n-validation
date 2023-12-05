<?php

namespace n2n\validation\plan;

use n2n\util\type\ArgUtils;
use n2n\util\col\ArrayUtils;

class DetailedName {

	function __construct(private array $parts) {
		ArgUtils::valArray($parts, 'string');
	}

	/**
	 * @return string[]
	 */
	function toArray(): array {
		return $this->parts;
	}

	/**
	 * @return string
	 */
	function getSymbolicName(): string {
		return ArrayUtils::end($this->parts) ?? '<root>';
	}

	function __toString() {
		if (empty($this->parts)) {
			return '<root>';
		}

		return join('/', $this->parts);
	}
}