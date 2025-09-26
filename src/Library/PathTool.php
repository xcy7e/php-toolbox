<?php
declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

/**
 * Directory path creation and analyzation utilities.
 */
class PathTool
{

	/**
	 * Generates a directory path from an array of path segments
	 */
	public static function buildPath(array $pathSegments):string
	{
		$path = '';
		foreach($pathSegments as $segment) {
			$path .= DIRECTORY_SEPARATOR . $segment;
		}

		$regexPattern = DIRECTORY_SEPARATOR === '/' ? '/\/+/' : '/\\\\+/';
		$replaceStr   = DIRECTORY_SEPARATOR === '/' ? DIRECTORY_SEPARATOR : '\\';

		return preg_replace($regexPattern, $replaceStr, $path);
	}

	public static function extractCategoryDirSuffix(string $pathname):string
	{
		preg_match('/[_]{1}[a-zA-Z]{3}/', $pathname, $matches, PREG_UNMATCHED_AS_NULL);
		return $matches[0] ?? '';
	}

	/**
	 * Evaluates if
	 */
	public static function isHashPath(string $path): bool
	{
		return 1 === preg_match('/\/([a-zA-Z0-9]{2})\/([a-zA-Z0-9]{2})\/([a-zA-Z0-9]{2})\//', $path);
	}

	public static function getEncPathGroup(string $dir): array
	{
		$group = [];
		preg_match('/\/([a-zA-Z0-9]{2})\/([a-zA-Z0-9]{2})\/([a-zA-Z0-9]{2})\//', $dir, $group);
		if (array_key_exists(0, $group)) {
			unset($group[0]);
		}
		return $group;
	}

}