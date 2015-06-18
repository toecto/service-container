<?php

namespace Reactor\ServiceContainer;

class ValueContainer {

    protected $data = array();
    protected $parent = null;

    public function setAll($data) {
        $this->data = $data;
    }

    public function setParent($parent) {
        $this->parent = $parent;
    }

    public function has($name) {
        if (isset($this->data[$name])) {
            return true;
        }
        return $this->parent !== null && $this->parent->has($name);
    }

    public function set($name, $value = null) {
        return $this->data[$name] = $value;
    }

    public function getValue($name) {
        if (!isset($this->data[$name])) {
            if ($this->parent !== null) {
                return $this->parent->getValue($name);
            }
            return null;
        }
        return $this->data[$name];
    }

    public function get($name) {
        return $this->getValue($name);
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
