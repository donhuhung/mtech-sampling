<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingCustomer2 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_customer', function($table)
        {
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->string('name', 191)->default('null')->change();
            $table->string('cmnd', 191)->default('null')->change();
            $table->string('dob', 191)->default('null')->change();
            $table->string('phone', 191)->default('null')->change();
            $table->string('address', 191)->default('null')->change();
            $table->string('otp', 191)->default('null')->change();
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_customer', function($table)
        {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->string('name', 191)->default('NULL')->change();
            $table->string('cmnd', 191)->default('NULL')->change();
            $table->string('dob', 191)->default('NULL')->change();
            $table->string('phone', 191)->default('NULL')->change();
            $table->string('address', 191)->default('NULL')->change();
            $table->string('otp', 191)->default('NULL')->change();
        });
    }
}
