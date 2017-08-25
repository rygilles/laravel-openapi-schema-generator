<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * The Schema Object allows the definition of input and output data types.
 * These types can be objects, but also primitives and arrays.
 * This object is an extended subset of the JSON Schema Specification Wright Draft 00.
 *
 * For more information about the properties, see JSON Schema Core and JSON Schema Validation.
 * Unless stated otherwise, the property definitions follow the JSON Schema.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#schemaObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class Schema extends JSONSchema
{
	/**
	 * Allows sending a null value for the defined schema. Default value is false.
	 *
	 * @var boolean
	 */
	public $nullable;

	/**
	 * Adds support for polymorphism.
	 * The discriminator is an object name that is used to differentiate between other schemas
	 * which may satisfy the payload description.
	 * See Composition and Inheritance for more details.
	 *
	 * @var Discriminator
	 */
	public $discriminator;

	/**
	 * Relevant only for Schema "properties" definitions. Declares the property as "read only".
	 * This means that it MAY be sent as part of a response but SHOULD NOT be sent as part of the request.
	 * If the property is marked as readOnly being true and is in the required list,
	 * the required will take effect on the response only.
	 * A property MUST NOT be marked as both readOnly and writeOnly being true.
	 * Default value is false.
	 *
	 * @var boolean
	 */
	public $readOnly;

	/**
	 * Relevant only for Schema "properties" definitions. Declares the property as "write only".
	 * Therefore, it MAY be sent as part of a request but SHOULD NOT be sent as part of the response.
	 * If the property is marked as writeOnly being true and is in the required list,
	 * the required will take effect on the request only.
	 * A property MUST NOT be marked as both readOnly and writeOnly being true.
	 * Default value is false.
	 *
	 * @var boolean
	 */
	public $writeOnly ;

	/**
	 * This MAY be used only on properties schemas. It has no effect on root schemas.
	 * Adds additional metadata to describe the XML representation of this property.
	 *
	 * @var XML
	 */
	public $xml;

	/**
	 * Additional external documentation for this schema.
	 *
	 * @var ExternalDocumentation
	 */
	public $externalDocs;

	/**
	 * A free-form property to include an example of an instance for this schema.
	 * To represent examples that cannot be naturally represented in JSON or YAML,
	 * a string value can be used to contain the example with escaping where necessary.
	 *
	 * @var mixed
	 */
	public $example;

	/**
	 * Specifies that a schema is deprecated and SHOULD be transitioned out of usage. Default value is false.
	 *
	 * @var bool
	 */
	public $deprecated;

	/**
	 * The following properties are taken from the JSON Schema definition but their definitions were adjusted to the OpenAPI Specification.
	 */

	/**
	 * Value MUST be a string. Multiple types via an array are not supported.
	 *
	 * @var string
	 */
	public $type;

	/**
	 * Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
	 *
	 * @var Schema|Reference
	 */
	public $allOf;

	/**
	 * Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
	 *
	 * @var Schema|Reference
	 */
	public $oneOf;

	/**
	 * Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
	 *
	 * @var Schema|Reference
	 */
	public $anyOf;

	/**
	 * Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
	 *
	 * @var Schema|Reference
	 */
	public $not;

	/**
	 * Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
	 * items MUST be present if the type is array.
	 *
	 * This associative array will be converted on an object (Value MUST be an object and not an array.)
	 *
	 * @var (Schema|Reference)[]
	 */
	public $items;

	/**
	 * Property definitions MUST be a Schema Object and not a standard JSON Schema (inline or referenced).
	 *
	 * @var (Schema|Reference)[]
	 */
	public $properties;

	/**
	 * Value can be boolean or object. Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
	 *
	 * @var boolean|Schema|Reference
	 */
	public $additionalProperties;

	/**
	 * CommonMark syntax MAY be used for rich text representation
	 *
	 * @var string
	 */
	public $description;

	/**
	 * See Data Type Formats for further details.
	 * While relying on JSON Schema's defined formats,
	 * the OAS offers a few additional predefined formats.
	 *
	 * @var string
	 */
	public $format;

	/**
	 * The default value represents what would be assumed by the consumer of the input as the value of the schema if one is not provided.
	 * Unlike JSON Schema, the value MUST conform to the defined type for the Schema Object defined at the same level.
	 * For example, if type is string, then default can be "foo" but cannot be 1.
	 *
	 * @var mixed
	 */
	public $default;

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			/* JSON Schema fixed attributes (heritage) */
			'title'                 => $this->title,
			'multipleOf'            => $this->multipleOf,
			'maximum'               => $this->maximum,
			'exclusiveMaximum'      => $this->exclusiveMaximum,
			'minimum'               => $this->minimum,
			'exclusiveMinimum'      => $this->exclusiveMinimum,
			'maxLength'             => $this->maxLength,
			'minLength'             => $this->minLength,
			'pattern'               => $this->pattern ,
			'maxItems'              => $this->maxItems,
			'minItems'              => $this->minItems,
			'uniqueItems'           => $this->uniqueItems,
			'maxProperties'         => $this->maxProperties,
			'minProperties'         => $this->minProperties,
			'required'              => $this->required,
			'enum'                  => $this->enum,
			/* OpenAPI Schema fixed attributes */
			'type'                  => $this->type,
			'allOf'                 => $this->allOf,
			'oneOf'                 => $this->oneOf,
			'not'                   => $this->not,
			'items'                 => $this->items,
			'properties'            => $this->properties,
			'additionalProperties'  => $this->additionalProperties,
			'description'           => $this->description,
			'format'                => $this->format,
			'default'               => $this->default,
			'nullable'              => $this->nullable,
			'discriminator'         => $this->discriminator,
			'readOnly'              => $this->readOnly,
			'writeOnly'             => $this->writeOnly,
			'wml'                   => $this->xml,
			'externalDocs'          => $this->externalDocs,
			'example'               => $this->example,
			'deprecated'            => $this->deprecated
		];
	}
}