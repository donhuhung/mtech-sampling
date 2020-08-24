<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingGifts6 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_gifts', function($table)
        {
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->integer('total_gift')->nullable(false)->default(0)->change();
            $table->integer('gift_inventory')->nullable(false)->default(0)->change();
            $table->string('gift_image', 191)->nullable(false)->default('0')->change();
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_gifts', function($table)
        {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->integer('total_gift')->nullable()->default(NULL)->change();
            $table->integer('gift_inventory')->nullable()->default(NULL)->change();
            $table->string('gift_image', 191)->nullable()->default('\'null\'')->change();
        });
    }
}
