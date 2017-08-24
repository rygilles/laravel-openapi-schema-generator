<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * Holds a set of reusable objects for different aspects of the OAS.
 * All objects defined within the components object will have no effect on the API
 * unless they are explicitly referenced from properties outside the components object.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#componentsObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class Components extends Object
{
	/**
	 * An object to hold reusable Schema Objects.
	 *
	 * @var (Schema|Reference)[]
	 */
	public $schemas;

	/**
	 * An object to hold reusable Response Objects.
	 *
	 * @var (Response|Reference)[]
	 */
	public $responses;

	/**
	 * An object to hold reusable Parameter Objects.
	 *
	 * @var (Parameter|Reference)[]
	 */
	public $parameters;

	/**
	 * An object to hold reusable Example Objects.
	 *
	 * @var (Example|Reference)[]
	 */
	public $examples;

	/**
	 * An object to hold reusable Request Body Objects.
	 *
	 * @var (RequestBody|Reference)[]
	 */
	public $requestBodies;

	/**
	 * An object to hold reusable Header Objects.
	 *
	 * @var (Header|Reference)[]
	 */
	public $headers;

	/**
	 * An object to hold reusable Security Scheme Objects.
	 *
	 * @var (SecurityScheme|Reference)[]
	 */
	public $securitySchemes;

	/**
	 * An object to hold reusable Link Objects.
	 *
	 * @var (Link|Reference)[]
	 */
	public $links;

	/**
	 * An object to hold reusable Callback Objects.
	 *
	 * @var (PathItem|Reference)[]
	 */
	public $callbacks;
	
	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'schemas'           => $this->schemas,
			'responses'         => $this->responses,
			'parameters'        => $this->parameters,
			'examples'          => $this->examples,
			'requestBodies'     => $this->requestBodies,
			'headers'           => $this->headers,
			'securitySchemes'   => $this->securitySchemes,
			'links'             => $this->links,
			'callbacks'         => $this->callbacks
		];
	}
}