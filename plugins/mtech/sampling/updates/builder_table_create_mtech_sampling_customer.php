<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMtechSamplingCustomer extends Migration
{
    public function up()
    {
        Schema::create('mtech_sampling_customer', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('cmnd');
            $table->string('dob');
            $table->integer('gender');
            $table->string('phone');
            $table->string('address');
            $table->string('otp');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mtech_sampling_customer');
    }
}
