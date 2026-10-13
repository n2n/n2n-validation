<?php

namespace n2n\validation\validator\impl\string;

use PHPUnit\Framework\TestCase;
use n2n\validation\build\impl\Validate;
use n2n\validation\validator\impl\Validators;

class NoSpecialCharsValidatorTest extends TestCase {

	function testNoSpecialChars(): void {
		$result = Validate::array(['prop1' => 'FFaaBB', 'prop2' => 'FFAABB',
				'new' => ['prop3' => 'ffaabb']])
				->prop('prop1', Validators::noSpecialChars())
				->props(['prop2', 'new/prop3'], Validators::noSpecialChars(errorMessage: 'individual error msg'))
				->exec();
		$this->assertTrue($result->isValid());
	}

	function testNoSpecialCharsLowercaseOnly(): void {
		$result = Validate::array(['prop1' => '#FFaaBB', 'prop2' => 'FFAABB', 'prop4' => 'FFAABB', 'prop5' => 'ff&aa&bb',
				'new' => ['prop3' => 'ffaabb']])
				->prop('prop1', Validators::noSpecialChars(), Validators::lowercaseOnlyChars())
				->props(['prop4', 'prop5'], Validators::noSpecialChars(), Validators::lowercaseOnlyChars())
				->props(['prop2', 'new/prop3'], Validators::noSpecialChars( errorMessage: 'individual error msg'), Validators::lowercaseOnlyChars('individual error msg'))
				->exec();
		$this->assertFalse($result->isValid());

		$dataErrorMap = $result->getErrorMap();
		$this->assertEquals('Special Chars', $dataErrorMap->getChild('prop1')->getMessages()[0]->__toString());
		$this->assertEquals('individual error msg', $dataErrorMap->getChild('prop2')->getMessages()[0]->__toString());
		$this->assertTrue($dataErrorMap->getChild('new')->getChild('prop3')->isEmpty());
		$this->assertEquals('Lowercase Chars', $dataErrorMap->getChild('prop4')->getMessages()[0]->__toString());
		$this->assertEquals('Special Chars', $dataErrorMap->getChild('prop5')->getMessages()[0]->__toString());
	}

	function testNoSpecialCharsUppercaseOnly(): void {
		$result = Validate::array(['prop1' => '#FFaaBB', 'prop2' => 'FFAABB', 'prop4' => 'ffaabb', 'prop5' => 'FF&AA&BB',
				'new' => ['prop3' => 'ffaabb']])
				->prop('prop1', Validators::noSpecialChars(), Validators::uppercaseOnlyChars())
				->props(['prop4', 'prop5'], Validators::noSpecialChars(), Validators::uppercaseOnlyChars())
				->props(['prop2', 'new/prop3'], Validators::noSpecialChars( errorMessage: 'individual error msg'), Validators::uppercaseOnlyChars('individual error msg'))
				->exec();
		$this->assertFalse($result->isValid());

		$dataErrorMap = $result->getErrorMap();
		$this->assertEquals('Special Chars', $dataErrorMap->getChild('prop1')->getMessages()[0]->__toString());
		$this->assertTrue($dataErrorMap->getChild('prop2')->isEmpty());
		$this->assertEquals('individual error msg', $dataErrorMap->getChild('new')->getChild('prop3')->getMessages()[0]->__toString());
		$this->assertEquals('Uppercase Chars', $dataErrorMap->getChild('prop4')->getMessages()[0]->__toString());
		$this->assertEquals('Special Chars', $dataErrorMap->getChild('prop5')->getMessages()[0]->__toString());

	}

	function testNoSpecialCharsFalse(): void {
		$result = Validate::array(['prop1' => '#FFQQBB', 'prop2' => '#FFAABBHoleradio', 'prop3' => 'FF&AA&BB',
				'new' => ['prop4' => ' indi ']])
				->prop('prop1', Validators::noSpecialChars())
				->prop('prop2', Validators::noSpecialChars())
				->props(['prop3', 'new/prop4'], Validators::noSpecialChars(errorMessage: 'individual error msg'))
				->exec();
		$this->assertFalse($result->isValid());

		$dataErrorMap = $result->getErrorMap()->getChildren();
		$this->assertEquals('Special Chars', $dataErrorMap['prop1']->getMessages()[0]->__toString());
		$this->assertEquals('Special Chars', $dataErrorMap['prop2']->getMessages()[0]->__toString());
		$this->assertEquals('individual error msg', $dataErrorMap['prop3']->getMessages()[0]->__toString());
		$this->assertEquals('individual error msg', $dataErrorMap['new']->getChildren()['prop4']->getMessages()[0]->__toString());
	}
}