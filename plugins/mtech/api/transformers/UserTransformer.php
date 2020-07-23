<?php
namespace Mtech\API\Transformers;

use Carbon\Carbon;
use League\Fractal;
use RainLab\User\Models\User As UserModel;

class UserTransformer extends Fractal\TransformerAbstract
{

    public function transform(UserModel $user)
    {        
        return [
            'id'               => (int) $user->id,
            'name'        => (string) $user->name,
            'username'         => (string) $user->last_name,                        
            'email'            => (string) $user->email,            
            'phone'            => (string) $user->phone, 
            'location'         => $this->parseUserLocation($user->locations),
            'change_password'  => $user->reset_password_code?true:false,
            'createdAt' => Carbon::parse($user->created_at)->format('Y-m-d'),
            'updatedAt' => Carbon::parse($user->updated_at)->format('Y-m-d'),
        ];
    }
    
    private function parseUserLocation($locations){
        foreach($locations as $location){
            unset($location->pivot);
            unset($location->updated_at);
            unset($location->created_at);
            unset($location->district_id);
            unset($location->end_date);
            unset($location->start_date);
            unset($location->gift_inventory);
            unset($location->total_gift);
            unset($location->project_id);            
            unset($location->gift_run);
        }
        return $locations;
    }
}
