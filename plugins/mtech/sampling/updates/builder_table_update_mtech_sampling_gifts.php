<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingGifts extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_gifts', function($table)
        {
            $table->string('gift_image')->nullable();
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_gifts', function($table)
        {
            $table->dropColumn('gift_image');
            $table->timestamp('created_at')->nullable()->default('NULL');
            $table->timestamp('updated_at')->nullable()->default('NULL');
        });
    }
}
