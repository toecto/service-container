<?php

namespace Reactor\ServiceContainer;

use \Reactor\ServiceContainer\Exceptions\CircularReferenceExeption;

class ServiceContainer extends ValueContainer {

    protected $loading = array();

    public function addService($name, $value = null) {
        if (is_a($value, 'Reactor\\ServiceContainer\\ServiceProvider')) {
            return $this->values[$name] = $value;
        }
        return $this->values[$name] = new ServiceProvider($value);
    }

    public function get($name) {
        $value = parent::get($name);
        if (is_a($value, 'Reactor\\ServiceContainer\\ServiceProvider')) {
            if (isset($this->loading[$name])) {
                throw new CircularReferenceExeption($name);
            }

            $this->loading[$name] = true;
            $value = $value->get($this);
            unset($this->loading[$name]);
        }
        return $value;
    }

}
