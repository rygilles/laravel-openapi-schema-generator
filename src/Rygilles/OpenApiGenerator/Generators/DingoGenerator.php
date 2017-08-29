<?php

namespace Rygilles\OpenApiGenerator\Generators;


class DingoGenerator extends Generator
{
	/**
	 * {@inheritdoc}
	 */
	public function getRouteUri($route)
	{
		return $route->uri();
	}

	/**
	 * {@inheritdoc}
	 */
	public function callRoute($method, $url, $parameters = [])
	{
		if ($method == 'HEAD') {
			return null;
		}
		
		$dispatcher = app('Dingo\Api\Dispatcher')->raw();

		$server = collect([
			'Content-Type' => 'application/json',
			'Accept' => 'application/json',
		])->toArray();

		$user = auth()->user();
		if ($user) {
			try {
				auth()->guard('api')->setUser($user);
			} catch (\Exception $e) {}
		}

		collect($server)->map(function ($key, $value) use ($dispatcher) {
			$dispatcher->header($value, $key);
		});

		$this->getParentCommand()->comment("\r\n" . 'Calling route (method="' . $method . '", "uri=' . ltrim($url, '/') . '", parameters=["' . implode('", "', $parameters) . '"])');

		try {
			$resp = call_user_func_array([$dispatcher, strtolower($method)], [$url]);
		} catch (\Exception $e) {
			// For debug purpose
			$this->getParentCommand()->warn('Call failed, ignore response : ' . get_class($e) . ' : '  . $e->getMessage() . "\r\n" . 'file ' . $e->getFile() . ' at line ' . $e->getLine());
			$resp = null;
		}

		return $resp;
	}
}