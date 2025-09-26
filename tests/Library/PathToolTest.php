<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use PHPUnit\Framework\TestCase;
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

	public function testExtractCategoryDirSuffix()
	{
		$p = '/var/data/abc_DEFghi_JPG/foo';
		$this->assertSame('_DEF', PathTool::extractCategoryDirSuffix($p));
		$this->assertSame('', PathTool::extractCategoryDirSuffix('/no/suffix/here'));
	}

	public function testIsHashPathAndGetEncPathGroup()
	{
		$dir = '/aa/bb/cc/';
		$this->assertTrue(PathTool::isHashPath($dir));
		$grp = PathTool::getEncPathGroup($dir);
		// getEncPathGroup returns numeric-indexed array [1=>aa,2=>bb,3=>cc]
		$this->assertSame(['aa', 'bb', 'cc'], array_values($grp));

		$this->assertFalse(PathTool::isHashPath('/aa/bb/ccc/')); // last is 3 chars
	}

}