<?php

namespace Mtech\API\Controllers;

use Illuminate\Http\Request;
use Mtech\Sampling\Models\Gifts;
use Mtech\Sampling\Models\CustomerGifts;
use Mtech\API\Transformers\UserTransformer;

/**
 * Gift Back-end Controller
 */
class Gift extends General {

    protected $giftRepository;
    protected  $customerRepository;

    public function __construct(Gifts $gift, CustomerGifts $customerGift) {
        $this->giftRepository = $gift;
        $this->customerRepository = $customerGift;
    }

}
