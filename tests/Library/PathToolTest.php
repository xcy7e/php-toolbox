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

	public function testBuildHashPathDepth3()
	{
		$uid      = Uuid::fromString('c0ffeeba-bebe-babe-babe-c0ffeec0ffee');
		$hashPath3 = PathTool::buildHashPath($uid, 3);
		$hashPath5 = PathTool::buildHashPath($uid, 5);

		// expect: "c0/ff/ee/" or "c0\ff\ee\"
		$this->assertEquals(sprintf('%s%s%s%s%s%s', 'c0', DIRECTORY_SEPARATOR, 'ff', DIRECTORY_SEPARATOR, 'ee', DIRECTORY_SEPARATOR), $hashPath3);
		// expect: "c0/ff/ee/ba/be" or "c0\ff\ee\ba\be"
		$this->assertEquals(sprintf('%s%s%s%s%s%s%s%s%s%s', 'c0', DIRECTORY_SEPARATOR, 'ff', DIRECTORY_SEPARATOR, 'ee', DIRECTORY_SEPARATOR, 'ba', DIRECTORY_SEPARATOR, 'be', DIRECTORY_SEPARATOR), $hashPath5);
		// Ensure no duplicate separators
		$this->assertStringNotContainsString(str_repeat(DIRECTORY_SEPARATOR, 2), $hashPath3);
	}

	public function testIsHashPath()
	{
		$hashPath1a = sprintf('%s', 'c0');	// c0
		$hashPath3a = sprintf('%s%s%s%s%s%s', 'c0', DIRECTORY_SEPARATOR, 'ff', DIRECTORY_SEPARATOR, 'ee', DIRECTORY_SEPARATOR);	// c0/ff/ee
		$hashPath3b = sprintf('%s%s%s%s%s%s', 'c0', DIRECTORY_SEPARATOR, 'ff', DIRECTORY_SEPARATOR, 'ee', DIRECTORY_SEPARATOR);	// c0/ff/ee/
		$hashPath3c = sprintf('%s%s%s%s%s%s', 'c0', DIRECTORY_SEPARATOR, 'ff', DIRECTORY_SEPARATOR, 'ee', DIRECTORY_SEPARATOR);	// /c0/ff/ee
		$hashPath3d = sprintf('%s%s%s%s%s%s', 'c0', DIRECTORY_SEPARATOR, 'ff', DIRECTORY_SEPARATOR, 'ee', DIRECTORY_SEPARATOR);	// /c0/ff/ee/
		$hashPath5a = sprintf('%s%s%s%s%s%s%s%s%s', 'c0', DIRECTORY_SEPARATOR, 'ff', DIRECTORY_SEPARATOR, 'ee', DIRECTORY_SEPARATOR, 'ba', DIRECTORY_SEPARATOR, 'be');	// /c0/ff/ee/ba/be
		$invalidHashPath = '/c0f/fee/';

		$this->assertTrue(PathTool::isHashPath($hashPath1a, 1));
		$this->assertTrue(PathTool::isHashPath($hashPath3a, 3));
		$this->assertTrue(PathTool::isHashPath($hashPath3b, 3));
		$this->assertTrue(PathTool::isHashPath($hashPath3c, 3));
		$this->assertTrue(PathTool::isHashPath($hashPath3d, 3));
		$this->assertTrue(PathTool::isHashPath($hashPath5a, 5));
		$this->assertFalse(PathTool::isHashPath($invalidHashPath, 2));
	}

	public function testGetHashPathSegment()
	{
		// Depth = 1
		$dir = '/c0/';
		$grp = PathTool::getHashPathSegment($dir, 1);

		// getHashPathSegment returns a numeric-indexed array [1=>aa,2=>bb,3=>cc]
		$this->assertSame(['c0'], array_values($grp));

		// Depth = 3
		$dir = '/c0/ff/ee/';
		$grp = PathTool::getHashPathSegment($dir, 3);

		// getHashPathSegment returns a numeric-indexed array [1=>aa,2=>bb,3=>cc]
		$this->assertSame(['c0', 'ff', 'ee'], array_values($grp));

		// Depth = 5
		$dir = '/c0/ff/ee/ba/be/';
		$grp = PathTool::getHashPathSegment($dir, 5);

		// getHashPathSegment returns a numeric-indexed array [1=>aa,2=>bb,3=>cc]
		$this->assertSame(['c0', 'ff', 'ee', 'ba', 'be'], array_values($grp));
	}

}