<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use PHPUnit\Framework\TestCase;
use Xcy7e\PhpToolbox\Library\StringTool;

/**
 * @package Xcy7e\PhpToolbox\Tests\Library
 * @author Jonathan Riedmair <jonathan@xcy7e.pro>
 */
class StringToolTest extends TestCase
{

    public function testReplaceUmlautsAndApostrophes()
    {
        $this->assertSame('Oesterreich', StringTool::replaceUmlautsAndApostrophes('Österreich'));
        $this->assertSame('Uebergroesse', StringTool::replaceUmlautsAndApostrophes('Übergröße'));
		$this->assertNotSame('uebergroesse', StringTool::replaceUmlautsAndApostrophes('Übergröße'));
		$this->assertSame("Lete", StringTool::replaceUmlautsAndApostrophes("L'été"));
    }

    public function testMakeStringComparable()
    {
        $this->assertSame('herroesterreich', StringTool::makeStringComparable('Herr Österreich '));
        $this->assertSame("jaimelete", StringTool::makeStringComparable("J'aime l'été"));
    }

	public function testCompareStr()
	{
		$this->assertTrue(StringTool::compareStr('Österreich', 'oesterreich'));
		$this->assertTrue(StringTool::compareStr(' Herr Österreich ', 'HERR österreich'));
		$this->assertTrue(StringTool::compareStr(' Herr Öster-reich ', 'HERR öster reich'));
		$this->assertFalse(StringTool::compareStr('Jonathan Doe', 'John Doe'));
	}

}