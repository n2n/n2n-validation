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
use n2n\validation\plan\ValidationContext;

class MaxValidator extends SimpleValidatorAdapter {
	private $max;
	
	function __construct(float $max, ?Message $errorMessage = null) {
		parent::__construct($errorMessage);
		$this->max = $max;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function testSingle(Validatable $validatable, ValidationContext $validationContext,  MagicContext $magicContext): bool {
		$value = $this->readSafeValue($validatable, TypeConstraints::float(true)->setConvertable(true));
		
		return $value === null || $value <= $this->max;
	}
	
	
	protected function createErrorMessage(Validatable $validatable, MagicContext $magicContext): Message {
		return ValidationMessages::max($this->max, $this->readLabel($validatable));
	}
}