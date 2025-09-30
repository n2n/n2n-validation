<?php

namespace n2n\validation\validator\impl\string;

use PHPUnit\Framework\TestCase;
use n2n\validation\build\impl\Validate;
use n2n\validation\validator\impl\Validators;

class ColorHexValidatorTest extends TestCase {

	function testColorHex(): void {
		$result = Validate::array(['prop1' => '#FFAABB', 'prop2' => '#FFAABB',
				'new' => ['prop3' => '#FFAABB']])
				->prop('prop1', Validators::colorHex())
				->props(['prop2', 'new/prop3'], Validators::colorHex('individual error msg'))
				->exec();
		$this->assertTrue($result->isValid());
	}

	function testColorHexFalse(): void {
		$result = Validate::array(['prop1' => '#FFQQBB', 'prop2' => '#FFAABBHoleradio', 'prop3' => 'FFAABB',
				'new' => ['prop4' => 'indi']])
				->prop('prop1', Validators::colorHex())
				->prop('prop2', Validators::colorHex())
				->props(['prop3', 'new/prop4'], Validators::colorHex('individual error msg'))
				->exec();
		$this->assertFalse($result->isValid());

		$dataErrorMap = $result->getErrorMap()->getChildren();
		$this->assertEquals('Hex Color', $dataErrorMap['prop1']->getMessages()[0]->__toString());
		$this->assertEquals('Hex Color', $dataErrorMap['prop2']->getMessages()[0]->__toString());
		$this->assertEquals('individual error msg', $dataErrorMap['prop3']->getMessages()[0]->__toString());
		$this->assertEquals('individual error msg', $dataErrorMap['new']->getChildren()['prop4']->getMessages()[0]->__toString());
	}
}