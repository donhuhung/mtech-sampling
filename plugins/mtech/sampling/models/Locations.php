<?php namespace Mtech\Sampling\Models;

use Model;
use Mtech\Sampling\Models\Gifts;


/**
 * Model
 */
class Locations extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_locations';

    /**
     * @var array Validation rules
     */
    public $rules = [];
    
    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [
        'gift' => ['Mtech\Sampling\Models\Gifts','key' => 'id', 'otherKey' => 'location_id'],        
    ];
    public $belongsTo = [
        'project' => 'Mtech\Sampling\Models\Projects',
        'user' => 'RainLab\User\Models\User',
        'district' => 'Mtech\Sampling\Models\Districts'
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    
     /**
     * @return mixed
     */
    public function getGiftInfoAttribute()
    {
        $gifts = Gifts::where('location_id', $this->id)->get();
        return $gifts;
    }
}
