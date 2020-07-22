<?php namespace Mtech\Sampling\Models;

use Model;
use Mtech\Sampling\Models\Gifts;
use RainLab\User\Models\User As UserModel;

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
        'district' => 'Mtech\Sampling\Models\Districts'
    ];
    public $belongsToMany = [
        'users' => [
            'RainLab\User\Models\User', 
            'table' => 'mtech_sampling_user_location',
            'key'      => 'location_id',
            'otherKey' => 'user_id'
            ]
    ];
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
    
    public function scopeFilterByProject($query, $filter) {
        return $query->whereHas('project', function($project) use ($filter) {
                    $project->whereIn('id', $filter);
                });
    }
}
