<?php

namespace Reactor\ServiceContainer;

class ServiceProvider implements ServiceProviderInterface {

    protected $scenario = array();
    protected $shared = false;
    protected $instance = null;

    public function __construct($igniter = null, $arguments = array()) {
        $this->createScenario($igniter, $arguments);
    }

    public function shared($flag = true) {
        $this->shared = (bool) $flag;
        return $this;
    }

    public function isShared() {
        return $this->shared;
    }

    public function createScenario($igniter = null, $arguments = array()) {
        $this->scenario = array();
        $this->scenario[] = array(
            'type' => 'create',
            'igniter' => $igniter,
            'arguments' => $arguments,
        );
        return $this;
    }

    public function addFactory($factory, $arguments = array()) {
        $this->scenario[] = array(
            'type' => 'factory',
            'igniter' => $factory,
            'arguments' => $arguments,
        );
        return $this;
    }

    public function addCall($method_name, $arguments = array()) {
        $this->scenario[] = array(
            'type' => 'call',
            'igniter' => $method_name,
            'arguments' => $arguments,
        );
        return $this;
    }

    public function addConfigurator($configurator, $arguments = array()) {
        $this->scenario[] = array(
            'type' => 'configurator',
            'igniter' => $configurator,
            'arguments' => $arguments,
        );
        return $this;
    }

    public function get($container) {
        if ($this->instance) {
            return $this->instance;
        }
        $instance = $this->createInstance($container);
        if ($this->shared) {
            $this->instance = $instance;
        }
        return $instance;
    }

    public function createInstance($container) {
        $scenario = $this->resolveReferences($this->scenario, $container);
        $instance = null;
        foreach($scenario as $step) {
            $instance = call_user_func_array(array($this, 'step_'.$step['type']), array($instance, $step['igniter'], $step['arguments']));
        }

        return $instance;
    }

    public function resolveReferences($data, $container) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->resolveReferences($value, $container);
            }
        } elseif (is_object($data)) {
            if (is_a($data, 'Reactor\\ServiceContainer\\Reference')) {
                $data = $data->get($container);
            }
        }
        return $data;
    }

    protected function step_create($instance, $igniter, $arguments) {
        if (is_callable($igniter)) {
            $instance = call_user_func_array($igniter, $arguments);
        } elseif (is_string($igniter)) {
            $class_reflection = new \ReflectionClass($igniter);
            $instance = $class_reflection->newInstanceArgs($arguments);
        } else {
            $instance = $igniter;
        }
        return $instance;
    }

    protected function step_factory($instance, $igniter, $arguments) {
        if (is_object($instance)) {
            return call_user_func_array(array($instance, $igniter), $arguments);
        } else {
            return call_user_func_array($igniter, $arguments);
        }
    }

    protected function step_call($instance, $igniter, $arguments) {
        call_user_func_array(array($instance, $igniter), $arguments);
        return $instance;
    }

    protected function step_configurator($instance, $igniter, $arguments) {
        call_user_func_array($igniter, array_merge(array($instance), $arguments));
        return $instance;
    }

}
