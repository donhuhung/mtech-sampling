<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMtechSamplingProduct extends Migration
{
    public function up()
    {
        Schema::create('mtech_sampling_product', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('id');
            $table->string('name');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mtech_sampling_product');
    }
}
