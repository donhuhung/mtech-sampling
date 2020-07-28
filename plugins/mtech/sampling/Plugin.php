<?php

namespace Mtech\Sampling;

use Backend;
use System\Classes\PluginBase;
use BackendMenu;
use Event;
use Mail;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Controllers\Users as UserController;

class Plugin extends PluginBase {

    private $isNew = false;
    private $sendEmail = false;

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
        //Extend user Model
        UserModel::extend(function($model) {
            $model->belongsToMany['locations'] = [
                'Mtech\Sampling\Models\Locations',
                'table' => 'mtech_sampling_user_location',
                'key' => 'user_id',
                'otherKey' => 'location_id'
            ];
        });

        //Extend Form Fields
        UserController::extendFormFields(function($form, $model, $context) {
            if (!$model instanceof UserModel)
                return;
            //Remove Another Fields
            $form->removeField('groups');
            $form->addTabFields([
                'address' => [
                    'label' => 'Address',
                    'type' => 'text',
                    'tab' => 'rainlab.user::lang.user.account',
                    'span' => 'auto',
                ],
                'phone' => [
                    'label' => 'Phone',
                    'type' => 'text',
                    'tab' => 'rainlab.user::lang.user.account',
                    'span' => 'auto',
                ],
                'gender' => [
                    'label' => 'Gender',
                    'type' => 'dropdown',
                    'options' => [
                        '1' => 'Male',
                        '2' => 'Female'
                    ],
                    'tab' => 'rainlab.user::lang.user.account',
                    'span' => 'auto',
                ],
                'locations' => [
                    'label' => 'Locations',
                    'type' => 'relation',
                    'select' => 'location_name',
                    'tab' => 'rainlab.user::lang.user.account',
                    'span' => 'left',
                ]
            ]);
        });

        UserModel::saved(function($model) {
            if (!$this->sendEmail) {
                if ($model->is_activated == 1) {
                    $this->sendEmail = true;
                    $new_password = $model->reset_password_code;
                    $params = [
                        'name' => $model->name,
                        'new_password' => $new_password
                    ];
                    Mail::sendTo($model->email, 'mtech.api::mail.resetpassword', $params);
                }
            }
        });
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
                        'permissions' => ['mtech.sampling.province'],
                        'counterLabel' => 'General',
                    ],
                    'district' => [
                        'label' => 'Districts',
                        'icon' => 'icon-map-marker',
                        'url' => Backend::url('mtech/sampling/district'),
                        'permissions' => ['mtech.sampling.district'],
                        'counterLabel' => 'General',
                    ],
                    'productbrand' => [
                        'label' => 'Product brands',
                        'icon' => 'icon-address-card-o',
                        'url' => Backend::url('mtech/sampling/productbrand'),
                        'permissions' => ['mtech.sampling.productbrand'],
                        'counterLabel' => 'General',
                    ],
                    'categorygift' => [
                        'label' => 'Category Gifts',
                        'icon' => 'icon-gift',
                        'url' => Backend::url('mtech/sampling/categorygift'),
                        'permissions' => ['mtech.sampling.categorygift'],
                        'counterLabel' => 'General',
                    ],
                    'productsampling' => [
                        'label' => 'Product Sampling',
                        'icon' => 'icon-newspaper-o',
                        'url' => Backend::url('mtech/sampling/productsampling'),
                        'permissions' => ['mtech.sampling.productsampling'],
                        'counterLabel' => 'General',
                    ],
                    'project' => [
                        'label' => 'Projects',
                        'icon' => 'icon-list',
                        'url' => Backend::url('mtech/sampling/project'),
                        'permissions' => ['mtech.sampling.project'],
                        'counterLabel' => 'Sampling',
                    ],
                    'location' => [
                        'label' => 'Locations',
                        'icon' => 'icon-map-marker',
                        'url' => Backend::url('mtech/sampling/location'),
                        'permissions' => ['mtech.sampling.location'],
                        'counterLabel' => 'Sampling',
                    ],
                    'gift' => [
                        'label' => 'Gifts',
                        'icon' => 'icon-gift',
                        'url' => Backend::url('mtech/sampling/gift'),
                        'permissions' => ['mtech.sampling.gift'],
                        'counterLabel' => 'Sampling',
                    ],
                    'customergift' => [
                        'label' => 'Customer Info',
                        'icon' => 'icon-address-card-o',
                        'url' => Backend::url('mtech/sampling/customergift'),
                        'permissions' => ['mtech.sampling.customer'],
                        'counterLabel' => 'Report Detail',
                    ],
                    'historypg' => [
                        'label' => 'PG Info',
                        'icon' => 'icon-address-card-o',
                        'url' => Backend::url('mtech/sampling/historypg'),
                        'permissions' => ['mtech.sampling.historypg'],
                        'counterLabel' => 'Report Detail',
                    ],
                    'configapp' => [
                        'label' => 'Config App',
                        'icon' => 'icon-cog',
                        'url' => Backend::url('mtech/sampling/configapp'),
                        'permissions' => ['mtech.sampling.configapp'],
                        'counterLabel' => 'Setting',
                    ],
                // 'customergift' => [
                //     'label' => 'Customer Gifts',
                //     'icon' => 'icon-history',
                //     'url' => Backend::url('mtech/sampling/customergift'),
                //     'permissions' => ['mtech.sampling.*'],
                //     'counterLabel' => 'History',
                // ]
                ]
            ],
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions() {

        return [
            'mtech.sampling.province' => [
                'tab' => 'Focus Sampling',
                'label' => 'Province'
            ],
            'mtech.sampling.district' => [
                'tab' => 'Focus Sampling',
                'label' => 'District'
            ],
            'mtech.sampling.productbrand' => [
                'tab' => 'Focus Sampling',
                'label' => 'Product Brand'
            ],
            'mtech.sampling.categorygift' => [
                'tab' => 'Focus Sampling',
                'label' => 'Category Gift'
            ],
            'mtech.sampling.productsampling' => [
                'tab' => 'Focus Sampling',
                'label' => 'Product Sampling'
            ],
            'mtech.sampling.project' => [
                'tab' => 'Focus Sampling',
                'label' => 'Project'
            ],
            'mtech.sampling.location' => [
                'tab' => 'Focus Sampling',
                'label' => 'Location'
            ],
            'mtech.sampling.project' => [
                'tab' => 'Focus Sampling',
                'label' => 'Project'
            ],
            'mtech.sampling.gift' => [
                'tab' => 'Focus Sampling',
                'label' => 'Gift'
            ],
            'mtech.sampling.customer' => [
                'tab' => 'Focus Sampling',
                'label' => 'Customer'
            ],
        ];
    }

    public function registerListColumnTypes() {
        return [
            // A local method, i.e $this->evalUppercaseListColumn()
            'location-project' => [$this, 'locationProject'],
        ];
    }

    public function locationProject($value, $column, $record) {
        if($record->location){
            return $record->location->location_name . ' - '.$record->location->project->project_name;        
        }        
    }

}
