<?php
namespace Mtech\API\Transformers;

use Carbon\Carbon;
use League\Fractal;
use Mtech\Sampling\Models\Gifts;

class GiftTransformer extends Fractal\TransformerAbstract
{
    protected $totalGiftReceive;
    public function __construct($totalGiftReceive) {
         $this->totalGiftReceive = $totalGiftReceive;
     }


    public function transform(Gifts $gift)
    {        
        return [
            'id'               => (int) $gift->id,
            'name'        => (string) $gift->gift_name,
            'image'         => (string) $gift->gift_image,         
            'number_gift' => (int)$gift->gift_inventory,
            'total_gift' => (int)$gift->total_gift,
            'totalGiftReceive' => $this->totalGiftReceive,
            'path' => '/storage/app/media',
            'createdAt' => Carbon::parse($gift->created_at)->format('Y-m-d'),
        ];
    }
}
