<?php

namespace Reactor\ServiceContainer;

use \Reactor\ServiceContainer\Exceptions\EntityNotFoundExeption;

class ValueContainer {
    
    protected $parent = null;
    protected $values = array();

    public function setValue($name, $value) {
        return $this->values[$name] = $value;
    }

    public function setValues($values) {
        $this->values = $values;
    }

    public function getValue($name) {
        if (isset($this->values[$name])) {
            return $this->getOwnValue($name);
        }

        if ($this->parent) {
            return $this->parent->getValue($name);
        }

        throw new EntityNotFoundExeption($name);
    }

    protected function getOwnValue($name) {
        return $this->values[$name];
    }

    public function hasValue($name) {
        if (isset($this->values[$name])) {
            return true;
        }

        if ($this->parent) {
            return $this->parent->hasValue($name);
        }

        return false;
    }

    public function resolveReferences($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->resolveReferences($value);
            }
        } elseif (is_object($data)) {
            if (is_a($data, 'Reactor\\ServiceContainer\\Reference')) {
                $data = $data->resolve($this);
            }
        }
        return $data;
    }

    public function setParent(ValueContainer $parent) {
        $this->parent = $parent;
    }

    public function openPath($path) {
        $node = $this;
        while($node->parent != null) {
            $node = $node->parent;
        }
        foreach($path as $name) {
            $node = $node[$name];
        }
        return $node;
    }

    public function __get($name) {
        return $this->getValue($name);
    }

}
