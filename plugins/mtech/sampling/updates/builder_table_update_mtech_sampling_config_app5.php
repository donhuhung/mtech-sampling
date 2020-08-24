<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingConfigApp5 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_config_app', function($table)
        {
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->integer('number_receive_gift')->default(null)->change();
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_config_app', function($table)
        {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->integer('number_receive_gift')->default(NULL)->change();
        });
    }
}