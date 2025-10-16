<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use Xcy7e\PhpToolbox\Library\CryptoTool;
use PHPUnit\Framework\TestCase;

/**
 * @package Xcy7e\PhpToolbox\Tests\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
class CryptoToolTest extends TestCase
{

	public function testEncrypt()
	{
		$enc = CryptoTool::encrypt('abc', 'def', CryptoTool::DEFAULT_ALGO, '1337-666-1337-00');

		$this->assertEquals('T0VOQqJc+iO603FRSu2fXA==', $enc);
	}

	public function testDecrypt()
	{
		$enc  = 'T0VOQqJc+iO603FRSu2fXA==';
		$data = CryptoTool::decrypt($enc, 'def', CryptoTool::DEFAULT_ALGO, '1337-666-1337-00');

		$this->assertEquals('abc', $data);
	}

	public function testHash()
	{
		$data = 'abc';
		$hash = CryptoTool::hash($data);

		$this->assertEquals('ba7816bf8f01cfea414140de5dae2223b00361a396177a9cb410ff61f20015ad', $hash);
	}

	public function testRandomStr()
	{
		$str  = CryptoTool::randomStr(6);
		$str2 = CryptoTool::randomStr(6);

		$this->assertIsString($str);
		$this->assertEquals(6, strlen($str));
		$this->assertNOtEquals($str, $str2);
	}

}