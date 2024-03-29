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
namespace n2n\validation\validator\impl;

use n2n\validation\plan\Validatable;
use n2n\util\type\TypeConstraint;
use n2n\util\ex\IllegalStateException;
use n2n\validation\err\ValidationMismatchException;
use n2n\validation\validator\Validator;
use n2n\util\type\ArgUtils;
use n2n\l10n\Lstr;
use n2n\util\type\ValueIncompatibleWithConstraintsException;

abstract class ValidatorAdapter implements Validator {

	/**
	 * @param Validatable $validatable
	 * @param TypeConstraint $typeConstraint
	 * @return mixed
	 */
	protected function readSafeValue(Validatable $validatable, TypeConstraint $typeConstraint) {
		$value = $validatable->getValue();

		try {
			return $typeConstraint->validate($value);
		} catch (ValueIncompatibleWithConstraintsException $e) {
			throw new ValidationMismatchException('Validatable ' . $validatable->getPath() . ' is not compatible with '
					. get_class($this), 0, $e);
		}
	}
	
	/**
	 * @param Validatable $validatable
	 * @return string|Lstr
	 */
	protected function readLabel(Validatable $validatable) {
		$label = $validatable->getLabel();
		ArgUtils::valType(['string', Lstr::class], $label);
		return $label;
	}
}