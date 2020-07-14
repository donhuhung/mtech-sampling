<?php namespace Mtech\Sampling\Models;

use Model;

/**
 * Model
 */
class Gifts extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_gifts';

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
        'category' => 'Mtech\Sampling\Models\CategoryGifts',
        'productBrand' => 'Mtech\Sampling\Models\ProductBrands'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
}
