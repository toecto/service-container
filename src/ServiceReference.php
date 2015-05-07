<?php

namespace Reactor\ServiceContainer;

class ServiceReference extends ValueReference {

    public function resolve($container) {
        if ($this->path) {
            $container = $container->openPath($this->path);
        } 
        if (!$this->name) {
            return $container;
        }
        return $container->getService($this->name);
    }

}
