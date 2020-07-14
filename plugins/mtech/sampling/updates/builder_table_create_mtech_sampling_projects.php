<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateMtechSamplingProjects extends Migration
{
    public function up()
    {
        Schema::create('mtech_sampling_projects', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('project_name');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('result');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('mtech_sampling_projects');
    }
}
