<?php

namespace Rygilles\OpenApiGenerator\Generators;


class LaravelGenerator extends Generator
{
	/**
	 * {@inheritdoc}
	 */
	public function getRouteUri($route)
	{
		if (version_compare(app()->version(), '5.4', '<')) {
			return $route->getUri();
		}

		return $route->uri();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function callRoute($method, $url, $parameters)
	{
		// TODO: Implement callRoute() method.
	}
}