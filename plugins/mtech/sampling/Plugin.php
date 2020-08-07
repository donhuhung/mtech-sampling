<?php

namespace Mtech\Sampling;

use Backend;
use System\Classes\PluginBase;
use BackendMenu;
use Event;
use Mail;
use RainLab\User\Models\User as UserModel;
use RainLab\User\Controllers\Users as UserController;
use Mtech\Sampling\Models\Customers;
use Mtech\Sampling\Models\CustomerGifts;
use Mtech\Sampling\Models\Locations;

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
        
        //Register Command Line
        $this->app->singleton('sampling:update_status_project', function() {
            return new \Mtech\Sampling\Console\UpdateStatusProject;
        });
        $this->commands('sampling:update_status_project');
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
                        'permissions' => ['mtech.sampling.customergift'],
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
                    'settingotp' => [
                        'label' => 'Config OTP',
                        'icon' => 'icon-cog',
                        'url' => Backend::url('mtech/sampling/settingotp'),
                        'permissions' => ['mtech.sampling.settingotp'],
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
            'mtech.sampling.customergift' => [
                'tab' => 'Focus Sampling',
                'label' => 'Customer Gift'
            ],
            'mtech.sampling.historypg' => [
                'tab' => 'Focus Sampling',
                'label' => 'PG Info'
            ],
            'mtech.sampling.configapp' => [
                'tab' => 'Focus Sampling',
                'label' => 'Config App'
            ],
            'mtech.sampling.settingotp' => [
                'tab' => 'Focus Sampling',
                'label' => 'Config OTP'
            ],
        ];
    }

    public function registerSettings() {
        return [
            'settings' => [
                'label' => 'Settings Focus Sampling',
                'description' => 'Settings Focus Sampling',
                'category' => 'Focus Sampling',
                'icon' => 'icon-cogs',
                'class' => 'Mtech\Sampling\Models\Setting',
                'order' => 500,
                'permissions' => ['*']
            ]
        ];
    }

    public function registerListColumnTypes() {
        return [
            'location-project' => [$this, 'locationProject'],
            'product_sampling' => [$this, 'productSampling'],
            'calculate_date_project' => [$this, 'calculateDateProject'],
            'total_gift_project' => [$this, 'totalGiftProject'],
            'total_gift_runed_project' => [$this, 'totalGiftRunnedProject'],
            'total_gift_inventory_project' => [$this, 'totalGiftInventoryProject'],
        ];
    }
    
    public function registerReportWidgets()
    {
        return [
            'Mtech\Sampling\ReportWidgets\CampaignGift'=>[
                'label'   => 'Campagin Gift',
                'context' => 'dashboard'
            ],
        ];
    }

    public function locationProject($value, $column, $record) {
        if ($record->location) {
            return $record->location->location_name . ' - ' . $record->location->project->project_name;
        }
    }

    public function productSampling($value, $column, $record) {
        $customerGift = CustomerGifts::find($record->id);
        if (isset($customerGift->customer->productSampling->name)) {
            return $customerGift->customer->productSampling->name;
        } else {
            return;
        }
    }

    public function calculateDateProject($value, $column, $record) {
        $now = date('Y-m-d H:i:s');
        $startDate = $record->start_date;
        $endDate = $record->end_date;
        $totalDate = date_diff(date_create($endDate), date_create($startDate));
        $totalDate = $totalDate->format("%a");

        $dateMade = date_diff(date_create($now), date_create($startDate));
        $dateMade = $dateMade->format("%a");
        
        if($dateMade > $totalDate){
            return $totalDate . '/' . $totalDate;
        }        
        return $dateMade . '/' . $totalDate;
    }

    public function totalGiftProject($value, $column, $record) {
        $projectId = $record->id;
        $locations = Locations::where('project_id', $projectId)->get();
        $totalGift = 0;
        foreach ($locations as $location) {
            $totalGift += $location->total_gift;
        }
        return $totalGift;
    }

    public function totalGiftRunnedProject($value, $column, $record) {
        $projectId = $record->id;
        $locations = Locations::where('project_id', $projectId)->get();
        $totalGiftRunned = 0;
        foreach ($locations as $location) {
            $totalGiftRunned += $location->total_gift - $location->gift_inventory;
        }
        return $totalGiftRunned;
    }

    public function totalGiftInventoryProject($value, $column, $record) {
        $projectId = $record->id;
        $locations = Locations::where('project_id', $projectId)->get();
        $totalGiftInventory = 0;
        foreach ($locations as $location) {
            $totalGiftInventory += $location->gift_inventory;
        }
        return $totalGiftInventory;
    }
    
    public function registerSchedule($schedule) {
        $schedule->command('sampling:update_status_project')->dailyAt('01:00');
    }

}
