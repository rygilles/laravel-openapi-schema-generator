<?php

namespace Rygilles\OpenApiGenerator\OpenApi\Exceptions;

use RuntimeException;

class JsonEncodingException extends RuntimeException
{
	/**
	 * Create a new JSON encoding exception for the object.
	 *
	 * @param mixed $object
	 * @param string $message
	 * @return static
	 */
	public static function forObject($object, $message)
	{
		return new static('Error encoding object ['.get_class($object).'] to JSON: '.$message);
	}

	/**
	 * Create a new JSON encoding exception for an attribute.
	 *
	 * @param mixed $object
	 * @param mixed $key
	 * @param string $message
	 * @return static
	 */
	public static function forAttribute($object, $key, $message)
	{
		$class = get_class($object);

		return new static("Unable to encode attribute [{$key}] for object [{$class}] to JSON: {$message}.");
	}
}
