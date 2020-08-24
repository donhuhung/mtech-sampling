<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingConfigApp4 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_config_app', function($table)
        {
            $table->integer('number_receive_gift')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_config_app', function($table)
        {
            $table->dropColumn('number_receive_gift');
        });
    }
}
