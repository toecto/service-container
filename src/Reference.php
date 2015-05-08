<?php

namespace Reactor\ServiceContainer;

class Reference {

    protected $name;
    protected $path = null;

    public function __construct($name = null, $path = null) {
        $this->name = $name;
        $this->path = $path;
    }

    public function resolve($container) {
        if ($this->path) {
            $container = $container->open($this->path);
        } 
        if (!$this->name) {
            return $container;
        }
        return $container->get($this->name);
    }

}
