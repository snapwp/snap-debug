<?php

namespace Snap\Debug\Utils;

use ReflectionMethod;
use ReflectionFunction;

/**
 * Lists debug info about all callbacks for a given hook.
 *
 * Returns information for all callbacks in order of execution and priority.
 */
class Debug_Hook
{
    /**
     * The hook data stack.
     *
     * @since  1.0.0
     * @var array
     */
    public $data = [];

    /**
     * Setup the data for the supplied hook.
     *
     * @since  1.0.0
     *
     * @param  string $hook The hook to find callbacks for.
     */
    public function __construct($hook = '')
    {
        global $wp_filter;

        $this->data = [];

        if (! \is_string($hook) || ! isset($wp_filter[ $hook ]) || empty($wp_filter[ $hook ]->callbacks)) {
            return $this->data;
        }

        foreach ($wp_filter[ $hook ]->callbacks as $priority => $callbacks) {
            $this->data[ $priority ] = [];

            foreach ($callbacks as $key => $callback) {
                $function = $callback['function'];
                $args = $callback['accepted_args'];

                if (\is_array($function)) {
                    // Is a class.
                    if (\is_callable([ $function[0], $function[1] ])) {
                        $this->data[ $priority ][ $key ] = $this->generate_hook_info(
                            'Class Method',
                            new ReflectionMethod($function[0], $function[1]),
                            $args
                        );
                    } else {
                        $this->data[ $priority ][ $key ] = $this->generate_undefined_hook_info();
                    }
                } elseif (\is_object($function) && $function instanceof \Closure) {
                    // Is a closure.
                    $this->data[ $priority ][ $key ] = $this->generate_hook_info(
                        'Closure',
                        new \ReflectionFunction($function),
                        $args
                    );
                } elseif (\strpos($function, '::') !== false) {
                    // Is a static method.
                    list( $class, $method ) = \explode('::', $function);

                    if (\is_callable([ $class, $method ])) {
                        $this->data[ $priority ][ $key ] = $this->generate_hook_info(
                            'Static Method',
                            new ReflectionMethod($class, $method),
                            $args
                        );
                    } else {
                        $this->data[ $priority ][ $key ] = $this->generate_undefined_hook_info();
                    }
                } else {
                    // Is a function.
                    if (\function_exists($function)) {
                        $this->data[ $priority ][ $key ] = $this->generate_hook_info(
                            'Function',
                            new ReflectionFunction($function),
                            $args
                        );
                    } else {
                        $this->data[ $priority ][ $key ] = $this->generate_undefined_hook_info();
                    }
                }
            }
        }
    }

    /**
     * Return all data for the hook.
     *
     * If $priority is supplied, then only data for that priority is returned.
     *
     * @since  1.0.0
     *
     * @param  int $priority The priority to return the hook stack for.
     * @return array
     */
    public function get_data($priority = null)
    {
        if ($priority === null) {
            return $this->data;
        }

        if (isset($this->data[ $priority ])) {
            return $this->data[ $priority ];
        }

        return [];
    }

    /**
     * Generate output for a hook callback.
     *
     * @since  1.0.0
     *
     * @param  string $type      The callback type.
     * @param  object $reflector The reflection object handling this callback.
     * @param  int    $args      The number of args defined when this callback was added.
     * @return array
     */
    private function generate_hook_info($type, $reflector, $args)
    {
        $output = [
            'type' => $type,
            'file_name' => $reflector->getFileName(),
            'line_number' => $reflector->getStartLine(),
            'class' => null,
            'name' => null,
            'is_internal' => false,
        ];

        if ($reflector instanceof ReflectionMethod) {
            $output['class'] = $reflector->getDeclaringClass()->getName();
        }

        if ('Closure' !== $type) {
            $output['name'] = $reflector->getName();
            $output['is_internal'] = $reflector->isInternal();
        }

        $output['accepted_args'] = $args;

        return $output;
    }

    /**
     * Generate output for an undefined callback.
     *
     * @since 1.0.0
     *
     * @return array
     */
    private function generate_undefined_hook_info()
    {
        return [
            'type' => 'Undefined',
            'file_name' => null,
            'line_number' => null,
            'class' => null,
            'name' => null,
            'is_internal' => null,
            'accepted_args' => 0,
        ];
    }
}
