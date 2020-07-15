<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingCustomer extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_customer', function($table)
        {
            $table->string('name', 191)->nullable()->change();
            $table->string('cmnd', 191)->nullable()->change();
            $table->string('dob', 191)->nullable()->change();
            $table->string('phone', 191)->nullable()->change();
            $table->string('address', 191)->nullable()->change();
            $table->string('otp', 191)->nullable()->change();
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->dropColumn('deleted_at');
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_customer', function($table)
        {
            $table->string('name', 191)->nullable(false)->change();
            $table->string('cmnd', 191)->nullable(false)->change();
            $table->string('dob', 191)->nullable(false)->change();
            $table->string('phone', 191)->nullable(false)->change();
            $table->string('address', 191)->nullable(false)->change();
            $table->string('otp', 191)->nullable(false)->change();
            $table->timestamp('created_at')->default('current_timestamp()');
            $table->timestamp('updated_at')->nullable()->default('NULL');
            $table->timestamp('deleted_at')->nullable()->default('NULL');
        });
    }
}
