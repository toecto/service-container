<?php

namespace Reactor\ServiceContainer;

class Reference {

    protected $name;
    protected $loading = false;

    public function __construct($name = null) {
        $this->name = $name;
    }

    public function resolve($container) {
        if ($this->loading) {
            throw new CircularReferenceExeption(implode("-", (array)$this->name));
        }
        $this->loading = true;

        $val = $container->get($this->name);

        $this->loading = false;
        return $val;
    }

}
