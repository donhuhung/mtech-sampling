<?php

namespace Mtech\Sampling\Models;

use Model;
use Mtech\Sampling\Models\Projects;
use Mtech\Sampling\Models\Locations;
use Mtech\Sampling\Models\CustomerGifts;
use BackendAuth;
use DB;

/**
 * Model
 */
class Gifts extends Model {

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
        'category' => ['Mtech\Sampling\Models\CategoryGifts', 'key' => 'category_gift'],
        'productBrand' => ['Mtech\Sampling\Models\ProductBrands', 'key' => 'product_brand'],
        'location' => ['Mtech\Sampling\Models\Locations', 'key' => 'location_id']
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];

    public function randomGift($user_id, $location_id, $numberReceiveGift) {
        date_default_timezone_set('Asia/Jakarta');
        $gift = $this->processRandomGift($user_id, $location_id, $numberReceiveGift);
        return $gift;
    }

    public function processRandomGift($user_id, $location_id, $numberReceiveGift) {
        //random gift 
        $arrGift = [];
        for ($i = 0; $i < $numberReceiveGift; $i++) {
            $data = $this->whereRaw('gift_inventory > 0 ')
                    ->where('location_id', $location_id)
                    ->orderByRaw('RAND()')
                    ->first();
            if ($data) {
                $gift_id = $data->id;
                $location_id = $data->location_id;
                //update number gift random    
                Db::table('mtech_sampling_locations')->where('id', $location_id)->decrement('gift_inventory');
                Db::table('mtech_sampling_gifts')->where('id', $gift_id)->decrement('gift_inventory');
                $this->insertUserReceiveGift($user_id, $gift_id, $location_id);
                array_push($arrGift, $gift_id);
            }
        }
        return (array(
            'gift' => $arrGift,
        ));
    }

    public function insertUserReceiveGift($user_id, $gift_id, $location_id) {
        $model_user_receive = new CustomerGifts();
        $model_user_receive['customer_id'] = $user_id;
        $model_user_receive['gift_id'] = $gift_id;
        $model_user_receive['location_id'] = $location_id;
        $model_user_receive->save();
        return true;
    }

    public function getLocationIdOptions() {
        $locations = Locations::get();
        $arrLocation = [];
        foreach ($locations as $location) {
            $projectName = $location->project->project_name;
            $arrLocation[$location->id] = $location->location_name . ' - ' . $projectName;
        }
        return $arrLocation;
    }

    public function getGiftOptions(){
        $arrayGifts = [];
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
                    //Get List Project                    
                    $projects = self::whereIn('id',$arrProject)->get();
                    $arr = [];
                    foreach($projects as $project){
                        array_push($arr, $project->id);
                    }
                    //Get List Location
                    $arrLocation = [];
                    $locations = Locations::whereIn('project_id',$arr)->get();
                    foreach($locations as $location){
                        array_push($arrLocation, $location->id);
                    }
                    $gifts = self::whereIn('location_id',$arrLocation)->get();
                }
                else{                    
                    $gifts = self::get();
                }
            }            
        }  
        foreach($gifts as $gift){            
           $arrayGifts[$gift->id] = $gift->gift_name;
        }
        return $arrayGifts;
    }
}
