<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingCustomer7 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_customer', function($table)
        {
            $table->string('file_name_avatar')->nullable();
            $table->string('file_name_bill')->nullable();
            $table->string('name', 191)->default('null')->change();
            $table->string('cmnd', 191)->default('null')->change();
            $table->string('dob', 191)->default('null')->change();
            $table->string('phone', 191)->default('null')->change();
            $table->string('address', 191)->default('null')->change();
            $table->string('otp', 191)->default('null')->change();
            $table->string('brand_in_use', 191)->default('null')->change();
            $table->string('product_name', 191)->default('null')->change();
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('deleted_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_customer', function($table)
        {
            $table->dropColumn('file_name_avatar');
            $table->dropColumn('file_name_bill');
            $table->string('name', 191)->default('\'null\'')->change();
            $table->string('cmnd', 191)->default('\'null\'')->change();
            $table->string('dob', 191)->default('\'null\'')->change();
            $table->string('phone', 191)->default('\'null\'')->change();
            $table->string('address', 191)->default('\'null\'')->change();
            $table->string('otp', 191)->default('\'null\'')->change();
            $table->string('brand_in_use', 191)->default('\'null\'')->change();
            $table->string('product_name', 191)->default('\'null\'')->change();
            $table->timestamp('created_at')->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable()->default('NULL');
            $table->timestamp('deleted_at')->nullable()->default('NULL');
        });
    }
}
