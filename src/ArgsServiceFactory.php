<?php

namespace Reactor\ServiceContainer;

class ArgsServiceFactory {

    protected $container;
    protected $service_name;

    public function __construct($container, $service_name) {
        $this->container = $container;
        $this->service_name = $service_name;
    }

    public function get($args) {
        return $this->getContainer($args)->get($this->service_name);
    }

    private function getContainer($args) {
        $container = new ServiceContainer();
        $container->setParent($this->container);
        $container->setAll($args);
        return $container;
    }

}
