<?php

namespace Reactor\ServiceContainer;

class CachedArgsServiceFactory extends ArgsServiceFactory {

    protected $cache = array();

    public function get($args) {
        $hash = $this->getHash($args);

        if (!isset($this->cache[$hash])) {
            $this->cache[$hash] = parent::get($args);
        }

        return $this->cache[$hash];
    }

    private function getHash($args) {
        return md5(serialize($args));
    }

}
