<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

/**
 * String utilities.
 *
 * @package Xcy7e\PhpToolbox\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
final class StringTool
{

	/**
	 * Compares two strings, regardless of umlauts, apostrophes, accents, case, special chars and spaces
	 *
	 * @param string $str1
	 * @param string $str2
	 * @return bool
	 */
	public static function compareStr(string $str1, string $str2): bool
	{
		return StringTool::makeStringComparable($str1) === StringTool::makeStringComparable($str2);
	}

	/**
	 * Transforms a string so that it's value can be compared regardless of spaces, case and umlauts
	 * Useful to compare e.g. names, iban, etc. in different typings
	 *
	 * @param string $str
	 * @return string
	 */
	public static function makeStringComparable(string $str): string
	{
		return strtolower(
			StringTool::replaceUmlautsAndApostrophes(
				preg_replace('/\s+/', '', $str)
			)
		);
	}

	/**
	 * Replaces all umlauts and apostrophe-letters with their ascii equivalent
	 *
	 * @param string $str
	 * @return string
	 */
	public static function replaceUmlautsAndApostrophes(string $str): string
	{
		// Replace umlauts with ae, oe, ue, etc.
		$umlauts      = ['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'];
		$replacements = ['ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss'];
		$str          = str_replace($umlauts, $replacements, $str);

		// Remove accents from letters (like á, é, etc.)
		$str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);

		// Remove any remaining non-ASCII characters after transliteration
		return preg_replace('/[^A-Za-z0-9\s]/', '', $str);
	}

}