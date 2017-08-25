<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * Describes a single request body.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#requestBodyObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class RequestBody extends Object
{
	/**
	 * A brief description of the request body. This could contain examples of use.
	 * CommonMark syntax MAY be used for rich text representation.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * The content of the request body. The key is a media type or media type range and the value describes it. (REQUIRED)
	 * For requests that match multiple keys,
	 * only the most specific key is applicable. e.g. text/plain overrides text/*
	 *
	 * @var MediaType[]
	 */
	public $content;

	/**
	 * Determines if the request body is required in the request. Defaults to false.
	 *
	 * @var boolean
	 */
	public $required;

	/**
	 * {@inheritdoc}
	 */
	protected $requiredAttributes = [
		'content'
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'description'   => $this->description,
			'content'       => $this->content,
			'required'      => $this->required
		];
	}
}