<?php

namespace RkType\PSModuleEnhancer\Interfaces;

use RkType\PSModuleEnhancer\Services\ModuleEnhancerService;

interface ModuleCronInterface
{
    public function __construct(ModuleEnhancerService $service);
    public function run();
}
