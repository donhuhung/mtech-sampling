<?php

namespace Mtech\API\Transformers;

use Carbon\Carbon;
use League\Fractal;
use Mtech\Sampling\Models\SettingOTP;

class SettingOTPTransformer extends Fractal\TransformerAbstract {

    public function transform(SettingOTP $config) {
        return [
            'id' => (int) $config->id,
            'project_id' => $config->project_id,           
            'time_count_down' => $config->time_expired_otp,
            'createdAt' => Carbon::parse($config->created_at)->format('Y-m-d'),
        ];
    }

}
