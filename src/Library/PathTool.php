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

	// region constants
	public const HASH_PATH_DEFAULT_DEPTH = 3;
	// endregion

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
	 * Generates an `n`-level hash path based on a given or random UUID, e.g. `c0/ff/ee/`
	 *
	 * @param string|Uuid|null $uuid
	 * @param int              $depth e.g. `5` = `c0/ff/ee/ba/be`
	 * @return string
	 */
	public static function buildHashPath(null|string|Uuid $uuid = null, int $depth = self::HASH_PATH_DEFAULT_DEPTH): string
	{
		$depth = max(1, min(16, $depth));    // depth = 1-16

		// Normalize input to a 32-char hex string without dashes
		if ($uuid instanceof Uuid) {
			$normalized = str_replace('-', '', $uuid->toRfc4122());
		} elseif (is_string($uuid) && $uuid !== '') {
			$normalized = str_replace('-', '', $uuid);
		} else {
			$normalized = str_replace('-', '', Uuid::v1()->toRfc4122());
		}

		$segments = [];
		for ($i = 0; $i < $depth; $i++) {
			$offset     = 0 + ($i * 2);
			$segments[] = substr($normalized, $offset, 2);
		}

		return implode(DIRECTORY_SEPARATOR, $segments) . DIRECTORY_SEPARATOR;
	}

	/**
	 * Evaluates if `$path` is a valid `n`-level hash path (with or without a leading/trailing directory separator)
	 *
	 * @param string $path
	 * @param int    $depth e.g. `3` = `/c0/ff/ee/`
	 * @return bool
	 */
	public static function isHashPath(string $path, int $depth = self::HASH_PATH_DEFAULT_DEPTH): bool
	{
		return 1 === preg_match(PathTool::buildHashPathRegex($depth), $path);
	}

	/**
	 * Extracts an `n`-level hash path segment from a given directory path
	 *
	 * @param string $dir
	 * @param int    $depth e.g. `3` = `/c0/ff/ee/`
	 * @return array
	 */
	public static function getHashPathSegment(string $dir, int $depth = self::HASH_PATH_DEFAULT_DEPTH): array
	{
		$group = [];
		preg_match(PathTool::buildHashPathRegex($depth, '/[\/|\\\]{0,1}([a-fA-F0-9]{2})%s[\/|\\\]{0,1}$/', '\/([a-fA-F0-9]{2})'), $dir, $group);

		if (array_key_exists(0, $group)) {
			unset($group[0]);
		}
		return $group;
	}

	/**
	 * Creates a regex pattern for a hash-path of a given depth
	 *
	 * Defaults to a regex pattern for this path structure: `/c0/ff/ee/` (leading and trailing directory separator)
	 *
	 * @param int    $depth      e.g. `3` = `/c0/ff/ee/`
	 * @param string $regex      regex pattern for the whole path with 1 segment and a format specifier for additional
	 *                           level segments
	 * @param string $lvlSegment regex pattern for a single level segment
	 * @return string
	 */
	private static function buildHashPathRegex(int $depth = 3, string $regex = '/(?:[\/|\\\]{0,1}[a-fA-F0-9]{2}%s[\/|\\\]{0,1})$/', string $lvlSegment = '[\/\\\][a-fA-F0-9]{2}'): string
	{
		for ($i = 1; $i < $depth; $i++) {
			$regex = sprintf($regex, $lvlSegment . '%s');    // depth++
		}
		return str_replace('%s', '', $regex);
	}

}