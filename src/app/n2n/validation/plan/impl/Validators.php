<?php
namespace n2n\validation\plan\impl;

use n2n\validation\plan\impl\common\MandatoryValidator;
use n2n\l10n\Message;
use n2n\validation\plan\impl\string\EmailValidator;

class Validators {
	
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
	static function email($errorMessage = null) {
		return new EmailValidator(Message::build($errorMessage));
	}
}