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

use n2n\validation\lang\ValidationMessages;
use n2n\l10n\Message;
use n2n\validation\validator\impl\ValidatorAdapter;
use n2n\validation\plan\ValidationContext;
use n2n\util\magic\MagicContext;

class ExistsValidator extends ValidatorAdapter {
	
	function __construct(?Message $errorMessage = null) {
	}
	
	/**
	 * {@inheritDoc}
	 * @see \n2n\validation\validator\Validator::test()
	 */
	function test(array $validatables, ValidationContext $validationContext, MagicContext $magicContext): bool {
		foreach ($validatables as $validatable) {
			if (!$validatable->doesExist()) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * {@inheritDoc}
	 * @see \n2n\validation\validator\Validator::validate()
	 */
	function validate(array $validatables, ValidationContext $validationContext, MagicContext $magicContext) {
		foreach ($validatables as $validatable) {
			if (!$validatable->doesExist()) {
				$validatable->addError(ValidationMessages::required($this->readLabel($validatable)));
			}
		}
	}
}