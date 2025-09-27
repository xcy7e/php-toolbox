<?php declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

use Symfony\Component\Uid\Uuid;

/**
 * Directory path creation and analyzation utilities.
 *
 * @package Xcy7e\PhpToolbox\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
final class PathTool
{

	/**
	 * Generates a directory path from an array of path segments
	 *
	 * @param array $pathSegments
	 * @return string
	 */
	public static function buildPath(array $pathSegments): string
	{
		$path = '';
		foreach ($pathSegments as $segment) {
			$path .= DIRECTORY_SEPARATOR . $segment;
		}

		$regexPattern = DIRECTORY_SEPARATOR === '/' ? '/\/+/' : '/\\\\+/';
		$replaceStr   = DIRECTORY_SEPARATOR === '/' ? DIRECTORY_SEPARATOR : '\\';

		return preg_replace($regexPattern, $replaceStr, $path);
	}

	/**
	 * Generates a 3-level hash path based on a given or random UUID, e.g. "c0/ff/ee/"
	 *
	 * @param string|Uuid|null $uuid
	 * @return string
	 */
	public static function buildHashPath(null|string|Uuid $uuid = null): string
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

	/**
	 * Evaluates if `$path` is a valid 3-level hash path (with or without leading/trailing DS)
	 *
	 * @param string $path
	 * @return bool
	 */
	public static function isHashPath(string $path): bool
	{
		return 1 === preg_match('/(?:[\/|\\\]{0,1}[a-fA-F0-9]{2}[\/\\\][a-fA-F0-9]{2}[\/\\\][a-fA-F0-9]{2}[\/|\\\]{0,1})/', $path);
	}

	/**
	 * Extracts a 3-level hash path segment from a given directory path
	 *
	 * @param string $dir
	 * @return array
	 */
	public static function getHashPathSegment(string $dir): array
	{
		$group = [];
		preg_match('/\/([a-zA-Z0-9]{2})\/([a-zA-Z0-9]{2})\/([a-zA-Z0-9]{2})\//', $dir, $group);
		if (array_key_exists(0, $group)) {
			unset($group[0]);
		}
		return $group;
	}

}