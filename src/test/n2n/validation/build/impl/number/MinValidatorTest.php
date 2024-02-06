<?php
namespace n2n\validation\build\impl\number;

use PHPUnit\Framework\TestCase;
use n2n\validation\validator\impl\Validators;
use n2n\util\magic\MagicContext;
use n2n\validation\build\impl\Validate;

class MinValidatorTest extends TestCase {

	function testMin() {
		$validationResult = Validate::value(2, 3, null)->val(Validators::min(2))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());

		$validationResult = Validate::value(1, null)->val(Validators::min(2, 'custom number error'))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());

		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(1, $messages);
		$this->assertEquals('custom number error', (string) $messages[0]);
	}

}