<?php

namespace Snap\Debug\Handlers;

use WP;
use WP_Post;
use WP_Query;
use Whoops\Handler\PrettyPageHandler;

/**
 * Normal HTTP request handler.
 */
class General
{
    /**
     * Return the Whoops PrettyPageHandler.
     *
     * @since  1.0.0
     *
     * @return PrettyPageHandler
     */
    public function get_handler()
    {
        $handler = new PrettyPageHandler();
            
        $handler->addDataTableCallback('$wp_query', [$this, 'add_query']);

        $handler->addDataTableCallback('$post', [$this, 'add_post']);

        $handler->addDataTableCallback('$wp', [$this, 'add_wp']);

        return $handler;
    }

    /**
     * Add the WP_Query global to Whoops data list.
     *
     * @since  1.0.0
     *
     * @return  array
     */
    public function add_query()
    {
        global $wp_query;

        if (! $wp_query instanceof WP_Query) {
            return [];
        }

        $output  = \get_object_vars($wp_query);

        $output['query_vars'] = \array_filter($output['query_vars']);

        unset($output['posts'], $output['post']);

        return \array_filter($output);
    }

    /**
     * Add the WP_Post global to Whoops data list.
     *
     * @since  1.0.0
     *
     * @return  array
     */
    public function add_post()
    {
        $post = get_post();

        if (! $post instanceof WP_Post) {
            return [];
        }

        return \get_object_vars($post);
    }

    /**
     * Add the WP global to Whoops data list.
     *
     * @since  1.0.0
     *
     * @return  array
     */
    public function add_wp()
    {
        global $wp;

        if (! $wp instanceof WP) {
            return array();
        }

        $output = \get_object_vars($wp);

        unset($output['private_query_vars'], $output['public_query_vars']);

        return \array_filter($output);
    }
}
