<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * Configuration details for a Client Credentials OAuth Flow
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#oauthFlowObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class ClientCredentialsOAuthFlow extends OAuthFlow
{
	/**
	 * {@inheritdoc}
	 */
	protected $requiredAttributes = [
		'tokenUrl',
		'scopes'
	];
}