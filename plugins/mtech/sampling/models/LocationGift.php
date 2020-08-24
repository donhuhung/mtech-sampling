<?php namespace Mtech\Sampling\Models;

use Model;
use Mtech\Sampling\Models\Projects;
use BackendAuth;
use DB;

/**
 * Model
 */
class LocationGift extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_location_gift';

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
        'gift' => ['Mtech\Sampling\Models\Gifts', 'key' => 'gift_id'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    
    public function getGiftIDOptions() {
        $arrayGifts = [];
        $user = BackendAuth::getUser();
        $userId = $user->id;
        $userGroups = $user->groups;
        if ($userGroups) {
            foreach ($userGroups as $group) {
                if ($group->code == "quan-ly-du-an" || $group->code == "tro-ly-du-an" || $group->code == "khach-hang") {
                    $userProjects = DB::table('mtech_sampling_backend_users_projects')->where('user_id', $userId)->get();
                    $arrProject = [];
                    foreach ($userProjects as $item) {
                        array_push($arrProject, $item->id);
                    }
                    $projects = self::whereIn('id', $arrProject)->get();
                    $arr = [];
                    foreach ($projects as $project) {
                        array_push($arr, $project->id);
                    }
                    $gifts = Gifts::whereIn('project_id', $arr)->get();
                } else {
                    $gifts = Gifts::get();
                }
            }
        }
        foreach ($gifts as $gift) {
            $arrayGifts[$gift->id] = $gift->gift_name;
        }
        return $arrayGifts;
    }
}
