<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

use finfo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mime\MimeTypes;

/**
 * Base64 utilities.
 *
 * @package Xcy7e\PhpToolbox\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
final class Base64Tool
{

	/**
	 * Encode file content to base64
	 *
	 * @param string $path
	 * @return string
	 */
	public static function base64EncodeFile(string $path): string
	{
		$file = (new File($path));
		$mime = (new MimeTypes())->getMimeTypes($file->guessExtension());
		$mime = $mime[0] ?? 'text/plain';  // fallback mime;

		return sprintf('data:%s;base64,%s', $mime, base64_encode($file->getContent()));
	}

	/**
	 * Returns the data-only part of a base64 encoded string
	 *
	 * @param string $base64
	 * @return string|false
	 */
	public static function getBase64Data(string $base64): string|false
	{
		$base64 = trim($base64);
		$pos    = strpos($base64, ',');

		return (($pos !== false)) ? substr($base64, $pos + 1) : false;
	}

	/**
	 * Guesses the file ending based on the embedded mimeType information in a base64-encoded string
	 *
	 * @param string $base64
	 * @return string|null
	 */
	public static function getFileEndingFromBase64(string $base64): string|null
	{
		$base64 = trim($base64);
		$mime   = null;

		// Try to extract MIME from data URI prefix: data:mime[;...],
		if (str_starts_with($base64, 'data:')) {
			if (preg_match('#^data:([^;,]+)#i', $base64, $m)) {
				$mime = strtolower($m[1]);
			}
		}

		// If no MIME found, attempt detection from decoded bytes
		if ($mime === null) {
			$payload = self::getBase64Data($base64);
			$binary  = base64_decode($payload, true);
			if ($binary !== false && $binary !== '') {
				if (class_exists(finfo::class)) {
					$finfo    = new finfo(FILEINFO_MIME_TYPE);
					$detected = $finfo->buffer($binary);
					if (is_string($detected) && $detected !== '') {
						$mime = strtolower($detected);
					}
				}
			}
		}

		if ($mime) {
			$types = new MimeTypes();
			// Prefer Symfony mapping, return the first known extension
			$exts = method_exists($types, 'getExtensions') ? $types->getExtensions($mime) : $types->getMimeTypes($mime);
			return ((string)$exts[0] ?? null);
		}

		return null;
	}

	/**
	 * Evaluates if `$base64` is a valid base64-encoded string
	 *
	 * @param string $base64
	 * @return bool
	 */
	public static function isValidBase64(string $base64): bool
	{
		$str = Base64Tool::stripBase64DataUriSchema($base64);

		// Compare encoded decoded data-part with input data part
		$decoded = base64_decode($str, true);
		return $decoded !== false && base64_encode($decoded) === $str;
	}

	/**
	 * Removes the Data-Uri-Schema from a base64 string
	 *
	 * @param string $base64
	 * @return string
	 */
	public static function stripBase64DataUriSchema(string $base64): string
	{
		return preg_replace('/^data:.*;base64,/', '', $base64);
	}


}