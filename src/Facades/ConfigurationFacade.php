<?php

namespace RkType\PSModuleEnhancer\Facades;

use Configuration;

/**
 * Class ConfigurationFacade
 * @package RkType\PSModuleEnhancer
 * @method static bool updateValue(string $key, mixed $values, bool $html = false, int $id_shop_group = null, int $id_shop = null)
 * @method static string get(string $key, int $id_lang = null, int $id_shop_group = null, int $id_shop = null)
 * @method static bool deleteByName(string $key)
 */
class ConfigurationFacade extends Configuration
{

}
