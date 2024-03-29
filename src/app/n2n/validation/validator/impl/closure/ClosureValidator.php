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
namespace n2n\validation\validator\impl\closure;

use n2n\validation\plan\Validatable;
use n2n\validation\validator\impl\ValidatorAdapter;
use n2n\validation\plan\ValidationContext;
use n2n\reflection\magic\MagicMethodInvoker;
use n2n\util\magic\MagicContext;
use n2n\util\type\ArgUtils;
use n2n\l10n\Message;
use n2n\validation\lang\ValidationMessages;
use n2n\l10n\Lstr;
use n2n\util\ex\NotYetImplementedException;
use n2n\validation\validator\impl\MultiValidatorAdapter;

class ClosureValidator extends MultiValidatorAdapter {

	function __construct(private \Closure $closure, private ?bool $everyValidatableMustExist) {
	}
	
	function validateMulti(array $validatables, ValidationContext $validationContext, MagicContext $magicContext): void {
		$invoker = new MagicMethodInvoker($magicContext);
		$invoker->setMethod(new \ReflectionFunction($this->closure));
		$invoker->setClassParamObject(ValidationContext::class, $validationContext);

		$existNum = 0;
		$args = [];
		foreach ($validatables as $validatable) {
			if (!$validatable->doesExist()) {
				$args[] = null;
			} else {
				$args[] = $validatable->getValue();
				$existNum++;
			}
		}

		if ($this->everyValidatableMustExist === null
				|| ($this->everyValidatableMustExist === true && (count($validatables) === $existNum))
				|| ($this->everyValidatableMustExist === false && $existNum > 0)) {
			$this->handleReturn($invoker->invoke(null, null, $args), $validatables);
		}
	}
	
	/**
	 * @param mixed|null $value
	 * @param Validatable[] $validatables
	 */
	private function handleReturn($value, $validatables) {
		ArgUtils::valTypeReturn($value, [Message::class, Lstr::class, 'string', 'bool', 'array',  'null'], null, $this->closure);
		
		if ($value === null || $value === true) {
			return;
		}
		
		$message = null;
		if ($value instanceof Message) {
			$message = $value;
		} else if (is_string($value)) {
			$message = Message::create($value);
		} else if ($this->handleIndiviualErrors($value, $validatables)) {
			return;
		}
		
		if ($message !== null) {
			foreach ($validatables as $validatable) {
				$validatable->addError($message);
			}
			return;
		}
		
		foreach ($validatables as $validatable) {
			$validatable->addError(ValidationMessages::invalid($validatable->getLabel()));
		}
	}
	
	/**
	 * @param mixed $returnValue
	 * @param Validatable[] $validatables
	 */
	private function handleIndiviualErrors($returnValue, $validatables) {
		if (!is_array($returnValue)) {
			return false;
		}
		
		$handled = false;
		foreach ($validatables as $validatable) {
			$name = (string) $validatable->getPath();
			if (!isset($returnValue[$name])) {
				continue;
			}
			
			if (is_string($returnValue[$name]) || $returnValue[$name] instanceof Message) {
				$validatable->addError(Message::create($returnValue[$name]));
			} else {
				$validatable->addError(ValidationMessages::invalid($validatable->getLabel()));
			}
			
			$handled = true;
		}
		
		return $handled;
	}
	
	public function testMulti(array $validatables, ValidationContext $validationContext, MagicContext $magicContext): bool {
		throw new NotYetImplementedException();
	}

}