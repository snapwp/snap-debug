<?php

use Snap\Debug\Utils\Debug_Hook;

if (! \function_exists('snap_get_hook_info')) {
    /**
     * Lists debug info about all callbacks for a given hook.
     *
     * Returns information for all callbacks in order of execution and priority.
     *
     * @since  1.0.0
     *
     * @param  string $hook     The hook to fetch the callback data for.
     * @param  int    $priority The priority to return the hook stack for.
     * @return array
     */
    function snap_get_hook_info($hook, $priority = null)
    {
        $debug = new Debug_Hook($hook);
        return $debug->get_data($priority);
    }
}