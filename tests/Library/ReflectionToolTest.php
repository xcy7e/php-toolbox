<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use PHPUnit\Framework\TestCase;
use Xcy7e\PhpToolbox\Library\ReflectionTool;

/**
 * @package Xcy7e\PhpToolbox\Tests\Library
 * @author Jonathan Riedmair <jonathan@xcy7e.pro>
 */
class ReflectionToolTest extends TestCase
{
    private function getSampleObject(): object
	{
        return new class {
            public function getName(): string
			{ return 'x'; }
            public function setValue($v) { }
            public function ping() { }
        };
    }

    public function testGetClassName()
    {
        $obj = $this->getSampleObject();
        $cn = ReflectionTool::getClassName($obj);
        $this->assertIsString($cn);
        $this->assertNotSame('', $cn);
        // Passing class-string should fail gracefully and return null
        $this->assertNull(ReflectionTool::getClassName('stdClass'));
    }

    public function testGetMethods()
    {
        $obj = $this->getSampleObject();
        $getters = ReflectionTool::getMethods($obj, 'get');
        $setters = ReflectionTool::getMethods($obj, 'set');
        $this->assertContains('getName', $getters);
        $this->assertNotContains('setValue', $getters);
        $this->assertContains('setValue', $setters);
        $this->assertNotContains('getName', $setters);
        // method without prefix should not appear
        $this->assertNotContains('ping', $getters);
    }

    public function testGetSnakeCase()
    {
        $this->assertSame('example_property', ReflectionTool::getSnakeCase('getExampleProperty'));
        $this->assertSame('firstname', ReflectionTool::getSnakeCase('setFirstname', 'set'));
        $this->assertSame('already_snake', ReflectionTool::getSnakeCase('already_snake'));
    }
}