<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMtechSamplingGifts extends Migration
{
    public function up()
    {
        Schema::create('mtech_sampling_gifts', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('gift_name');
            $table->integer('location_id');
            $table->integer('total_gift');
            $table->integer('gift_inventory');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mtech_sampling_gifts');
    }
}
