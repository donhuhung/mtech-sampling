<?php

namespace Mtech\API\Transformers;

use Carbon\Carbon;
use League\Fractal;
use Mtech\Sampling\Models\ConfigApp;

class SettingTransformer extends Fractal\TransformerAbstract {

    public function transform(ConfigApp $config) {
        return [
            'id' => (int) $config->id,
            'project_id' => $config->project_id,
            'show_customer_name' => $config->show_customer_name,
            'show_customer_cmnd' => $config->show_customer_cmnd,
            'show_customer_dob' => $config->show_customer_dob,
            'show_customer_gender' => $config->show_customer_gender,
            'show_customer_phone' => $config->show_customer_phone,
            'show_customer_address' => $config->show_customer_address,
            'show_customer_otp' => $config->show_customer_otp,
            'number_receive_gift' => $config->number_receive_gift,
            'time_not_login_from' => $config->time_not_login_from,
            'time_not_login_to' => $config->time_not_login_to,
            'createdAt' => Carbon::parse($config->created_at)->format('Y-m-d'),
        ];
    }

}
