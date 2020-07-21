<?php

namespace Mtech\Sampling\Models;

use Model;
use Mtech\Sampling\Models\Projects;
use Mtech\Sampling\Models\Locations;
use Mtech\Sampling\Models\CustomerGifts;
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
        'location' => ['Mtech\Sampling\Models\Locations']
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
        $projects = Projects::where('status', 1)->get();
        if ($projects) {
            $arrProject = [];
            foreach ($projects as $project) {
                array_push($arrProject, $project->id);
            }
            $locations = Locations::whereIn('project_id', $arrProject)->get();
            $arrLocation = [];
            foreach ($locations as $location) {
                array_push($arrLocation, $location->id);
            }
            for ($i = 0; $i < $numberReceiveGift; $i++) {
                $data = $this->whereRaw('total_gift > 0 ')
                        ->whereIn('location_id', $arrLocation)
                        ->orderByRaw('RAND()')
                        ->first();
                if ($data) {
                    $gift_id = $data->id;
                    $location_id = $data->location_id;
                    //update number gift random    
                    Db::table('mtech_sampling_locations')->where('id', $location_id)->decrement('total_gift');
                    Db::table('mtech_sampling_gifts')->where('id', $gift_id)->decrement('total_gift');
                    $this->insertUserReceiveGift($user_id, $gift_id, $location_id);
                    array_push($arrGift, $gift_id);
                }
            }
            return (array(
                'gift' => $arrGift,
            ));
        }
        return false;
    }

    public function insertUserReceiveGift($user_id, $gift_id, $location_id) {
        $model_user_receive = new CustomerGifts();
        $model_user_receive['customer_id'] = $user_id;
        $model_user_receive['gift_id'] = $gift_id;
        $model_user_receive['location_id'] = $location_id;
        $model_user_receive->save();
        return true;
    }

}
