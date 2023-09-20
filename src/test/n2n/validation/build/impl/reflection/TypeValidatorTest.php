<?php
namespace n2n\validation\build\impl\reflection;

use PHPUnit\Framework\TestCase;
use n2n\validation\validator\impl\Validators;
use n2n\util\magic\MagicContext;
use n2n\validation\build\impl\Validate;
use n2n\util\type\TypeConstraints;
use n2n\validation\err\ValidationMismatchException;

class TypeValidatorTest extends TestCase {

	function testSuccess() {
		$validationResult = Validate::value(1, 'string', null)->val(Validators::type(TypeConstraints::scalar(true)))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());

		$validationResult = Validate::value(1, 'string', null)
				->val(Validators::type(TypeConstraints::scalar(true), TypeConstraints::int(true)))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$this->assertCount(1, $validationResult->getErrorMap()->getChild(1)->getMessages());


		$validationResult = Validate::value(1, 'string', null)
				->val(Validators::type(null, TypeConstraints::int(true), 'custom type error'))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());

		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(1, $messages);
		$this->assertEquals('custom type error', (string) $messages[0]);
	}

	function testTypeError(): void {
		$this->expectException(ValidationMismatchException::class);

		Validate::value(1, 'string', null)->val(Validators::type(TypeConstraints::scalar(false)))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());

	}

}