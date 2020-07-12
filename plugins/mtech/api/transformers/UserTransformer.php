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
            'createdAt' => Carbon::parse($user->created_at)->format('Y-m-d'),
            'updatedAt' => Carbon::parse($user->updated_at)->format('Y-m-d'),
        ];
    }
}
