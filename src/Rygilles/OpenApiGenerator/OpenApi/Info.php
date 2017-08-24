<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * The object provides metadata about the API.
 * The metadata MAY be used by the clients if needed,
 * and MAY be presented in editing or documentation generation tools for convenience.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#infoObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class Info extends Object
{
	/**
	 * The title of the application. (REQUIRED)
	 *
	 * @var string
	 */
	public $title;

	/**
	 * A short description of the application. CommonMark syntax MAY be used for rich text representation.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * A URL to the Terms of Service for the API. MUST be in the format of a URL.
	 * 
	 * @var string
	 */
	public $termsOfService;

	/**
	 * The contact information for the exposed API.
	 *
	 * @var License
	 */
	public $contact;

	/**
	 * A URL to the Terms of Service for the API. MUST be in the format of a URL.
	 *
	 * @var Contact
	 */
	public $license;

	/**
	 * The version of the OpenAPI document
	 * (which is distinct from the OpenAPI Specification version or the API implementation version).
	 * (REQUIRED)
	 *
	 * @var string
	 */
	public $version;

	/**
	 * {@inheritdoc}
	 */
	protected $requiredAttributes = [
		'title',
		'version'
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'title'             => $this->title,
			'description'       => $this->description,
			'termsOfService'    => $this->termsOfService,
			'contact'           => $this->contact,
			'license'           => $this->license,
			'version'           => $this->version
		];
	}
}