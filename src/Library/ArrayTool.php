<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

/**
 * Array utilities.
 *
 * @package Xcy7e\PhpToolbox\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
final class ArrayTool
{

	/**
	 * Trims all strings in an array recursively
	 *
	 * @param array $a
	 * @return array
	 */
	public static function trimElements(array $a): array
	{
		foreach ($a as $k => $v) {
			if (is_array($v)) {
				$a[$k] = ArrayTool::trimElements($v);
			} elseif (is_string($v)) {
				$a[$k] = trim($v);
			}
		}
		return $a;
	}

}