<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

use Exception;
use Symfony\Component\HttpFoundation\IpUtils;

/**
 * Security utilities.
 *
 * @package Xcy7e\PhpToolbox\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
final class SecurityTool
{

	// region constants
	public const RANDOM_PASSWORD_MIN_LENGTH = 4;
	// endregion

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
		$length = max($length, self::RANDOM_PASSWORD_MIN_LENGTH);

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
	 *  - Keys matching any of `$needles` (case-insensitive) are replaced with `$replacement`.
	 *  - Strings longer than `$maxStringBytes` (in raw bytes) are truncated and suffixed with`$replacement`.
	 *
	 * @param array  $data
	 * @param string $replacement
	 * @param array  $needles
	 * @param int    $maxStringBytes
	 * @return array
	 */
	public static function sanitizeDataRecursive(#[\SensitiveParameter] array $data, string $replacement = '##STRIPPED##', array $needles = ['password', 'Password', 'fileHolderBase64', '_token'], int $maxStringBytes = 255): array
	{
		if ($data === []) {
			return $data;
		}

		// Normalize keywords for case-insensitive comparison
		$needle = array_map(static fn($v) => strtolower((string)$v), $needles);

		foreach ($data as $k => $v) {
			$keyLower = strtolower((string)$k);

			if (in_array($keyLower, $needle, true)) {
				$data[$k] = $replacement;
				continue;
			}

			if (is_string($v)) {
				// Byte-length boundary to be conservative about PII spills
				if ($maxStringBytes > 0 && mb_strlen($v, '8bit') > $maxStringBytes) {
					$data[$k] = substr($v, 0, $maxStringBytes) . $replacement;
					continue;
				}
			}

			if (is_array($v)) {
				// recursion \o/
				$data[$k] = self::sanitizeDataRecursive($v, $replacement, $needles, $maxStringBytes);
			}
		}

		return $data;
	}

	/**
	 * Evaluates the current runtime request client ip address if possible
	 *
	 * @return string|null
	 */
	public static function getClientIp(): ?string
	{
		return $_SERVER['HTTP_CLIENT_IP']
			?? $_SERVER['HTTP_X_FORWARDED_FOR']
			?? $_SERVER['REMOTE_ADDR']
			?? null;
	}

	/**
	 * Evaluates if the current `$request` IP is whitelisted in `$ipWhitelist`
	 *
	 * @param string|null  $ipAddress [optional] NULL = current request clientIp
	 * @param array|string $ipWhitelist
	 * @return bool
	 */
	public static function isIpWhitelisted(array|string $ipWhitelist, ?string $ipAddress = null): bool
	{
		$clientIp = $ipAddress ?? SecurityTool::getClientIp();
		if (!is_string($clientIp) || in_array($clientIp, ['', '0.0.0.0'])) {
			return false;
		}

		$ranges = array_values(
			array_filter(
				array_map(
					'trim',
					is_array($ipWhitelist) ? $ipWhitelist : explode(',', $ipWhitelist)
				),
				static fn($v) => $v !== ''
			)
		);

		return $ranges !== [] && IpUtils::checkIp($clientIp, $ranges);
	}

}