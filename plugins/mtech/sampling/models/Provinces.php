<?php namespace Mtech\Sampling\Models;

use Model;

/**
 * Model
 */
class Provinces extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_provinces';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
