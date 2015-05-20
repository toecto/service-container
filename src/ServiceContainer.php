<?php

namespace Reactor\ServiceContainer;


class ServiceContainer {

    protected $data = array();

    public function setAll($data) {
        $this->data = $data;
    }

    public function has($name) {
        return isset($this->data[$name]);
    }

    public function createService($name, $value = null) {
        if (is_a($value, 'Reactor\\ServiceContainer\\ServiceProviderInterface')) {
            return $this->data[$name] = $value;
        }
        return $this->data[$name] = new ServiceProvider($value);
    }
    public function set($name, $value = null) {
        return $this->data[$name] = $value;
    }

    public function get($name) {
        if (!isset($this->data[$name])) {
            throw new Exceptions\ServiceNotFoundExeption($name);
        }
        $value = $this->data[$name];
        if (is_a($value, 'Reactor\\ServiceContainer\\ServiceProviderInterface')) {
            return $value->get($this);
        }
        return $value;
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

}
