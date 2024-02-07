<?php

namespace n2n\validation\build\impl\closure;

use PHPUnit\Framework\TestCase;
use n2n\validation\validator\impl\closure\ConditionalValidator;
use n2n\util\magic\MagicContext;
use n2n\validation\plan\ValidationContext;
use n2n\validation\validator\Validator;

class ConditionalValidatorTest extends TestCase {
	private $validatorMock;
	private $validationContextMock;
	private $magicContextMock;

	protected function setUp(): void {
		parent::setUp();
		$this->validatorMock = $this->createMock(Validator::class);
		$this->validationContextMock = $this->createMock(ValidationContext::class);
		$this->magicContextMock = $this->createMock(MagicContext::class);
	}

	public function testValidatorInvokedOnTrueCondition() {
		$adminMode = true;
		$condition = function() use ($adminMode) { return  $adminMode; };

		$this->validatorMock->expects($this->once())
				->method('validate');

		$conditionalValidator = new ConditionalValidator($condition, $this->validatorMock);

		$conditionalValidator->validate(['asdf', 'bsdf'], $this->validationContextMock, $this->magicContextMock);
	}

	public function testValidatorNotInvokedOnFalseCondition() {
		$condition = function() { return false; };

		$this->validatorMock->expects($this->never())
				->method('validate');

		$conditionalValidator = new ConditionalValidator($condition, $this->validatorMock);

		$conditionalValidator->validate([], $this->validationContextMock, $this->magicContextMock);
	}
}