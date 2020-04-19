<?php

namespace RkType\PSModuleEnhancer\Facades;

use Module;

/**
 * Class ModuleFacade
 * @package RkType\PSModuleEnhancer
 * @property string $name
 * @property string $tab
 * @property string $version
 * @property string $author
 * @property int $need_instance
 * @property array $ps_versions_compliancy
 * @property string $bootstrap
 * @property string $displayName
 * @property string $description
 * @property string $confirmUninstall
 * @property string $warning
 * @property bool $active
 * @method bool registerHook(string $hook_name, array $shop_list = null)
 * @method string l(string $string, bool|string $specific = false)
 * @method string displayError(string $error)
 * @method string displayWarning(string $warning)
 * @method string displayConfirmation(string $string)
 */
class ModuleFacade extends Module
{

}
