<?php
namespace n2n\validation\build\impl\elements;

use PHPUnit\Framework\TestCase;
use n2n\validation\validator\impl\Validators;
use n2n\util\magic\MagicContext;
use n2n\validation\build\impl\Validate;

class ElementsValidatorTest extends TestCase {

	function testMin() {
		$validationResult = Validate::value(['asdf', 'bsdf'])->val(Validators::minElements(1))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());

		$validationResult = Validate::value(new \ArrayObject(['asdf', 'bsdf']))
				->val(Validators::minElements(3, 'custom error'))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());

		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(1, $messages);
		$this->assertEquals('custom error', (string) $messages[0]);
	}

	function testMax() {
		$validationResult = Validate::value(['asdf', 'bsdf'])->val(Validators::maxElements(2))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());

		$validationResult = Validate::value(new \ArrayObject(['asdf', 'bsdf']))
				->val(Validators::maxElements(1, 'custom error'))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());

		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(1, $messages);
		$this->assertEquals('custom error', (string) $messages[0]);
	}

}