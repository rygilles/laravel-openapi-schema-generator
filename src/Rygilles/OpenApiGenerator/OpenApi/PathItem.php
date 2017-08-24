<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * Describes the operations available on a single path.
 * A Path Item MAY be empty, due to ACL constraints.
 * The path itself is still exposed to the documentation viewer but
 * they will not know which operations and parameters are available.
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#pathItemObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class PathItem extends Object
{
	/**
	 * Allows for an external definition of this path item.
	 * The referenced structure MUST be in the format of a Path Item Object.
	 * If there are conflicts between the referenced definition and this Path Item's definition,
	 * the behavior is undefined.
	 *
	 * @var string
	 */
	public $ref;

	/**
	 * An optional, string summary, intended to apply to all operations in this path.
	 *
	 * @var string
	 */
	public $summary;

	/**
	 * An optional, string description, intended to apply to all operations in this path.
	 * CommonMark syntax MAY be used for rich text representation.
	 *
	 * @var string
	 */
	public $description;

	/**
	 * A definition of a GET operation on this path.
	 *
	 * @var Operation
	 */
	public $get;

	/**
	 * A definition of a PUT operation on this path.
	 *
	 * @var Operation
	 */
	public $put;

	/**
	 * A definition of a POST operation on this path.
	 *
	 * @var Operation
	 */
	public $post;

	/**
	 * A definition of a DELETE operation on this path.
	 *
	 * @var Operation
	 */
	public $delete;

	/**
	 * A definition of a OPTIONS operation on this path.
	 *
	 * @var Operation
	 */
	public $options;

	/**
	 * A definition of a HEAD operation on this path.
	 *
	 * @var Operation
	 */
	public $head;

	/**
	 * A definition of a PATCH operation on this path.
	 *
	 * @var Operation
	 */
	public $patch;

	/**
	 * A definition of a TRACE operation on this path.
	 *
	 * @var Operation
	 */
	public $trace;

	/**
	 * An alternative server array to service all operations in this path.
	 *
	 * @var Server[]
	 */
	public $servers;

	/**
	 * A list of parameters that are applicable for all the operations described under this path.
	 * These parameters can be overridden at the operation level,
	 * but cannot be removed there. The list MUST NOT include duplicated parameters.
	 * A unique parameter is defined by a combination of a name and location.
	 * The list can use the Reference Object to link to parameters
	 * that are defined at the OpenAPI Object's components/parameters.
	 *
	 * @var (Parameter|Reference)[]
	 */
	public $parameters;

	/**
	 * {@inheritdoc}
	 */
	protected function getFixedAttributes()
	{
		return [
			'$ref'          => $this->ref,
			'summary'       => $this->summary,
			'description'   => $this->description,
			'get'           => $this->get,
			'put'           => $this->put,
			'post'          => $this->post,
			'delete'        => $this->delete,
			'options'       => $this->options,
			'head'          => $this->head,
			'patch'         => $this->patch,
			'trace'         => $this->trace,
			'servers'       => $this->servers,
			'parameters'    => $this->parameters
		];
	}
}