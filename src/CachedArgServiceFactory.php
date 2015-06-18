<?php

namespace Reactor\ServiceContainer;

class CachedArgServiceFactory extends ArgsServiceFactory {

    protected $cache = array();

    public function get($argument) {
        if (!isset($this->cache[$argument])) {
            $this->cache[$argument] = parent::get(array('dynamic-argument' => $argument));
        }

        return $this->cache[$argument];
    }

}
