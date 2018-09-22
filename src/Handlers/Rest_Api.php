<?php
namespace Snap\Debug\Handlers;

use Whoops\Exception\Formatter;
use Whoops\Handler\Handler;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Util\Misc;

/**
 * WordPress-specific version of Json handler for REST API.
 */
class Rest_Api extends JsonResponseHandler
{
    /**
     * The error handler. Send the error is the standard WordPress REST API format.
     *
     * @since  1.0.0
     *
     * @return int
     */
    public function handle()
    {
        if (! $this->is_rest_request()) {
            return Handler::DONE;
        }
        
        $data = Formatter::formatExceptionAsDataArray($this->getInspector(), $this->addTraceToOutput());
        
        $response = array(
            'code'    => $data['type'],
            'message' => $data['message'],
            'data'    => $data,
        );

        if (Misc::canSendHeaders()) {
            status_header(500);
            \header('Content-Type: application/json; charset=' . get_option('blog_charset'));
        }

        echo wp_json_encode($response, JSON_PRETTY_PRINT);

        return Handler::QUIT;
    }

    /**
     * Check if the current request is a REST API request.
     *
     * @since  1.0.0
     *
     * @return bool
     */
    private function is_rest_request()
    {
        if (\defined('REST_REQUEST') && REST_REQUEST) {
            return true;
        }

        if (! empty($_SERVER['REQUEST_URI']) && false !== \stripos($_SERVER['REQUEST_URI'], rest_get_url_prefix())) {
            return true;
        }

        return false;
    }
}
