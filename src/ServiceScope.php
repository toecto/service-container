<?php 

namespace Reactor\ServiceContainer;

use \Reactor\ServiceContainer\Exceptions\ServiceNotFoundExeption;

class ServiceScope extends ServiceContainer {
    
    private $parent_scope = null;
    private $sub_scopes = array();

    public function __construct($parent = null) {
        $this->setParentScope($parent);
    }

    public function addScope($id, $scope = null) {
        $this->sub_scopes[$id] = $scope ?: new ServiceScope($this);
        return $this->sub_scopes[$id];
    }

    public function getService($service_name) {
        $scope = $this;
        $service = $scope->getOwnService($service_name);
        while (!$service) {
            $scope = $this->parent_scope;
            if (!$scope) {
                throw new ServiceNotFoundExeption('Service "'.$service_name.'" not found');
            }
            $service = $scope->getOwnService($service_name);
        }
        return $service;
    }

    public function getOwnService($service_name) {
        return isset($this->services[$service_name]) ? $this->services[$service_name] : null;
    }

    public function getParentScope() {
        return $this->parent_scope;
    }

}
