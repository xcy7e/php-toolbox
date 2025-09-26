<?php

declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

/**
 * Conversion utilities.
 */
class ConversionTool
{

	/**
	 * Converts shorthand-byte-notations into bytes, e.g. `8M` -> ~`8e+6`, `12K` -> ~`12000`
	 */
	public static function parseByteShorthand(string $byteNotation): float|int|string
	{
		if (is_numeric($byteNotation))
			return $byteNotation;

		$byteNotation = trim($byteNotation);
		$last         = strtolower($byteNotation[strlen($byteNotation) - 1]);
		$bytes        = substr($byteNotation, 0, -1); // necessary since PHP 7.1; otherwise optional

		switch ($last) {
			case 'g':    // 'G' modifier available since PHP 5.1.0
				$bytes *= 1024;
			case 'm':
				$bytes *= 1024;
			case 'k':
				$bytes *= 1024;
		}

		return $bytes;
	}

}