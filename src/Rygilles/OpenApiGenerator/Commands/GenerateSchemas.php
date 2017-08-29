<?php

namespace Rygilles\OpenApiGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use phpDocumentor\Reflection\DocBlockFactory;
use Rygilles\OpenApiGenerator\Generators\DingoGenerator;
use Rygilles\OpenApiGenerator\Generators\Generator;
use Rygilles\OpenApiGenerator\Generators\LaravelGenerator;


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
		if (!config('openapischemas.router')) {
			$this->error('You need to specify a "router" value in your openapischemas.php configuration file first.');
			exit();
		}

		if (!in_array(config('openapischemas.router'), ['laravel', 'dingo'])) {
			$this->error('The "router" value in your openapischemas.php configuration file must be "laravel" or "dingo".');
			exit();
		}

		$profiles = config('openapischemas.profiles');
		$possibleProfiles = array_keys($profiles);

		if (count($possibleProfiles) > 1) {
			$profileName = $this->choice('For which profile ?', $possibleProfiles);
		} else {
			$profileName = 'default';
		}

		$profile = $profiles[$profileName];

		if (isset($profile['act_as_user_id'])) {
			$this->setUserToBeImpersonated($profile['act_as_user_id']);
		}

		$docBlockFactory = DocBlockFactory::createInstance();

		if (config('openapischemas.router') === 'laravel') {
			$this->generator = new LaravelGenerator($docBlockFactory, $profile, $this);
		} else {
			$this->generator = new DingoGenerator($docBlockFactory, $profile, $this);
		}
		$this->generator->applyProfileOpenApiBindings();
		
		$routes = $this->getRoutes();
		$this->generator->processRoutes($routes);

		file_put_contents($profile['output'], $this->generator->generateJSON());
	}

	/**
	 * Return routes according to the configuration file router and routes prefix
	 *
	 * @return mixed
	 */
	private function getRoutes()
	{
		$this->info('Loading Api routes...');

		if (config('openapischemas.router') === 'laravel') {
			return Route::getRoutes();
		} else {
			return app('Dingo\Api\Routing\Router')->getRoutes()[config('openapischemas.routes_prefix')];
		}
	}

	/**
	 * Set current user for Api calls
	 *
	 * @param mixed $userId
	 */
	private function setUserToBeImpersonated($userId)
	{
		if (!empty($userId)) {
			if (version_compare($this->laravel->version(), '5.2.0', '<')) {
				$userModel = config('auth.model');
				$user = $userModel::find($userId);
				$this->laravel['auth']->setUser($user);
			} else {
				if (!config('openapischemas.auth_provider')) {
					$this->error('You need to specify a "auth_provider" value in your openapischemas.php configuration file first.');
					exit();
				}
				$provider = config('openapischemas.auth_provider');
				$userModel = config("auth.providers.$provider.model");
				if (!config('auth.providers.' . $provider . '.model')) {
					$this->error('No model in your config/auth.php matching auth provider "' . $provider . '" from your openapischemas.php configuration file.');
					exit();
				}
				$user = $userModel::find($userId);
				$auth_guard = config('openapischemas.auth_guard');
				$this->laravel['auth']->guard($auth_guard)->setUser($user);
			}
		}
	}
}