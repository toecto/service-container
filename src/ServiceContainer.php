<?php

namespace Reactor\ServiceContainer;

use \Reactor\ServiceContainer\Exceptions\CircularReferenceExeption;

class ServiceContainer extends ValueContainer {

    protected $loading = array();

    public function setService($name, $value = null) {
        if ($value === null) {
            return $this->values[$name] = new ServiceProvider();
        }
        if (is_a($value, 'Reactor\\ServiceContainer\\ServiceProvider')) {
            return $this->values[$name] = $value;
        }
        return $this->values[$name] = new ServiceProvider($value);
    }

    public function getOwnValue($name) {
        $value = $this->values[$name];
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

    public function __get($name) {
        return $this->getService($name);
    }

}
