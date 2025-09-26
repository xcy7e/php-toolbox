<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Xcy7e\PhpToolbox\Library\HashTool;

/**
 * @package Xcy7e\PhpToolbox\Tests\Library
 * @author Jonathan Riedmair <jonathan@xcy7e.pro>
 */
class HashToolTest extends TestCase
{
    public function testGetHash()
    {
        $this->assertSame(md5('abc'), HashTool::getHash('abc', 'md5'));
        // unknown algo falls back to sha256
        $this->assertSame(hash('sha256', 'abc'), HashTool::getHash('abc', 'unknown'));
    }

    public function testRandomStr()
    {
        $s = HashTool::randomStr(10);
        $this->assertIsString($s);
        $this->assertSame(10, strlen($s));
        $this->assertMatchesRegularExpression('/^[0-9A-Za-z]+$/', $s);

        $this->assertSame('', HashTool::randomStr(0));
        $this->assertSame('', HashTool::randomStr(-5));
    }

    public function testGenerateHashPath()
    {
        $uuid = Uuid::fromString('00112233-4455-6677-8899-aabbccddeeff');
        $path = HashTool::generateHashPath($uuid);
        $sep = DIRECTORY_SEPARATOR;
        $this->assertSame('00' . $sep . '11' . $sep . '22' . $sep, $path);

        // Using string
        $path2 = HashTool::generateHashPath('ffeeddcc-bbaa-9988-7766-554433221100');
        $this->assertSame('ff' . $sep . 'ee' . $sep . 'dd' . $sep, $path2);

        // Random UUID: structure check only
        $path3 = HashTool::generateHashPath();
        $quotedSep = preg_quote(DIRECTORY_SEPARATOR, '#');
        $pattern = '#^[A-Fa-f0-9]{2}' . $quotedSep . '[A-Fa-f0-9]{2}' . $quotedSep . '[A-Fa-f0-9]{2}' . $quotedSep . '$#';
        $this->assertMatchesRegularExpression($pattern, $path3);
    }
}