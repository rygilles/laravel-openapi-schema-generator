<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * A basic representation of a JSON Schema object
 * @todo Real "JSON Schema Specification Wright Draft 00." integration
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#schemaObject
 * @see http://json-schema.org/latest/json-schema-validation.html
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class JSONSchema extends BaseObject
{
	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var float
	 */
	public $multipleOf;

	/**
	 * @var float
	 */
	public $maximum;

	/**
	 * @var float
	 */
	public $exclusiveMaximum;

	/**
	 * @var float
	 */
	public $minimum;

	/**
	 * @var float
	 */
	public $exclusiveMinimum;

	/**
	 * @var int
	 */
	public $maxLength;

	/**
	 * @var int
	 */
	public $minLength;

	/**
	 * @var string
	 */
	public $pattern;

	/**
	 * @var int
	 */
	public $maxItems;

	/**
	 * @var int
	 */
	public $minItems;

	/**
	 * @var boolean
	 */
	public $uniqueItems;

	/**
	 * @var int
	 */
	public $maxProperties;

	/**
	 * @var int
	 */
	public $minProperties;

	/**
	 * @var string[]
	 */
	public $required;

	/**
	 * @var string[]
	 */
	public $enum;
	
	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'title'             => $this->title,
			'multipleOf'        => $this->multipleOf,
			'maximum'           => $this->maximum,
			'exclusiveMaximum'  => $this->exclusiveMaximum,
			'minimum'           => $this->minimum,
			'exclusiveMinimum'  => $this->exclusiveMinimum,
			'maxLength'         => $this->maxLength,
			'pattern'           => $this->pattern ,
			'maxItems'          => $this->maxItems,
			'minItems'          => $this->minItems,
			'uniqueItems'       => $this->uniqueItems,
			'maxProperties'     => $this->maxProperties,
			'minProperties'     => $this->minProperties,
			'required'          => $this->required,
			'enum'              => $this->enum
		];
	}
}