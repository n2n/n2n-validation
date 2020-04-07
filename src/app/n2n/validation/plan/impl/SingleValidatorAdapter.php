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
namespace n2n\validation\plan\impl;

use n2n\validation\build\ValidatableResolver;
use n2n\util\type\ArgUtils;
use n2n\validation\plan\Validatable;
use n2n\l10n\Message;

abstract class SingleValidatorAdapter extends ValidatorAdapter {

	
// 	/**
// 	 * @param string|\n2n\l10n\Lstr $label
// 	 * @return \n2n\validation\plan\impl\SingleValidatorAdapter
// 	 */
// 	function setLabel($label) {
// 		ArgUtils::valType($label, ['string', Lstr::class]);
// 		$this->label = $label;
// 		return $this;
// 	}
	
// 	function getLabel() {
// 		return $this->label;
// 	}
	
// 	protected function determineLabel() {
		
// 	}
	
	final function validate(array $validatables, ValidatableResolver $validatableResolver) {
		ArgUtils::valArray($validatables, Validatable::class);
		
		foreach ($validatables as $validatable) {
			if (!$validatable->isOpenForValidation()) {
				continue;
			}
			
			$messages = $this->validateSingle($validatable);
			ArgUtils::valArrayReturn($messages, $this, 'validateValue', Message::class);
			if (empty($messages)) {
				continue;
			}
			
			foreach ($messages as $message) {
				$validatable->addError($message);
			}
		}
	}
	
	protected abstract function validateSingle(Validatable $validatable);
	
}