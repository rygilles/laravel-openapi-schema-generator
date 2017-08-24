<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * Describes a single API operation on a path.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#operationObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class Operation extends Object
{
	/**
	 * A list of tags for API documentation control.
	 * Tags can be used for logical grouping of operations by resources or any other qualifier.
	 *
	 * @var string[]
	 */
	public $tags;

	/**
	 * A short summary of what the operation does.
	 *
	 * @var string
	 */
	public $summary;

	/**
	 * A verbose explanation of the operation behavior.
	 * CommonMark syntax MAY be used for rich text representation.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Additional external documentation for this operation.
	 *
	 * @var ExternalDocumentation
	 */
	public $externalDocs;

	/**
	 * Unique string used to identify the operation.
	 * The id MUST be unique among all operations described in the API.
	 * Tools and libraries MAY use the operationId to uniquely identify an operation,
	 * therefore, it is RECOMMENDED to follow common programming naming conventions.
	 *
	 * @var string
	 */
	public $operationId;

	/**
	 * A list of parameters that are applicable for this operation.
	 * If a parameter is already defined at the Path Item,
	 * the new definition will override it but can never remove it.
	 * The list MUST NOT include duplicated parameters.
	 * A unique parameter is defined by a combination of a name and location.
	 * The list can use the Reference Object to link to parameters
	 * that are defined at the OpenAPI Object's components/parameters.
	 *
	 * @var (Parameter|Reference)[]
	 */
	public $parameters;

	/**
	 * The request body applicable for this operation.
	 * The requestBody is only supported in HTTP methods where the HTTP 1.1 specification RFC7231
	 * has explicitly defined semantics for request bodies.
	 * In other cases where the HTTP spec is vague, requestBody SHALL be ignored by consumers.
	 *
	 * @var RequestBody|Reference
	 */
	public $requestBody;

	/**
	 * The list of possible responses as they are returned from executing this operation. (REQUIRED)
	 *
	 * A container for the expected responses of an operation.
	 * The container maps a HTTP response code to the expected response.
	 *
	 * The documentation is not necessarily expected to cover all possible HTTP response codes
	 * because they may not be known in advance. However,
	 * documentation is expected to cover a successful operation response and any known errors.
	 *
	 * The default MAY be used as a default response object for all HTTP codes
	 * that are not covered individually by the specification.
	 *
	 * The Responses Object MUST contain at least one response code,
	 * and it SHOULD be the response for a successful operation call.
	 *
	 * Array key pattern :  "default" (fixed fields)
	 *                      or HTTP Status Code
	 *
	 * The HTTP Status Codes are used to indicate the status of the executed operation.
	 * The available status codes are defined by RFC7231 and registered status codes
	 * are listed in the IANA Status Code Registry.
	 *
	 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#responsesObject
	 * @var Response[]
	 */
	public $responses;

	/**
	 * A map of possible out-of band callbacks related to the parent operation.
	 * The key is a unique identifier for the Callback Object.
	 * Each value in the map is a Callback Object that describes a request
	 * that may be initiated by the API provider and the expected responses.
	 * The key value used to identify the callback object is an expression,
	 * evaluated at runtime, that identifies a URL to use for the callback operation.
	 *
	 * @var (PathItem|Reference)[]
	 */
	public $callbacks;

	/**
	 * Declares this operation to be deprecated. Consumers SHOULD refrain from usage of the declared operation.
	 * Default value is false.
	 *
	 * @var boolean
	 */
	public $deprecated = false;

	/**
	 * A declaration of which security mechanisms can be used for this operation.
	 * The list of values includes alternative security requirement objects that can be used.
	 * Only one of the security requirement objects need to be satisfied to authorize a request.
	 * This definition overrides any declared top-level security.
	 * To remove a top-level security declaration, an empty array can be used.
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
	 * An alternative server array to service this operation.
	 * If an alternative server object is specified at the Path Item Object or Root level,
	 * it will be overridden by this value.
	 *
	 * @var Server[]
	 */
	public $servers;

	/**
	 * {@inheritdoc}
	 */
	protected $requiredAttributes = [
		'responses'
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'tags'          => $this->tags,
			'summary'       => $this->summary,
			'description'   => $this->description,
			'externalDocs'  => $this->externalDocs,
			'operationId'   => $this->operationId,
			'parameters'    => $this->parameters,
			'requestBody'   => $this->requestBody,
			'responses'     => $this->responses,
			'callbacks'     => $this->callbacks,
			'deprecated'    => $this->deprecated,
			'security'      => $this->security,
			'servers'       => $this->servers
		];
	}
}