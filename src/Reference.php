<?php

namespace Reactor\ServiceContainer;

class Reference implements ServiceProviderInterface {

    protected $name;
    protected $loading = false;

    public function __construct($name = null) {
        $this->name = $name;
    }

    public function get($container) {
        if ($this->loading) {
            throw new Exceptions\CircularReferenceExeption(implode("-", (array)$this->name));
        }
        $this->loading = true;

        if (empty($this->name)) {
            $val = $container;
        } else {
            $val = $container->get($this->name);
        }

        $this->loading = false;
        return $val;
    }

}
