<?php
namespace n2n\validation\impl;


use n2n\l10n\Message;
use n2n\io\managed\File;

class ValidationMessages {
	const NS = 'n2n\validation';
	
	/**
	 * @param string $fieldName
	 * @return \n2n\l10n\impl\TextCodeMessage
	 */
	static function mandatory(string $fieldName = null) {
		if ($fieldName === null) {
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
		if ($fieldName === null) {
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
		if ($fieldName === null) {
			return Message::createCode('max_elements_err', null, self::NS, $max);
		}
		
		return Message::createCodeArg('field_max_elements_err', ['field' => $fieldName], null, self::NS, $max);
	}
	
	/**
	 * @param int $maxSize
	 */
	static function uploadMaxSize(int $maxSize, string $fileName, string $size, string $fieldName = null) {
		if ($fieldName === null) {
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
		if ($fieldName === null) {
			return Message::createCodeArg('upload_incomplete_err', ['fileName' => $fileName],
					null, self::NS);
		}
		
		return Message::createCodeArg('field_upload_incomplete_err',
				['fileName' => $fileName, 'field' => $fieldName], null, self::NS);
	}
	

// 	static function extension(string $fileName, array $allowedFileExtensions, string $fieldName = null) {
// 		if ($fieldName === null) {
// 			return Message::createCodeArg('invalid_file_extension_err',
// 					['fileName' => $fileName, 'allowedExtensions' => implode(', ', $allowedFileExtensions)],
// 					null, self::NS);
// 		}
		
// 		return Message::createCodeArg('field_invalid_file_extension_err',
// 				['fileName' => $fileName, 'allowedExtensions' => implode(', ', $allowedFileExtensions), 'field' => $fieldName],
// 				null, self::NS);
// 	}
	
// 	static function mimeType(string $givenMimeType, array $allowedMimeTypes, string $fieldName = null) {
// 		if ($fieldName === null) {
// 			return Message::createCodeArg('invalid_file_mime_type_err',
// 					['givenMimeType' => $givenMimeType, 'allowedMimeTypes' => implode(', ', $allowedMimeTypes)],
// 					null, self::NS);
// 		}
		
// 		return Message::createCodeArg('field_invalid_file_mimetype_err',
// 				['givenMimeType' => $givenMimeType, 'allowedMimeTypes' => implode(', ', $allowedMimeTypes), 'field' => $fieldName],
// 				null, self::NS);
// 	}
	
	/**
	 * @param File $file
	 * @param array $allowedTypes
	 * @param string $fieldName
	 * @return \n2n\l10n\Message
	 */
	static function fileType(File $file, array $allowedTypes, string $fieldName = null) {
		$fileStr = $file->getOriginalName() . ' (' . $file->getFileSource()->getMimeType() . ')';
		
		if ($fieldName === null) {
			return Message::createCodeArg('unsupported_file_type_err',
					['file' => $fileStr, 'allowedTypes' => implode(', ', $allowedTypes)],
					null, self::NS);
		}
		
		return Message::createCodeArg('field_unsupported_file_type_err',
				['file' => $fileStr, 'allowedTypes' => implode(', ', $allowedTypes), 
						'field' => $fieldName], null, self::NS);
	}
	
	static function imageResolution(string $imageName, string $fieldName = null) {
		if ($fieldName === null) {
			return Message::createCodeArg('image_resolution_err', ['image' => $imageName], null, self::NS);
		}
		
		return Message::createCodeArg('field_image_resolution_err', ['image' => $imageName, 'fieldName' => $fieldName], null, self::NS);
	}
}