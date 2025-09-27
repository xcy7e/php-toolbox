<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Tests\Library;

use Xcy7e\PhpToolbox\Library\ArrayTool;
use PHPUnit\Framework\TestCase;

/**
 * @package Xcy7e\PhpToolbox\Tests\Library
 * @author Jonathan Riedmair <jonathan@xcy7e.pro>
 */
class ArrayToolTest extends TestCase
{

	public function testTrimElements()
	{
		$array = [
			'item1',
			' item2',
			' item3 ',
			'item4 ',
			'item 5'
		];

		$array = ArrayTool::trimElements($array);

		$this->assertIsArray($array);
		$this->assertEquals(['item1', 'item2', 'item3', 'item4', 'item 5'], $array);
	}

}