<?php namespace Mtech\Sampling\Models;

use Model;

/**
 * Model
 */
class SettingOTP extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_setting_otp_project';

    /**
     * @var array Validation rules
     */
    public $rules = [];
    
    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'project' => ['Mtech\Sampling\Models\Projects','key' => 'project_id'],                
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
}
