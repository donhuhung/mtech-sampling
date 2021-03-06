<?php namespace Mtech\Sampling\Models;

use Model;

/**
 * Model
 */
class ProductBrands extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_product_brands';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];
}
