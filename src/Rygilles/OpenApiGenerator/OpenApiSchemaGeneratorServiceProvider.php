<?php

namespace Rygilles\OpenApiGenerator;

use Illuminate\Support\ServiceProvider;
use Rygilles\OpenApiGenerator\Commands\GenerateSchemas;


class OpenApiSchemaGeneratorServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/../../resources/openapischemas.php' => config_path('openapischemas.php'),
		], 'openapischemas');
	}

	/**
	 * Register the API doc commands.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('openApiSchemas.generate', function () {
			return new GenerateSchemas();
		});

		$this->commands([
			'openApiSchemas.generate',
		]);
	}
}
