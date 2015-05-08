<?php

namespace Reactor\ServiceContainer;

class ServiceProvider implements ServiceProviderInterface {

    protected $arguments = array();
    protected $igniter = null;
    protected $factory = null;
    protected $factory_arguments = array();
    protected $shared = false;
    protected $instance = null;

    public function __construct($igniter = null, $arguments = array()) {
        $this->igniter = $igniter;  
        $this->setArguments($arguments);
    }

    public function addArgument($value) {
        $this->arguments[] = $value;
        return $this;
    }

    public function setArguments($arguments) {
        $this->arguments = $arguments;
        return $this;
    }

    public function addFactoryArgument($value) {
        $this->factory_arguments[] = $value;
        return $this;
    }

    public function setFactoryArguments($factory_arguments) {
        $this->factory_arguments = $factory_arguments;
        return $this;
    }

    public function shared($flag = true) {
        $this->shared = (bool) $flag;
        return $this;
    }

    public function isShared() {
        return $this->shared;
    }

    public function factory($factory, $factory_arguments = array()) {
        $this->factory = $factory;
        $this->setFactoryArguments($factory_arguments);
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
        $igniter = null;
        if ($this->igniter) {
            $arguments = $this->resolveReferences($container, $this->arguments);
            if (is_callable($this->igniter)) {
                $igniter = call_user_func_array($this->igniter, $arguments);
            } elseif (is_string($this->igniter)) {
                $class_reflection = new \ReflectionClass($this->igniter);
                $igniter = $class_reflection->newInstanceArgs($arguments);
            } else {
                $igniter = $this->resolveReferences($container, $this->igniter);
            }
        }

        if ($this->factory) {
            $factory_arguments = $this->resolveReferences($container, $this->factory_arguments);
            if ($igniter) {
                return call_user_func_array(array($igniter, $this->factory), $factory_arguments);
            } else {
                return call_user_func_array($this->factory, $factory_arguments);
            }
        }

        return $igniter;
    }

    public function resolveReferences($container, $data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->resolveReferences($container, $value);
            }
        } elseif (is_object($data)) {
            if (is_a($data, 'Reactor\\ServiceContainer\\Reference')) {
                $data = $data->resolve($container);
            }
        }
        return $data;
    }

}
