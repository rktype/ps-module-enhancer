<?php

namespace RkType\PSModuleEnhancer\Interfaces;

use RkType\PSModuleEnhancer\Services\ModuleEnhancerService;

interface ModuleConfigurationPageInterface
{
    public function __construct(ModuleEnhancerService $service);
    public function getContent();
    public function postProcess();
}
