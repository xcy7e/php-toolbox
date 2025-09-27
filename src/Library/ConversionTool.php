<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;


use Normalizer;

/**
 * Conversion utilities.
 *
 * @package Xcy7e\PhpToolbox\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
final class ConversionTool
{

	/** @var array<int,string> */
	private const BYTE_UNITS = ['B', 'K', 'M', 'G', 'T'];

	/** @var string[] */
	private const APOSTROPHES = ["'", "’", "‘", "`", "´", "ʼ"];

	/** @var array<string,string> */
	private const TRANSLITERATION_MAP = [
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

	/**
	 * Parse shorthand byte notation like "8M" or "12K" into integer bytes.
	 *
	 * @param string $notation
	 * @return int
	 */
	public static function parseShorthandToBytes(string $notation): int
	{
		$notation = trim($notation);
		if ($notation === '') {
			return 0;
		}
		if (is_numeric($notation)) {
			return (int)$notation;
		}

		$normalized = strtolower($notation);
		$valuePart  = rtrim($normalized, "tgmkb");
		$unitPart   = substr($normalized, strlen($valuePart));
		if ($valuePart === '' || !is_numeric($valuePart)) {
			return 0;
		}

		$multiplier = match ($unitPart) {
			't', 'tb' => 1024 ** 4,
			'g', 'gb' => 1024 ** 3,
			'm', 'mb' => 1024 ** 2,
			'k', 'kb' => 1024,
			'', 'b' => 1,
			default => 1,
		};

		return (int)@round(((float)$valuePart) * $multiplier, 0, PHP_ROUND_HALF_UP);
	}

	/**
	 * Convert integer bytes into human-readable shorthand like "12K".
	 *
	 * @param int           $bytes
	 * @param string[]|null $units Optional override of units.
	 * @return string
	 */
	public static function formatBytesShorthand(int $bytes, ?array $units = null): string
	{
		$units = $units ?? self::BYTE_UNITS;

		if ($bytes <= 0) {
			return '0' . ($units[0] ?? 'B');
		}

		$exponent = (int)floor(log((float)$bytes, 1024));
		$exponent = max(0, min($exponent, count($units) - 1));

		$power = $bytes / (1024 ** $exponent);
		$value = round($power, 2, PHP_ROUND_HALF_UP);

		return rtrim(rtrim((string)$value, '0'), '.') . ($units[$exponent] ?? 'B');
	}

	/**
	 * Remove apostrophes and normalize language-specific letters to ASCII-like counterparts.
	 *
	 * @param string $text
	 * @param bool   $removeApostrophes
	 * @return string
	 */
	public static function stripAccents(string $text, bool $removeApostrophes = true): string
	{
		$text = self::normalizeToDecomposed($text);
		$text = self::removeCombiningMarks($text);
		$text = strtr($text, self::TRANSLITERATION_MAP);

		if ($removeApostrophes) {
			$text = str_replace(self::APOSTROPHES, '', $text);
		}

		return $text;
	}

	/**
	 * Normalize text to decomposed form (NFD) when possible.
	 * @param string $text
	 * @return string
	 */
	private static function normalizeToDecomposed(string $text): string
	{
		if (function_exists('normalizer_normalize')) {
			return (string)normalizer_normalize($text, Normalizer::FORM_D);
		}
		if (class_exists(Normalizer::class)) {
			return Normalizer::normalize($text, Normalizer::FORM_D);
		}
		return $text;
	}

	/**
	 * Remove Unicode combining diacritical marks.
	 * @param string $text
	 * @return string
	 */
	private static function removeCombiningMarks(string $text): string
	{
		$clean = preg_replace('/\p{Mn}+/u', '', $text);
		return $clean ?? $text;
	}

}