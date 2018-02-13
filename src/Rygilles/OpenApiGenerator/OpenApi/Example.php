<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * Example Object
 * This object MAY be extended with Specification Extensions.
 * In all cases, the example value is expected to be compatible with the type schema of its associated value.
 * Tooling implementations MAY choose to validate compatibility automatically,
 * and reject the example value(s) if incompatible.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#exampleObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class Example extends BaseObject
{
	/**
	 * Short description for the example.
	 *
	 * @var string
	 */
	public $summary;

	/**
	 * Long description for the example.
	 * CommonMark syntax MAY be used for rich text representation.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Embedded literal example.
	 * The value field and externalValue field are mutually exclusive.
	 * To represent examples of media types that cannot naturally represented in JSON or YAML,
	 * use a string value to contain the example, escaping where necessary.
	 *
	 * @var mixed
	 */
	public $value;

	/**
	 * A URL that points to the literal example.
	 * This provides the capability to reference examples that cannot easily be included in JSON or YAML documents.
	 * The value field and externalValue field are mutually exclusive.
	 *
	 * @var string
	 */
	public $externalValue;

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'summary'           => $this->summary,
			'description'       => $this->description,
			'value'             => $this->value,
			'externalValue'     => $this->externalValue,
		];
	}
}