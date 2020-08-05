<?php namespace Mtech\Sampling\Models;

use Model;
use Mtech\Sampling\Models\Locations;
use Mtech\Sampling\Models\Gifts;
use BackendAuth;
use DB;

/**
 * Model
 */
class Projects extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_projects';

    /**
     * @var array Validation rules
     */
    public $rules = [];
    
    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [
        'usersBackend' => [
            'Backend\Models\User', 
            'table' => 'mtech_sampling_backend_users_projects',
            'key'      => 'project_id',
            'otherKey' => 'user_id'
            ]
    ];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    
    public function matchingProjects(){
        $arrayProjects = [];
        $user = BackendAuth::getUser();
        $userId = $user->id;        
        $userGroups = $user->groups;
        if ($userGroups) {
            foreach ($userGroups as $group) {                
                if ($group->code == "quan-ly-du-an" || $group->code == "tro-ly-du-an" || $group->code == "khach-hang") {
                    $userProjects = DB::table('mtech_sampling_backend_users_projects')->where('user_id',$userId)->get();                                        
                    $arr = [];
                    foreach($userProjects as $item){
                        array_push($arr, $item->id);
                    }                    
                    $projects = self::whereIn('id',$arr)->get();
                }
                else{                    
                    $projects = self::get();
                }
            }            
        }        
        foreach($projects as $project){            
           $arrayProjects[$project->id] = $project->project_name;
        }
        return $arrayProjects;
    }
}
