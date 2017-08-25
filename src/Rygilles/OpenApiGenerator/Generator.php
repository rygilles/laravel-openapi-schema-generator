<?php

namespace Rygilles\OpenApiGenerator;

use Illuminate\Console\Command;
use Illuminate\Foundation\Http\FormRequest;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use Rygilles\OpenApiGenerator\OpenApi\Document;
use Rygilles\OpenApiGenerator\OpenApi\MediaType;
use Rygilles\OpenApiGenerator\OpenApi\OpenAPI;
use Rygilles\OpenApiGenerator\OpenApi\Operation;
use Rygilles\OpenApiGenerator\OpenApi\Parameter;
use Rygilles\OpenApiGenerator\OpenApi\PathItem;
use Rygilles\OpenApiGenerator\OpenApi\Reference;
use Rygilles\OpenApiGenerator\OpenApi\RequestBody;
use Rygilles\OpenApiGenerator\OpenApi\Response;
use Rygilles\OpenApiGenerator\OpenApi\Schema;


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
	 * Profile provided from Laravel configuration file 'openapischemas.php'
	 *
	 * @var mixed[]
	 */
	protected $profile;

	/**
	 * Create a new command instance.
	 *
	 * @param DocBlockFactory $docBlockFactory

	 * @param Command $parentCommand
	 */
	public function __construct(DocBlockFactory $docBlockFactory, $profile, Command $parentCommand)
	{
		$this->docBlockFactory = $docBlockFactory;
		$this->parentCommand = $parentCommand;
		$this->profile = $profile;

		$this->openAPI = new OpenAPI();
	}

	/**
	 * Apply profile bindings provided from configuration file
	 */
	public function applyProfileBindings()
	{
		$this->openAPI->info = $this->profile['bindings']['info'];

		if (isset($this->profile['bindings']['servers'])) {
			$this->openAPI->servers = $this->profile['bindings']['servers'];
		}

		if (isset($this->profile['bindings']['components'])) {
			$this->openAPI->components = $this->profile['bindings']['components'];
		}
	}

	/**
	 * Generate and return the Open API JSON schema
	 */
	public function generateJSON()
	{
		// __toString() -> JSON
		return $this->openAPI;
	}

	/**
	 * Process routes and create path item objects on the root Open API document.
	 *
	 * @param mixed $routes
	 */
	public function processRoutes($routes)
	{
		if (is_null($this->openAPI->paths)) {
			$this->openAPI->paths = [];
		}

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

		$this->getParentCommand()->info('Processing route [' . implode('/', $route->methods()) . '] ' . $routeAction['uri']);



		foreach ($route->methods() as $httpMethod) {
			if (isset($this->openAPI->paths[$routeAction['uri']])) {
				$pathItem = $this->openAPI->paths[$routeAction['uri']];
			} else {
				$pathItem = $this->openAPI->paths[$routeAction['uri']] = new PathItem();

				$routeControllerDocBlock = $this->getRouteControllerDocBlock($route);
				if (!is_null($routeControllerDocBlock)) {
					$pathItem->summary = $routeControllerDocBlock->getSummary();
					if (empty($pathItem->summary)) {
						$pathItem->summary = null;
					}
					$routeControllerDescriptionDocBlock = $routeControllerDocBlock->getDescription();
					$pathItem->description = $routeControllerDescriptionDocBlock->render();
					if (empty($pathItem->description)) {
						$pathItem->description = null;
					}
				}
			}

			$operation = new Operation();

			$routeMethodDocBlock = $this->getRouteMethodDocBlock($route);
			if (!is_null($routeMethodDocBlock)) {
				$operation->summary = $routeMethodDocBlock->getSummary();
				if (empty($operation->summary)) {
					$operation->summary = null;
				}
				$routeMethodDescriptionDocBlock = $routeMethodDocBlock->getDescription();
				$operation->description = $routeMethodDescriptionDocBlock->render();
				if (empty($operation->description)) {
					$operation->description = null;
				}
			}

			// Try to get route validation rules
			$routeValidationRules = $this->getRouteValidationRules($route);

			if (count($routeValidationRules) > 0) {
				switch (strtolower($httpMethod)) {
					case 'get':
					case 'head':

						$operation->parameters = [];

						foreach ($routeValidationRules as $parameterName => $rulesString) {
							$rules = explode('|', $rulesString);

							$parameter = new Parameter([
								'name' => $parameterName,
								'in' => 'query',
								'description' => null, /* @todo Grab description from phpdoc custom tag ? */
								'required' => in_array('required', $rules),
							]);

							$parameter->schema = $this->getPropertyValidationRulesSchema($rules);

							$operation->parameters[] = $parameter;
						}

						break;

					case 'put':
					case 'patch':
					case 'post':

						$requestBody = new RequestBody();
						$mediaType = new MediaType();
						$mediaType->schema = new Schema();
						$mediaType->schema->properties = [];

						foreach ($routeValidationRules as $parameterName => $rulesString) {
							$rules = explode('|', $rulesString);
							$mediaType->schema->properties[$parameterName] = $this->getPropertyValidationRulesSchema($rules);
						}

						$mediaType->schema->required = $this->getValidationRulesSchemaRequired($routeValidationRules);

						$requestBody->content['application/json'] = $mediaType;
						$operation->requestBody = $requestBody;

						$operation->responses = [];

						break;
				}
			}

			$response = new Response();
			$responseMediaType = new MediaType();

			$apiProfileResponseSchemaRefTags = $routeMethodDocBlock->getTagsByName('ApiProfileResponseSchemaRef');
			if (count($apiProfileResponseSchemaRefTags) > 0) {
				$apiProfileResponseSchemaRefTag = $apiProfileResponseSchemaRefTags[0];
				$apiProfileResponseSchemaRef = trim(ltrim($apiProfileResponseSchemaRefTag->render(), '@ApiProfileResponseSchemaRef'));
				$responseMediaType->schema = new Reference([
					'ref' => $apiProfileResponseSchemaRef
				]);
			}

			$response->description = '';
			$apiProfileResponseDescriptionTags = $routeMethodDocBlock->getTagsByName('ApiProfileResponseDescription');
			if (count($apiProfileResponseDescriptionTags) > 0) {
				$apiProfileResponseDescriptionTag = $apiProfileResponseDescriptionTags[0];
				$apiProfileResponseDescription = trim(ltrim($apiProfileResponseDescriptionTag->render(), '@ApiProfileResponseDescription'));
				$response->description = $apiProfileResponseDescription;
			}

			$response->content['application/json'] = $responseMediaType;

			switch (strtolower($httpMethod)) {
				// @todo different HTTP code ?
				case 'get':
				case 'head':
					$operation->responses['200'] = $response;
					break;

				case 'put':
				case 'patch':
				case 'post':
					$operation->responses['201'] = $response;
					break;

				case 'delete':
					$operation->responses['204'] = $response;
					break;
			}
			$pathItem->{strtolower($httpMethod)} = $operation;
		}
	}

	/**
	 * Get schema required array from validation rules or null if no required fields
	 *
	 * @param mixed[] $validationRules
	 * @return string[]|null
	 */
	protected function getValidationRulesSchemaRequired($validationRules)
	{
		$required = [];

		foreach ($validationRules as $propertyName => $rulesString) {
			$propertyRules = explode('|', $rulesString);

			// Parse property rules to get format/schema
			foreach ($propertyRules as $rule) {
				$explodedRule = explode(':', $rule);
				$ruleName = $explodedRule[0];
				if ($ruleName == 'required') {
					$required[] = $propertyName;
				}
			}
		}

		return count($required > 0) ? $required : null;
	}

	/**
	 * Return a new schema based on validation rules for a property.
	 *
	 * @param string[] $propertyRules Array of property rules
	 * @return Schema
	 */
	protected function getPropertyValidationRulesSchema($propertyRules)
	{
		$schema = new Schema();

		// Parse property rules to get format/schema
		foreach ($propertyRules as $rule) {
			$explodedRule = explode(':', $rule);
			$ruleName = $explodedRule[0];
			if (count($explodedRule) > 1) {
				$ruleParams = explode(',', $explodedRule[1]);
			}

			switch ($ruleName) {
				case 'integer':
					$schema->type = 'integer';
					break;
				case 'string':
					$schema->type = 'string';
					break;
				case 'uuid':
					$schema->type = 'string';
					$schema->pattern = '^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}$';
					$schema->description = 'UUID';
					break;
				case 'min':
					if ($schema->type == 'string') {
						$schema->minLength = (int)$ruleParams[0];
					} elseif ($schema->type == 'integer') {
						$schema->minimum = (int)$ruleParams[0];
					}
					break;
				case 'max':
					if ($schema->type == 'string') {
						$schema->maxLength = (int)$ruleParams[0];
					} elseif ($schema->type == 'integer') {
						$schema->maximum = (int)$ruleParams[0];
					}
					break;
			}
		}

		return $schema;
	}

	/**
	 * Try to get route validation rules if an instance of class/sub class of FormRequest is injected in the route method.
	 *
	 * @param \Illuminate\Routing\Route $route
	 * @return string[]
	 */
	protected function getRouteValidationRules($route)
	{
		$routeAction = $route->getAction();
		list($class, $method) = explode('@', $routeAction['uses']);

		$reflection = new ReflectionClass($class);
		$reflectionMethod = $reflection->getMethod($method);

		foreach ($reflectionMethod->getParameters() as $parameter) {
			$parameterType = $parameter->getClass();
			if (! is_null($parameterType) && class_exists($parameterType->name)) {
				$className = $parameterType->name;

				if (is_subclass_of($className, FormRequest::class)) {
					$parameterReflection = new $className;

					if (method_exists($parameterReflection, 'validator')) {
						return $parameterReflection->validator()->getRules();
					} else {
						return $parameterReflection->rules();
					}
				}
			}
		}

		return [];
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

	/**
	 * Return route controller class doc block if comments are provided else return null.
	 *
	 * @param \Illuminate\Routing\Route $route
	 * @return DocBlock|null
	 */
	protected function getRouteControllerDocBlock($route)
	{
		$routeAction = $route->getAction();
		list($class, $method) = explode('@', $routeAction['uses']);

		$reflectionClass = new ReflectionClass($class);

		$comment = $reflectionClass->getDocComment();

		if ($comment) {
			return($this->docBlockFactory->create($comment));
		}

		return null;
	}

	/**
	 * Return route method doc block if comments are provided else return null.
	 *
	 * @param \Illuminate\Routing\Route $route
	 * @return DocBlock|null
	 */
	protected function getRouteMethodDocBlock($route)
	{
		$routeAction = $route->getAction();
		list($class, $method) = explode('@', $routeAction['uses']);

		$reflectionClass = new ReflectionClass($class);
		$reflectionMethod = $reflectionClass->getMethod($method);

		$comment = $reflectionMethod->getDocComment();

		if ($comment) {
			return($this->docBlockFactory->create($comment));
		}

		return null;
	}
}