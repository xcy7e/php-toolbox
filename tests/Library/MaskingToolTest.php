<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use PHPUnit\Framework\TestCase;
use Xcy7e\PhpToolbox\Library\MaskingTool;

/**
 * @package Xcy7e\PhpToolbox\Tests\Library
 * @author Jonathan Riedmair <jonathan@xcy7e.pro>
 */
class MaskingToolTest extends TestCase
{
    public function testMaskIbanWithDefaults()
    {
        $iban = 'DE12500105170648489890';
        $masked = MaskingTool::maskIban($iban);

        // Uppercased and grouped in blocks of 4 separated by spaces
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{2}\d{2}(?: [0-9*\s]{4}){4} [0-9*]{2}/', $masked);
        // First 4 unchanged
        $this->assertStringStartsWith(substr(strtoupper($iban), 0, 4), $masked);
        // Count of asterisks equals default maskLength (14)
        $this->assertSame(14, substr_count($masked, '*'));
    }

    public function testMaskIbanWithoutGrouping()
    {
        $iban = 'DE12500105170648489890';
        $masked = MaskingTool::maskIban($iban, false, 4, 6, 4);
        // No spaces when grouping=false
        $this->assertStringNotContainsString(' ', $masked);
        // Prefix unchanged and 6 stars starting at pos 4
        $this->assertStringStartsWith('DE12******', $masked);
    }
}