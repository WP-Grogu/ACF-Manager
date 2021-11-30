<?php

namespace App\Wordpress\Hooks;

use OP\Framework\Wordpress\Hook;
use WordPlate\Acf\ConfigDefaults;
use function \Roots\config;

class AcfGroups extends Hook
{
    /**
     * Event name to hook on.
     */
    public $hook = 'acf/init';


    /**
     * The actions to perform.
     *
     * @return void
     */
    public function execute()
    {
        $this->setDefaults();
        $this->setGroups();
    }


    /**
     * Setup default configuration for ACf fields.
     */
    private function setDefaults()
    {
        $defaults = config('acf.defaults') ?: [];

        ConfigDefaults::push($defaults);
    }


    /**
     * Register field groups into ACF.
     */
    private function setGroups()
    {
        $groups = config('acf.groups') ?: [];

        collect($groups)->each(function ($group) {
            (new $group())->boot();
        });
    }
}
