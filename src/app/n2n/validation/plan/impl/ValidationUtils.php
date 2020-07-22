<?php
namespace n2n\validation\plan\impl;

use n2n\util\uri\Url;
use n2n\util\StringUtils;
use n2n\io\managed\File;
use n2n\io\IoUtils;
use n2n\io\managed\img\ImageFile;

class ValidationUtils {
	/**
	 * checks a string, if it is a valid e-mail address
	 *
	 * @param string $email
	 * @return bool
	 */
	public static function isEmail(string $email) {
		return false !== filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	/**
	 * checks a string, if it is a valid url address
	 *
	 * @param string $url
	 * @return bool
	 */
	public static function isUrl(string $url, bool $schemeRequired = true) {
		try {
			$url = Url::create($url)->toIdnaAsciiString();
		} catch (\InvalidArgumentException $e) {
			return false;
		}
		
		if ($schemeRequired) {
			if (false !== filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
				return true;
			}
		} else {
			if (false !== filter_var($url, FILTER_VALIDATE_URL)) {
				return true;
			}
		}
		
		return false;
	}
	
	static function matchesUrlSchema($value) {
		if ($value === null) return;
		
		if (!self::isUrl($value, !$this->relativeAllowed)) {
			$this->failed($this->errorMessage, self::DEFAULT_ERROR_TEXT_CODE_INVALID, array(), 'n2n\impl\web\dispatch');
			return;
		}
		
		if ($this->allowedSchemes === null) return;
		
		$matches = null;
		if (preg_match('#^([^:]+):#', $value, $matches)
				&& in_array($matches[1], $this->allowedSchemes)) {
			return;
		}
				
		$this->failed($this->errorMessage, self::DEFAULT_ERROR_TEXT_CODE_INVALID_SCHEME,
				array('allowed_schemes' => implode(', ', $this->allowedSchemes)), 'n2n\impl\web\dispatch');
	}
	
	static function minlength(string $str, int $minlength) {
		return mb_strlen($str) <= $minlength;
	}
	
	static function isNotLongerThen(string $str, int $maxlength) {
		return mb_strlen($str) <= $maxlength;
	}
	
	static function isNotEmpty(?string $str) {
		return $str !== null && !StringUtils::isEmpty($str);
	}
	
	static function isFileTypeSupported(File $file, ?array $allowedMimeTypes, array $allowedExtensions = null) {
		return ($allowedMimeTypes === null && $allowedExtensions === null)
				|| ($allowedExtensions !== null && in_array($file->getOriginalExtension(), $allowedExtensions))
				|| ($allowedMimeTypes !== null && in_array($file->getFileSource()->getMimeType(), $allowedMimeTypes));
	}
	
	const RESERVATED_MEMORY_SIZE = 1048576;
	
	/**
	 * @param ImageFile $imageFile
	 */
	static function isImageResolutionManagable(ImageFile $imageFile) {
		$memoryLimit = IoUtils::determinMemoryLimit();
		$requiredMemorySize = $imageFile->getImageSource()->calcResourceMemorySize();

		return !(self::RESERVATED_MEMORY_SIZE + ($requiredMemorySize * 2) > $memoryLimit);
	}
}
