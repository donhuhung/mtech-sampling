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

}
