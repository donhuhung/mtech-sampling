<?php namespace Mtech\Api;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
   /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails() {
        return [
            'name' => 'Api Plugin',
            'description' => 'Provides some api of Mtech.',
            'author' => 'Mtech Corporation',
            'icon' => 'icon-link'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register() {
        
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot() {
        
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents() {
        return [];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions() {
        return [
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation() {
        return [];
    }
}
