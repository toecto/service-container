<?php

namespace Reactor\ServiceContainer;

use \Reactor\ServiceContainer\Exceptions\EntityNotFoundExeption;

class ValueContainer {
    
    protected $values = array();

    public function set($name, $value) {
        return $this->values[$name] = $value;
    }

    public function setAll($values) {
        $this->values = $values;
    }

    public function get($name) {
        return $this->values[$name];
    }

    public function has($name) {
        return isset($this->values[$name]);
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function __set($name, $value) {
        return $this->set($name, $value);
    }

    public function __isset($name) {
        return $this->has($name);
    }

    public function open($path) {
        $node = $this;
        foreach($path as $name) {
            $node = $node->get($name);
        }
        return $node;
    }

}
