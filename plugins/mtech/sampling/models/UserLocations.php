<?php namespace Mtech\Sampling\Models;

use Model;

/**
 * Model
 */
class UserLocations extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_user_location';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
