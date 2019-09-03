<?php
namespace n2n\validation\impl;


use n2n\l10n\Message;

class ValidationMessages {
	const NS = 'n2n\validation';
	
	/**
	 * @param string $fieldName
	 * @return \n2n\l10n\impl\TextCodeMessage
	 */
	static function mandatory(string $fieldName = null) {
		if ($fieldName) {
			return Message::createCode('mandatory_err', null, self::NS);
		}
		
		return Message::createCodeArg('field_mandatory_err', ['field' => $fieldName], null, self::NS);
	}
	
	/**
	 * @param int $min
	 * @param string $fieldName
	 * @return \n2n\l10n\impl\TextCodeMessage
	 */
	static function minElements(int $min, string $fieldName = null) {
		if ($fieldName) {
			return Message::createCode('min_elements_err', null, self::NS, $min);
		}
		
		return Message::createCodeArg('field_min_elements_err', ['field' => $fieldName], null, self::NS, $min);
	}
	
	/**
	 * @param int $max
	 * @param string $fieldName
	 * @return \n2n\l10n\impl\TextCodeMessage
	 */
	static function maxElements(int $max, string $fieldName = null) {
		if ($fieldName) {
			return Message::createCode('max_elements_err', null, self::NS, $max);
		}
		
		return Message::createCodeArg('field_max_elements_err', ['field' => $fieldName], null, self::NS, $max);
	}
	
	/**
	 * @param int $maxSize
	 */
	static function uploadMaxSize(int $maxSize, string $fileName, string $size, string $fieldName = null) {
		if ($fieldName) {
			return Message::createCodeArg('upload_size_err', ['fileName' => $fileName, 'size' => $size], 
					null, self::NS);
		}
		
		return Message::createCodeArg('field_upload_max_size_err', 
				['fileName' => $fileName, 'size' => $size, 'field' => $fieldName], null, self::NS);
	}
	
	/**
	 * @param string $fileName
	 * @param string $fieldName
	 * @return \n2n\l10n\impl\TextCodeMessage
	 */
	static function uploadIncomplete(string $fileName, string $fieldName = null) {
		if ($fieldName) {
			return Message::createCodeArg('upload_incomplete_err', ['fileName' => $fileName],
					null, self::NS);
		}
		
		return Message::createCodeArg('field_upload_incomplete_err',
				['fileName' => $fileName, 'field' => $fieldName], null, self::NS);
	}
}