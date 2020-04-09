<?php
namespace n2n\validation\build\impl;

use PHPUnit\Framework\TestCase;
use n2n\util\type\attrs\DataMap;
use n2n\validation\plan\impl\Validators;

class ValidateTest extends TestCase {
	
	function testAuthBindable() {
		$dataMap = new DataMap(['firstname' => 'Huii', 'lastname' => null]);
		
		$validationResult = Validate::attrs($dataMap)
				->props(['firstname', 'lastname'], Validators::mandatory())
				->exec();
		
		$this->assertTrue($validationResult->hasErrors());
		
		$this->assertTrue(isset($validationResult->getErrorMap()->getChildren()['firstname']));
		$this->assertTrue($validationResult->getErrorMap()->getChildren()['firstname']->isEmpty());
		
		$this->assertTrue(isset($validationResult->getErrorMap()->getChildren()['lastname']));
		$this->assertTrue(!$validationResult->getErrorMap()->getChildren()['lastname']->isEmpty());
	}
	
}