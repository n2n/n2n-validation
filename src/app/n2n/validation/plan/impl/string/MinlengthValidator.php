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
namespace n2n\validation\plan\impl\string;

use n2n\validation\plan\Validatable;
use n2n\validation\lang\ValidationMessages;
use n2n\validation\plan\impl\SimpleValidatorAdapter;
use n2n\validation\plan\impl\ValidationUtils;
use n2n\util\type\TypeConstraints;

class MinlengthValidator extends SimpleValidatorAdapter {
	private $minlength;
	
	function __construct(int $minlength, Message $errorMessage = null) {
		parent::__construct(TypeConstraints::string(true), $errorMessage);
		$this->minlength = $minlength;
	}
	
	/**
	 * {@inheritdoc}
	 */
	protected function validateSingle(Validatable $validatable) {
		$value = $this->readSafeValue($validatable);
		
		if ($value !== null && !ValidationUtils::minlength($value, $this->minlength)) {
			$validatable->addError(ValidationMessages::minlength($this->minlength, $this->readLabel($validatable)));
		}
	}
}