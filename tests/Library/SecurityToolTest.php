<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use Exception;
use PHPUnit\Framework\TestCase;
use Xcy7e\PhpToolbox\Library\SecurityTool;

/**
 * @package Xcy7e\PhpToolbox\Tests\Library
 * @author Jonathan Riedmair <jonathan@xcy7e.pro>
 */
class SecurityToolTest extends TestCase
{

	/**
	 * @throws Exception
	 */
	public function testGenerateRandomPassword()
	{
		$pwd = SecurityTool::generateRandomPassword(12);

		$this->assertIsString($pwd);
		$this->assertGreaterThanOrEqual(12, strlen($pwd));
		$this->assertMatchesRegularExpression('/^[A-Za-z0-9]+$/', $pwd);
	}

    public function testSanitizeDataRecursiveMasksAndTruncates()
    {
        $data = [
            'username' => 'john',
            'password' => 'secret123',
            'nested' => [
                'fileHolderBase64' => 'abc',
                'note' => str_repeat('x', 260),
            ],
        ];

        $result = SecurityTool::sanitizeDataRecursive($data, '##MASK##', ['password', 'fileHolderBase64'], 255);

        $this->assertSame('##MASK##', $result['password']);
        $this->assertSame('##MASK##', $result['nested']['fileHolderBase64']);
        // note should be truncated to 255 bytes with suffix
        $this->assertTrue(strlen($result['nested']['note']) > 255);
        $this->assertStringEndsWith('##MASK##', $result['nested']['note']);
    }

    public function testIsIpWhitelisted()
    {
        $ip = '10.0.0.1';
        $this->assertTrue(SecurityTool::isIpWhitelisted($ip, '10.0.0.1'));

        $ip = '192.168.1.50';
        $this->assertFalse(SecurityTool::isIpWhitelisted($ip, '192.168.1.49'));

        // Empty whitelist => false
        $this->assertFalse(SecurityTool::isIpWhitelisted($ip, ''));
    }
}