<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

use Exception;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;

/**
 * Security utilities.
 *
 * @package Xcy7e\PhpToolbox\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
final class SecurityTool
{

	/**
	 * Generates a random password using only cryptographically secure randomness (CSPRNG).
	 *
	 * - All random decisions (group sizes, character selection, and shuffling) use `random_int()`.
	 * - With a length of 24 characters, entropy is roughly > 105 bits depending on the exact draws.
	 *
	 * @param int $length Minimum length is 4
	 * @return string The generated password.
	 * @throws Exception
	 */
	public static function generateRandomPassword(int $length = 24): string
	{
		$length = max($length, 4); // at least 4 chars

		$mapNumbers      = '23456789';
		$mapLettersBig   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
		$mapLettersSmall = 'abcdefghjkmnpqrstuvwxyz';

		$out = [];

		// 1–2 digits
		$digitsCount = random_int(1, 2);
		for ($i = 0; $i < $digitsCount; $i++) {
			$out[] = $mapNumbers[random_int(0, strlen($mapNumbers) - 1)];
		}

		// 1–2 uppercase letters
		$upperCount = random_int(1, 2);
		for ($i = 0; $i < $upperCount; $i++) {
			$out[] = $mapLettersBig[random_int(0, strlen($mapLettersBig) - 1)];
		}

		// Rest lowercase letters
		$lowerCount = $length - $digitsCount - $upperCount;
		for ($i = 0; $i < $lowerCount; $i++) {
			$out[] = $mapLettersSmall[random_int(0, strlen($mapLettersSmall) - 1)];
		}

		// Secure Fisher–Yates shuffle using random_int
		for ($i = count($out) - 1; $i > 0; $i--) {
			$j = random_int(0, $i);
			if ($i !== $j) {
				[$out[$i], $out[$j]] = [$out[$j], $out[$i]];
			}
		}

		return implode('', $out);
	}

	/**
	 * Recursively sanitizes arrays by masking sensitive values and truncating long strings.
	 *
	 *  - Keys matching any of `$stripKeywords` (case-insensitive) are replaced with `$stripText`.
	 *  - Strings longer than `$maxStringBytes` (in raw bytes) are truncated and suffixed with`$stripText`.
	 *
	 * @param array  $data
	 * @param string $stripText
	 * @param array  $stripKeywords
	 * @param int    $maxStringBytes
	 * @return array
	 */
	public static function sanitizeDataRecursive(#[\SensitiveParameter] array $data, string $stripText = '##STRIPPED##', array $stripKeywords = ['password', 'Password', 'fileHolderBase64', '_token'], int $maxStringBytes = 255): array
	{
		if ($data === []) {
			return $data;
		}

		// Normalize keywords for case-insensitive comparison
		$needle = array_map(static fn($v) => strtolower((string)$v), $stripKeywords);

		foreach ($data as $k => $v) {
			$keyLower = strtolower((string)$k);

			if (in_array($keyLower, $needle, true)) {
				$data[$k] = $stripText;
				continue;
			}

			if (is_string($v)) {
				// Byte-length boundary to be conservative about PII spills
				if ($maxStringBytes > 0 && mb_strlen($v, '8bit') > $maxStringBytes) {
					$data[$k] = substr($v, 0, $maxStringBytes) . $stripText;
					continue;
				}
			}

			if (is_array($v)) {
				// recursion \o/
				$data[$k] = self::sanitizeDataRecursive($v, $stripText, $stripKeywords, $maxStringBytes);
			}
		}

		return $data;
	}

	/**
	 * Evaluates if the current `$request` IP is whitelisted in `$ipWhitelist`
	 */
	public static function isIpWhitelisted(Request $request, string $ipWhitelist): bool
	{
		$clientIp = $request->getClientIp();
		if (!is_string($clientIp) || $clientIp === '') {
			return false;
		}

		$ranges = array_values(array_filter(array_map('trim', explode(',', $ipWhitelist)), static fn($v) => $v !== ''));
		if ($ranges === []) {
			return false;
		}

		return IpUtils::checkIp($clientIp, $ranges);
	}

}