<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMtechSamplingConfigApp extends Migration
{
    public function up()
    {
        Schema::create('mtech_sampling_config_app', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('project_id');
            $table->integer('show_customer_name');
            $table->integer('show_customer_cmnd');
            $table->integer('show_customer_dob');
            $table->integer('show_customer_gender');
            $table->integer('show_customer_phone');
            $table->integer('show_customer_address');
            $table->integer('show_customer_otp');
            $table->integer('number_receive_gift');
            $table->time('time_not_login_from');
            $table->time('time_not_login_to');
            $table->integer('status');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mtech_sampling_config_app');
    }
}
