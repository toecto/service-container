<?php 

namespace Reactor\ServiceContainer;

use \Reactor\ServiceContainer\Exceptions\ServiceNotFoundExeption;

class ServiceScope extends ServiceContainer {
    
    private $parent_scope = null;

    public function __construct($parent = null) {
        $this->setParentScope($parent);
    }

    public function setParentScope($parent) {
        $this->parent_scope = $parent;
    }

    public function addScope($id, $scope = null) {
        return $this->set($id, $scope ?: new ServiceScope($this));
    }

    public function get($service_name) {
        if (parent::has($service_name)) {
            return parent::get($service_name);    
        }
        if ($this->parent_scope) {
            return $this->parent_scope->get($service_name);
        }
        throw new ServiceNotFoundExeption('Service "'.$service_name.'" not found');
    }

    public function has($service_name) {
        if (parent::has($service_name)) {
            return true;
        }
        if ($this->parent_scope) {
            return $this->parent_scope->has($service_name);
        }
        return false;
    }

    public function open($path) {
        $node = $this;
        while($node->parent != null) {
            $node = $node->parent;
        }
        foreach($path as $name) {
            $node = $node->get($name);
        }
        return $node;
    }

}
