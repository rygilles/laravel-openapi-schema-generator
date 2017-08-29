<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * When request bodies or response payloads may be one of a number of different schemas,
 * a discriminator object can be used to aid in serialization, deserialization, and validation.
 * The discriminator is a specific object in a schema
 * which is used to inform the consumer of the specification
 * of an alternative schema based on the value associated with it.
 *
 * When using the discriminator, inline schemas will not be considered.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#discriminatorObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class Discriminator extends Object
{
	/**
	 * The name of the property in the payload that will hold the discriminator value. (REQUIRED)
	 *
	 * @var string
	 */
	public $propertyName;

	/**
	 * An object to hold mappings between payload values and schema names or references.
	 *
	 * @var string[]
	 */
	public $mapping;

	/**
	 * {@inheritdoc}
	 */
	protected $requiredAttributes = [
		'propertyName'
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'propertyName'      => $this->propertyName,
			'mapping'           => $this->mapping
		];
	}
}