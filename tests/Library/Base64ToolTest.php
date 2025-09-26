<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use Xcy7e\PhpToolbox\Library\Base64Tool;
use PHPUnit\Framework\TestCase;

/**
 * @package Xcy7e\PhpToolbox\Tests\Library
 * @author Jonathan Riedmair <jonathan@xcy7e.pro>
 */
class Base64ToolTest extends TestCase
{

	public function testGetFileEndingFromBase64()
	{
		// Create a small temporary text file
		$dir  = sys_get_temp_dir();
		$path = tempnam($dir, 'b64tool_');
		file_put_contents($path, "Hello Base64Tool!\nThis is a plain text file.");

		try {
			$base64 = Base64Tool::base64EncodeFile($path);

			// Ensure data URI has the expected mime prefix
			$this->assertStringStartsWith('data:text/plain;base64,', $base64);

			// Now derive the file ending from the produced base64 string
			$ext = Base64Tool::getFileEndingFromBase64($base64);
			$this->assertIsString($ext);
			$this->assertSame('txt', $ext, 'Expected extension derived from base64 to be txt');
		} finally {
			// Cleanup
			if (is_file($path)) {
				@unlink($path);
			}
		}
	}

	public function testBase64EncodeFile()
	{
		$dir  = sys_get_temp_dir();
		$path = tempnam($dir, 'b64tool_');
		$content = "Hello World!";
		file_put_contents($path, $content);

		try {
			$base64 = Base64Tool::base64EncodeFile($path);
			$this->assertStringStartsWith('data:text/plain;base64,', $base64);
			// Extract data and ensure it decodes back to the original content
			[$prefix, $data] = explode(',', $base64, 2);
			$this->assertSame($content, base64_decode($data));
		} finally {
			@unlink($path);
		}
	}

	public function testIsValidBase64()
	{
		$valid = base64_encode('foobar');
		$this->assertTrue(Base64Tool::isValidBase64($valid));
		$this->assertTrue(Base64Tool::isValidBase64('data:text/plain;base64,' . $valid));

		$this->assertFalse(Base64Tool::isValidBase64('not-base64!!'));
		$this->assertFalse(Base64Tool::isValidBase64('data:text/plain;base64,not-base64!!'));
	}

	public function testGetBase64Data()
	{
		$payload = base64_encode('xyz');
		$dataUri = 'data:text/plain;base64,' . $payload;
		$this->assertSame($payload, Base64Tool::getBase64Data($dataUri));

		// Non data-URI but contains a comma should return substring after comma
		$this->assertSame('b', Base64Tool::getBase64Data('a,b'));

		// No comma present should return false
		$this->assertFalse(Base64Tool::getBase64Data('nocommahere'));
	}

	public function testStripBase64DataUriSchema()
	{
		$payload = base64_encode('hello');
		$dataUri = 'data:text/plain;base64,' . $payload;
		$this->assertSame($payload, Base64Tool::stripBase64DataUriSchema($dataUri));

		// If no schema, the string should be returned unchanged
		$this->assertSame('abc', Base64Tool::stripBase64DataUriSchema('abc'));
	}

}