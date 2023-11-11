<?php

namespace Grogu\Acf\Core;

use Grogu\Acf\Core\Config;
use Illuminate\Support\Arr;
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
        $this->conf = Config::getInstance();

        if (!function_exists('add_action')) {
            return;
        }

        # Load defaults, groups, blocks
        add_action('acf/init', [$this, 'boot']);

        # Register editor parameters
        add_action('block_categories_all', [$this, 'blockCategories'], 10, 2);
        add_action('after_setup_theme', [$this, 'editorStyles']);
        add_action('current_screen', [$this, 'editorStylesPostType']);
    }

    /**
     * Boot the package.
     */
    public function boot()
    {
        if (!function_exists('register_field_group')) {
            add_action(
                'admin_notices',
                fn () => sprintf('<div class="error notice"><p>%s</p></div>', __('ACF Manager: Please install ACF plugin to use this package.', 'acf-manager'))
            );
            return;
        }

        $this->setDefaults();
        $this->setGroups();
        $this->setBlocks();
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
    
    /**
     * Register Gutemberg Blocks into ACF.
     */
    private function setBlocks()
    {
        $blocks = $this->conf->get('acf.blocks') ?: [];

        collect($blocks)->each(function ($block) {
            (new $block())->compose();
        });
    }

    /**
     * Register gutemberg block categories.
     *
     * @return void
     */
    public function blockCategories($categories, $post)
    {
        $cats = $this->conf->get('acf.gutemberg.categories') ?: [];

        return array_merge(
            $categories,
            array_map(fn ($title, $slug) => compact('title', 'slug'), $cats),
        );
    }
    
    /**
     * Load editor styles.
     *
     * @return void
     */
    public function editorStyles()
    {
        $stylesheet = $this->conf->get('acf.gutemberg.stylesheets.*') ?: '';
        
        if ($stylesheet) {
            add_theme_support('editor-styles');
            add_editor_style(trim($stylesheet, '/'));
        }
    }
    
    /**
     * Load editor styles for a specific post type.
     *
     * @return void
     */
    public function editorStylesPostType()
    {
        global $pagenow, $current_screen;

        $stylesheets = Arr::except($this->conf->get('acf.gutemberg.stylesheets') ?: [], ['*']);
        
        # Current post-type
        $post_type = $current_screen->post_type ?: '';

        # Available custom post_types
        $cpt = array_values(get_post_types(['public' => true, '_builtin' => false]));

        # Append default post types
        $post_types = array_merge($cpt, [
            'page',
            'post',
        ]);

        if (in_array($post_type, $post_types)
                && ('post-new.php' === $pagenow || 'post.php' === $pagenow)
                && ($stylesheet = Arr::get($stylesheets, $post_type))
        ) {
            add_editor_style($stylesheet);
        }
    }
}
