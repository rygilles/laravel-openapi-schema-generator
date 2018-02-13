<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * Allows referencing an external resource for extended documentation.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#externalDocumentationObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class ExternalDocumentation extends BaseObject
{
	/**
	 * A short description of the target documentation.
	 * CommonMark syntax MAY be used for rich text representation.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * The URL for the target documentation. Value MUST be in the format of a URL. (REQUIRED)
	 *
	 * @var string
	 */
	public $url;

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
			'description'   => $this->description,
			'url'           => $this->url
		];
	}
}