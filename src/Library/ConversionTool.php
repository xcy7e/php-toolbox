<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

/**
 * Conversion utilities.
 *
 * @package Xcy7e\PhpToolbox\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
final class ConversionTool
{

	/**
	 * Converts shorthand-byte-notations into bytes, e.g. `8M` -> ~`8e+6`, `12K` -> ~`12000`
	 */
	public static function parseByteShorthand(string $byteNotation): float|int|string
	{
		if (is_numeric($byteNotation))
			return $byteNotation;

		$byteNotation = strtolower(trim($byteNotation));
		$bytes        = rtrim($byteNotation, 'gmkb');
		$notation     = str_replace($bytes, '', $byteNotation);

		switch ($notation) {
			case 'g':
			case 'gb':
				$bytes *= 1024;
			case 'm':
			case 'mb':
				$bytes *= 1024;
			case 'k':
			case 'kb':
				$bytes *= 1024;
		}

		return $bytes;
	}

	/**
	 * Remove apostrophes and normalize language-specific letters to their base ASCII counterparts.
	 *
	 * Examples:
	 *  - "côte d’ivoire" => "cote divoire" (apostrophe removed by concatenation, diacritics stripped)
	 *  - "âäáàã" => "aaaaa"
	 *  - "ß" => "ss", "Æ" => "AE"
	 *
	 * @param string $text              Input text
	 * @param bool   $removeApostrophes Whether to remove apostrophes (', ’, ‘, `, ´, ʼ). Default true.
	 * @return string                    Normalized text
	 */
	public static function stripAccents(string $text, bool $removeApostrophes = true): string
	{
		// Normalize to NFD (decomposed) if Normalizer is available, so accents become combining marks
		if (function_exists('normalizer_normalize')) {
			$text = normalizer_normalize($text, \Normalizer::FORM_D);
		} elseif (class_exists('Normalizer')) { // polyfill may expose the class
			/** @noinspection PhpFullyQualifiedNameUsageInspection */
			$text = \Normalizer::normalize($text, \Normalizer::FORM_D);
		}

		// Remove combining diacritical marks
		$text = preg_replace('/\p{Mn}+/u', '', $text ?? '');

		// Map special letters that don't decompose into ASCII nicely
		$map  = [
			'ß' => 'ss', 'ẞ' => 'SS',
			'Æ' => 'AE', 'æ' => 'ae',
			'Ø' => 'O', 'ø' => 'o',
			'Ð' => 'D', 'ð' => 'd',
			'Þ' => 'TH', 'þ' => 'th',
			'Œ' => 'OE', 'œ' => 'oe',
			'Ł' => 'L', 'ł' => 'l',
			'Đ' => 'D', 'đ' => 'd',
			'Ħ' => 'H', 'ħ' => 'h',
			'Ŋ' => 'N', 'ŋ' => 'n',
		];
		$text = strtr($text, $map);

		if ($removeApostrophes) {
			// Remove various apostrophe-like characters by concatenating surrounding letters
			$text = str_replace([
				"'", "’", "‘", "`", "´", "ʼ"
			], '', $text); // remove apostrophes entirely
		}

		return $text;
	}

}