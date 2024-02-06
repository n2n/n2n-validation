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
namespace n2n\validation\validator\impl\common;

use n2n\validation\plan\Validatable;
use n2n\validation\lang\ValidationMessages;
use n2n\validation\validator\impl\SimpleValidatorAdapter;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\l10n\Message;
use n2n\util\magic\MagicContext;
use n2n\validation\plan\ValidationContext;

class MandatoryValidator extends SimpleValidatorAdapter {
	function __construct(Message $errorMessage = null) {
		parent::__construct($errorMessage);
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function testSingle(Validatable $validatable, ValidationContext $validationContext, MagicContext $magicContext): bool {
		$value = $validatable->getValue();
		
		return $this->isValid($value);
	}

	/**
	 * {@inheritdoc}
	 */
//	protected function validateSingle(Validatable $validatable, MagicContext $magicContext): void {
//		$value = $validatable->getValue();
//
//		if (!$this->isValid($value)) {
//			$validatable->addError(ValidationMessages::mandatory($this->readLabel($validatable)));
//		}
//	}
//
	protected function createErrorMessage(Validatable $validatable, MagicContext $magicContext): Message {
		return ValidationMessages::mandatory($this->readLabel($validatable));
	}
	
	private function isValid($value) {
		return null !== $value && (!is_scalar($value) || ValidationUtils::isNotEmpty($value));
	}
}