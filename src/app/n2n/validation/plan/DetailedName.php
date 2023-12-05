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

	function toHash(): string {
		return base64_encode(json_encode($this->parts));
	}

	function __toString() {
		return '/' . join('/', $this->parts);
	}
}