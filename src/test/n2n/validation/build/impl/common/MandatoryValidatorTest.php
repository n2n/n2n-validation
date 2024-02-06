<?php
namespace n2n\validation\build\impl\common;

use PHPUnit\Framework\TestCase;
use n2n\validation\validator\impl\Validators;
use n2n\util\magic\MagicContext;
use n2n\validation\build\impl\Validate;

class MandatoryValidatorTest extends TestCase {

	function testValid() {
		$validationResult = Validate::value(0, 'aasdfdf', 1)->val(Validators::mandatory())
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());
	}

	function testInvalid() {
		$validationResult = Validate::value(null)->val(Validators::mandatory())
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());

		$validationResult = Validate::value('')->val(Validators::mandatory())
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());

		$validationResult = Validate::value(' ')->val(Validators::mandatory())
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());

		$validationResult = Validate::value(false)->val(Validators::mandatory())
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
	}

	function testInvalidCustomMessage() {
		$validationResult = Validate::value(null)->val(Validators::mandatory('uiii nei!'))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$this->assertEquals('uiii nei!', (string) $validationResult->getErrorMap()->getAllMessages()[0]);
	}

}