<?php

namespace Rygilles\OpenApiGenerator\OpenApi;


/**
 * The Header Object follows the structure of the Parameter Object with the following changes:
 *
 * name MUST NOT be specified, it is given in the corresponding headers map.
 * in MUST NOT be specified, it is implicitly in header.
 * All traits that are affected by the location MUST be applicable to a location of header (for example, style).
 *
 * @version 3.0.0
 * @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#headerObject
 *
 * @package Rygilles\OpenApiGenerator\OpenApi
 */
class Header extends Parameter
{

}