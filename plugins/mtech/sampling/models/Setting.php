<?php

namespace Mtech\Sampling\Models;

use Model;

/**
 * Setting Model
 */
class Setting extends Model {

    public $implement = ['System.Behaviors.SettingsModel'];
    public $settingsCode = 'abbott.backend.access_setting';
    public $settingsFields = 'fields.yaml';

}
