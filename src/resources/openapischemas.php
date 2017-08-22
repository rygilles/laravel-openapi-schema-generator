<?php

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
	| Define your Api routes prefix ("v1" commonly)
	|
	*/

	'routes_prefix' => 'v1',

	/*
	|--------------------------------------------------------------------------
	| Open API Schemas Generator : Models namespace
	|--------------------------------------------------------------------------
	|
	| Define your models namespace ("App" commonly)
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
	| if there is more than one profile in this array
	|
	*/

	'profiles' => [

		'default' => [

			/*
			|--------------------------------------------------------------------------
			| Output folder
			|--------------------------------------------------------------------------
			|
			| The root folder where the schemas will be generated
			|
			*/
			'output_folder' => realpath(base_path('public/docs/openapi/default')),

			/*
			|--------------------------------------------------------------------------
			| Base url
			|--------------------------------------------------------------------------
			|
			| The base url of your schemas entry point (eg.: http://mywebsite.com/schemas/)
			| This value will be used to generate schemas id property
			|
			*/
			"base_url" => '',

		]

	]
];