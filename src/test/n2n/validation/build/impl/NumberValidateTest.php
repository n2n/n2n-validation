<?php
namespace n2n\validation\build\impl;

use PHPUnit\Framework\TestCase;
use n2n\util\type\attrs\DataMap;
use n2n\validation\validator\impl\Validators;
use n2n\util\magic\MagicContext;
use n2n\util\ex\UnsupportedOperationException;
use ReflectionClass;

class NumberValidateTest extends TestCase {

	function testMax() {
		$validationResult = Validate::value(1, 2, null)->val(Validators::max(2))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());

		$validationResult = Validate::value(4, null)->val(Validators::max(2, 'custom number error'))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());

		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(1, $messages);
		$this->assertEquals('custom number error', (string) $messages[0]);
	}

	function testStep() {

	}
}