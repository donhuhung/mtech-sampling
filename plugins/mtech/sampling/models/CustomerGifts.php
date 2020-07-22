<?php

namespace Mtech\Sampling\Models;

use Model;

/**
 * Model
 */
class CustomerGifts extends Model {

    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_customer_gift';

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
        'customer' => 'Mtech\Sampling\Models\Customers',
        'gift' => 'Mtech\Sampling\Models\Gifts',
        'location' => 'Mtech\Sampling\Models\Locations'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    protected $fillable = ['customer_id', 'gift_id', 'location_id'];

    public function scopeFilterByCustomer($query, $filter) {
        return $query->whereHas('customer', function($customer) use ($filter) {
                    $customer->whereIn('id', $filter);
                });
    }
    
    public function scopeFilterByGift($query, $filter) {
        return $query->whereHas('gift', function($gift) use ($filter) {
                    $gift->whereIn('id', $filter);
                });
    }
    
    public function scopeFilterByLocation($query, $filter) {
        return $query->whereHas('location', function($location) use ($filter) {
                    $location->whereIn('id', $filter);
                });
    }

}
