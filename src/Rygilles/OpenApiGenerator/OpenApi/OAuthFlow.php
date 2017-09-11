<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * Configuration details for a supported OAuth Flow
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#oauthFlowObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
abstract class OAuthFlow extends Object
{
	/**
	 * The authorization URL to be used for this flow. This MUST be in the form of a URL. (REQUIRED)
	 *
	 * @var string
	 */
	public $authorizationUrl;

	/**
	 * The token URL to be used for this flow. This MUST be in the form of a URL. (REQUIRED)
	 *
	 * @var string
	 */
	public $tokenUrl;

	/**
	 * The URL to be used for obtaining refresh tokens. This MUST be in the form of a URL.
	 *
	 * @var string
	 */
	public $refreshUrl;

	/**
	 * The available scopes for the OAuth2 security scheme. A map between the scope name and a short description for it. (REQUIRED)
	 *
	 * @var string[]
	 */
	public $scopes;

	/**
	 * {@inheritdoc}
	 */
	protected $requiredAttributes = [
		'scopes'
	];
	
	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'authorizationUrl'  => $this->authorizationUrl,
			'tokenUrl'          => $this->tokenUrl,
			'refreshUrl'        => $this->refreshUrl,
			'scopes'            => (object) $this->scopes
		];
	}
}