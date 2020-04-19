<?php

namespace RkType\PSModuleEnhancer\Services\Reserved;

use Exception;
use RkType\PSModuleEnhancer\Facades\ConfigurationFacade;
use RkType\PSModuleEnhancer\Facades\ModuleFacade;

class ModuleEnhancerConfigurationService
{

    /**
     * @var ModuleFacade
     */
    public $module;

    protected $values = [];

    public function __construct(ModuleFacade $module)
    {
        $this->module = $module;
    }

    public function initValues($values)
    {
        foreach ($values as $key => $value) {
            $this->initValue($key, $value);
        }
    }

    public function initValue($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * @throws Exception
     */
    public function resetValues()
    {
        $this->setValues($this->values);
    }

    /**
     * @param $key
     * @param $value
     * @throws Exception
     */
    public function setValue($key, $value)
    {
        if(!array_key_exists($key, $this->values)) {
            throw new Exception("No '{$key}' value initialized");
        }

        return ConfigurationFacade::updateValue($key, $value);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getValue($key)
    {
        return ConfigurationFacade::get($key);
    }

    /**
     * @param $key
     * @throws Exception
     */
    public function delValue($key)
    {
        if(!array_key_exists($key, $this->values)) {
            throw new Exception("No '{$key}' value initialized");
        }

        ConfigurationFacade::deleteByName($key);
    }

    /**
     * @param null $keys
     * @return array
     */
    public function getValues($keys = null)
    {
        $results = [];

        foreach (array_keys($this->values) as $key) {
            if(is_null($keys) || in_array($key, $keys)){
                $results[$key] = $this->getValue($key);
            }
        }

        return $results;
    }

    /**
     * @param $values
     * @return bool
     * @throws Exception
     */
    public function setValues($values)
    {
        $results = true;
        foreach ($values as $key => $value) {
            $results = $results && $this->setValue($key, $value);
        }
        return $results;
    }

    /**
     * @param null $keys
     * @throws Exception
     */
    public function delValues($keys = null)
    {
        foreach (array_keys($this->values) as $key) {
            if(is_null($keys) || in_array($key, $keys)){
                $this->delValue($key);
            }
        }
    }


}
