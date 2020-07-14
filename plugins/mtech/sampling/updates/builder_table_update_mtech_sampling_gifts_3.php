<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingGifts3 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_gifts', function($table)
        {
            $table->integer('category_gift')->default(0);
            $table->integer('product_brand')->default(0);
            $table->integer('sampling_product')->default(0);
            $table->string('gift_image', 191)->default('null')->change();
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_gifts', function($table)
        {
            $table->dropColumn('category_gift');
            $table->dropColumn('product_brand');
            $table->dropColumn('sampling_product');
            $table->string('gift_image', 191)->default('\'null\'')->change();
            $table->timestamp('created_at')->nullable()->default('NULL');
            $table->timestamp('updated_at')->nullable()->default('NULL');
        });
    }
}
