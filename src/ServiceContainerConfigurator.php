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
        $config = $this->parseServiceConfig($config);

        $service = new ServiceProvider();

        if (isset($config['scenario'])) {
            $this->loadScenario($service, $config['scenario']);
        } else {
            if (!isset($config['create'])) {
                throw new Exceptions\ServiceConfigurationExeption("Nothing to create ".var_export($config, true));
            }
            if (!isset($config['arguments'])) {
                $config['arguments'] = array();
            }
            $service->createScenario($config['create'], $config['arguments']);
        }

        if (isset($config['shared'])) {
            $service->shared($config['shared']);
        }

        return $service;
    }

    protected function loadScenario($service, $scenario) {
        foreach ($scenario as $step) {
            if (!isset($step['arguments'])) {
                $step['arguments'] = array();
            }
            if (isset($step['create'])) {
                $service->createScenario($step['create'], $step['arguments']);
            } elseif (isset($step['factory'])) {
                $service->addFactory($step['factory'], $step['arguments']);
            } elseif (isset($step['call'])) {
                $service->addCall($step['call'], $step['arguments']);
            } elseif (isset($step['configurator'])) {
                $service->addConfigurator($step['configurator'], $step['arguments']);
            } else {
                throw new Exceptions\ServiceConfigurationExeption("Cannot parse step ".var_export($step, true));
            }
        }
    }

    protected function parseServiceConfig($config) {
        $data = array();
        foreach($config as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->parseServiceConfig($value);
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
