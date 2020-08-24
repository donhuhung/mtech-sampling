<?php
namespace Mtech\API\Transformers;

use Carbon\Carbon;
use League\Fractal;
use Mtech\Sampling\Models\LocationGift;
use Mtech\Sampling\Models\Gifts;

class LocationGiftTransformer extends Fractal\TransformerAbstract
{
    protected $totalGiftReceive;
    public function __construct($totalGiftReceive) {
         $this->totalGiftReceive = $totalGiftReceive;
     }


    public function transform(LocationGift $locationGift)
    {
        $gift = Gifts::find($locationGift->gift_id);
        return [
            'id'               => (int) $gift->id,
            'name'        => (string) $gift->gift_name,
            'image'         => (string) $gift->gift_image,         
            'number_gift' => (int)$locationGift->gift_inventory,
            'total_gift' => (int)$locationGift->total_gift,
            'totalGiftReceive' => $this->totalGiftReceive,
            'path' => '/storage/app/media',
            'createdAt' => Carbon::parse($gift->created_at)->format('Y-m-d'),
        ];
    }
}
