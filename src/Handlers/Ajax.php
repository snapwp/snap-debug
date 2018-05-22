<?php

namespace Snap\Whoops\Handlers;

use Whoops\Exception\Formatter;
use Whoops\Handler\Handler;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Util\Misc;

/**
 * WordPress-specific version of Json handler.
 */
class Ajax extends JsonResponseHandler 
{
	/**
	 * The error handler. Send the error is the standard WordPress AJAX format.
	 * 
	 * @since 1.0.0
	 * 
	 * @return int
	 */
	public function handle() {
		if ( ! $this->is_ajax_request() ) {
			return Handler::DONE;
		}

		$response = [
			'success' => false,
			'data'    => Formatter::formatExceptionAsDataArray( $this->getInspector(), $this->addTraceToOutput() ),
		];

		if ( Misc::canSendHeaders() ) {
			header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset' ) );
		}

		echo wp_json_encode( $response, JSON_PRETTY_PRINT );

		return Handler::QUIT;
	}

	/**
	 * Whether the current request is admin-ajax or not.
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	private function is_ajax_request() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}
}