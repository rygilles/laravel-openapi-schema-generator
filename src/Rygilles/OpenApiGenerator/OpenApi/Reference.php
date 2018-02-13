<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * A simple object to allow referencing other components in the specification, internally and externally.
 * The Reference Object is defined by JSON Reference and follows the same structure, behavior and rules.
 * For this specification, reference resolution is accomplished as defined by the JSON Reference specification and not by the JSON Schema specification.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#referenceObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class Reference extends BaseObject
{
	/**
	 * The reference string. (REQUIRED)
	 *
	 * @var string
	 */
	public $ref;

	/**
	 * {@inheritdoc}
	 */
	protected $requiredAttributes = [
		'ref'
	];

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'$ref'  => $this->ref
		];
	}
}