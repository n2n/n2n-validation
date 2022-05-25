<?php
namespace n2n\validation\build\impl;

use PHPUnit\Framework\TestCase;
use n2n\util\type\attrs\DataMap;
use n2n\validation\plan\impl\Validators;
use n2n\util\magic\MagicContext;
use n2n\util\ex\UnsupportedOperationException;

class ValidateTest extends TestCase {
	
	function testAuthBindable() {
		$dataMap = new DataMap(['firstname' => 'Huii', 'lastname' => null, 'data' => [ 'huii' => null ]]);
		
		$validationResult = Validate::attrs($dataMap)
				->props(['firstname', 'lastname', 'data/huii'], Validators::mandatory(),
						Validators::closure(function ($firstname, $lastname, $huii) {
							return ['data/huii' => 'wrong!'];
						}))
				->exec(new EmptyMagicContext());
		
		$this->assertTrue($validationResult->hasErrors());
		
		$this->assertTrue(isset($validationResult->getErrorMap()->getChildren()['firstname']));
		$this->assertTrue($validationResult->getErrorMap()->getChildren()['firstname']->isEmpty());
		
		$this->assertTrue(isset($validationResult->getErrorMap()->getChildren()['lastname']));
		$this->assertTrue(!$validationResult->getErrorMap()->getChildren()['lastname']->isEmpty());

		$this->assertTrue(isset($validationResult->getErrorMap()->getChildren()['data']->getChildren()['huii']));
		$this->assertTrue(!$validationResult->getErrorMap()->getChildren()['data']->getChildren()['huii']->isEmpty());
	}


}

class EmptyMagicContext implements MagicContext {
	public function lookup($id, $required = true) {
		throw new UnsupportedOperationException();
	}

	public function lookupParameterValue(\ReflectionParameter $parameter) {
		throw new UnsupportedOperationException();
	}
}
