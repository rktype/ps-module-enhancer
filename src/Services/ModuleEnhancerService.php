<?php

namespace RkType\PSModuleEnhancer\Services;

use Closure;
use Exception;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use RkType\PSModuleEnhancer\Facades\ModuleFacade;
use RkType\PSModuleEnhancer\Interfaces\ModuleConfigurationPageInterface;
use RkType\PSModuleEnhancer\Interfaces\ModuleCronInterface;
use RkType\PSModuleEnhancer\Services\Reserved\ModuleEnhancerConfigurationService;
use RkType\PSModuleEnhancer\Services\Reserved\ModuleEnhancerDBService;
use RkType\PSModuleEnhancer\Services\Reserved\ModuleEnhancerHooksService;
use RkType\PSModuleEnhancer\Services\Reserved\ModuleEnhancerMailservice;
use RkType\PSModuleEnhancer\Services\Reserved\ModuleEnhancerRequestService;
use RkType\PSModuleEnhancer\Services\Reserved\ModuleEnhancerValidationService;

class ModuleEnhancerService
{

    /**
     * @var ModuleFacade
     */
    protected $module;

    /**
     * @var ModuleEnhancerHooksService
     */
    protected $hook_service;

    /**
     * @var ModuleEnhancerConfigurationService
     */
    protected $config_service;

    protected $custom_validation_rules = [];

    /**
     * ModuleEnhancerService constructor.
     * @param ModuleFacade $module
     */
    public function __construct(ModuleFacade $module)
    {
        $this->module = $module;
        $this->hook_service = new ModuleEnhancerHooksService($module);
        $this->config_service = new ModuleEnhancerConfigurationService($module);
        $this->db_service = new ModuleEnhancerDBService();
    }

    /**
     * @return ModuleFacade
     */
    public function module()
    {
        return $this->module;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getDefaultLang()
    {
        return $this->config_service->getValue('PS_LANG_DEFAULT');
    }

    /**
     * @return false|string
     */
    public function urlToken()
    {
        return substr(_COOKIE_KEY_, 34, 8);
    }

    /**
     * @param string|null $path
     * @return string
     */
    public function getModulePath($path = null)
    {
        return implode('/', array_filter([
            rtrim(_PS_MODULE_DIR_, '/'),
            trim($this->module->name, '/'),
            trim($path, '/')
        ]));
    }



    /**
     * ------------------------------------------------------------------------
     * Module methods
     * ------------------------------------------------------------------------
     */

    /**
     * Automate the registration of the hooks and configuration initialization
     * @return bool
     * @throws Exception
     */
    public function install()
    {
        $this->config_service->resetValues();
        return $this->hook_service->registerHooks();
    }

    /**
     * Remove all configuration values
     * @return bool
     * @throws Exception
     */
    public function uninstall()
    {
        $this->config_service->delValues();
        return true;
    }

    /**
     * @param string $string
     * @param bool|string $specific
     * @return string
     */
    public function l($string, $specific = false)
    {
        return $this->module->l($string, $specific);
    }

    /**
     * @param $error
     * @return string
     */
    public function displayError($error)
    {
        return $this->module->displayError($error);
    }

    /**
     * @param $warning
     * @return string
     */
    public function displayWarning($warning)
    {
        return $this->module->displayWarning($warning);
    }

    /**
     * @param $string
     * @return string
     */
    public function displayConfirmation($string)
    {
        return $this->module->displayConfirmation($string);
    }



    /*
     * ------------------------------------------------------------------------
     * Configuration methods
     * ------------------------------------------------------------------------
     */

    /**
     * @param array $values Key value array
     */
    public function initConfigValues($values)
    {
        $this->config_service->initValues($values);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getConfigValue($key)
    {
        return $this->config_service->getValue($key);
    }


    /**
     * @return array
     */
    public function getConfigValues()
    {
        return $this->config_service->getValues();
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     * @throws Exception
     */
    public function setConfigValue($key, $value)
    {
        return $this->config_service->setValue($key, $value);
    }

    /**
     * @param $values
     * @return bool
     * @throws Exception
     */
    public function setConfigValues($values)
    {
        return $this->config_service->setValues($values);
    }



    /**
     * ------------------------------------------------------------------------
     * Contents
     * ------------------------------------------------------------------------
     */

    /**
     * @param string $configuration_page_class
     * @return string
     * @throws Exception
     */
    public function getContent($configuration_page_class)
    {
        if (!in_array(ModuleConfigurationPageInterface::class, class_implements($configuration_page_class))) {
            throw new Exception("The class '{$configuration_page_class}' must implement '" . ModuleConfigurationPageInterface::class . "' interface");
        }

        /** @var ModuleConfigurationPageInterface $configuration_page */
        $configuration_page = new $configuration_page_class($this);

        return $configuration_page->postProcess() . $configuration_page->getContent();
    }



    /**
     * ------------------------------------------------------------------------
     * Validation
     * ------------------------------------------------------------------------
     */


    /**
     * @param string $rule_name
     * @param Closure $closure
     */
    public function addValidationRule($rule_name, $closure)
    {
        $this->custom_validation_rules[$rule_name] = $closure;
    }

    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @return ModuleEnhancerValidationService
     * @throws Exception
     */
    public function validate($data, $rules, $messages = [])
    {
        $validator = new ModuleEnhancerValidationService();
        $validator->setCustomValidationRules($this->custom_validation_rules);
        return $validator->validate($data, $rules, $messages);
    }



    /**
     * ------------------------------------------------------------------------
     * Request data
     * ------------------------------------------------------------------------
     */

    /**
     * @return ModuleEnhancerRequestService
     */
    public function request()
    {
        return new ModuleEnhancerRequestService;
    }

    /**
     * @param $submit_action
     * @return bool
     */
    public function isSubmit($submit_action)
    {
        return $this->request()->method() === 'POST' && $this->request()->has($submit_action);
    }

    /**
     * @param string $param
     * @return bool
     */
    public function hasValidToken($param = 'token')
    {
        return $this->urlToken() === $this->request()->input($param);
    }



    /**
     * ------------------------------------------------------------------------
     * Cron
     * ------------------------------------------------------------------------
     */

    /**
     * @param $cron_class
     * @return int
     * @throws Exception
     */
    public function runCron($cron_class)
    {
        if (!in_array(ModuleCronInterface::class, class_implements($cron_class))) {
            throw new Exception("The class '{$cron_class}' must implement '" . ModuleCronInterface::class . "' interface");
        }

        if(!$this->module->active || !$this->hasValidToken()) {
            return 1;
        }

        return (new $cron_class($this))->run();
    }



    /**
     * ------------------------------------------------------------------------
     * Database
     * ------------------------------------------------------------------------
     */

    /**
     * @return Builder
     */
    public function query()
    {
        return $this->db_service->query();
    }



    /**
     * ------------------------------------------------------------------------
     * Mail
     * ------------------------------------------------------------------------
     */

    /**
     * @return ModuleEnhancerMailservice
     * @throws Exception
     */
    public function mail()
    {
        return (new ModuleEnhancerMailservice)
            ->idLang($this->getDefaultLang());
    }

}
