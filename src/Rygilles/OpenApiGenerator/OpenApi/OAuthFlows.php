<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * Allows configuration of the supported OAuth Flows.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#oauthFlowsObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class OAuthFlows extends Object
{
	/**
	 * Configuration for the OAuth Implicit flow
	 *
	 * @var OAuthFlow
	 */
	public $implicit;

	/**
	 * Configuration for the OAuth Resource Owner Password flow
	 *
	 * @var OAuthFlow
	 */
	public $password;

	/**
	 * Configuration for the OAuth Client Credentials flow. Previously called application in OpenAPI 2.0.
	 *
	 * @var OAuthFlow
	 */
	public $clientCredentials;

	/**
	 * Configuration for the OAuth Authorization Code flow. Previously called accessCode in OpenAPI 2.0.
	 * 
	 * @var OAuthFlow
	 */
	public $authorizationCode;

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'implicit'          => $this->implicit,
			'password'          => $this->password,
			'clientCredentials' => $this->clientCredentials,
			'authorizationCode' => $this->authorizationCode
		];
	}
}