<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMtechSamplingOtp extends Migration
{
    public function up()
    {
        Schema::create('mtech_sampling_otp', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('otp');
            $table->integer('user_id');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mtech_sampling_otp');
    }
}
