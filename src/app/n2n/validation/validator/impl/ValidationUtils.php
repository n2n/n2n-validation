<?php
namespace n2n\validation\validator\impl;

use n2n\util\uri\Url;
use n2n\util\StringUtils;
use n2n\util\io\IoUtils;
use n2n\util\ex\IllegalStateException;
use n2n\io\managed\File;
use n2n\io\managed\img\ImageFile;

class ValidationUtils {
	/**
	 * checks a string, if it is a valid e-mail address
	 *
	 * @param string $email
	 * @return bool
	 */
	public static function isEmail(string $email): bool {
		return false !== filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE);
	}


	public static function isUrl(string $urlStr, bool $absoluteRequired = true): bool {
		if ($absoluteRequired) {
			return is_string(filter_var($urlStr, FILTER_VALIDATE_URL));
		}

		$schemeStr = parse_url($urlStr, PHP_URL_SCHEME);
		$hostStr = parse_url($urlStr, PHP_URL_HOST);
		if (null !== $schemeStr && null !== $hostStr) {
			return is_string(filter_var($urlStr, FILTER_VALIDATE_URL));
		}

		if ($schemeStr !== null && $hostStr === null) {
			throw new IllegalStateException('Could not handle url: ' . $urlStr);
		}

		if ($hostStr === null) {
			$urlStr = 'https://example.ch/' . ltrim($urlStr, '/');
		} else if ($schemeStr === null) {
			$urlStr = 'https://' . ltrim($urlStr, '/');
		}

		return is_string(filter_var($urlStr, FILTER_VALIDATE_URL));
	}
	
//	/**
//	 * checks a string, if it is a valid url address
//	 *
//	 * @param string $url
//	 * @return bool
//	 */
//	public static function isUrl(string $url): bool {
//		if (empty(trim($url))) {
//			return false;
//		}
//
//		// Check if URL ends with invalid characters
//		if (preg_match('/[.:]$/', trim($url))) {
//			return false;
//		}
//
//		if (!parse_url($url, PHP_URL_SCHEME)) {
//			$url = 'http://' . $url;
//		}
//
//		try {
//			$url = Url::create($url)->toIdnaAsciiString();
//		} catch (\InvalidArgumentException $e) {
//			return false;
//		}
//
//		// Check if parse_url can handle the URL (not seriously malformed)
//		$parsedUrl = parse_url($url);
//		if ($parsedUrl === false) {
//			return false;
//		}
//
//		// Ensure we have both scheme and host, and host is not empty
//		$scheme = parse_url($url, PHP_URL_SCHEME);
//		$host = parse_url($url, PHP_URL_HOST);
//
//		if (!$scheme || !$host || empty(trim($host))) {
//			return false;
//		}
//
//		// Validate hostname format (must contain dot or be localhost/IP)
//		if (!filter_var($host, FILTER_VALIDATE_IP) &&
//				$host !== 'localhost' &&
//				!str_contains($host, '.')) {
//			return false;
//		}
//
//		// Final validation with PHP's built-in filter
//		if (!filter_var($url, FILTER_VALIDATE_URL)) {
//			return false;
//		}
//
//		return true;
//	}

	static function isLowerCaseOnly(string $str): bool {
		return mb_strtolower($str) === $str;
	}

	static function isUpperCaseOnly(string $str): bool {
		return mb_strtoupper($str) === $str;
	}

	static function minlength(string $str, int $minlength): bool {
		return mb_strlen($str) >= $minlength;
	}
	
	static function isNotShorterThan(string $str, int $minlength): bool {
		return mb_strlen($str) >= $minlength;
	}
	
	static function maxlength(string $str, int $maxlength): bool {
		return mb_strlen($str) <= $maxlength;
	}
	
	static function isNotLongerThen(string $str, int $maxlength): bool {
		return mb_strlen($str) <= $maxlength;
	}
	
	static function isNotEmpty(?string $str): bool {
		return $str !== null && !StringUtils::isEmpty($str);
	}
	
	static function isFileTypeSupported(File $file, ?array $allowedMimeTypes, ?array $allowedExtensions = null): bool {
		return ($allowedMimeTypes === null && $allowedExtensions === null)
				|| ($allowedExtensions !== null && in_array($file->getOriginalExtension(), $allowedExtensions))
				|| ($allowedMimeTypes !== null && in_array($file->getFileSource()->getMimeType(), $allowedMimeTypes));
	}
	
	const RESERVATED_MEMORY_SIZE = 1048576;
	
	/**
	 * @param ImageFile $imageFile
	 */
	static function isImageResolutionManagable(ImageFile $imageFile): bool {
		$memoryLimit = IoUtils::determinMemoryLimit();
		$requiredMemorySize = $imageFile->getImageSource()->calcResourceMemorySize();

		return !(self::RESERVATED_MEMORY_SIZE + ($requiredMemorySize * 2) > $memoryLimit);
	}
}
