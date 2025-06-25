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
namespace n2n\validation\validator\impl\string;

use n2n\util\uri\Url;
use n2n\validation\validator\impl\SingleValidatorAdapter;
use n2n\util\magic\MagicContext;
use n2n\validation\plan\Validatable;
use n2n\l10n\Message;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\validation\lang\ValidationMessages;
use n2n\util\type\ArgUtils;
use n2n\util\type\TypeConstraints;
use n2n\validation\plan\ValidationContext;

class UrlValidator extends SingleValidatorAdapter {
	
	public function __construct(private bool $schemeRequired = false, private ?array $allowedSchemes = null,
			private ?Message $errorMessage = null, private ?Message $schemeErrorMessage = null) {

		ArgUtils::valArray($allowedSchemes, 'string', true, 'allowedSchemes');
	}
	
	protected function testSingle(Validatable $validatable, ValidationContext $validationContext, MagicContext $magicContext): bool {
		$value = $this->readSafeValue($validatable, TypeConstraints::type(['string', Url::class, 'null']));
		
		if ($value === null) {
			return true;
		}

		if (!ValidationUtils::isUrl($value, $this->schemeRequired)) {
			return false;
		}

		if ($this->allowedSchemes === null) {
			return true;
		}

		$url = Url::create($value);
		if ($url->hasScheme() && !in_array($url->getScheme(), $this->allowedSchemes)) {
			return false;
		}
		
		return true;
	}
	
	protected function validateSingle(Validatable $validatable, ValidationContext $validationContext, MagicContext $magicContext): void {
		$value = $this->readSafeValue($validatable, TypeConstraints::type(['string', Url::class, 'null']));
		
		if ($value === null)  {
			return;
		}

		if (!ValidationUtils::isUrl($value, $this->schemeRequired)) {
			$validatable->addError($this->errorMessage ?? ValidationMessages::url($this->readLabel($validatable)));
			return;
		}

		if ($this->allowedSchemes === null) {
			return;
		}


		$url = Url::create($value);
		if ($url->hasScheme() && !in_array($url->getScheme(), $this->allowedSchemes)) {
			$validatable->addError($this->schemeErrorMessage
					?? ValidationMessages::urlScheme($this->allowedSchemes, $this->readLabel($validatable)));
			return;
		}
	}
}
