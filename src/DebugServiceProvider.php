<?php

namespace Snap\Debug;

use Snap\Services\Config;
use Snap\Services\Container;
use Snap\Services\ServiceProvider;
use Snap\Debug\Dumper\Handle;
use Snap\Debug\Handlers\Ajax;
use Snap\Debug\Handlers\General;
use Snap\Debug\Handlers\RestApi;
use Symfony\Component\VarDumper\VarDumper;
use Whoops\Run;

/**
 * Snap Debug service provider.
 */
class DebugServiceProvider extends ServiceProvider
{
    /**
     * Register the Service.
     */
    public function register()
    {
        $this->addConfigLocation(\realpath(__DIR__ . '/../config'));

        require_once __DIR__ . '/functions.php';

        if ($this->isEnabled()) {
            $this->initWhoops();
        }
        
        $this->initVarDumper();

        $this->publishesConfig(\realpath(__DIR__ . '/../config'));
    }

    /**
     * Register the Snap VarDumper handler.
     */
    private function initVarDumper()
    {
        VarDumper::setHandler([Handle::class, 'dump']);
    }

    /**
     * Register the Whoops service into the service container.
     */
    private function initWhoops()
    {
        $whoops = new Run;
        $general = new General();
    
        $whoops->pushHandler($general->get_handler());
        $whoops->pushHandler(new Ajax());
        $whoops->pushHandler(new RestApi());

        $whoops->register();

        \ob_start();

        // Add to Service container.
        Container::addInstance($whoops);
    }

    /**
     * Whether WP_DEBUG is enabled.
     *
     * @return bool
     */
    private function isDebug()
    {
        return \defined('WP_DEBUG') && WP_DEBUG;
    }

    /**
     * Whether WP_DEBUG_DISPLAY is enabled.
     *
     * @return bool
     */
    private function isDebugDisplay()
    {
        return \defined('WP_DEBUG_DISPLAY') && false !== WP_DEBUG_DISPLAY;
    }

    /**
     * Whether to load Whoops or not.
     *
     * @return bool
     */
    private function isEnabled()
    {
        if (! $this->isDebug() || ! $this->isDebugDisplay()) {
            return false;
        }

        return Config::get('debug.enable_whoops');
    }
}
