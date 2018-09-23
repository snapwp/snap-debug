<?php

namespace Snap\Debug;

use Snap\Core\Snap;
use Snap\Services\Provider;
use Snap\Debug\Dumper\Handle;
use Snap\Debug\Handlers\Ajax;
use Snap\Debug\Handlers\General;
use Snap\Debug\Handlers\Rest_Api;
use Symfony\Component\VarDumper\VarDumper;
use Whoops\Run;

/**
 * Snap Debug service provider.
 */
class Debug_Provider extends Provider
{
    /**
     * Register the Service.
     *
     * @since  1.0.0
     */
    public function register()
    {
        $this->add_config_location(\realpath(__DIR__ . '/../config'));

        require_once __DIR__ . '/functions.php';

        if ($this->is_enabled()) {
            $this->init_whoops();
        }
        
        $this->init_var_dumper();

        $this->publishes_config(\realpath(__DIR__ . '/../config'));
    }

    /**
     * Register the Snap VarDumper handler.
     *
     * @since  1.0.0
     */
    private function init_var_dumper()
    {
        VarDumper::setHandler([Handle::class, 'dump']);
    }

    /**
     * Register the Whoops service into the service container.
     *
     * @since  1.0.0
     */
    private function init_whoops()
    {
        $whoops = new Run;
        $general = new General();
    
        $whoops->pushHandler($general->get_handler());
        $whoops->pushHandler(new Ajax());
        $whoops->pushHandler(new Rest_Api());

        $whoops->register();

        \ob_start();

        // Add to Service container.
        Snap::Services()->addInstance($whoops);
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
        return \defined('WP_DEBUG') && WP_DEBUG;
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
        return \defined('WP_DEBUG_DISPLAY') && false !== WP_DEBUG_DISPLAY;
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
        if (! $this->is_debug() || ! $this->is_debug_display()) {
            return false;
        }

        return Snap::config('debug.enable_whoops');
    }
}
