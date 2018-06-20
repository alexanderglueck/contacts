<?php

namespace App;

use ScoutElastic\Migratable;
use ScoutElastic\IndexConfigurator;

class ContactIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    /**
     * @var array
     */
    protected $settings = [
        //
    ];
}
