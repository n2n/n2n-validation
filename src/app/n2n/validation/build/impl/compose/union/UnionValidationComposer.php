<?php
namespace n2n\validation\build\impl\compose\union;

use n2n\util\magic\MagicContext;
use n2n\validation\plan\ValidationTask;
use n2n\validation\plan\ValidationPlan;
use n2n\validation\plan\Validatable;
use n2n\validation\validator\Validator;
use n2n\util\type\ArgUtils;
use n2n\validation\plan\ValidationGroup;
use n2n\validation\plan\ValidationResult;
use n2n\util\magic\impl\MagicContexts;

class UnionValidationComposer implements ValidationTask {
	/**
	 * @var ValidationPlan
	 */
	private $validationPlan;
	/**
	 * @var \Closure
	 */
	private $assembleClosures = [];
	
	/**
	 * @param UnionValidationComposerSource $source
	 */
	function __construct(private UnionValidationComposerSource $source) {
		$this->validationPlan = new ValidationPlan($this->source);
	}
	
	/**
	 * @param Validator[] $validators
	 * @return UnionValidationComposer
	 */
	function val(Validator ...$validators) {
		$this->assembleClosures[] = function() use ($validators) {
			$validatables = $this->source->getValidatables();
			ArgUtils::valArrayReturn($validatables, $this->source, 'getValidatables',
					Validatable::class);

			$this->validationPlan->addValidationGroup(new ValidationGroup($validators, $validatables,
					$this->source));
		};
		
		return $this;
	}
	
	private function prepareJob() {
		while (null !== ($closure = array_shift($this->assembleClosures))) {
			$closure();
		}
	}
	
	function test(MagicContext $magicContext): bool {
		$this->prepareJob();
		
		return $this->validationPlan->test($magicContext);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \n2n\validation\plan\ValidationTask::exec()
	 */
	function exec(MagicContext $magicContext = null, mixed $input = null): ValidationResult {
		$this->prepareJob();

		return $this->validationPlan->exec($magicContext ?? MagicContexts::simple([]));
	}	
}
