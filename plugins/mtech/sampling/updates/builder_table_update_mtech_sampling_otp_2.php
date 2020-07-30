<?php namespace Mtech\Sampling\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateMtechSamplingOtp2 extends Migration
{
    public function up()
    {
        Schema::table('mtech_sampling_otp', function($table)
        {
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->string('phone', 191)->default('null')->change();
        });
    }
    
    public function down()
    {
        Schema::table('mtech_sampling_otp', function($table)
        {
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
            $table->string('phone', 191)->default('NULL')->change();
        });
    }
}
