<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingGifts5 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_gifts', function($table)
        {
            $table->integer('total_gift')->nullable()->change();
            $table->integer('gift_inventory')->nullable()->change();
            $table->string('gift_image', 191)->default('null')->change();
            $table->renameColumn('location_id', 'project_id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_gifts', function($table)
        {
            $table->integer('total_gift')->nullable(false)->change();
            $table->integer('gift_inventory')->nullable(false)->change();
            $table->string('gift_image', 191)->default('\'null\'')->change();
            $table->renameColumn('project_id', 'location_id');
            $table->timestamp('created_at')->nullable()->default('NULL');
            $table->timestamp('updated_at')->nullable()->default('NULL');
        });
    }
}
