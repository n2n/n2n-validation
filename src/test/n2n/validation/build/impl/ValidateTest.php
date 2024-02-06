<?php
namespace n2n\validation\build\impl;

use PHPUnit\Framework\TestCase;
use n2n\util\type\attrs\DataMap;
use n2n\validation\validator\impl\Validators;
use n2n\util\magic\MagicContext;
use n2n\util\ex\UnsupportedOperationException;
use ReflectionClass;

class ValidateTest extends TestCase {
	
	function testAuthBindable() {
		$dataMap = new DataMap(['firstname' => 'Huii', 'lastname' => null, 'data' => [ 'huii' => null, 'huii2' => 'hoi' ]]);
		
		$validationResult = Validate::attrs($dataMap)
				->props(['firstname', 'lastname', 'data/huii', 'data/huii2'],
						Validators::closure(function ($firstname, $lastname, $huii, $huii2) {
							return ['data/huii2' => 'wrong: ' . $huii2];
						}))
				->exec(new EmptyMagicContext());
		
		$this->assertTrue($validationResult->hasErrors());
		$this->assertFalse($validationResult->get());
		
		$this->assertTrue(isset($validationResult->getErrorMap()->getChildren()['firstname']));
		$this->assertTrue($validationResult->getErrorMap()->getChildren()['firstname']->isEmpty());
		
		$this->assertTrue(isset($validationResult->getErrorMap()->getChildren()['lastname']));
		$this->assertTrue($validationResult->getErrorMap()->getChildren()['lastname']->isEmpty());

		$this->assertTrue(isset($validationResult->getErrorMap()->getChildren()['data']));

		$dataErrorMap = $validationResult->getErrorMap()->getChildren()['data'];
		$this->assertTrue($dataErrorMap->getChildren()['huii']->isEmpty());
		$this->assertEquals('wrong: hoi', $dataErrorMap->getChildren()['huii2']->getMessages()[0]->__toString());
	}

	function testValidateValues() {
		$validationResult = Validate::value('huii', null)->val(Validators::mandatory())
				->exec($this->getMockBuilder(MagicContext::class)->getMock());


		$this->assertTrue($validationResult->hasErrors());
		$this->assertFalse($validationResult->get());
		$this->assertArrayHasKey(1, $validationResult->getErrorMap()->getChildren());
	}

	function testSpecialChars() {
		$validationResult = Validate::value('asdf', null)->val(Validators::noSpecialChars())
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());
		$this->assertTrue($validationResult->get());

		$validationResult = Validate::value('@asdf', null)->val(Validators::noSpecialChars())
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$this->assertFalse($validationResult->get());

		$validationResult = Validate::value('asdf/', null)->val(Validators::noSpecialChars())
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$this->assertFalse($validationResult->get());
	}

}

class EmptyMagicContext implements MagicContext {
	function lookup(string|ReflectionClass $id, bool $required = true, string $contextNamespace = null): mixed {
		throw new UnsupportedOperationException();
	}

	function lookupParameterValue(\ReflectionParameter $parameter): mixed {
		throw new UnsupportedOperationException();
	}

	function get(string $id) {
		throw new UnsupportedOperationException();
	}

	function has(ReflectionClass|string $id): bool {
		throw new UnsupportedOperationException();
	}
}
