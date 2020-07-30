<?php namespace Mtech\Sampling\Models;

use Model;

/**
 * Model
 */
class OTP extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_otp';

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['user_id','otp','phone','created_at'];
    
    
    /**
     * @var array Validation rules
     */
    public $rules = [];
}
