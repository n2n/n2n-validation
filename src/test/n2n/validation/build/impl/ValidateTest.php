<?php
namespace n2n\validation\build\impl;

use PHPUnit\Framework\TestCase;
use n2n\util\type\attrs\DataMap;
use n2n\validation\validator\impl\Validators;
use n2n\util\magic\MagicContext;
use n2n\util\ex\UnsupportedOperationException;

class ValidateTest extends TestCase {
	
	function testAuthBindable() {
		$dataMap = new DataMap(['firstname' => 'Huii', 'lastname' => null, 'data' => [ 'huii' => null, 'huii2' => 'hoi' ]]);
		
		$validationResult = Validate::attrs($dataMap)
				->props(['firstname', 'lastname', 'data/huii', 'data/huii2'], Validators::mandatory(),
						Validators::closure(function ($firstname, $lastname, $huii, $huii2) {
							return ['data/huii2' => 'wrong: ' . $huii2];
						}))
				->exec(new EmptyMagicContext());
		
		$this->assertTrue($validationResult->hasErrors());
		
		$this->assertTrue(isset($validationResult->getErrorMap()->getChildren()['firstname']));
		$this->assertTrue($validationResult->getErrorMap()->getChildren()['firstname']->isEmpty());
		
		$this->assertTrue(isset($validationResult->getErrorMap()->getChildren()['lastname']));
		$this->assertTrue(!$validationResult->getErrorMap()->getChildren()['lastname']->isEmpty());

		$this->assertTrue(isset($validationResult->getErrorMap()->getChildren()['data']));

		$dataErrorMap = $validationResult->getErrorMap()->getChildren()['data'];
		$this->assertTrue(!$dataErrorMap->getChildren()['huii']->isEmpty());
		$this->assertEquals('wrong: hoi', $dataErrorMap->getChildren()['huii2']->getMessages()[0]->__toString());
	}

	function testValidateValues() {
		$validationResult = Validate::value('huii', null)->val(Validators::mandatory())
				->exec($this->getMockBuilder(MagicContext::class)->getMock());


		$this->assertTrue($validationResult->hasErrors());
		$this->assertArrayHasKey(1, $validationResult->getErrorMap()->getChildren());
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
