<?php

namespace Grogu\Acf\Core;

use Grogu\Acf\Core\Config;
use WordPlate\Acf\ConfigDefaults;

/**
 * The package bootloader.
 *
 * @package wp-grogu/acf-manager
 * @author Thomas <thomas@hydrat.agency>
 */
final class Bootloader
{
    /**
     * @var Config
     */
    private $conf;

    /**
     * Initiate the booting hook.
     */
    public function __construct()
    {
        add_action('acf/init', [$this, 'boot']);
    }

    /**
     * Boot the package.
     */
    public function boot()
    {
        $this->conf = Config::getInstance();

        $this->setDefaults();
        $this->setGroups();
    }

    /**
     * Setup default configuration for ACf fields.
     */
    private function setDefaults()
    {
        $defaults = $this->conf->get('acf.defaults') ?: [];

        ConfigDefaults::push($defaults);
    }

    /**
     * Register field groups into ACF.
     */
    private function setGroups()
    {
        $groups = $this->conf->get('acf.groups') ?: [];

        collect($groups)->each(function ($group) {
            (new $group())->boot();
        });
    }
}
