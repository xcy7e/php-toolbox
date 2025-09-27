<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Xcy7e\PhpToolbox\Library\PathTool;

/**
 * @package Xcy7e\PhpToolbox\Tests\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
class PathToolTest extends TestCase
{

	public function testBuildPath()
	{
		$segments = ['var', 'log', 'app'];
		$path     = PathTool::buildPath($segments);
		$this->assertStringEndsWith(implode(DIRECTORY_SEPARATOR, $segments), $path);
		// Ensure no duplicate separators
		$this->assertStringNotContainsString(str_repeat(DIRECTORY_SEPARATOR, 2), $path);
	}

	public function testBuildHashPath()
	{
		$uid      = Uuid::fromString('c0ffeeba-babe-babe-babe-c0ffeec0ffee');
		$hashPath = PathTool::buildHashPath($uid);

		// expect: "c0/ff/ee/" or "c0\ff\ee\"
		$this->assertEquals(sprintf('%s%s%s%s%s%s', 'c0', DIRECTORY_SEPARATOR, 'ff', DIRECTORY_SEPARATOR, 'ee', DIRECTORY_SEPARATOR), $hashPath);
		// Ensure no duplicate separators
		$this->assertStringNotContainsString(str_repeat(DIRECTORY_SEPARATOR, 2), $hashPath);
	}

	public function testIsHashPath()
	{
		$hashPath1 = sprintf('%s%s%s%s%s%s', 'c0', DIRECTORY_SEPARATOR, 'ff', DIRECTORY_SEPARATOR, 'ee', DIRECTORY_SEPARATOR);	// c0/ff/ee
		$hashPath2 = sprintf('%s%s%s%s%s%s', 'c0', DIRECTORY_SEPARATOR, 'ff', DIRECTORY_SEPARATOR, 'ee', DIRECTORY_SEPARATOR);	// c0/ff/ee/
		$hashPath3 = sprintf('%s%s%s%s%s%s', 'c0', DIRECTORY_SEPARATOR, 'ff', DIRECTORY_SEPARATOR, 'ee', DIRECTORY_SEPARATOR);	// /c0/ff/ee
		$hashPath4 = sprintf('%s%s%s%s%s%s', 'c0', DIRECTORY_SEPARATOR, 'ff', DIRECTORY_SEPARATOR, 'ee', DIRECTORY_SEPARATOR);	// /c0/ff/ee/
		$nonHashPath = '/c0f/fee/';

		$this->assertTrue(PathTool::isHashPath($hashPath1));
		$this->assertTrue(PathTool::isHashPath($hashPath2));
		$this->assertTrue(PathTool::isHashPath($hashPath3));
		$this->assertTrue(PathTool::isHashPath($hashPath4));
		$this->assertFalse(PathTool::isHashPath($nonHashPath));
	}

	public function testGetHashPathSegment()
	{
		$dir = '/c0/ff/ee/';
		$grp = PathTool::getHashPathSegment($dir);

		// getEncPathGroup returns numeric-indexed array [1=>aa,2=>bb,3=>cc]
		$this->assertSame(['c0', 'ff', 'ee'], array_values($grp));
	}

}