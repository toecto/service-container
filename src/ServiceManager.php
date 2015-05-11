<?php

namespace Reactor\ServiceContainer;

class ServiceContainerTree {

    protected $key = 1;
    protected $data = array();
    protected $hierarchy = array();
    protected $parents = array()

    public function __construct() {
        
    }

    public function add($name, $obj, $parent_key = 0) {
        $key = $this->key;
        $this->key++;
        $this->data[$key] = $obj;
        $this->hierarchy[$parent_key][$name] = $key;
        $this->parents[$key] = $parent_key;
        return $key;
    }

    public function findByName($path, $parent_key = 0) {
        $path = (array)$path;
        $key = $parent_key;
        foreach($path as $node) {
            if (isset($this->hierarchy[$key][$node])) {
                $key = $this->hierarchy[$key][$node];
            } else {
                return null;
            }
        }
        return $key;
    }

    public function findParentById($key) {
        return $this->parents[$key];
    }

    public function getByKey($key) {
        return $this->data[$key];
    }

    public function getByName($name, $parent_key = 0) {
        return $this->data[$this->findByName($name, $parent_key)];
    }
    
    public function getParentByKey($key) {
        return $this->data[$this->findParentById($key)];
    }
}


