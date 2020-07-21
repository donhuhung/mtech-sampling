<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingProjects3 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_projects', function($table)
        {
            $table->integer('allow_choose_gift');
            $table->integer('number_receive_gift');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('deleted_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_projects', function($table)
        {
            $table->dropColumn('allow_choose_gift');
            $table->dropColumn('number_receive_gift');
            $table->timestamp('created_at')->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable()->default('NULL');
            $table->timestamp('deleted_at')->nullable()->default('NULL');
        });
    }
}
