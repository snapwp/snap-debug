<?php

namespace Snap\Whoops;

use Whoops\Run;
use Snap\Whoops\Handlers\Ajax;
use Snap\Whoops\Handlers\General;
use Snap\Whoops\Handlers\Rest_Api;
use Snap\Core\Snap;
use Snap\Core\Services\Provider;

class Whoops_Provider extends Provider
{
	/**
	 * Register any
	 * @return [type] [description]
	 */
	public function register()
	{
		// Bail early if we are not outputting debug info.
		if ( ! $this->is_debug() || ! $this->is_debug_display() ) {
			return;
		}

		if ($this->is_enabled()) {
			$whoops = new Run;
			$general = new General();
	

			$whoops->pushHandler( $general->get_handler() );
			$whoops->pushHandler( new Ajax() );
			$whoops->pushHandler( new Rest_Api() );

			$whoops->register();

			// Add to Service container.
			Snap::Services()->addInstance($whoops);
		}
	}

	/**
	 * @return bool
	 */
	private function is_debug() {
		return defined( 'WP_DEBUG' ) && WP_DEBUG;
	}

	/**
	 * @return bool
	 */
	private function is_debug_display() {
		return defined( 'WP_DEBUG_DISPLAY' ) && false !== WP_DEBUG_DISPLAY;
	}

	/**
	 * @return bool
	 */
	private function is_enabled() {
		return Snap::config('theme.enable_whoops', true);
	}
}