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
use n2n\validation\validator\impl\closure\ConditionalValidator;
use n2n\validation\validator\impl\closure\UniqueClosureValidator;

class Validators {

	/**
	 * @param TypeConstraint|null $typeConstraint
	 * @param TypeConstraint|null $valTypeConstraint
	 * @param mixed $errorMessage
	 * @return TypeValidator
	 */
	static function type(?TypeConstraint $typeConstraint, TypeConstraint $valTypeConstraint = null, mixed $errorMessage = null): TypeValidator {
		return new TypeValidator($typeConstraint, $valTypeConstraint, Message::build($errorMessage));
	}

	/**
	 * @param mixed $errorMessage
	 * @return ExistsValidator
	 */
	static function exists(mixed $errorMessage = null): ExistsValidator {
		return new ExistsValidator(Message::build($errorMessage));
	}
	
	/**
	 * @param mixed $errorMessage
	 * @return MandatoryValidator
	 */
	static function mandatory(mixed $errorMessage = null): MandatoryValidator {
		return new MandatoryValidator(Message::build($errorMessage));
	}

	/**
	 * Creates a ConditionalValidator that wraps a MandatoryValidator
	 *
	 * @param Closure|bool $condition A boolean value or a Closure that returns a boolean indicating if the validator should be applied.
	 * @param string|null $errorMessage The error message for the validator.
	 * @return ConditionalValidator Returns an instance of ConditionalValidator wrapping a MandatoryValidator.
	 */
	public static function mandatoryIf(Closure|bool $condition, $errorMessage = null): ConditionalValidator {
		if ($condition instanceof Closure) {
			return new ConditionalValidator($condition, new MandatoryValidator($errorMessage));
		}

		return new ConditionalValidator(fn() => $condition, new MandatoryValidator($errorMessage));
	}

	/**
	 * @param mixed $errorMessage
	 * @return MinlengthValidator
	 */
	static function minlength(int $minlength, mixed $errorMessage = null): MinlengthValidator {
		return new MinlengthValidator($minlength, Message::build($errorMessage));
	}
	
	/**
	 * @param mixed $errorMessage
	 * @return MaxlengthValidator
	 */
	static function maxlength(int $maxlength, mixed $errorMessage = null): MaxlengthValidator {
		return new MaxlengthValidator($maxlength, Message::build($errorMessage));
	}
	
	/**
	 * @param mixed $errorMessage
	 * @return MinValidator
	 */
	static function min(float $min, mixed $errorMessage = null): MinValidator {
		return new MinValidator($min, Message::build($errorMessage));
	}
	
	/**
	 * @param mixed $errorMessage
	 * @return MaxValidator
	 */
	static function max(float $max, mixed $errorMessage = null): MaxValidator {
		return new MaxValidator($max, Message::build($errorMessage));
	}

	/**
	 * @param mixed $errorMessage
	 * @return StepValidator
	 */
	static function step(float $step, mixed $errorMessage = null, float $offset = 0): StepValidator {
		return new StepValidator($step, Message::build($errorMessage), $offset);
	}

	/**
	 * @param mixed $errorMessage
	 * @return EmailValidator
	 */
	static function email(mixed $errorMessage = null): EmailValidator {
		return new EmailValidator(Message::build($errorMessage));
	}

	/**
	 * @param bool $schemeRequired
	 * @param array|null $allowedSchemes
	 * @return UrlValidator
	 */
	static function url(bool $schemeRequired = false, array $allowedSchemes = null): UrlValidator {
		return new UrlValidator($schemeRequired, $allowedSchemes);
	}
	
	/**
	 * @param mixed $errorMessage
	 * @return EnumValidator
	 */
	static function enum(array $values, mixed $errorMessage = null): EnumValidator {
		return new EnumValidator($values, Message::build($errorMessage));
	}
	
	/**
	 * Closure gets called once for all values, even if the values do not exist.
	 *
	 * @param Closure $closure
	 * @return ClosureValidator
	 */
	static function closure(Closure $closure): ClosureValidator {
		return new ClosureValidator($closure, null);
	}

	/**
	 * Closure gets called once for all values, if at least one value exists.
	 *
	 * @param Closure $closure
	 * @return ClosureValidator
	 */
	static function closureAny(Closure $closure): ClosureValidator {
		return new ClosureValidator($closure, false);
	}

	/**
	 * Closure gets called once for all values, if all values exists.
	 *
	 * @param Closure $closure
	 * @return ClosureValidator
	 */
	static function closureEvery(Closure $closure): ClosureValidator {
		return new ClosureValidator($closure, true);
	}

	/**
	 * Closure gets called for every value and only if it exists.
	 *
	 * @param Closure $closure
	 * @return ValueClosureValidator
	 */
	static function valueClosure(Closure $closure): ValueClosureValidator {
		return new ValueClosureValidator($closure);
	}

	static function noSpecialChars(mixed $errorMessage = null): NoSpecialCharsValidator {
		return new NoSpecialCharsValidator(Message::build($errorMessage));
	}

	static function uniqueClosure(\Closure $uniqueTester, mixed $errorMessage = null): UniqueClosureValidator {
		return new UniqueClosureValidator($uniqueTester, Message::build($errorMessage));
	}
}