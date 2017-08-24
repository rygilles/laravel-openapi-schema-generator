<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * An object representing a Server Variable for server URL template substitution.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#serverVariableObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class ServerVariable extends Objects
{
	/**
	 * An enumeration of string values to be used if the substitution options are from a limited set.
	 *
	 * @var string[]
	 */
	public $enum;

	/**
	 * The default value to use for substitution, and to send, if an alternate value is not supplied.
	 * Unlike the Schema Object's default, this value MUST be provided by the consumer. (REQUIRED)
	 *
	 * @var string
	 */
	public $default;

	/**
	 * An optional description for the server variable.
	 * CommonMark syntax MAY be used for rich text representation.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * {@inheritdoc}
	 */
	protected $requiredAttributes = [
		'default'
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'enum'          => $this->enum,
			'default'       => $this->default,
			'description'   => $this->description
		];
	}
}