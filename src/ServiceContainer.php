<?php

namespace Reactor\ServiceContainer;


class ServiceContainer extends ValueContainer {

    public function createService($name, $value = null) {
        if (is_a($value, 'Reactor\\ServiceContainer\\ServiceProviderInterface')) {
            $value = new ServiceProvider($value);
        }
        return $this->data[$name] = $value;
    }

    public function get($name) {
        $value = $this->getValue($name);
        if ($value === null) {
            throw new Exceptions\ServiceNotFoundExeption($name);
        }
        if (is_a($value, 'Reactor\\ServiceContainer\\ServiceProviderInterface')) {
            return $value->get($this);
        }
        return $value;
    }

}
