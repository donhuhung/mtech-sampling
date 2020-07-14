<?php namespace Mtech\Sampling\Models;

use Model;
use Mtech\Sampling\Models\Provinces;

/**
 * Model
 */
class Districts extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_districts';

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
        'province' => 'Mtech\Sampling\Models\Provinces',
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    
}
