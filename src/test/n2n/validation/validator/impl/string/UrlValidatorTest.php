<?php

namespace n2n\validation\validator\impl\string;

use PHPUnit\Framework\TestCase;
use n2n\util\uri\Url;
use n2n\validation\plan\Validatable;
use n2n\validation\plan\ValidationContext;
use n2n\util\magic\MagicContext;
use n2n\validation\lang\ValidationMessages;

class UrlValidatorTest extends TestCase {

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

	/**
	 * @throws \ReflectionException
	 */
	public function testValidUrlWithSchemePassesValidation() {
		$validator = new UrlValidator();
		$validatable = $this->createMockValidatable('https://example.com');

		$result = $this->invokeTestSingle($validator, $validatable);

		$this->assertTrue($result);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testValidUrlWithoutSchemeFailsWhenSchemeRequired() {
		$validator = new UrlValidator(schemeRequired: true);
		$validatable = $this->createMockValidatable('example.com');

		$result = $this->invokeTestSingle($validator, $validatable);

		$this->assertFalse($result);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testValidUrlWithoutSchemePassesWhenSchemeNotRequired() {
		$validator = new UrlValidator(schemeRequired: false);
		$validatable = $this->createMockValidatable('example.com');

		$result = $this->invokeTestSingle($validator, $validatable);

		$this->assertTrue($result);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testUrlWithAllowedSchemePassesValidation() {
		$validator = new UrlValidator(allowedSchemes: ['https', 'http']);
		$validatable = $this->createMockValidatable('https://example.com');

		$result = $this->invokeTestSingle($validator, $validatable);

		$this->assertTrue($result);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testUrlWithDisallowedSchemeFailsValidation() {
		$validator = new UrlValidator(allowedSchemes: ['https']);
		$validatable = $this->createMockValidatable('ftp://example.com');

		$result = $this->invokeTestSingle($validator, $validatable);

		$this->assertFalse($result);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testUrlObjectPassesValidation() {
		$validator = new UrlValidator();
		$url = Url::create('https://example.com');
		$validatable = $this->createMockValidatable($url);

		$result = $this->invokeTestSingle($validator, $validatable);

		$this->assertTrue($result);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testNullValuePassesValidation() {
		$validator = new UrlValidator();
		$validatable = $this->createMockValidatable(null);

		$result = $this->invokeTestSingle($validator, $validatable);

		$this->assertTrue($result);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testInvalidUrlFailsValidation() {
		$validator = new UrlValidator();
		$validatable = $this->createMockValidatable('not- a-url');

		$result = $this->invokeTestSingle($validator, $validatable);

		$this->assertFalse($result);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testEmptyString() {
		$validator = new UrlValidator();
		$validatable = $this->createMockValidatable('');

		$result = $this->invokeTestSingle($validator, $validatable);

		$this->assertTrue($result);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testValidationAddsErrorForInvalidUrl() {
		$validator = new UrlValidator();
		$validatable = $this->createMock(Validatable::class);
		$validatable->method('getValue')->willReturn('invalid- url');
		$validatable->expects($this->once())->method('addError');

		$this->invokeValidateSingle($validator, $validatable);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testValidationAddsErrorForMissingRequiredScheme() {
		$validator = new UrlValidator(schemeRequired: true);
		$validatable = $this->createMock(Validatable::class);
		$validatable->method('getValue')->willReturn('example.com');
		$validatable->expects($this->once())->method('addError');

		$this->invokeValidateSingle($validator, $validatable);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testValidationAddsErrorForDisallowedScheme() {
		$validator = new UrlValidator(allowedSchemes: ['https']);
		$validatable = $this->createMock(Validatable::class);
		$validatable->method('getValue')->willReturn('ftp://example.com');
		$validatable->expects($this->once())->method('addError');

		$this->invokeValidateSingle($validator, $validatable);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testValidationUsesCustomErrorMessage() {
		$customMessage = ValidationMessages::invalid('somefield');
		$validator = new UrlValidator(errorMessage: $customMessage);
		$validatable = $this->createMock(Validatable::class);
		$validatable->method('getValue')->willReturn('invalid- url');
		$validatable->expects($this->once())->method('addError')->with($customMessage);

		$this->invokeValidateSingle($validator, $validatable);
	}

//	public function testValidationUsesCustomSchemeRequiredErrorMessage() {
//		$customMessage = ValidationMessages::invalid('somefield');
//		$validator = new UrlValidator(schemeRequired: true, schemeRequiredErrorMessage: $customMessage);
//		$validatable = $this->createMock(Validatable::class);
//		$validatable->method('getValue')->willReturn('example.com');
//		$validatable->expects($this->once())->method('addError')->with($customMessage);
//
//		$this->invokeValidateSingle($validator, $validatable);
//	}

	/**
	 * @throws \ReflectionException
	 */
	public function testValidationUsesCustomSchemeErrorMessage() {
		$customMessage = ValidationMessages::invalid('somefield');
		$validator = new UrlValidator(allowedSchemes: ['https'], schemeErrorMessage: $customMessage);
		$validatable = $this->createMock(Validatable::class);
		$validatable->method('getValue')->willReturn('ftp://example.com');
		$validatable->expects($this->once())->method('addError')->with($customMessage);

		$this->invokeValidateSingle($validator, $validatable);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testValidationDoesNotAddErrorForNullValue() {
		$validator = new UrlValidator();
		$validatable = $this->createMock(Validatable::class);
		$validatable->method('getValue')->willReturn(null);
		$validatable->expects($this->never())->method('addError');

		$this->invokeValidateSingle($validator, $validatable);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testValidationDoesNotAddErrorForValidUrl() {
		$validator = new UrlValidator();
		$validatable = $this->createMock(Validatable::class);
		$validatable->method('getValue')->willReturn('https://example.com');
		$validatable->expects($this->never())->method('addError');

		$this->invokeValidateSingle($validator, $validatable);
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testMultipleAllowedSchemesWork() {
		$validator = new UrlValidator(allowedSchemes: ['http', 'https', 'ftp']);

		$httpUrl = $this->createMockValidatable('http://example.com');
		$httpsUrl = $this->createMockValidatable('https://example.com');
		$ftpUrl = $this->createMockValidatable('ftp://example.com');
		$invalidUrl = $this->createMockValidatable('gopher://example.com');

		$this->assertTrue($this->invokeTestSingle($validator, $httpUrl));
		$this->assertTrue($this->invokeTestSingle($validator, $httpsUrl));
		$this->assertTrue($this->invokeTestSingle($validator, $ftpUrl));
		$this->assertFalse($this->invokeTestSingle($validator, $invalidUrl));
	}

	public function testConstructorValidatesAllowedSchemesArray() {
		$this->expectException(\InvalidArgumentException::class);
		new UrlValidator(allowedSchemes: ['valid', 123]);
	}

	/**
	 * @throws \ReflectionException
	 */
	private function invokeTestSingle(UrlValidator $validator, Validatable $validatable) {
		$reflection = new \ReflectionClass($validator);
		$method = $reflection->getMethod('testSingle');

		return $method->invoke(
				$validator,
				$validatable,
				$this->createMockContext(),
				$this->createMockMagicContext()
		);
	}

	/**
	 * @throws \ReflectionException
	 */
	private function invokeValidateSingle(UrlValidator $validator, Validatable $validatable): void {
		$reflection = new \ReflectionClass($validator);
		$method = $reflection->getMethod('validateSingle');

		$method->invoke(
				$validator,
				$validatable,
				$this->createMockContext(),
				$this->createMockMagicContext()
		);
	}
}