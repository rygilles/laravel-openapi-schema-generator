<?php

namespace Rygilles\OpenApiGenerator;

use Illuminate\Console\Command;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use Rygilles\OpenApiGenerator\OpenApi\Document;
use Rygilles\OpenApiGenerator\OpenApi\OpenAPI;


class Generator
{
	/**
	 * The parent command object
	 *
	 * @var Command
	 */
	protected $parentCommand = null;

	/**
	 * Return defined parent command object
	 *
	 * @return Command|null
	 */
	public function getParentCommand()
	{
		return $this->parentCommand;
	}

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
	 * Root document object pf the OpenAPI document
	 *
	 * @var OpenAPI
	 */
	protected $openAPI;

	/**
	 * Create a new command instance.
	 *
	 * @param DocBlockFactory $docBlockFactory
	 * @param Command $parentCommand
	 */
	public function __construct(DocBlockFactory $docBlockFactory, Command $parentCommand)
	{
		$this->docBlockFactory = $docBlockFactory;
		$this->parentCommand = $parentCommand;
		$this->openAPI = new OpenAPI();
		echo($this->openAPI . "\n");
		die();
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

		//$this->parentCommand->info('Route');
	}

	/**
	 * Return route group
	 *
	 * @param string $route
	 * @return string
	 */
	protected function getRouteGroup($route)
	{
		if (!config('openapischemas.model_tag')) {
			$this->parentCommand->error('The "model_tag" value in your apischemas.php configuration file must defined.');
			exit();
		}

		list($class, $method) = explode('@', $route);
		$reflection = new ReflectionClass($class);
		$comment = $reflection->getDocComment();
		if ($comment) {
			$docBlock = $this->docBlockFactory->create($comment);
			foreach ($docBlock->getTags() as $tag) {
				if ($tag->getName() === config('openapischemas.model_tag')) {
					return $tag->getDescription()->render();
				}
			}
		}

		return 'general';
	}
}