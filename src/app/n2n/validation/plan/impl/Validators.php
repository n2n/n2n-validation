<?php
namespace n2n\validation\plan\impl;

use n2n\validation\plan\impl\common\MandatoryValidator;
use n2n\l10n\Message;
use n2n\validation\plan\impl\string\EmailValidator;
use n2n\util\type\TypeConstraint;
use n2n\validation\plan\impl\string\MinlengthValidator;
use n2n\validation\plan\impl\string\MaxlengthValidator;
use n2n\validation\plan\impl\reflection\TypeValidator;

class Validators {
	
	static function type(TypeConstraint $typeConstraint, $errorMessage = null) {
		return new TypeValidator($typeConstraint, $errorMessage);
	}
	
	/**
	 * @param Message|null $errorMessage
	 * @return \n2n\validation\plan\impl\common\MandatoryValidator
	 */
	static function mandatory($errorMessage = null) {
		return new MandatoryValidator(Message::build($errorMessage));
	}
	
	/**
	 * @param Message|null $errorMessage
	 * @return \n2n\validation\plan\impl\common\MandatoryValidator
	 */
	static function minlength(int $minlength, $errorMessage = null) {
		return new MinlengthValidator($minlength, Message::build($errorMessage));
	}
	
	/**
	 * @param Message|null $errorMessage
	 * @return \n2n\validation\plan\impl\common\MandatoryValidator
	 */
	static function maxlength(int $maxlength, $errorMessage = null) {
		return new MaxlengthValidator($maxlength, Message::build($errorMessage));
	}
	
	/**
	 * @param Message|null $errorMessage
	 * @return \n2n\validation\plan\impl\common\MandatoryValidator
	 */
	static function email($errorMessage = null) {
		return new EmailValidator(Message::build($errorMessage));
	}
	
	
}