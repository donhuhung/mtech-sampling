<?php namespace Mtech\Sampling\Models;

use Model;
use Mtech\Sampling\Models\Gifts;
use Mtech\Sampling\Models\Projects;
use RainLab\User\Models\User As UserModel;
use BackendAuth;
use DB;

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
        'gift' => ['Mtech\Sampling\Models\Gifts','key' => 'location_id', 'otherKey' => 'id'],        
    ];
    public $belongsTo = [
        'project' => ['Mtech\Sampling\Models\Projects','key' => 'project_id'],        
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
    
    public function getLocationOptions(){
        $arrayLocations = [];
        $user = BackendAuth::getUser();
        $userId = $user->id;        
        $userGroups = $user->groups;                
        if ($userGroups) {
            foreach ($userGroups as $group) {                
                if ($group->code == "quan-ly-du-an" || $group->code == "tro-ly-du-an" || $group->code == "khach-hang") {
                    $userProjects = DB::table('mtech_sampling_backend_users_projects')->where('user_id',$userId)->get();                                        
                    $arrProject = [];
                    foreach($userProjects as $item){
                        array_push($arrProject, $item->id);
                    }                    
                    $projects = self::whereIn('id',$arrProject)->get();
                    $arr = [];
                    foreach($projects as $project){
                        array_push($arr, $project->id);
                    }
                    $locations = self::whereIn('project_id',$arr)->get();
                }
                else{                    
                    $locations = self::get();
                }
            }            
        }  
        foreach($locations as $location){            
           $arrayLocations[$location->id] = $location->location_name.' - '.$location->project->project_name;
        }
        return $arrayLocations;
    }
    
    
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
