<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * License information for the exposed API.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#licenseObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class License extends BaseObject
{
	/**
	 * The license name used for the API. (REQUIRED)
	 *
	 * @var string
	 */
	public $name;

	/**
	 * A URL to the license used for the API. MUST be in the format of a URL.
	 *
	 * @var string
	 */
	public $url;

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
			'name'  => $this->name,
			'url'   => $this->url
		];
	}
}