<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMtechSamplingLocationGift extends Migration
{
    public function up()
    {
        Schema::create('mtech_sampling_location_gift', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('location_id');
            $table->integer('gift_id');
            $table->integer('total_gift');
            $table->integer('gift_inventory');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mtech_sampling_location_gift');
    }
}
