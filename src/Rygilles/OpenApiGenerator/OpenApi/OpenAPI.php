<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * This is the root document object of the OpenAPI document.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#oasDocument
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class OpenAPI extends Object
{
	/**
	 * This string MUST be the semantic version number of the OpenAPI Specification version that the OpenAPI document uses.
	 * The openapi field SHOULD be used by tooling specifications and clients to interpret the OpenAPI document.
	 * This is not related to the API info.version string. (REQUIRED)
	 *
	 * @var string
	 */
	public $openapi;

	/**
	 * Provides metadata about the API. The metadata MAY be used by tooling as required. (REQUIRED)
	 * @var Info
	 */
	public $info;

	/**
	 * An array of Server Objects, which provide connectivity information to a target server.
	 * If the servers property is not provided, or is an empty array,
	 * the default value would be a Server Object with a url value of /.
	 *
	 * @var Server[]
	 */
	public $servers;

	/**
	 * The available paths and operations for the API. (REQUIRED)
	 *
	 * The field name (Array key) MUST begin with a slash.
	 * The path is appended (no relative URL resolution) to the expanded URL from the Server Object's url field
	 * in order to construct the full URL. Path templating is allowed.
	 * When matching URLs, concrete (non-templated) paths would be matched before their templated counterparts.
	 * Templated paths with the same hierarchy but different templated names MUST NOT exist as they are identical.
	 * In case of ambiguous matching, it's up to the tooling to decide which one to use.
	 *
	 * Array key pattern : "/{path}"
	 *
	 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#pathsObject
	 * @var PathItem[]
	 */
	public $paths;

	/**
	 * An element to hold various schemas for the specification.
	 *
	 * @var Components
	 */
	public $components;

	/**
	 * A declaration of which security mechanisms can be used across the API.
	 * The list of values includes alternative security requirement objects that can be used.
	 * Only one of the security requirement objects need to be satisfied to authorize a request.
	 * Individual operations can override this definition.
	 *
	 * Array key pattern : "{name}"
	 *
	 * Each name MUST correspond to a security scheme which is declared in the Security Schemes under the Components Object.
	 * If the security scheme is of type "oauth2" or "openIdConnect",
	 * then the value is a list of scope names required for the execution.
	 * For other security scheme types,the array MUST be empty.
	 *
	 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#securityRequirementObject
	 * @var string[]
	 */
	public $security;

	/**
	 * A list of tags used by the specification with additional metadata.
	 * The order of the tags can be used to reflect on their order by the parsing tools.
	 * Not all tags that are used by the Operation Object must be declared.
	 * The tags that are not declared MAY be organized randomly or based on the tools' logic.
	 * Each tag name in the list MUST be unique.
	 *
	 * @var Tag[]
	 */
	public $tags;

	/**
	 * Additional external documentation.
	 *
	 * @var ExternalDocumentation
	 */
	public $externalDocs;

	/**
	 * {@inheritdoc}
	 */
	protected $requiredAttributes = [
		'openapi',
		'info',
		'paths'
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'openapi'           => $this->openapi,
			'info'              => $this->info,
			'servers'           => $this->servers,
			'paths'             => $this->paths,
			'components'        => $this->components,
			'security'          => $this->security,
			'tags'              => $this->tags,
			'externalDocs'      => $this->externalDocs
		];
	}
}