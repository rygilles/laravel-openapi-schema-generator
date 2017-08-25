<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * Describes a single operation parameter.
 *
 * A unique parameter is defined by a combination of a name and location.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#parameterObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class Parameter extends Object
{
	/**
	 * The name of the parameter. Parameter names are case sensitive. (REQUIRED)
	 *
	 * If in is "path",
	 *      the name field MUST correspond to the associated path segment from the path field
	 *      in the Paths Object. See Path Templating for further information.
	 *
	 * If in is "header" and the name field is "Accept", "Content-Type" or "Authorization",
	 *      the parameter definition SHALL be ignored.
	 *
	 * For all other cases, the name corresponds to the parameter name used by the in property.
	 *
	 * @var string
	 */
	public $name;

	/**
	 * The location of the parameter. Possible values are "query", "header", "path" or "cookie". (REQUIRED)
	 *
	 * @var string
	 */
	public $in;

	/**
	 * A brief description of the parameter. This could contain examples of use.
	 * CommonMark syntax MAY be used for rich text representation.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Determines whether this parameter is mandatory.
	 * If the parameter location is "path", this property is REQUIRED and its value MUST be true.
	 * Otherwise, the property MAY be included and its default value is false.
	 *
	 * @var boolean
	 */
	public $required;

	/**
	 * Specifies that a parameter is deprecated and SHOULD be transitioned out of usage.
	 *
	 * @var boolean
	 */
	public $deprecated;

	/**
	 * Sets the ability to pass empty-valued parameters.
	 * This is valid only for query parameters and allows sending a parameter with an empty value.
	 * Default value is false. If style is used, and if behavior is n/a (cannot be serialized),
	 * the value of allowEmptyValue SHALL be ignored.
	 *
	 * @var boolean
	 */
	public $allowEmptyValue;

	/**
	 * Describes how the parameter value will be serialized depending on the type of the parameter value.
	 * Default values (based on value of in):
	 *  for query - form;
	 *  for path - simple;
	 *  for header - simple;
	 *  for cookie - form.
	 *
	 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#parameterStyle
	 * @var string
	 */
	public $style;

	/**
	 * When this is true, parameter values of type array or object generate separate parameters for each value of the array or key-value pair of the map.
	 * For other types of parameters this property has no effect.
	 * When style is form, the default value is true. For all other styles, the default value is false.
	 *
	 * @var boolean
	 */
	public $explode;

	/**
	 * Determines whether the parameter value SHOULD allow reserved characters,
	 * as defined by RFC3986 :/?#[]@!$&'()*+,;= to be included without percent-encoding.
	 * This property only applies to parameters with an in value of query. The default value is false.
	 *
	 * @var boolean
	 */
	public $allowReserved;

	/**
	 * The schema defining the type used for the parameter.
	 *
	 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#parameterSchema
	 * @var Schema|Reference
	 */
	public $schema;

	/**
	 * Example of the media type. The example SHOULD match the specified schema and encoding properties if present.
	 * The example object is mutually exclusive of the examples object.
	 * Furthermore, if referencing a schema which contains an example,
	 * the example value SHALL override the example provided by the schema.
	 * To represent examples of media types that cannot naturally be represented in JSON or YAML,
	 * a string value can contain the example with escaping where necessary.
	 *
	 * @var mixed
	 */
	public $example;

	/**
	 * Examples of the media type. Each example SHOULD contain a value in the correct format as specified in the parameter encoding.
	 * The examples object is mutually exclusive of the example object.
	 * Furthermore, if referencing a schema which contains an example,
	 * the examples value SHALL override the example provided by the schema.
	 *
	 * @var (Example|Reference)[]
	 */
	public $examples;

	/**
	 * A map containing the representations for the parameter. The key is the media type and the value describes it.
	 * The map MUST only contain one entry.
	 *
	 * @var MediaType[]
	 */
	public $content;

	/**
	 * {@inheritdoc}
	 */
	protected $requiredAttributes = [
		'name',
		'in',
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'name'              => $this->name,
			'in'                => $this->in,
			'description'       => $this->description,
			'required'          => $this->required,
			'deprecated'        => $this->deprecated,
			'allowEmptyValue'   => $this->allowEmptyValue,
			'style'             => $this->style,
			'explode'           => $this->explode,
			'allowReserved'     => $this->allowReserved,
			'schema'            => $this->schema,
			'example'           => $this->example,
			'examples'          => $this->examples,
			'content'           => $this->content
		];
	}
}