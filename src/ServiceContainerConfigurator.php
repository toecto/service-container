<?php

namespace Reactor\ServiceContainer;

class ServiceContainerConfigurator {
    protected $container;

    public function __construct($container) {
        $this->container = $container;
    }

    public function load($config) {
        foreach($config['parameters'] as $name => $value) {
            $this->container->set($name, $value);
        }
        foreach($config['services'] as $name => $service_config) {
            $this->container->set($name, $this->createProvider($service_config));
        }
    }

    public function createProvider($config) {
        $config = $this->normalizeServiceConfig($config);

        if (!isset($config['igniter'])) {
            $config['igniter'] = null;
        }
        $service = new ServiceProvider($config['igniter']);

        if (isset($config['arguments'])) {
            $service->setArguments($config['arguments']);
        }

        if (isset($config['factory'])) {
            $service->setFactory($config['factory']);
            if (isset($config['factory_arguments'])) {
                $service->setFactoryArguments($config['factory_arguments']);
            }
        }

        if (isset($config['shared'])) {
            $service->shared($config['shared']);
        }

        return $service;
    }

    protected function normalizeServiceConfig($config) {
        $data = array();
        foreach($config as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->normalizeServiceConfig($value);
            } else {
                if (is_string($value)) {
                    $data[$key] = $this->handleValue($value);
                } else {
                    $data[$key] = $value;
                }
            }
        }
        return $data;
    }

    protected function handleValue($value) {
        $start = $value[0];
        $stop = substr($value, -1, 1);
        $inner_value = substr($value, 1, -1);
        if ($start == '%' && $start == $stop) {
            return new Reference($inner_value);
        }
        if ($start == '$' && $start == $stop) {
            return getenv($inner_value);
        }
        return $value;
    }

}
