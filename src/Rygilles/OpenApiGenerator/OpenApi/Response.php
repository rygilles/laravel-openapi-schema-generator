<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * Describes a single response from an API Operation, including design-time, static links to operations based on the response.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#responseObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class Response extends BaseObject
{
	/**
	 * A short description of the response.
	 * CommonMark syntax MAY be used for rich text representation.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * Maps a header name to its definition. RFC7230 states header names are case insensitive.
	 * If a response header is defined with the name "Content-Type", it SHALL be ignored.
	 *
	 * @var (Header|Reference)[]
	 */
	public $headers;

	/**
	 * A map containing descriptions of potential response payloads.
	 * The key is a media type or media type range and the value describes it.
	 * For responses that match multiple keys,
	 * only the most specific key is applicable. e.g. text/plain overrides text/*
	 *
	 * @var MediaType[]
	 */
	public $content;

	/**
	 * A map of operations links that can be followed from the response.
	 * The key of the map is a short name for the link,
	 * following the naming constraints of the names for Component Objects.
	 * 
	 * @var (Link|Reference)[]
	 */
	public $links;

	/**
	 * {@inheritdoc}
	 */
	protected $requiredAttributes = [
		'description'
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'description'   => $this->description,
			'headers'       => $this->headers,
			'content'       => $this->content,
			'links'         => $this->links
		];
	}
}