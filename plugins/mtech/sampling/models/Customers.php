<?php namespace Mtech\Sampling\Models;

use Model;

/**
 * Model
 */
class Customers extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'mtech_sampling_customer';

    /**
     * @var array Validation rules
     */
    public $rules = [];
    
    /**
     * @var array Fillable fields
     */
    protected $fillable = ['name','cmnd','dob','gender','phone','address','otp','brand_in_use','product_name','product_sampling','location_id','file_name_avatar','file_name_bill'];
    
    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'productSampling' => ['Mtech\Sampling\Models\ProductSampling','key' => 'product_sampling'],
        'location' => ['Mtech\Sampling\Models\Locations','key' => 'location_id'],
    ];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    
    public function scopeFilterByLocation($query, $filter) {
        return $query->whereHas('location', function($location) use ($filter) {
                    $location->whereIn('id', $filter);
                });
    }
    
    public function getCustomerOptions(){
        $arrayCustomers = [];
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
                    $customers = self::whereIn('location_id',$arrLocation)->groupBy('id')->get();
                }
                else{                    
                    $customers = self::get();
                }
            }            
        }  
        foreach($customers as $customer){            
           $arrayCustomers[$customer->id] = $customer->name;
        }
        return $arrayCustomers;
    }
}
