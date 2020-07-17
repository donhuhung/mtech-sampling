<?php

namespace Mtech\API\Transformers;

use Carbon\Carbon;
use League\Fractal;
use Mtech\Sampling\Models\ProductSampling;

class ProductSamplingTransformer extends Fractal\TransformerAbstract {

    public function transform(ProductSampling $product) {
        return [
            'id' => (int) $product->id,
            'name' => $product->name,            
            'createdAt' => Carbon::parse($product->created_at)->format('Y-m-d'),
        ];
    }

}
