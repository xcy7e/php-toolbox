<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

/**
 * Encrypting & Decrypting utilities.
 *
 * @package Xcy7e\PhpToolbox\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
final class CryptoTool
{

	public const DEFAULT_ALGO = 'aes-256-cbc';

	/**
	 * Encrypts data with the given passphrase and returns the result as base64 encoded string.
	 *
	 * @param string $data
	 * @param string $passphrase should have been previously generated in a cryptographically safe way, like `openssl_random_pseudo_bytes`
	 * @param string $algorithm
	 * @param string $iv [optional] A non-NULL Initialization Vector.
	 * @return string
	 */
	public static function encrypt(string $data, #[\SensitiveParameter] string $passphrase, string $algorithm = CryptoTool::DEFAULT_ALGO, string $iv = ""): string
	{
		return base64_encode(openssl_encrypt($data, $algorithm, $passphrase, OPENSSL_RAW_DATA, $iv));
	}

	/**
	 * Decrypts base64 wrapped encrypted data with the given passphrase.
	 *
	 * @param string $data
	 * @param string $passphrase
	 * @param string $algorithm
	 * @param string $iv [optional] A non-NULL Initialization Vector.
	 * @return string
	 */
	public static function decrypt(string $data, #[\SensitiveParameter] string $passphrase, string $algorithm = CryptoTool::DEFAULT_ALGO, string $iv = ""): string
	{
		return openssl_decrypt(base64_decode($data), $algorithm, $passphrase, OPENSSL_RAW_DATA, $iv);
	}

	/**
	 * Builds a hash out of `$plain` with the specified algorithm.
	 * Falls back to sha256 if the provided algorithm is not available.
	 *
	 * @param string $plain
	 * @param string $algo
	 * @return string
	 */
	public static function hash(string $plain, string $algo = 'sha256'): string
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
	 *
	 * @param int $length
	 * @return string|int
	 */
	public static function randomStr(int $length = 6): string|int
	{
		if ($length <= 0) {
			return '';
		}

		$alphabet = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$n        = strlen($alphabet); // 62
		$result   = '';

		try {
			// Rejection sampling to avoid modulo bias
			$threshold     = intdiv(256, $n) * $n; // Largest multiple of $n less than or equal to 255
			$bytesPerBatch = max(16, $length);

			while (strlen($result) < $length) {
				$bytes = random_bytes($bytesPerBatch);
				$len   = strlen($bytes);
				for ($i = 0; $i < $len && strlen($result) < $length; $i++) {
					$val = ord($bytes[$i]);
					if ($val >= $threshold) {
						continue; // reject to avoid bias
					}
					$idx    = $val % $n;
					$result .= $alphabet[$idx];
				}
			}

			return $result;
		} catch (\Throwable) {
			return time();
		}
	}

}