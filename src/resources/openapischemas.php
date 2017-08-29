<?php

use Rygilles\OpenApiGenerator\OpenApi\Info;

return [

	/*
	|--------------------------------------------------------------------------
	| Open API Schemas Generator : Router
	|--------------------------------------------------------------------------
	|
	| Router used for your Api in Laravel.
	| Can be 'laravel' or 'dingo'
	|
	*/

	'router' => '',

	/*
	|--------------------------------------------------------------------------
	| Open API Schemas Generator : Routes prefix
	|--------------------------------------------------------------------------
	|
	| Define your Api routes prefix ("v1" commonly).
	|
	*/

	'routes_prefix' => 'v1',

	/*
	|--------------------------------------------------------------------------
	| Open API Schemas Generator : Models namespace
	|--------------------------------------------------------------------------
	|
	| Define your models namespace ("App" commonly).
	|
	*/

	'models_namespace' => 'App',

	/*
	|--------------------------------------------------------------------------
	| Open API Schemas Generator : Model tag
	|--------------------------------------------------------------------------
	|
	| Define your model tag ("resource" for @resource, "model" for @model, etc.)
	| This tag should by in your controller classes docblock and can be overloaded
	| on the methods.
	|
	*/

	'model_tag' => 'model',

	/*
	|--------------------------------------------------------------------------
	| Open API Schemas Generator : Auth Provider
	|--------------------------------------------------------------------------
	|
	| User provider to use for the "act as user" feature.
	| Pick the right name from your config/auth.php file (default is "users")
	|
	*/
	'auth_provider' => 'users',

	/*
	|--------------------------------------------------------------------------
	| Open API Schemas Generator : Auth Guard
	|--------------------------------------------------------------------------
	|
	| Auth guard to use for the "act as user" feature.
	| default is "web"
	|
	*/
	'auth_guard' => 'web',

	/*
	|--------------------------------------------------------------------------
	| Open API Schemas Generator : Profiles
	|--------------------------------------------------------------------------
	|
	| Array of profiles.
	| If you need to generate different schemas according to users rights :
	| Copy the default entry and edit the name and values.
	| The command execution will ask you which profile you want to generate
	| if there is more than one profile in this array.
	|
	*/

	'profiles' => [

		'default' => [

			/*
			|--------------------------------------------------------------------------
			| Act As User Id
			|--------------------------------------------------------------------------
			|
			| Act as user Id for Api calls
			|
			*/
			'act_as_user_id' => 'your-user-id',

			/*
			|--------------------------------------------------------------------------
			| Output
			|--------------------------------------------------------------------------
			|
			| The path where the json schema file will be generated.
			|
			*/
			'output' => (base_path('public/docs/openapi/default.json')),

			/*
			|--------------------------------------------------------------------------
			| Api Calls Bindings
			|--------------------------------------------------------------------------
			|
			| Array of fixed bindings for routes calls (for example responses)
			| Each entry is an array
			|
			*/
			'api_calls_bindings' => [

				[
					/*
					|--------------------------------------------------------------------------
					| Routes Aliases
					|--------------------------------------------------------------------------
					|
					| Array of routes aliases concerned
					|
					*/
					'routes_aliases' => [
						'myResource.get',
						'myOtherRoute.show'
					],

					/*
					|--------------------------------------------------------------------------
					| Bindings
					|--------------------------------------------------------------------------
					|
					| Array of routes aliases concerned
					|
					*/
					'bindings' => [

						[
							/* Can be "query-route", "query-injected" or "body"
							 * "query-route" will replace the {values} of your route
							 * "query-injected" will add the value in the query string ?name=value
							 * "body" will add the binding as a form field (for PUT/PATCH/POST routes)
							 */
							'in' => 'query-route',
							'name' => 'myParameterName',
							'value' => 'myValue'
						]

					]

				]

			],

			/*
			|--------------------------------------------------------------------------
			| Open API Bindings
			|--------------------------------------------------------------------------
			|
			| Define fixed bindings of the Open API document here.
			|
			*/
			'openapi_bindings' => [

				/*
				|--------------------------------------------------------------------------
				| info
				|--------------------------------------------------------------------------
				|
				| Define values related to Info Object here.
				| Fields with null value will be ignored.
				| 'title' and 'version' fields are mandatory.
				| @see https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#infoObject
				|
				*/

				'info' => new Info([
					'title' => '', // REQUIRED
					/*
					'description' => null,
					'termsOfService' => null,
					'contact' => new \Rygilles\OpenApiGenerator\OpenApi\Contact([
						'name'  => 'API support',
						'url'   => 'http://www.example.com/support',
						'email' => 'support@example.com',
					]),
					'license' => new \Rygilles\OpenApiGenerator\OpenApi\License([
						'name' => 'Apache 2.0', // REQUIRED
						'url' => 'http://www.apache.org/licenses/LICENSE-2.0.html'
					]),
					*/
					'version' => '1.0', // REQUIRED
				]),

				/*
				'servers' => [
					new \Rygilles\OpenApiGenerator\OpenApi\Server([
						'url' => 'myapp.com/api'
					])
				],
				*/

				/*
				'components' => new \Rygilles\OpenApiGenerator\OpenApi\Components([
					'schemas' => [
						'YourSchemaId' => new \Rygilles\OpenApiGenerator\OpenApi\Schema([
							...
						])
					]
				]),
				*/
			],

		]

	]
];