<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use PHPUnit\Framework\TestCase;
use Xcy7e\PhpToolbox\Library\MimeTool;

/**
 * @package Xcy7e\PhpToolbox\Tests\Library
 * @author Jonathan Riedmair <jonathan@xcy7e.pro>
 */
class MimeToolTest extends TestCase
{
    public function testMime2ExtMappings()
    {
        $this->assertSame('png', MimeTool::mime2ext('image/png'));
        $this->assertSame('txt', MimeTool::mime2ext('text/plain'));
        $this->assertSame('jpeg', MimeTool::mime2ext('image/jpeg'));
        $this->assertSame('json', MimeTool::mime2ext('application/json'));
    }

    public function testMime2ExtUnknown()
    {
        $this->assertFalse(MimeTool::mime2ext('application/unknown-type'));
    }
}