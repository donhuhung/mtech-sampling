<?php

namespace Mtech\Sampling;

use Backend;
use System\Classes\PluginBase;
use BackendMenu;

class Plugin extends PluginBase {

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails() {
        return [
            'name' => 'Sampling',
            'description' => 'No description provided yet...',
            'author' => 'Sampling',
            'icon' => 'icon-leaf'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register() {
        BackendMenu::registerContextSidenavPartial('Mtech.Sampling', 'sampling', '~/plugins/mtech/sampling/partials/_sidenav.htm');
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
        return []; // Remove this line to activate

        return [
            'aura.backend.some_permission' => [
                'tab' => 'Backend',
                'label' => 'Some permission'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation() {
        return [
            'sampling' => [
                'label' => 'Sampling',
                'url' => Backend::url('mtech/sampling/project'),
                'icon' => 'icon-globe',
                'permissions' => ['mtech.sampling.*'],
                'order' => 500,
                'sideMenu' => [
                    'province' => [
                        'label' => 'Provinces',
                        'icon' => 'icon-sitemap',
                        'url' => Backend::url('mtech/sampling/province'),
                        'permissions' => ['mtech.sampling.*'],
                        'counterLabel' => 'General',
                    ],
                    'district' => [
                        'label' => 'Districts',
                        'icon' => 'icon-map-marker',
                        'url' => Backend::url('mtech/sampling/district'),
                        'permissions' => ['mtech.sampling.*'],
                        'counterLabel' => 'General',
                    ],                    
                    'productbrand' => [
                        'label' => 'Product brands',
                        'icon' => 'icon-address-card-o',
                        'url' => Backend::url('mtech/sampling/productbrand'),
                        'permissions' => ['mtech.sampling.*'],
                        'counterLabel' => 'General',
                    ],
                    'categorygift' => [
                        'label' => 'Category Gifts',
                        'icon' => 'icon-gift',
                        'url' => Backend::url('mtech/sampling/categorygift'),
                        'permissions' => ['mtech.sampling.*'],
                        'counterLabel' => 'General',
                    ],
                    'project' => [
                        'label' => 'Projects',
                        'icon' => 'icon-list',
                        'url' => Backend::url('mtech/sampling/project'),
                        'permissions' => ['mtech.sampling.*'],
                        'counterLabel' => 'Sampling',
                    ],
                    'location' => [
                        'label' => 'Locations',
                        'icon' => 'icon-map-marker',
                        'url' => Backend::url('mtech/sampling/location'),
                        'permissions' => ['mtech.sampling.*'],
                        'counterLabel' => 'Sampling',
                    ],
                    'gift' => [
                        'label' => 'Gifts',
                        'icon' => 'icon-gift',
                        'url' => Backend::url('mtech/sampling/gift'),
                        'permissions' => ['mtech.sampling.*'],
                        'counterLabel' => 'Sampling',
                    ],
                    'customer' => [
                        'label' => 'Customer Info',
                        'icon' => 'icon-gift',
                        'url' => Backend::url('mtech/sampling/customer'),
                        'permissions' => ['mtech.sampling.*'],
                        'counterLabel' => 'Report Detail',
                    ],
                    'historypg' => [
                        'label' => 'PG Info',
                        'icon' => 'icon-gift',
                        'url' => Backend::url('mtech/sampling/historypg'),
                        'permissions' => ['mtech.sampling.*'],
                        'counterLabel' => 'Report Detail',
                    ]
                ]
            ],
        ];
    }

}
