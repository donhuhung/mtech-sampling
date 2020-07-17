<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingCustomer3 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_customer', function($table)
        {
            $table->string('brand_in_use')->nullable();
            $table->string('product_name')->nullable();
            $table->integer('product_sampling');
            $table->string('name', 191)->default('null')->change();
            $table->string('cmnd', 191)->default('null')->change();
            $table->string('dob', 191)->default('null')->change();
            $table->string('phone', 191)->default('null')->change();
            $table->string('address', 191)->default('null')->change();
            $table->string('otp', 191)->default('null')->change();
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_customer', function($table)
        {
            $table->dropColumn('brand_in_use');
            $table->dropColumn('product_name');
            $table->dropColumn('product_sampling');
            $table->string('name', 191)->default('\'null\'')->change();
            $table->string('cmnd', 191)->default('\'null\'')->change();
            $table->string('dob', 191)->default('\'null\'')->change();
            $table->string('phone', 191)->default('\'null\'')->change();
            $table->string('address', 191)->default('\'null\'')->change();
            $table->string('otp', 191)->default('\'null\'')->change();
            $table->timestamp('created_at')->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable()->default('NULL');
        });
    }
}
