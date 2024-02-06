<?php
/*
 * Copyright (c) 2012-2016, Hofmänner New Media.
 * DO NOT ALTER OR REMOVE COPYRIGHT NOTICES OR THIS FILE HEADER.
 *
 * This file is part of the N2N FRAMEWORK.
 *
 * The N2N FRAMEWORK is free software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * N2N is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details: http://www.gnu.org/licenses/
 *
 * The following people participated in this project:
 *
 * Andreas von Burg.....: Architect, Lead Developer
 * Bert Hofmänner.......: Idea, Frontend UI, Community Leader, Marketing
 * Thomas Günther.......: Developer, Hangar
 */

namespace n2n\validation\validator\impl\number;

use n2n\validation\plan\Validatable;
use n2n\validation\lang\ValidationMessages;
use n2n\validation\validator\impl\SimpleValidatorAdapter;
use n2n\util\type\TypeConstraints;
use n2n\util\magic\MagicContext;
use n2n\l10n\Message;
use InvalidArgumentException;
use n2n\validation\plan\ValidationContext;

class StepValidator extends SimpleValidatorAdapter {
	private float $step;

	function __construct(float $step, Message $errorMessage = null, private float $offset = 0.0) {
		parent::__construct($errorMessage);

		if (round($step, 8) !== $step) {
			throw new InvalidArgumentException('Step should not have more than 8 digits after decimal separator');
		}
		if (round($offset, 8) !== $offset) {
			throw new InvalidArgumentException('Offset should not have more than 8 digits after decimal separator');
		}

		$this->step = (float) abs($step);
	}

	private function offsetValue(float $value): float {
		if ($this->offset === 0.0) {
			return $value;
		}

		if ($value >= $this->offset) {
			return $value - $this->offset;
		}

		return $this->offset - $value;
	}

	protected function testSingle(Validatable $validatable, ValidationContext $validationContext, MagicContext $magicContext): bool {
		$value = $this->readSafeValue($validatable, TypeConstraints::float(true, convertable: true));

		if ($value === null) {
			return true;
		}

		if ((round($value, 8) !== $value)) {
			return false;
		}

		$offsetValue = $this->offsetValue($value);

		if ($offsetValue === 0.0 && $this->step === 0.0) {
			return true;
		}

		if ($this->step === 0.0) {
			return false;
		}

		$precision = 0.0000000001; // fewer decimals means lower precision
		return (abs(round($offsetValue / $this->step) - ($offsetValue / $this->step)) < $precision);
	}

	protected function createErrorMessage(Validatable $validatable, MagicContext $magicContext): Message {
		if (isset($this->offset) && $this->offset !== 0.0) {
			return ValidationMessages::offsetStep($this->step, $this->offset, $this->readLabel($validatable));
		}
		return ValidationMessages::step($this->step, $this->readLabel($validatable));
	}
}