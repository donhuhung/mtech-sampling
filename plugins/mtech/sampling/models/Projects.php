<?php namespace Mtech\Sampling\Models;

use Model;
use Mtech\Sampling\Models\Locations;
use Mtech\Sampling\Models\Gifts;

/**
 * Model
 */
class Projects extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_projects';

    /**
     * @var array Validation rules
     */
    public $rules = [];
    
    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'usersBackend' => [
            'Backend\Models\User', 
            'table' => 'mtech_sampling_backend_users_projects',
            'key'      => 'project_id',
            'otherKey' => 'user_id'
            ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
}
