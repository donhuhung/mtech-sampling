<?php namespace Mtech\Sampling\Models;

use Model;

/**
 * Model
 */
class ProductSampling extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_product';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
