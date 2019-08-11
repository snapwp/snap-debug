<?php

namespace Snap\Debug\Handlers;

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
     * @return int
     */
    public function handle()
    {
        if (! $this->isAjaxRequest()) {
            return Handler::DONE;
        }

        $response = [
            'success' => false,
            'data'    => Formatter::formatExceptionAsDataArray($this->getInspector(), $this->addTraceToOutput()),
        ];

        if (Misc::canSendHeaders()) {
            \header('Content-Type: application/json; charset=' . get_option('blog_charset'));
        }

        echo wp_json_encode($response, JSON_PRETTY_PRINT);

        return Handler::QUIT;
    }

    /**
     * Whether the current request is admin-ajax or not.
     *
     * @return bool
     */
    private function isAjaxRequest()
    {
        return \defined('DOING_AJAX') && DOING_AJAX;
    }
}
