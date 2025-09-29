<?php

namespace n2n\validation\validator\impl\string;

use PHPUnit\Framework\TestCase;
use n2n\validation\validator\impl\ValidationUtils;
use n2n\validation\build\impl\Validate;
use n2n\validation\validator\impl\Validators;

class HexColorValidatorTest extends TestCase {
	public function testValidHexColor() {
		$this->assertTrue(ValidationUtils::isHexColor('#FFAABB')); // true
		$this->assertFalse(ValidationUtils::isHexColor('#FFAABB', false)); // false, because prefix = false
		$this->assertTrue(ValidationUtils::isHexColor('FFAABB', false)); // true, because $hasPrefix = false
		$this->assertTrue(ValidationUtils::isHexColor('#FFAABBCC', true, true)); // true
		$this->assertFalse(ValidationUtils::isHexColor('#FFAA')); // false because not 6 Chars
		$this->assertFalse(ValidationUtils::isHexColor('#FFQQBB')); // false because not Hex Chars
	}

	function testHexColor(): void {
		$result = Validate::array(['prop1' => '#FFAABB'])
				->prop('prop1', Validators::hexColor())
				->exec();
		$this->assertTrue($result->isValid());
	}

	function testHexColorFalse(): void {
		$result = Validate::array(['prop1' => '#FFQQBB', 'prop2' => '#FFAABBHoleradio', 'prop3' => 'FFAABB'])
				->prop('prop1', Validators::hexColor())
				->prop('prop2', Validators::hexColor())
				->prop('prop3', Validators::hexColor())
				->exec();
		$this->assertFalse($result->isValid());

		$dataErrorMap = $result->getErrorMap()->getChildren();
		$this->assertEquals('Hex Color', $dataErrorMap['prop1']->getMessages()[0]->__toString());
		$this->assertEquals('Hex Color', $dataErrorMap['prop2']->getMessages()[0]->__toString());
		$this->assertEquals('Hex Color', $dataErrorMap['prop3']->getMessages()[0]->__toString());
	}
}