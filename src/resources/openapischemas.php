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
			| Output
			|--------------------------------------------------------------------------
			|
			| The path where the json schema file will be generated.
			|
			*/
			'output' => (base_path('public/docs/openapi/default.json')),

			/*
			|--------------------------------------------------------------------------
			| Bindings
			|--------------------------------------------------------------------------
			|
			| Define fixed bindings of the Open API document here.
			|
			*/
			'bindings' => [

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