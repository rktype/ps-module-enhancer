<?php

namespace RkType\PSModuleEnhancer\Services\Reserved;

class ModuleEnhancerRequestService
{

    public function all()
    {
        return is_array($_REQUEST) ? $_REQUEST : [];
    }

    public function has($key)
    {
        $request = $this->all();
        return array_key_exists($key, $request);
    }

    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->all()[$key] : $default;
    }

    public function input($key, $default = null)
    {
        return $this->get($key, $default);
    }

    public function method()
    {
        return array_key_exists('REQUEST_METHOD', $_SERVER) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    }
}
