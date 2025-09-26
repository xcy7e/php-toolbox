<?php

declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

use Symfony\Component\Uid\Uuid;

/**
 * Hashing and random string generation utilities.
 */
class HashTool
{

	/**
	 * Builds a hash out of `$plain` with the specified algorithm.
	 * Falls back to sha256 if the provided algorithm is not available.
	 */
	public static function getHash(string $plain, string $algo = 'sha256'): string
	{
		$algo = strtolower($algo);
		static $available = null;
		$available ??= array_map('strtolower', hash_algos());

		if (!in_array($algo, $available, true)) {
			$algo = 'sha256';
		}

		return hash($algo, $plain);
	}

	/**
	 * Builds a random string of length `$length` using a cryptographically secure PRNG.
	 * Returns a timestamp on error.
	 */
	public static function randomStr(int $length = 6): string|int
	{
		if ($length <= 0) {
			return '';
		}

		$alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$n = strlen($alphabet); // 62
		$result = '';

		try {
			// Rejection sampling to avoid modulo bias
			$threshold = intdiv(256, $n) * $n; // Largest multiple of $n less than or equal to 255
			$bytesPerBatch = max(16, $length);

			while (strlen($result) < $length) {
				$bytes = random_bytes($bytesPerBatch);
				$len = strlen($bytes);
				for ($i = 0; $i < $len && strlen($result) < $length; $i++) {
					$val = ord($bytes[$i]);
					if ($val >= $threshold) {
						continue; // reject to avoid bias
					}
					$idx = $val % $n;
					$result .= $alphabet[$idx];
				}
			}

			return $result;
		} catch (\Throwable) {
			return time();
		}
	}

	/**
	 * Generates a 3-level hash path based on a given or random UUID, e.g. "c0/ff/ee/"
	 */
	public static function generateHashPath(null|string|Uuid $uuid = null): string
	{
		// Normalize input to a 32-char hex string without dashes
		if ($uuid instanceof Uuid) {
			$normalized = str_replace('-', '', $uuid->toRfc4122());
		} elseif (is_string($uuid) && $uuid !== '') {
			$normalized = str_replace('-', '', $uuid);
		} else {
			$normalized = str_replace('-', '', Uuid::v1()->toRfc4122());
		}

		return implode(DIRECTORY_SEPARATOR, [
				substr($normalized, 0, 2),
				substr($normalized, 2, 2),
				substr($normalized, 4, 2),
			]) . DIRECTORY_SEPARATOR;
	}

}