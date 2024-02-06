<?php

namespace n2n\validation\build\impl\number;

use PHPUnit\Framework\TestCase;
use n2n\validation\validator\impl\Validators;
use n2n\util\magic\MagicContext;
use n2n\validation\build\impl\Validate;
use n2n\validation\err\ValidationMismatchException;
use TypeError;

class StepValidatorTest extends TestCase {

	function testStep() {
		//input Step and Values are like integer (code will handle them as float anyway)
		$validationResult = Validate::value(2, 4, 100, 100000000000000, null)->val(Validators::step(2))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());


		//input Step and Values that are float
		$validationResult = Validate::value(-1.1, 2.2, 9.9, 11, 123.2, null)->val(Validators::step(1.1))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());


		//negative Values are also Ok
		$validationResult = Validate::value(-0.3, -0.2, -0.25, 0.3, 2.3, null)->val(Validators::step(0.05))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());


		//negative Step is also Ok, step is converted and handled like it is positive
		$validationResult = Validate::value(-0.3, 0.2, 0.25, 4.1, 5.75, null)->val(Validators::step(-0.05))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());


		//more exact Step are possible (up to 8 digits after decimal separator)
		$validationResult = Validate::value(-0.00003, 0.000205, 4.1010101, null)->val(Validators::step(0.0000001))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());


		//if Values are not exact enough we create error messages
		$validationResult = Validate::value(9.9, 9.8765, 9.876543, 9.87654322, null)->val(Validators::step(9.87654321))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());

		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(4, $messages);
		$this->assertEquals('Step [step = 9.87654321]', (string) $messages[0]);


		//create error messages independent of negative sign of Values or Step, default error show positive step
		$validationResult = Validate::value(-0.315, 0.22, 0.259, 4.91, 5.77, null)->val(Validators::step(-0.05))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());

		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(5, $messages);
		$this->assertEquals('Step [step = 0.05]', (string) $messages[0]);


		//custom error message
		$validationResult = Validate::value(1, null)->val(Validators::step(2, 'custom number error'))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());

		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(1, $messages);
		$this->assertEquals('custom number error', (string) $messages[0]);


		//create only as many errors as not correct Values
		$validationResult = Validate::value(0.1, 0.2, 0.25, 0.3, null)->val(Validators::step(0.1))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());

		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(1, $messages);
		$this->assertEquals('Step [step = 0.1]', (string) $messages[0]);


		//Step 0 and Value 0 or null is OK
		$validationResult = Validate::value(0, null)->val(Validators::step(0))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());


		//Step 0 and any other Value create Errors
		$validationResult = Validate::value(1, 0.25, -1)->val(Validators::step(0))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());

		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(3, $messages);
		$this->assertEquals('Step [step = 0]', (string) $messages[0]);

	}

	function testInvalidArgumentValue() {
		//to precise Value: max 8 digits after decimal separator allowed
		$validationResult = Validate::value(0.987654321)->val(Validators::step(9.87654321))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
	}

	function testInvalidArgumentStep() {
		//to precise Step: max 8 digits after decimal separator allowed
		$this->expectException(\InvalidArgumentException::class);
		$validationResult = Validate::value(987654321)->val(Validators::step(0.987654321))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
	}

	function testTypeMismatchValue(): void {
		//input is not a number
		$this->expectException(ValidationMismatchException::class);

		$validationResult = Validate::value('huiiii')->val(Validators::step(3))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
	}

	function testTypeMismatchStep(): void {
		//input is not a number
		$this->expectException(TypeError::class);
		$validationResult = Validate::value(1000)->val(Validators::step('aaa'))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
	}

	function testOffsetStep(): void {
		$validationResult = Validate::value(-8, 22, 52, 82, null)->val(Validators::step(30, offset: 22))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());
	}
	function testZeroOffsetStep(): void {
		$validationResult = Validate::value(-30, 30, 60, 90, null)->val(Validators::step(30, offset: 0))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());

		//if offset = 0: it is not used and error message contain no offset hint
		$validationResult = Validate::value(-30, 30, 60, 90, null)->val(Validators::step(33, offset: 0))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(4, $messages);
		$this->assertEquals('Step [step = 33]', (string) $messages[0]);
	}
	function testOffsetStepFails(): void {
		//if offset <> 0: error message contain step and offset as hint
		$validationResult = Validate::value(-10, 0, 50, 80, null)->val(Validators::step(30, offset: 5))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(4, $messages);
		$this->assertEquals('Offset Step [step = 30, offset = 5]', (string) $messages[0]);
	}
	function testOffsetStepNull(): void {
		//positive offset: step start by 30 (offset)
		$validationResult = Validate::value(0, null)->val(Validators::step(0, offset: 30))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(1, $messages);

		$validationResult = Validate::value(30, null)->val(Validators::step(0, offset: 30))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());

		$validationResult = Validate::value(-30, null)->val(Validators::step(0, offset: 30))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(1, $messages);

		//negative offset: step start by -30 (offset)
		$validationResult = Validate::value(0, null)->val(Validators::step(0, offset: -30))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(1, $messages);

		$validationResult = Validate::value(30, null)->val(Validators::step(0, offset: -30))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(1, $messages);

		$validationResult = Validate::value(-30, null)->val(Validators::step(0, offset: -30))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());
	}
	function testNegativeOffsetStep(): void {
		$validationResult = Validate::value(-20, 20, 50, 70, null)->val(Validators::step(-20, offset: 30))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(2, $messages);

		$validationResult = Validate::value(-20, 20, 50, 70, null)->val(Validators::step(-20, offset: -30))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(2, $messages);

		//step of 40 shifted by -3
		$validationResult = Validate::value(77, 37, -43, -83, null)->val(Validators::step(-40, offset: -3))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());

		//step of 40 shifted by -3 use wrong shift
		$validationResult = Validate::value(-77, -37, 43, 83, null)->val(Validators::step(-40, offset: -3))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(4, $messages);

		//step of 40 shifted by 3
		$validationResult = Validate::value(-77, -37, 43, 83, null)->val(Validators::step(-40, offset: 3))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());

		//step of 40 shifted by 3 use wrong shift
		$validationResult = Validate::value(77, 37, -43, -83, null)->val(Validators::step(-40, offset: 3))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(4, $messages);

		//6 out of 9 are false, below the correct ones
		$validationResult = Validate::value(-4, -3, -2, -1, 0, 1, 2, 3, 4, null)->val(Validators::step(3, offset: 1))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(6, $messages);
		$validationResult = Validate::value(-2, 1, 4, null)->val(Validators::step(3, offset: 1))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());

		//6 out of 9 are false, below the correct ones
		$validationResult = Validate::value(-4, -3, -2, -1, 0, 1, 2, 3, 4, null)->val(Validators::step(3, offset: 0))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(6, $messages);
		$validationResult = Validate::value(-3, 0, 3, null)->val(Validators::step(3, offset: 0))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());

		//6 out of 9 are false, below the correct ones
		$validationResult = Validate::value(-4, -3, -2, -1, 0, 1, 2, 3, 4, null)->val(Validators::step(3, offset: -1))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertTrue($validationResult->hasErrors());
		$messages = $validationResult->getErrorMap()->getAllMessages();
		$this->assertCount(6, $messages);
		$validationResult = Validate::value(-4, -1, 2, null)->val(Validators::step(3, offset: -1))
				->exec($this->getMockBuilder(MagicContext::class)->getMock());
		$this->assertFalse($validationResult->hasErrors());
	}
}