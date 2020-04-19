<?php

namespace RkType\PSModuleEnhancer\Services\Reserved;

use RkType\PSModuleEnhancer\Facades\ModuleFacade;

class ModuleEnhancerHooksService
{

    /**
     * @var ModuleFacade
     */
    public $module;

    public function __construct(ModuleFacade $module)
    {
        $this->module = $module;
    }

    /**
     * Returns all hooks of the module based on methods name (hook*)
     * @return array
     */
    protected function getModuleHooks()
    {
        $hooks = [];

        foreach (get_class_methods($this->module) as $method) {
            if(preg_match('/^hook(.+)$/', $method, $matches)) {
                $hooks[] = lcfirst($matches[1]);
            }
        }

        return $hooks;
    }

    /**
     * Register all hooks implemented by the module
     * @return bool
     */
    public function registerHooks()
    {
        $results = true;

        foreach ($this->getModuleHooks() as $hook) {
            $results = $results && $this->module->registerHook($hook);
        }

        return $results;

    }
}
