<?php

namespace Rygilles\OpenApiGenerator\OpenApi;

use ErrorException;
use JsonSerializable;
use Rygilles\OpenApiGenerator\OpenApi\Contracts\Arrayable;
use Rygilles\OpenApiGenerator\OpenApi\Contracts\Jsonable;
use Rygilles\OpenApiGenerator\OpenApi\Exceptions\JsonEncodingException;


/**
 * Describes a OpenAPI object
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
abstract class Object implements Arrayable, Jsonable, JsonSerializable
{
	/**
	 * Array of required attributes keys of the object
	 *
	 * @var string[]
	 */
	protected $requiredAttributes = [];

	/**
	 * This object MAY be extended with Specification Extensions.
	 * 
	 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#specificationExtensions
	 * @var mixed[]
	 */
	protected $specificationExtensions = [];

	/**
	 * Object constructor.
	 *
	 * @param mixed[] $attributes Object attributes map
	 */
	public function __construct($attributes = [])
	{
		foreach ($attributes as $k => $v) {
			if (!is_null($v)) {
				$this->$k = $v;
			}
		}
	}

	/**
	 * Return an array of attributes
	 *
	 * @return mixed[]
	 */
	abstract protected function getFixedAttributes();

	/**
	 * Return an array of required attributes keys
	 *
	 * @return string[]
	 */
	public function getRequiredAttributes()
	{
		return $this->requiredAttributes;
	}

	/**
	 * Convert the object instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return array_merge($this->getFixedAttributes(), $this->specificationExtensions);
	}

	/**
	 * Convert the object instance to JSON.
	 *
	 * @param int $options
	 * @return string
	 *
	 * @throws JsonEncodingException
	 */
	public function toJson($options = 0)
	{
		$json = json_encode($this->jsonSerialize(), $options);

		if (JSON_ERROR_NONE !== json_last_error()) {
			throw JsonEncodingException::forObject($this, json_last_error_msg());
		}

		return $json;
	}

	/**
	 * Convert the object into something JSON serializable.
	 *
	 * @return array
	 */
	public function jsonSerialize()
	{
		// Remove attributes not defined and not required (null)
		$fixedAttributes = $this->getFixedAttributes();
		foreach ($fixedAttributes as $k => $v) {
			if (!in_array($k, $this->requiredAttributes) && is_null($v)) {
				unset($fixedAttributes[$k]);
			}
		}

		// Add "x-" before each key of specification extensions
		$prefixedSpecificationExtensions = [];
		foreach ($this->specificationExtensions as $k => $v) {
			$prefixedSpecificationExtensions['x-' . $k] = $v;
		}

		return array_merge($fixedAttributes, $prefixedSpecificationExtensions);
	}

	/**
	 * Convert the object to its string representation.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->toJson(JSON_PRETTY_PRINT);
	}

	/**
	 * Dynamically retrieve attributes on the object.
	 *
	 * @param string $key
	 * @return mixed
	 * @throws ErrorException
	 */
	public function __get($key)
	{
		if (isset($this->specificationExtensions[$key])) {
			return $this->specificationExtensions[$key];
		}

		throw new ErrorException('Undefined property: ' . static::class . '::$' . $key);
	}

	/**
	 * Dynamically set attributes on the object.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 */
	public function __set($key, $value)
	{
		if (isset($this->$key)) {
			$this->$key = $value;
		} else {
			$this->specificationExtensions[$key] = $value;
		}
	}

	/**
	 * Determine if an attribute exists on the object.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function __isset($key)
	{
		if (isset($this->$key) && (!is_null($this->$key))) {
			return true;
		}
		if (!isset($this->specificationExtensions[$key])) {
			return false;
		}
		return !is_null($this->specificationExtensions[$key]);
	}

	/**
	 * Unset an attribute on the object.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function __unset($key)
	{
		if (isset($this->$key)) {
			$this->$key = null;
		}
		unset($this->specificationExtensions[$key]);
	}
}