<?php

declare(strict_types=1);

namespace Xcy7e\PhpToolbox\Library;

/**
 * Class and object reflection utilities.
 *
 * @package Xcy7e\PhpToolbox\Library
 * @author  Jonathan Riedmair <jonathan@xcy7e.pro>
 */
final class ReflectionTool
{

	/**
	 * Returns the class name of the given class or object (without namespace).
	 *
	 * @param string|object $class
	 * @return string|null
	 */
	public static function getClassName(string|object $class): string|null
	{
		try {
			$path = explode('\\', get_class($class));
			return array_pop($path);
		} catch (\Throwable) {
			return null;
		}
	}

	/**
	 * Returns all methods of the given class that start with `$setOrGet`.
	 *
	 * @param object|string $entity
	 * @param string        $setOrGet
	 * @return array
	 */
	public static function getMethods(object|string $entity, string $setOrGet = 'get'): array
	{
		return array_filter(get_class_methods($entity), static function ($method) use ($setOrGet) {
			return str_starts_with($method, $setOrGet);
		});
	}

	/**
	 * Converts a camelCase method name to snake_case,
	 * e.g. `getExampleProperty` => `example_property`, `setFirstname` => `firstname`
	 *
	 * @param array|string $method
	 * @param string       $setOrGet
	 * @return string
	 */
	public static function getSnakeCase(array|string $method, string $setOrGet = 'get'): string
	{
		$method = strtolower(ltrim(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $method), '_'));
		return str_starts_with($method, $setOrGet . '_') ? substr($method, 4) : $method;
	}

}