<?php

namespace Rygilles\OpenApiGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use phpDocumentor\Reflection\DocBlockFactory;
use Rygilles\OpenApiGenerator\Generator;


class GenerateSchemas extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'openApiSchemas:generate
                            {--profile=default : The profile to use for generated schemas}
    ';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Generate your OpenAPI schemas from existing Laravel Api routes.';

	/**
	 * The schemas generator
	 *
	 * @var Generator
	 */
	protected $generator;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return false|null
	 */
	public function handle()
	{
		$docBlockFactory = DocBlockFactory::createInstance();
		$this->generator = new Generator($docBlockFactory, $this);
		$routes = $this->getRoutes();
		$this->generator->processRoutes($routes);
	}

	/**
	 * Return routes according to the configuration file router and routes prefix
	 *
	 * @return mixed
	 */
	private function getRoutes()
	{
		$this->info('Loading Api routes...');

		if (!config('openapischemas.router')) {
			$this->error('You need to specify a "router" value in your openapischemas.php configuration file first.');
			exit();
		}

		if (!in_array(config('openapischemas.router'), ['laravel', 'dingo'])) {
			$this->error('The "router" value in your openapischemas.php configuration file must be "laravel" or "dingo".');
			exit();
		}

		if (config('openapischemas.router') === 'laravel') {
			return Route::getRoutes();
		} else {
			return app('Dingo\Api\Routing\Router')->getRoutes()[config('openapischemas.routes_prefix')];
		}
	}


}