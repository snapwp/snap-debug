<?php

namespace Snap\Whoops;

use Snap\Core\Services\Provider;
use Snap\Core\Snap;
use Snap\Whoops\Handlers\Ajax;
use Snap\Whoops\Handlers\General;
use Snap\Whoops\Handlers\Rest_Api;
use Whoops\Run;

/**
 * Snap Whoops service provider.
 */
class Whoops_Provider extends Provider
{
	/**
	 * Register the Whoops service into the service container.
	 * 
	 * @since  1.0.0
	 */
	public function register()
	{
		if ($this->is_enabled()) {
			$whoops = new Run;
			$general = new General();
	
			$whoops->pushHandler( $general->get_handler() );
			$whoops->pushHandler( new Ajax() );
			$whoops->pushHandler( new Rest_Api() );

			$whoops->register();

			ob_start();

			// Add to Service container.
			Snap::Services()->addInstance($whoops);
		}
	}

	/**
	 * Whether WP_DEBUG is enabled.
	 * 
	 * @since  1.0.0
	 * 
	 * @return bool
	 */
	private function is_debug() 
	{
		return defined( 'WP_DEBUG' ) && WP_DEBUG;
	}

	/**
	 * Whether WP_DEBUG_DISPLAY is enabled.
	 * 
	 * @since  1.0.0
	 * 
	 * @return bool
	 */
	private function is_debug_display() 
	{
		return defined( 'WP_DEBUG_DISPLAY' ) && false !== WP_DEBUG_DISPLAY;
	}

	/**
	 * Whether to load Whoops or not.
	 * 
	 * @since  1.0.0
	 * 
	 * @return bool
	 */
	private function is_enabled() 
	{
		if ( ! $this->is_debug() || ! $this->is_debug_display() ) {
			return false;
		}

		return Snap::config('theme.enable_whoops', true);
	}
}