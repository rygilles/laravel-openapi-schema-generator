<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * An object representing a Server.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#serverObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class Server extends Object
{
	/**
	 * A URL to the target host.
	 * This URL supports Server Variables and MAY be relative,
	 * to indicate that the host location is relative to the location where the OpenAPI document is being served.
	 * Variable substitutions will be made when a variable is named in {brackets}. (REQUIRED)
	 *
	 * @var string
	 */
	public $url;

	/**
	 * An optional string describing the host designated by the URL.
	 * CommonMark syntax MAY be used for rich text representation.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * A map between a variable name and its value.
	 * The value is used for substitution in the server's URL template.
	 *
	 * @var ServerVariable[]
	 */
	public $variables;

	/**
	 * {@inheritdoc}
	 */
	protected $requiredAttributes = [
		'url'
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'url'           => $this->url,
			'description'   => $this->description,
			'variables'     => $this->variables
		];
	}
}