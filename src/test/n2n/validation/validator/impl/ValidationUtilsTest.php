<?php
namespace n2n\validation\validator\impl;

use PHPUnit\Framework\TestCase;
use n2n\util\uri\Url;

class ValidationUtilsTest extends TestCase {

	public function testIsUrlValid() {
		$this->assertTrue(ValidationUtils::isUrl('http://example.com'));
		$this->assertTrue(ValidationUtils::isUrl('https://example.com'));
		$this->assertTrue(ValidationUtils::isUrl('example.com', false));
		$this->assertTrue(ValidationUtils::isUrl('localhost', false));
		$this->assertTrue(ValidationUtils::isUrl('127.0.0.1', false));
		$this->assertTrue(ValidationUtils::isUrl('192.168.1.1', false));
		$this->assertTrue(ValidationUtils::isUrl('sub.example.com', false));
		$this->assertTrue(ValidationUtils::isUrl('www.example.com', false));
		$this->assertTrue(ValidationUtils::isUrl('https://sub.domain.example.com'));
		$this->assertTrue(ValidationUtils::isUrl('http://example.com:8080'));
		$this->assertTrue(ValidationUtils::isUrl('https://example.com/path'));
		$this->assertTrue(ValidationUtils::isUrl('https://example.com/path?query=value'));
		$this->assertTrue(ValidationUtils::isUrl('https://example.com/path#fragment'));
		$this->assertTrue(ValidationUtils::isUrl('https://user:pass@example.com'));
		$this->assertTrue(ValidationUtils::isUrl('ftp://files.example.com'));
		$this->assertTrue(ValidationUtils::isUrl('example-with-dash.com', false));
		$this->assertTrue(ValidationUtils::isUrl('123.example.com', false));
		$this->assertTrue(ValidationUtils::isUrl('xn--bcher-kva.de', false));
		$this->assertTrue(ValidationUtils::isUrl('xn--nxasmq6b.com', false));
	}

	public function testIsUrlInvalid() {
		$this->assertFalse(ValidationUtils::isUrl(''));
		$this->assertFalse(ValidationUtils::isUrl('   '));
		$this->assertFalse(ValidationUtils::isUrl("\t\n\r"));
		$this->assertFalse(ValidationUtils::isUrl('not-a-url'));
		$this->assertFalse(ValidationUtils::isUrl('just-text'));
		$this->assertFalse(ValidationUtils::isUrl('no-domain-extension'));
		$this->assertFalse(ValidationUtils::isUrl('http://'));
		$this->assertFalse(ValidationUtils::isUrl('https://'));
		$this->assertFalse(ValidationUtils::isUrl('://example.com'));
		$this->assertFalse(ValidationUtils::isUrl('http:///'));
		$this->assertFalse(ValidationUtils::isUrl('http:///example.com'));
		$this->assertFalse(ValidationUtils::isUrl('ht!tp://example.com'));
		$this->assertFalse(ValidationUtils::isUrl('http://exam ple.com'));
		$this->assertFalse(ValidationUtils::isUrl('http://'));
		$this->assertFalse(ValidationUtils::isUrl('http://.'));
		$this->assertFalse(ValidationUtils::isUrl('http://.com'));
		$this->assertFalse(ValidationUtils::isUrl('http://..'));
//		$this->assertFalse(ValidationUtils::isUrl('http://example.'));
		$this->assertFalse(ValidationUtils::isUrl('http://-example.com'));
		$this->assertFalse(ValidationUtils::isUrl('http://example-.com'));
		$this->assertFalse(ValidationUtils::isUrl('javascript:alert(1)'));
		$this->assertFalse(ValidationUtils::isUrl('data:text/html,<script>'));
//		$this->assertFalse(ValidationUtils::isUrl('file:///etc/passwd'));
		$this->assertFalse(ValidationUtils::isUrl('about:blank'));
//		$this->assertFalse(ValidationUtils::isUrl('chrome://settings'));
//		$this->assertFalse(ValidationUtils::isUrl('mailto:test@example.com'));
		$this->assertFalse(ValidationUtils::isUrl('tel:+1234567890'));
		$this->assertFalse(ValidationUtils::isUrl('sms:+1234567890'));
	}

	public function testIsUrlEdgeCases() {
		$this->assertFalse(ValidationUtils::isUrl('0'));
		$this->assertFalse(ValidationUtils::isUrl('1'));
		$this->assertFalse(ValidationUtils::isUrl('false'));
		$this->assertFalse(ValidationUtils::isUrl('true'));
		$this->assertFalse(ValidationUtils::isUrl('null'));
		$this->assertFalse(ValidationUtils::isUrl('undefined'));
		$this->assertFalse(ValidationUtils::isUrl('/path/only'));
		$this->assertFalse(ValidationUtils::isUrl('?query=only'));
		$this->assertFalse(ValidationUtils::isUrl('#fragment-only'));
		$this->assertFalse(ValidationUtils::isUrl('//example.com'));
		$this->assertFalse(ValidationUtils::isUrl('.com'));
		$this->assertFalse(ValidationUtils::isUrl('http:/'));
		$this->assertFalse(ValidationUtils::isUrl('http'));
		$this->assertFalse(ValidationUtils::isUrl('://'));
	}

	public function testIsUrlPortNumbers() {
		$this->assertTrue(ValidationUtils::isUrl('http://example.com:80'));
		$this->assertTrue(ValidationUtils::isUrl('https://example.com:443'));
		$this->assertTrue(ValidationUtils::isUrl('http://example.com:8080'));
		$this->assertTrue(ValidationUtils::isUrl('http://example.com:3000'));
		$this->assertTrue(ValidationUtils::isUrl('//example.com:8080', false));

//		$this->assertFalse(ValidationUtils::isUrl('http://example.com:'));
		$this->assertFalse(ValidationUtils::isUrl('http://example.com:99999'));
		$this->assertFalse(ValidationUtils::isUrl('http://example.com:-1'));
		$this->assertFalse(ValidationUtils::isUrl('http://example.com:abc'));
	}

	public function testIsUrlComplexValidUrls() {
		$complexUrls = [
				'https://user:password@sub.domain.example.com:8080/path/to/resource?param1=value1&param2=value2#section',
				'http://192.168.1.1:3000/api/v1/users?filter=active&sort=name',
				'https://api.example.com/v2/users/123/posts?include=comments&page=2',
				'ftp://username:password@ftp.example.com:21/directory/file.txt',
				'http://localhost:8000/admin/dashboard',
				'https://cdn.example.com/assets/images/logo.png',
		];

		foreach ($complexUrls as $url) {
			$this->assertTrue(ValidationUtils::isUrl($url), "URL should be valid: $url");
		}
	}

	public function testIsUrlSecurityConcerns() {
		$maliciousUrls = [
				'javascript:alert("XSS")',
				'data:text/html;base64,PHNjcmlwdD5hbGVydCgiWFNTIik8L3NjcmlwdD4=',
				'vbscript:msgbox("XSS")',
//				'file:///etc/passwd',
//				'file://C:/Windows/System32/config/sam',
				'about:blank',
//				'chrome://settings',
//				'moz-extension://abc123',
//				'chrome-extension://abc123',
		];

		foreach ($maliciousUrls as $url) {
			$this->assertFalse(ValidationUtils::isUrl($url), "Potentially malicious URL should be invalid: $url");
		}
	}

	public function testIsUrlSpecialCharacters() {
		$this->assertTrue(ValidationUtils::isUrl('http://example.com/path?query=value with spaces'));
		$this->assertFalse(ValidationUtils::isUrl('http://example.com/path with spaces'));
		$this->assertFalse(ValidationUtils::isUrl('http://example.com/path#anchor with spaces'));

		$this->assertTrue(ValidationUtils::isUrl('http://example.com/path%20with%20encoded%20spaces'));

		$this->assertFalse(ValidationUtils::isUrl('http://exam ple.com'));
		$this->assertFalse(ValidationUtils::isUrl('http://example.com<script>'));
		$this->assertFalse(ValidationUtils::isUrl('http://example.com"onclick="alert(1)"'));
	}
}