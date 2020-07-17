<?php
namespace Mtech\API\Transformers;

use Carbon\Carbon;
use League\Fractal;
use Mtech\Sampling\Models\Customers;

class CustomerTransformer extends Fractal\TransformerAbstract
{

    public function transform(Customers $customer)
    {        
        return [
            'id'               => (int) $customer->id,
            'name'        => (string) $customer->name,
            'cmnd'         => (string) $customer->cmnd,                        
            'email'            => (string) $customer->email,            
            'dob'            => (string) $customer->dob,            
            'gender'            => (string) $customer->gender,            
            'phone'            => (string) $customer->phone,            
            'address'            => (string) $customer->address,                
            'brand_in_use'            => (string) $customer->brand_in_use,            
            'product_name'            => (string) $customer->product_name,            
            'product_sampling'            => (string) $customer->product_sampling,                      
            'location_id'            => (string) $customer->location_id,                      
            'createdAt' => Carbon::parse($customer->created_at)->format('Y-m-d'),            
        ];
    }
}
