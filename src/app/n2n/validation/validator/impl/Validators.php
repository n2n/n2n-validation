<?php
namespace n2n\validation\validator\impl;

use n2n\validation\validator\impl\common\MandatoryValidator;
use n2n\l10n\Message;
use n2n\validation\validator\impl\number\StepValidator;
use n2n\validation\validator\impl\string\EmailValidator;
use n2n\util\type\TypeConstraint;
use n2n\validation\validator\impl\string\MinlengthValidator;
use n2n\validation\validator\impl\string\MaxlengthValidator;
use n2n\validation\validator\impl\reflection\TypeValidator;
use n2n\validation\validator\impl\enum\EnumValidator;
use n2n\validation\validator\impl\closure\ValueClosureValidator;
use n2n\validation\validator\impl\common\ExistsValidator;
use n2n\validation\validator\impl\string\UrlValidator;
use n2n\validation\validator\impl\closure\ClosureValidator;
use n2n\validation\validator\impl\number\MinValidator;
use n2n\validation\validator\impl\number\MaxValidator;
use Closure;
use n2n\validation\validator\impl\string\NoSpecialCharsValidator;

class Validators {

	/**
	 * @param TypeConstraint|null $typeConstraint
	 * @param TypeConstraint|null $valTypeConstraint
	 * @param null $errorMessage
	 * @return TypeValidator
	 */
	static function type(?TypeConstraint $typeConstraint, TypeConstraint $valTypeConstraint = null, $errorMessage = null): TypeValidator {
		return new TypeValidator($typeConstraint, $valTypeConstraint, Message::build($errorMessage));
	}

	/**
	 * @param Message|null $errorMessage
	 * @return ExistsValidator
	 */
	static function exists($errorMessage = null) {
		return new ExistsValidator(Message::build($errorMessage));
	}
	
	/**
	 * @param Message|null $errorMessage
	 * @return MandatoryValidator
	 */
	static function mandatory($errorMessage = null) {
		return new MandatoryValidator(Message::build($errorMessage));
	}
	
	/**
	 * @param Message|null $errorMessage
	 * @return MinlengthValidator
	 */
	static function minlength(int $minlength, $errorMessage = null) {
		return new MinlengthValidator($minlength, Message::build($errorMessage));
	}
	
	/**
	 * @param Message|null $errorMessage
	 * @return MaxlengthValidator
	 */
	static function maxlength(int $maxlength, $errorMessage = null) {
		return new MaxlengthValidator($maxlength, Message::build($errorMessage));
	}
	
	/**
	 * @param Message|null $errorMessage
	 * @return MinValidator
	 */
	static function min(float $min, $errorMessage = null) {
		return new MinValidator($min, Message::build($errorMessage));
	}
	
	/**
	 * @param Message|null $errorMessage
	 * @return MaxValidator
	 */
	static function max(float $max, $errorMessage = null) {
		return new MaxValidator($max, Message::build($errorMessage));
	}

	/**
	 * @param Message|null $errorMessage
	 * @return StepValidator
	 */
	static function step(float $step, $errorMessage = null, float $offset = 0) {
		return new StepValidator($step, Message::build($errorMessage), $offset);
	}
	
	/**
	 * @param Message|null $errorMessage
	 * @return EmailValidator
	 */
	static function email($errorMessage = null) {
		return new EmailValidator(Message::build($errorMessage));
	}

	/**
	 * @param bool $schemeRequired
	 * @param array|null $allowedSchemes
	 * @return UrlValidator
	 */
	static function url(bool $schemeRequired = false, array $allowedSchemes = null) {
		return new UrlValidator($schemeRequired, $allowedSchemes);
	}
	
	/**
	 * @param Message|null $errorMessage
	 * @return EnumValidator
	 */
	static function enum(array $values, $errorMessage = null) {
		return new EnumValidator($values, Message::build($errorMessage));
	}
	
	/**
	 * Closure gets called once for all values, even if the values do not exist.
	 *
	 * @param Closure $closure
	 * @return ClosureValidator
	 */
	static function closure(Closure $closure) {
		return new ClosureValidator($closure, null);
	}

	/**
	 * Closure gets called once for all values, if at least one value exists.
	 *
	 * @param Closure $closure
	 * @return ClosureValidator
	 */
	static function closureAny(Closure $closure) {
		return new ClosureValidator($closure, false);
	}

	/**
	 * Closure gets called once for all values, if all values exists.
	 *
	 * @param Closure $closure
	 * @return ClosureValidator
	 */
	static function closureEvery(Closure $closure) {
		return new ClosureValidator($closure, true);
	}

	/**
	 * Closure gets called for every value and only if it exists.
	 *
	 * @param Closure $closure
	 * @return ValueClosureValidator
	 */
	static function valueClosure(Closure $closure) {
		return new ValueClosureValidator($closure);
	}

	static function noSpecialChars(Message $errorMessage = null) {
		return new NoSpecialCharsValidator($errorMessage);
	}
}