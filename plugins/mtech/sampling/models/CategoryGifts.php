<?php namespace Mtech\Sampling\Models;

use Model;

/**
 * Model
 */
class CategoryGifts extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_category_gift';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
