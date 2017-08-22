<?php

namespace Rygilles\OpenApiGenerator;

use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;


class Generator
{
	/**
	 * DocBlockFactory for doc block analysis
	 *
	 * @var DocBlockFactory
	 */
	protected $docBlockFactory;

	/**
	 * Schemas data array with location as key
	 *
	 * @var string[]
	 */
	protected $identifiedSchemas = [];

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(DocBlockFactory $docBlockFactory)
	{
		$this->docBlockFactory = $docBlockFactory;
	}

	/**
	 * Process routes
	 *
	 * @param mixed $routes
	 */
	public function processRoutes($routes)
	{
		foreach ($routes as $route) {
			$this->processRoute($route);
		}
	}

	/**
	 * Process a route
	 *
	 * @param \Illuminate\Routing\Route $route
	 */
	protected function processRoute($route)
	{
		$routeAction = $route->getAction();
		$routeGroup = $this->getRouteGroup($routeAction['uses']);
		dd($routeGroup);
		//$routeDescription = $this->getRouteDescription($routeAction['uses']);

		//$this->info('Route');
	}

	/**
	 * Return route group
	 *
	 * @param string $route
	 * @return string
	 */
	protected function getRouteGroup($route)
	{
		if (!config('apischemas.model_tag')) {
			$this->error('The "mode_tag" value in your apischemas.php configuration file must defined.');
			exit();
		}

		list($class, $method) = explode('@', $route);
		$reflection = new ReflectionClass($class);
		$comment = $reflection->getDocComment();
		if ($comment) {
			$docBlock = $this->docBlockFactory->create($comment);
			foreach ($docBlock->getTags() as $tag) {
				if ($tag->getName() === config('apischemas.model_tag')) {
					return $tag->getDescription()->render();
				}
			}
		}

		return 'general';
	}
}