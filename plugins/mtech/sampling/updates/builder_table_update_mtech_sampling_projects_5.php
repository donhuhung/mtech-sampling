<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingProjects5 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_projects', function($table)
        {
            $table->integer('kpi_day_gift');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('deleted_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_projects', function($table)
        {
            $table->dropColumn('kpi_day_gift');
            $table->timestamp('created_at')->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable()->default('NULL');
            $table->timestamp('deleted_at')->nullable()->default('NULL');
        });
    }
}
