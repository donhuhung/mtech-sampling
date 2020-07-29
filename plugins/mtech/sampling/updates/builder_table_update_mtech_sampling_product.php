<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingProduct extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_product', function($table)
        {
            $table->integer('status');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_product', function($table)
        {
            $table->dropColumn('status');
            $table->timestamp('created_at')->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable()->default('NULL');
        });
    }
}
