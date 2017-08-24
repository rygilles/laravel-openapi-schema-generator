<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * Adds metadata to a single tag that is used by the Operation Object. It is not mandatory to have a Tag Object per tag defined in the Operation Object instances.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#tagObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class Tag extends Object
{
	/**
	 * The name of the tag. (REQUIRED)
	 * 
	 * @var string
	 */
	public $name;

	/**
	 * A short description for the tag.
	 * CommonMark syntax MAY be used for rich text representation.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Additional external documentation for this tag.
	 *
	 * @var ExternalDocumentation
	 */
	public $externalDocs;

	/**
	 * {@inheritdoc}
	 */
	protected $requiredAttributes = [
		'name'
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'name'          => $this->name,
			'description'   => $this->description,
			'externalDocs'  => $this->externalDocs
		];
	}
}