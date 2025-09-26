<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use PHPUnit\Framework\TestCase;
use Xcy7e\PhpToolbox\Library\ConversionTool;

/**
 * @package Xcy7e\PhpToolbox\Tests\Library
 * @author Jonathan Riedmair <jonathan@xcy7e.pro>
 */
class ConversionToolTest extends TestCase
{
    public function testParseByteShorthandBasic()
    {
		// One-Letter notation (e.g. "8M")
        $this->assertSame(8 * 1024 * 1024, ConversionTool::parseByteShorthand('8M'));
        $this->assertSame(12 * 1024, ConversionTool::parseByteShorthand('12K'));
        $this->assertSame(1 * 1024 * 1024 * 1024, ConversionTool::parseByteShorthand('1G'));
        $this->assertSame(16 * 1024, ConversionTool::parseByteShorthand(' 16k '));
		// Two-Letter notation (e.g. "8MB")
		$this->assertSame(8 * 1024 * 1024, ConversionTool::parseByteShorthand('8MB'));
		$this->assertSame(12 * 1024, ConversionTool::parseByteShorthand('12KB'));
		$this->assertSame(1 * 1024 * 1024 * 1024, ConversionTool::parseByteShorthand('1GB'));
		$this->assertSame(16 * 1024, ConversionTool::parseByteShorthand(' 16kB '));
        // numeric passthrough
        $this->assertSame('123', ConversionTool::parseByteShorthand('123'));
    }

    public function testStripAccentsDefaultRemovesApostrophes()
    {
        $in  = "côte d’ivoire"; // includes a curly apostrophe
        $out = ConversionTool::stripAccents($in);
        $this->assertSame('cote divoire', $out);

        $this->assertSame('aaaaa', ConversionTool::stripAccents('âäáàã'));
        $this->assertSame('ss', ConversionTool::stripAccents('ß'));
        $this->assertSame('AE', ConversionTool::stripAccents('Æ'));
    }

    public function testStripAccentsKeepApostrophes()
    {
        $in  = "l’été d'Avril"; // multiple apostrophe-like chars

		$out = ConversionTool::stripAccents($in, false);
		$this->assertSame("l’ete d'Avril", $out);

		$out = ConversionTool::stripAccents($in, true);
		$this->assertSame("lete dAvril", $out);
    }
}