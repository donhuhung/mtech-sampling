<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMtechSamplingLocations extends Migration
{
    public function up()
    {
        Schema::create('mtech_sampling_locations', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('location_name');
            $table->integer('project_id');
            $table->integer('user_id');
            $table->integer('total_gift');
            $table->integer('gift_run');
            $table->integer('gift_inventory');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mtech_sampling_locations');
    }
}
