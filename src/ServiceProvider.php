<?php

namespace Reactor\ServiceContainer;

class ServiceProvider implements ServiceProviderInterface {

    protected $arguments = array();
    protected $igniter = null;
    protected $factory_method = null;
    protected $shared = true;
    protected $instance = null;

    public function __construct($igniter = null) {
        $this->igniter = $igniter;  
    }

    public function addArgument($value) {
        $this->arguments[] = $value;
        return $this;
    }

    public function setArguments($values) {
        $this->arguments = $values;
        return $this;
    }

    public function setShared($flag) {
        $this->shared = (bool) $flag;
        return $this;
    }

    public function isShared() {
        return $this->shared;
    }

    public function setFactoryMethod($factory_method) {
        $this->factory_method = $factory_method;
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
        $arguments = $container->resolveReferences($this->arguments);
        
        $igniter = null;
        if ($this->igniter) {
            if (is_callable($this->igniter)) {
                $igniter = call_user_func_array($this->igniter, $arguments);
            } elseif (is_string($this->igniter)) {
                $class_reflection = new \ReflectionClass($this->igniter);
                $igniter = $class_reflection->newInstanceArgs($arguments);
            } else {
                $igniter = $container->resolveReferences($this->igniter);
            }
        }

        if ($this->factory_method) {
            if ($igniter) {
                return call_user_func_array(array($igniter, $this->factory_method), $arguments);
            } else {
                return call_user_func_array($this->factory_method, $arguments);
            }
        }

        return $igniter;
    }

}
