<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingLocations extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_locations', function($table)
        {
            $table->integer('district_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('deleted_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_locations', function($table)
        {
            $table->dropColumn('district_id');
            $table->timestamp('created_at')->nullable()->default('NULL');
            $table->timestamp('updated_at')->nullable()->default('NULL');
            $table->timestamp('deleted_at')->nullable()->default('NULL');
        });
    }
}
