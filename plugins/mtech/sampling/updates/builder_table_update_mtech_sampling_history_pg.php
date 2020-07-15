<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingHistoryPg extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_history_pg', function($table)
        {
            $table->integer('location_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_history_pg', function($table)
        {
            $table->dropColumn('location_id');
            $table->timestamp('created_at')->nullable()->default('NULL');
            $table->timestamp('updated_at')->nullable()->default('NULL');
        });
    }
}
