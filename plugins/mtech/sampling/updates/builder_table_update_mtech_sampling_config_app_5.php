<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingConfigApp5 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_config_app', function($table)
        {
            $table->integer('view_inventory');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_config_app', function($table)
        {
            $table->dropColumn('view_inventory');
        });
    }
}
