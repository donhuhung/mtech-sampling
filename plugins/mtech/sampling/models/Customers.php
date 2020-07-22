<?php namespace Mtech\Sampling\Models;

use Model;

/**
 * Model
 */
class Customers extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_customer';

    /**
     * @var array Validation rules
     */
    public $rules = [];
    
    /**
     * @var array Fillable fields
     */
    protected $fillable = ['name','cmnd','dob','gender','phone','address','otp','brand_in_use','product_name','product_sampling','location_id','file_name_avatar','file_name_bill'];
    
    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'productSampling' => ['Mtech\Sampling\Models\ProductSampling','key' => 'product_sampling'],
        'location' => ['Mtech\Sampling\Models\Locations','key' => 'location_id'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    
    public function scopeFilterByLocation($query, $filter) {
        return $query->whereHas('location', function($location) use ($filter) {
                    $location->whereIn('id', $filter);
                });
    }
}
