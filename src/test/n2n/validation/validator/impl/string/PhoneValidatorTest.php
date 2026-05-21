<?php
namespace n2n\validation\validator\impl\string;
use PHPUnit\Framework\TestCase;
use n2n\validation\plan\Validatable;
use n2n\validation\plan\ValidationContext;
use n2n\util\magic\MagicContext;
use n2n\validation\lang\ValidationMessages;

class PhoneValidatorTest extends TestCase {
	private function createMockValidatable($value) {
		$validatable = $this->createMock(Validatable::class);
		$validatable->method('getValue')->willReturn($value);
		return $validatable;
	}

	private function createMockContext() {
		return $this->createMock(ValidationContext::class);
	}

	private function createMockMagicContext() {
		return $this->createMock(MagicContext::class);
	}


	private function invokeTestSingle(PhoneValidator $validator, Validatable $validatable) {
		$reflection = new \ReflectionClass($validator);
		$method = $reflection->getMethod('testSingle');

		return $method->invoke(
				$validator,
				$validatable,
				$this->createMockContext(),
				$this->createMockMagicContext()
		);
	}

	private function invokeValidateSingle(PhoneValidator $validator, Validatable $validatable) {
		$reflection = new \ReflectionClass($validator);
		$method = $reflection->getMethod('validateSingle');

		$method->invoke(
				$validator,
				$validatable,
				$this->createMockContext(),
				$this->createMockMagicContext()
		);
	}

	public function testValidPhonePassesValidation() {
		$validator = new PhoneValidator();
		$validatable = $this->createMockValidatable('+49 (0)228-997799-0');

		$result = $this->invokeTestSingle($validator, $validatable);

		$this->assertTrue($result);
	}

//	public function testPhoneObjectPassesValidation() {
//		$validator = new PhoneValidator();
//		$phone = Phone::from('https://example.com');
//		$validatable = $this->createMockValidatable($phone);
//
//		$result = $this->invokeTestSingle($validator, $validatable);
//
//		$this->assertTrue($result);
//	}

	public function testNullValuePassesValidation() {
		$validator = new PhoneValidator();
		$validatable = $this->createMockValidatable(null);

		$result = $this->invokeTestSingle($validator, $validatable);

		$this->assertTrue($result);
	}

	public function testInvalidPhoneFailsValidation() {
		$validator = new PhoneValidator();
		$validatable = $this->createMockValidatable('+41 (1)23');

		$result = $this->invokeTestSingle($validator, $validatable);

		$this->assertFalse($result);
	}

	public function testEmptyString() {
		$validator = new PhoneValidator();
		$validatable = $this->createMockValidatable('');

		$result = $this->invokeTestSingle($validator, $validatable);

		$this->assertTrue($result);
	}

	public function testValidationAddsErrorForInvalidPhone() {
		$validator = new PhoneValidator();
		$validatable = $this->createMock(Validatable::class);
		$validatable->method('getValue')->willReturn('invalid-phone +41 123');
		$validatable->expects($this->once())->method('addError');

		$this->invokeValidateSingle($validator, $validatable);
	}

	public function testValidationUsesCustomErrorMessage() {
		$customMessage = ValidationMessages::invalid('somefield');
		$validator = new PhoneValidator(errorMessage: $customMessage);
		$validatable = $this->createMock(Validatable::class);
		$validatable->method('getValue')->willReturn('invalid-phone +41 123');
		$validatable->expects($this->once())->method('addError')->with($customMessage);

		$this->invokeValidateSingle($validator, $validatable);
	}
}