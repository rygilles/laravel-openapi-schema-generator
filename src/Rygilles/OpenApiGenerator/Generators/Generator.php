<?php

namespace Rygilles\OpenApiGenerator\Generators;

use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Foundation\Http\FormRequest;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use Rygilles\OpenApiGenerator\OpenApi\Document;
use Rygilles\OpenApiGenerator\OpenApi\Example;
use Rygilles\OpenApiGenerator\OpenApi\MediaType;
use Rygilles\OpenApiGenerator\OpenApi\OpenAPI;
use Rygilles\OpenApiGenerator\OpenApi\Operation;
use Rygilles\OpenApiGenerator\OpenApi\Parameter;
use Rygilles\OpenApiGenerator\OpenApi\PathItem;
use Rygilles\OpenApiGenerator\OpenApi\Reference;
use Rygilles\OpenApiGenerator\OpenApi\RequestBody;
use Rygilles\OpenApiGenerator\OpenApi\Response;
use Rygilles\OpenApiGenerator\OpenApi\Schema;


abstract class Generator
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
	 * Apply profile Open API bindings provided from configuration file
	 */
	public function applyProfileOpenApiBindings()
	{
		$this->openAPI->info = $this->profile['openapi_bindings']['info'];

		if (isset($this->profile['openapi_bindings']['servers'])) {
			$this->openAPI->servers = $this->profile['openapi_bindings']['servers'];
		}

		if (isset($this->profile['openapi_bindings']['components'])) {
			$this->openAPI->components = $this->profile['openapi_bindings']['components'];
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
			// Ignore HEAD

			if (strtolower($httpMethod) == 'head') {
				continue;
			}

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
					//case 'head':

						$operation->parameters = [];

						foreach ($routeValidationRules as $parameterName => $rulesString) {
							$rules = explode('|', $rulesString);

							$parameter = new Parameter([
								'name' => $parameterName,
								'in' => 'query',
								'description' => null, /* @todo Grab description from phpdoc custom tag ? */
								'required' => in_array('required', $rules),
							]);

							$parameter->schema = $this->getPropertyValidationRulesSchema(
								$rules,
								'Validation rules for parameter "' . $parameterName . '"'
							);
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
							$mediaType->schema->properties[$parameterName] = $this->getPropertyValidationRulesSchema(
								$rules,
								'Validation rules for parameter "' . $parameterName . '"'
							);
						}

						$mediaType->schema->required = $this->getValidationRulesSchemaRequired($routeValidationRules);

						$requestBody->content['application/json'] = $mediaType;
						$operation->requestBody = $requestBody;

						$operation->responses = [];

						break;
				}
			}

			$response = new Response();
			$responseMediaType = null;

			$apiProfileResponseSchemaRefTags = $routeMethodDocBlock->getTagsByName('ApiProfileResponseSchemaRef');
			if (count($apiProfileResponseSchemaRefTags) > 0) {
				if (is_null($responseMediaType)) {
					$responseMediaType = new MediaType();
				}
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

			$apiDocsNoCallTags = $routeMethodDocBlock->getTagsByName('ApiDocsNoCall');
			if (count($apiDocsNoCallTags) == 0) {
				$exampleResponse = $this->getRouteCallExampleResponse($route, $httpMethod);
				if (!is_null($exampleResponse)) {
					if (is_null($responseMediaType)) {
						$responseMediaType = new MediaType();
					}

					$exampleSchemaId = $this->getRouteExampleResponseId($route);

					// Create Example schema in components

					if (is_null($this->openAPI->components)) {
						$this->openAPI->components = new Components();
					}

					$this->openAPI->components->examples[$exampleSchemaId] = new Example([
						'value' => json_decode($exampleResponse, true)
					]);

					$responseMediaType->example = new Reference([
						'ref' => '#/components/examples/' . $exampleSchemaId
					]);
				}
			}

			if (!is_null($responseMediaType)) {
				$response->content['application/json'] = $responseMediaType;
			}

			switch (strtolower($httpMethod)) {
				// @todo different HTTP code ?
				case 'get':
				//case 'head':
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
	 * Return the route example response Id to use for $ref in the schema/compoenents
	 *
	 * @param \Illuminate\Routing\Route $route
	 * @return stirng
	 */
	protected function getRouteExampleResponseId($route)
	{
		$routeAction = $route->getAction();
		$alias = studly_case(str_replace('.', '_', $routeAction['as']));

		return $alias . 'ExampleResponse';
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
	 * @param string $errorContext Information on context if an error occurs
	 * @return Schema
	 * @throws ErrorException
	 */
	protected function getPropertyValidationRulesSchema($propertyRules, $errorContext)
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
				/* Primitive types */

				case 'integer':
					$schema->type = 'integer';
					break;
				case 'boolean':
					$schema->type = 'boolean';
					break;
				case 'string':
					$schema->type = 'string';
					break;

				/* Advanced types */

				case 'uuid':
					$schema->type = 'string';
					// @fixme Use of custom "format" or must we defined a "pattern" ? Better case should be defining the custom "type" in "components"...
					$schema->format = 'uuid';
					//$schema->pattern = '^[a-fA-F0-9]{8}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{4}-[a-fA-F0-9]{12}$';
					//$schema->description = 'UUID';
					break;
				case 'min':
					if (count($explodedRule) == 0) {
						throw new ErrorException('Laravel framework "min" rule parameter is missing.' . "\n" . $errorContext);
					}
					if ($schema->type == 'string') {
						$schema->minLength = (int)$ruleParams[0];
					} elseif ($schema->type == 'integer') {
						$schema->minimum = (int)$ruleParams[0];
					}
					break;
				case 'max':
					if (count($explodedRule) == 0) {
						throw new ErrorException('Laravel framework "min" rule parameter is missing.' . "\n" . $errorContext);
					}
					if ($schema->type == 'string') {
						$schema->maxLength = (int)$ruleParams[0];
					} elseif ($schema->type == 'integer') {
						$schema->maximum = (int)$ruleParams[0];
					}
					break;
				case 'in':
					if (is_null($schema->type)) {
						// Assuming it's a string
						$schema->type = 'string';
					}
					$schema->enum = $ruleParams;
					break;
				case 'date':
					$schema->type = 'string';
					// Not really "date" or "date-time" format
					$schema->description = 'Must be a valid date according to the strtotime PHP function.';
					break;
				case 'email':
					$schema->type = 'string';
					// @fixme Use of custom "format" or must we defined a "pattern" ? Better case should be defining the custom "type" in "components"...
					$schema->format = 'email';
					//$schema->pattern = '/^[a-zA-Z0-9.!#$%&\'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/';
					break;
				case 'password':
				case 'strength':
					$schema->type = 'string';
					// @fixme Use of custom "format" or must we defined a "pattern" ? Better case should be defining the custom "type" in "components"...
					$schema->format = 'password';
					//$schema->pattern = '/(?=.*\d)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/';
					break;
				case 'url':
					$schema->type = 'string';
					// @fixme Use of custom "format" or must we defined a "pattern" ? Better case should be defining the custom "type" in "components"...
					$schema->format = 'url';
					/*
					// From Laravel "validateUrl" pattern :
					$schema->pattern = '~^
			            ((aaa|aaas|about|acap|acct|acr|adiumxtra|afp|afs|aim|apt|attachment|aw|barion|beshare|bitcoin|blob|bolo|callto|cap|chrome|chrome-extension|cid|coap|coaps|com-eventbrite-attendee|content|crid|cvs|data|dav|dict|dlna-playcontainer|dlna-playsingle|dns|dntp|dtn|dvb|ed2k|example|facetime|fax|feed|feedready|file|filesystem|finger|fish|ftp|geo|gg|git|gizmoproject|go|gopher|gtalk|h323|ham|hcp|http|https|iax|icap|icon|im|imap|info|iotdisco|ipn|ipp|ipps|irc|irc6|ircs|iris|iris.beep|iris.lwz|iris.xpc|iris.xpcs|itms|jabber|jar|jms|keyparc|lastfm|ldap|ldaps|magnet|mailserver|mailto|maps|market|message|mid|mms|modem|ms-help|ms-settings|ms-settings-airplanemode|ms-settings-bluetooth|ms-settings-camera|ms-settings-cellular|ms-settings-cloudstorage|ms-settings-emailandaccounts|ms-settings-language|ms-settings-location|ms-settings-lock|ms-settings-nfctransactions|ms-settings-notifications|ms-settings-power|ms-settings-privacy|ms-settings-proximity|ms-settings-screenrotation|ms-settings-wifi|ms-settings-workplace|msnim|msrp|msrps|mtqp|mumble|mupdate|mvn|news|nfs|ni|nih|nntp|notes|oid|opaquelocktoken|pack|palm|paparazzi|pkcs11|platform|pop|pres|prospero|proxy|psyc|query|redis|rediss|reload|res|resource|rmi|rsync|rtmfp|rtmp|rtsp|rtsps|rtspu|secondlife|service|session|sftp|sgn|shttp|sieve|sip|sips|skype|smb|sms|smtp|snews|snmp|soap.beep|soap.beeps|soldat|spotify|ssh|steam|stun|stuns|submit|svn|tag|teamspeak|tel|teliaeid|telnet|tftp|things|thismessage|tip|tn3270|turn|turns|tv|udp|unreal|urn|ut2004|vemmi|ventrilo|videotex|view-source|wais|webcal|ws|wss|wtai|wyciwyg|xcon|xcon-userid|xfire|xmlrpc\.beep|xmlrpc.beeps|xmpp|xri|ymsgr|z39\.50|z39\.50r|z39\.50s))://                                 # protocol
			            (([\pL\pN-]+:)?([\pL\pN-]+)@)?          # basic auth
			            (
			                ([\pL\pN\pS-\.])+(\.?([\pL]|xn\-\-[\pL\pN-]+)+\.?) # a domain name
			                    |                                              # or
			                \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}                 # an IP address
			                    |                                              # or
			                \[
			                    (?:(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){6})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:::(?:(?:(?:[0-9a-f]{1,4})):){5})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){4})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,1}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){3})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,2}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){2})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,3}(?:(?:[0-9a-f]{1,4})))?::(?:(?:[0-9a-f]{1,4})):)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,4}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,5}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,6}(?:(?:[0-9a-f]{1,4})))?::))))
			                \]  # an IPv6 address
			            )
			            (:[0-9]+)?                              # a port (optional)
			            (/?|/\S+|\?\S*|\#\S*)                   # a /, nothing, a / with something, a query or a fragment
			        $~ixu';
					*/
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

	/**
	 * Call the specified route to get an example response
	 *
	 * @param \Illuminate\Routing\Route $route
	 * @param string $method
	 * @return string
	 */
	protected function getRouteCallExampleResponse($route, $method)
	{
		$url = $this->getRouteUrlWithBindings($route);
		
		$response = $this->callRoute($method, $url, []);
		
		if (is_null($response)) {
			return null;
		}

		if (is_object($response)) {
			$response = $response->content();
		}

		return $response;
	}

	/**
	 * Return the route url to call with bindings from configuration files.
	 *
	 * @param $route
	 * @return string
	 */
	protected function getRouteUrlWithBindings($route)
	{
		$urlRouteBindings = [];
		$urlInjectedBindings = [];

		$routeAction = $route->getAction();
		if (isset($routeAction['as'])) {
			if (isset($this->profile['api_calls_bindings'])) {
				foreach ($this->profile['api_calls_bindings'] as $apiCallsBinding) {
					$routesAliases = $apiCallsBinding['routes_aliases'];
					foreach ($routesAliases as $routeAlias) {
						if ($routeAlias == $routeAction['as']) {
							foreach ($apiCallsBinding['bindings'] as $binding) {
								if ($binding['in'] == 'query-route') {
									$urlRouteBindings[$binding['name']] = $binding['value'];
								} else if ($binding['in'] == 'query-injected') {
									$urlInjectedBindings[$binding['name']] = $binding['value'];
								}
							}
						}
					}
				}
			}
		}

		$url = $this->getRouteUri($route);

		foreach ($urlRouteBindings as $name => $value) {
			$url = str_replace('{'.$name.'}', $value, $url);
		}

		if (count($urlInjectedBindings) > 0) {
			$firstName = array_keys($urlInjectedBindings)[0];
			$firstValue = array_shift($urlInjectedBindings);
			$url .= '?' . $firstName . '=' . $firstValue;
			foreach ($urlInjectedBindings as $name => $value) {
				$url .= '&' . $name . '=' . $value;
			}
		}

		return $url;
	}

	/**
	 * Return the URI of a route
	 *
	 * @param \Illuminate\Routing\Route $route
	 * @return string
	 */
	abstract protected function getRouteUri($route);

	/**
	 * Call a route
	 *
	 * @param string $method HTTP method to use
	 * @param string $url
	 * @param mixed[] $parameters
	 * @return \Illuminate\Http\Response|null
	 */
	abstract protected function callRoute($method, $url, $parameters);
}