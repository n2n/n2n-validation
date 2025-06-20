<?php
namespace n2n\validation\validator\impl;

use n2n\util\uri\Url;
use n2n\util\StringUtils;
use n2n\io\managed\File;
use n2n\util\io\IoUtils;
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
	 * @param bool $schemeRequired
	 * @return bool
	 */
	public static function isUrl(string $url, bool $schemeRequired = true): bool {
		try {
			$url = Url::create($url)->toIdnaAsciiString();
		} catch (\InvalidArgumentException $e) {
			return false;
		}

		if (parse_url($url, PHP_URL_SCHEME)
				&& parse_url($url, PHP_URL_HOST)
				&& filter_var($url, FILTER_VALIDATE_URL)) {
			return true;
		}

		if (!$schemeRequired) {
			str_contains($url, '.') && preg_match('/\.[a-zA-Z]{2,}$/', $url);
		}

		return false;
	}

	static function isLowerCaseOnly(string $str): bool {
		return mb_strtolower($str) === $str;
	}

	static function isUpperCaseOnly(string $str): bool {
		return mb_strtoupper($str) === $str;
	}

	static function minlength(string $str, int $minlength) {
		return mb_strlen($str) >= $minlength;
	}
	
	static function isNotShorterThan(string $str, int $minlength) {
		return mb_strlen($str) >= $minlength;
	}
	
	static function maxlength(string $str, int $maxlength) {
		return mb_strlen($str) <= $maxlength;
	}
	
	static function isNotLongerThen(string $str, int $maxlength) {
		return mb_strlen($str) <= $maxlength;
	}
	
	static function isNotEmpty(?string $str) {
		return $str !== null && !StringUtils::isEmpty($str);
	}
	
	static function isFileTypeSupported(File $file, ?array $allowedMimeTypes, ?array $allowedExtensions = null) {
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
