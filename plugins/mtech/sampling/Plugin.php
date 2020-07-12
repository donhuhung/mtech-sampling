<?php namespace Mtech\Sampling;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        
    }

    public function registerSettings()
    {
    }

    public function pluginDetails()
    {
        return [
            'name' => 'Sampling Plugin',
            'description' => 'Provides some sample of Mtech.',
            'author' => 'Mtech Corporation',
            'icon' => 'icon-leaf'
        ];
    }
}
