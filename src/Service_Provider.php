<?php

namespace Snap\Whoops;

use Snap\Core\Service_Provider;

class Whoops_Service_Provider extends Service_Provider
{
	public $services = [];
	
	public $factories = [];

	/**
	 * Called after all service providers have been registered.
	 *
	 * @since 1.0.0
	 */
	public function boot()
	{
		// hooks/filters? call the whoops bootstrap
	}

	/**
	 * Register any
	 * @return [type] [description]
	 */
	public function register()
	{

	}
}