<?php
namespace Mtech\API\Transformers;

use Carbon\Carbon;
use League\Fractal;
use Mtech\Sampling\Models\Gifts;

class GiftTransformer extends Fractal\TransformerAbstract
{

    public function transform(Gifts $gift)
    {        
        return [
            'id'               => (int) $gift->id,
            'name'        => (string) $gift->gift_name,
            'image'         => (string) $gift->gift_image,         
            'path' => '/storage/app/media',
            'createdAt' => Carbon::parse($gift->created_at)->format('Y-m-d'),
        ];
    }
}