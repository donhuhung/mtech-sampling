<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMtechSamplingSettingOtpProject extends Migration
{
    public function up()
    {
        Schema::create('mtech_sampling_setting_otp_project', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('project_id');
            $table->string('brand_name');
            $table->string('account_name');
            $table->string('account_password');
            $table->string('url_telco');
            $table->string('text_sms');
            $table->integer('length_otp');
            $table->integer('time_expired_otp');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mtech_sampling_setting_otp_project');
    }
}
