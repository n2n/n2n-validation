<?php

namespace n2n\validation\build\impl\closure;

use PHPUnit\Framework\TestCase;
use n2n\validation\validator\impl\Validators;
use n2n\util\magic\MagicContext;
use n2n\validation\build\impl\Validate;

class UniqueClosureValidatorTest extends TestCase {

	function testUniqueSuccessCallback() {
		$called = 0;
		$closureMock = function(int $value) use (&$called) {
			$this->assertEquals(1, $value);
			$called++;
			return true;
		};
		$validationResult = Validate::value(1)->val(Validators::uniqueClosure($closureMock))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());

		$this->assertEquals(1, $called);
		$this->assertFalse($validationResult->hasErrors());
	}

	function testUniqueFailCallback() {
		$called = 0;
		$closureMock = function(int $value) use (&$called) {
			$this->assertEquals(1, $value);
			$called++;
			return false;
		};
		$validationResult = Validate::value(1)->val(Validators::uniqueClosure($closureMock))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());

		$this->assertEquals(1, $called);
		$this->assertTrue($validationResult->hasErrors());
	}

	function testUniqueFallCustomMessageCallback() {
		$called = 0;
		$closureMock = function(int $value) use (&$called) {
			$this->assertEquals(1, $value);
			$called++;
			return false;
		};
		$validationResult = Validate::value(1)
				->val(Validators::uniqueClosure($closureMock, 'Not Unique, Try again :-P'))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());

		$this->assertEquals(1, $called);
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(1, $messages);
		$this->assertEquals('Not Unique, Try again :-P', (string) $messages[0]);
	}
}