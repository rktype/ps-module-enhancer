<?php

namespace RkType\PSModuleEnhancer\Services\Reserved;

use Illuminate\Database\Capsule\Manager as Capsule;

class ModuleEnhancerDBService
{
    /**
     * @var Capsule
     */
    private $capsule;

    public function __construct()
    {
        $this->capsule = new Capsule;

        $this->capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => _DB_SERVER_,
            'database'  => _DB_NAME_,
            'username'  => _DB_USER_,
            'password'  => _DB_PASSWD_,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => _DB_PREFIX_,
        ]);
    }

    public function query()
    {
        return $this->capsule->getConnection()->query();
    }
}
