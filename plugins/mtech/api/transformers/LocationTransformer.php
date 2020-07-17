<?php
namespace Mtech\API\Transformers;

use Carbon\Carbon;
use League\Fractal;
use Mtech\Sampling\Models\Locations;

class LocationTransformer extends Fractal\TransformerAbstract
{

    public function transform(Locations $location)
    {        
        return [
            'id'               => (int) $location->id,
            'name'        => (string) $location->location_name,
            'total_gift'         => $location->total_gift,                        
            'gift_run'            => $location->gift_run,            
            'gift_inventory'            => $location->gift_inventory,            
            'start_date' => Carbon::parse($location->start_date)->format('Y-m-d'),            
            'end_adte' => Carbon::parse($location->end_date)->format('Y-m-d'),            
            'createdAt' => Carbon::parse($location->created_at)->format('Y-m-d'),            
        ];
    }
}
