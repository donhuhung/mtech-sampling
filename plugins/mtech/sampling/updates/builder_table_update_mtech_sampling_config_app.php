<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingConfigApp extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_config_app', function($table)
        {
            $table->integer('brand_in_use');
            $table->integer('product_name');
            $table->integer('product_sampling');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_config_app', function($table)
        {
            $table->dropColumn('brand_in_use');
            $table->dropColumn('product_name');
            $table->dropColumn('product_sampling');
            $table->timestamp('created_at')->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable()->default('NULL');
        });
    }
}
